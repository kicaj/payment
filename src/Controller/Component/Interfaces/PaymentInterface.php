<?php
namespace Payment\Controller\Component\Interfaces;

interface PaymentInterface
{
    
    public function transaction($transaction = []);

    public function callback();
}
