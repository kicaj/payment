<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;
use Payment\Controller\Component\Interfaces\PaymentInterface;

class P24Component extends Component implements PaymentInterface
{

    /**
     * URL to Przelewy24 gateway
     *
     * @var string
     */
    protected $_url = 'https://sandbox.przelewy24.pl';

    /**
     * {@inheritDoc}
     */
    public function transaction($transaction = [])
    {
        $client = new Client();

        $data = [
            'p24_merchant_id' => $this->_config['merchant_id'],
            'p24_pos_id' => $this->_config['pos_id'],
            'p24_session_id' => $transaction['session_id'],
            'p24_amount' => round($transaction['amount'] * 100),
            'p24_currency' => $transaction['currency'],
            'p24_description' => $transaction['description'],
            'p24_email' => $transaction['email'],
            'p24_country' => $transaction['country'],
            'p24_url_return' => $transaction['return'],
            'p24_url_status' => $transaction['callback'],
            'p24_api_version' => 3.2,
            'p24_encoding' => 'UTF-8',
        ];

        pr($data);exit;

        $data['p24_sign'] = md5($e = $data['p24_session_id'] . '|' . $data['p24_merchant_id'] . '|' . $data['p24_amount'] . '|' . $data['p24_currency'] . '|' . $this->_config['crc']);

        $response = $client->post($this->_url . '/trnRegister', $data, [
            'ssl_verify_peer' => false,
            'ssl_verify_peer_name' => false,
        ]);

        if ($response->isOk()) {
            parse_str($response->body(), $response);

            if ($response['error'] == 0) {
                return $this->getController()->redirect($this->_url . '/trnRequest/' . $response['token']);
            } else {
                throw new \Exception('Z: ' . $response['errorMessage']);
            }
        } else {
            throw new \Exception('Blad 1');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function callback()
    {
        echo 'callback!';
        exit;
    }
}
