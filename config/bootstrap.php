<?php
use Cake\Core\Configure;

/**
 * Payment plugin additional configuration.
 */
Configure::write('Payment', [
    'PayU' => [
        'url' => env('PAYMENT_PAYU_URL'),
        'pos_id' => env('PAYMENT_PAYU_POS_ID'),
        'signature_key' => env('PAYMENT_PAYU_SIGNATURE_KEY'),
        'client_id' => env('PAYMENT_PAYU_CLIENT_ID'),
        'client_secret' => env('PAYMENT_PAYU_CLIENT_SECRET'),
    ],
]);
