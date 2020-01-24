<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Client;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Payment\Controller\Component\Interfaces\PaymentComponentInterface;
use Payment\Exception\RedirectParamException;

class PayUComponent extends Component implements PaymentComponentInterface
{

    /**
     * Payment PayU statuses.
     */
    public const PAYMENT_PAYU_STATUS_CANCELED = 'CANCELED';
    public const PAYMENT_PAYU_STATUS_COMPLETED = 'COMPLETED';
    public const PAYMENT_PAYU_STATUS_PENDING = 'PENDING';
    public const PAYMENT_PAYU_STATUS_WAITING = 'WAITING';

    /**
     * {@inheritDoc}
     */
    public function pay($transaction = [])
    {
        $url = Configure::readOrFail('Payment.PayU.url');

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
                    'customerIp' => $this->getController()->getRequest()->clientIp(), // Required
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
                            // @TODO Tax or tax value
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
                    return $this->getController()->redirect($response['redirectUri']);
                } else {
                    throw new RedirectParamException($response['status']['statusDesc']);
                }
            } else {
                throw new UnauthorizedException(__d('payment', 'Empty or incorrect access token.'));
            }
        } else {
            throw new UnauthorizedException(__d('payment', 'Request to provider is incorrect.'));
        }
    }

    /**
     * {@inheritDoc}
     * @return boolean True, if it is successful.
     */
    public function status(ServerRequest $request)
    {
        $headers = $request->getHeader('Openpayu-Signature');
        $signature = [];

        if (isset($headers[0]) && preg_match('/signature=([0-9a-z]{32})/', $headers[0], $signature)) {
            $key = Configure::readOrFail('Payment.PayU.signature_key');

            if (isset($signature[1]) && $signature[1] == md5($request->input() . $key)) {
                $this->getController()->getEventManager()->dispatch(new Event('Payment.PayU.afterStatus', $this, [
                    'request' => $request->input('json_decode'),
                ]));

                return true;
            }
        }

        return false;
    }
}
