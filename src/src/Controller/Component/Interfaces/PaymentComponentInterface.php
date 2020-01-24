<?php
namespace Payment\Controller\Component\Interfaces;

use Cake\Http\ServerRequest;

interface PaymentComponentInterface
{

    /**
     * Payment.
     *
     * @param array $transaction Payment transaction data.
     */
    public function pay($transaction = []);

    /**
     * Payment status.
     *
     * @param ServerRequest $request Request.
     */
    public function status(ServerRequest $request);
}
