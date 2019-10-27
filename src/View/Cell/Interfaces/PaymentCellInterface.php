<?php
namespace Payment\View\Cell\Interfaces;

interface PaymentCellInterface
{

    /**
     * Generate button
     *
     * @param array $transaction Transaction data.
     */
    public function display(array $transaction = []);
}
