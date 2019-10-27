<?php
namespace Payment\View\Cell;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Http\Exception\UnauthorizedException;
use Cake\View\Cell;
use Cake\Routing\Router;
use Payment\Exception\RedirectParamException;
use Payment\View\Cell\Interfaces\PaymentCellInterface;

class PayUCell extends Cell implements PaymentCellInterface
{

    /**
     * {@inheritDoc}
     */
    public function display(array $transaction = [])
    {
        $url = 'https://secure.snd.payu.com';

        $client = new Client();

        $auth = $client->post($url . '/pl/standard/user/oauth/authorize', [
            'grant_type' => 'client_credentials',
            'client_id' => Configure::readOrFail('Payment.PayU.client_id'),
            'client_secret' => Configure::readOrFail('Payment.PayU.client_secret'),
        ]);

        if ($auth->isOk()) {
            $auth = $auth->getJson();

            if (isset($auth['access_token']) && !empty($auth['access_token'])) {
                $data = [
                    'merchantPosId' => Configure::readOrFail('Payment.PayU.pos_id'), // Required
                    'description' => $transaction['description'], // Required
                    'currencyCode' => $transaction['currency'], // Required
                    'totalAmount' => $transaction['amount'] * 100, // Required
                    'notifyUrl' => $transaction['url_status'] ?? Router::url([
                        'plugin' => 'Payment',
                        'controller' => 'Payments',
                        'action' => 'status',
                        'PayU',
                    ], true),
                    'customerIp' => $this->request->clientIp(), // Required
                    'buyer' => [
                        'email' => $transaction['customer']['email'], // Required
                    ],
                    'products' => [],
                ];

                if (!empty($transaction['products']) && is_array($transaction['products'])) {
                    foreach ($transaction['products'] as $product) {
                        $data['products'][] = [
                            'name' => $product['name'],
                            'quantity' => $product['quantity'],
                            'unitPrice' => $product['price'] * 100,
                        ];
                    }
                } else {
                    // throw new...
                }

                if (isset($transaction['id'])) {
                    $data['extOrderId'] = $transaction['id'];
                } else {
                    // throw new...
                }

                // Redirect after payment
                if (isset($transaction['url_thanks'])) {
                    $data['continueUrl'] = $transaction['url_thanks'];
                }

                // Client identifier
                if (isset($transaction['customer']['id'])) {
                    $data['buyer']['extCustomerId'] = $transaction['customer']['id'];
                }

                $order = $client->post($url . '/api/v2_1/orders', json_encode($data), [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $auth['access_token'],
                        'Content-Type' => 'application/json',
                    ],
                ]);

                $response = $order->getJson();

                if ($order->isRedirect()) {
                    $this->set('payment', $response['redirectUri']);
                } else {
                    throw new RedirectParamException($response['status']['statusDesc']);
                }
            } else {
                echo 'access_token empty!';
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}
