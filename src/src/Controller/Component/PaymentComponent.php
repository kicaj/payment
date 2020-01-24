<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;
use Payment\Exception\StatusUnauthorizedException;
use Cake\Http\Exception\BadRequestException;

class PaymentComponent extends Component
{

    /**
     * Payment.
     *
     * @param string $gateway Payment gateway name.
     * @return array $transaction Payment transaction data.
     */
    public function pay($gateway, $transaction = [])
    {
        $this->getController()->loadComponent('Payment.' . $gateway = Inflector::classify($gateway));

        $this->getController()->{$gateway}->pay($transaction);
    }

    /**
     * Payment status.
     *
     * @param null|string $gateway Payment gateway name.
     * @return Entity $payment Payment entity.
     * @throws StatusUnauthorizedException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function status($gateway = null)
    {
        if (!is_null($gateway)) {
            $this->getController()->loadComponent('Payment.' . $gateway = Inflector::classify($gateway));

            $request = $this->getController()->getRequest();

            if (!empty($transaction = $request->input())) {
                $this->getController()->loadModel('Payment.Payments');

                $payment = $this->getController()->Payments->patchEntity($this->getController()->Payments->newEntity(), [
                    'gateway' => $gateway,
                    'transaction' => $transaction,
                ]);

                if ($this->getController()->{$gateway}->status($request)) {
                    $this->getController()->Payments->save($payment);

                    return $payment;
                } else {
                    throw new StatusUnauthorizedException();
                }
            } else {
                throw new BadRequestException(__d('payment', 'Input data is empty.'));
            }
        }

        throw new NotFoundException();
    }
}
