<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;
use Payment\Exception\StatusUnauthorizedException;

class PaymentComponent extends Component
{

    /**
     * Payment status.
     *
     * @param null|string $gateway Payment gateway name.
     * @return Entity $payment Payment entity.
     * @throws StatusUnauthorizedException
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
                echo '';
            }
        }

        throw new NotFoundException();
    }
}
