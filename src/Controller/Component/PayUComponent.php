<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Payment\Controller\Component\Interfaces\PaymentComponentInterface;

class PayUComponent extends Component implements PaymentComponentInterface
{

    /**
     * Payment PayU statuses.
     */
    const PAYMENT_PAYU_STATUS_CANCELED = 'CANCELED';
    const PAYMENT_PAYU_STATUS_COMPLETED = 'COMPLETED';
    const PAYMENT_PAYU_STATUS_PENDING = 'PENDING';
    const PAYMENT_PAYU_STATUS_WAITING = 'WAITING';

    public function transaction($transaction = [])
    {

    }

    /**
     * {@inheritDoc}
     * @return boolean True, if it is successful.
     */
    public function status(ServerRequest $request)
    {
        $headers = $request->getHeader('Openpayu-Signature');
        $signature = [];

        if (isset($headers[0]) && preg_match('/signature=([0-9a-z]{32})/', $headers[0], $signature)) {
            $key = Configure::readOrFail('Payment.PayU.second_key');

            if (isset($signature[1]) && $signature[1] == md5($request->input() . $key)) {
                $this->getController()->getEventManager()->dispatch(new Event('Payment.PayU.afterStatus', $this, [
                    'request' => $request->input('json_decode'),
                ]));

                return true;
            }
        }

        return false;
    }
}
