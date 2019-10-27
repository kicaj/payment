<?php
namespace Payment\Controller\Component\Interfaces;

use Cake\Http\ServerRequest;

interface PaymentComponentInterface
{

    public function transaction($transaction = []);

    public function status(ServerRequest $request);
}
