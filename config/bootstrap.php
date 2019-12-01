<?php
use Cake\Core\Configure;

/**
 * Payment plugin additional configuration.
 */
Configure::write('Payment', [
    'PayU' => [
        'url' => env('PAYU_URL'),
        'pos_id' => env('PAYU_POS_ID'),
        'signature_key' => env('PAYU_SIGNATURE_KEY'),
        'client_id' => env('PAYU_CLIENT_ID'),
        'client_secret' => env('PAYU_CLIENT_SECRET'),
    ],
]);
