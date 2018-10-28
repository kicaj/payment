<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Payment\Controller\Component\Interfaces\PaymentInterface;
use Payment\Exception\InterfaceException;
use Payment\Exception\GatewayException;

class PaymentComponent extends Component
{

    /**
     * Payment component
     *
     * @var string
     */
    protected $_component;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        if (isset($config['gateway']) && ($gateway = Inflector::classify($config['gateway']))) {
            list($plugin, $component) = pluginSplit($gateway);

            $config = array_merge(Configure::readOrFail('Payment.' . $component), $config);

            if (!empty($plugin) && class_exists($plugin . '\Controller\Component\\' . $component . 'Component')) {
                $this->getController()->loadComponent($gateway, $config);
            } elseif (class_exists($plugin = $this->getController()->getPlugin() . '\Controller\Component\\' . $component . 'Component')) {
                $this->getController()->loadComponent($plugin . '.' . $component, $config);
            } else {
                if (class_exists('Controller\Component\\' . $component . 'Component')) {
                    $this->getController()->loadComponent($component, $config);
                } else {
                    $this->getController()->loadComponent('Payment.' . $component, $config);
                }
            }

            $this->_component = $this->getController()->{$component};

            if (!$this->_component instanceof PaymentInterface) {
                throw new InterfaceException(__d('payment', 'Component {0} should use PaymentInterface.', $component));
            }
        } else {
            throw new GatewayException(__d('payment', 'There is not gateway selected.'));
        }
    }

    /**
     * Start payment transaction
     *
     * @param array $transaction Transaction
     */
    public function transaction($transaction = [])
    {
        if (isset($transaction['return'])) {
            $transaction['return'] = Router::url([
                'plugin' => 'Payment',
                'controller' => 'Payments',
                'action' => 'callback',
                $this->_component->getConfig('gateway'),
                '?' => [
                    'redirect' => $transaction['return'],
                ],
            ], true);
        }

        pr($this->_component->transaction($transaction));
        exit;
    }

    public function callback()
    {
        pr($this->component->register());
        exit;
    }
}
