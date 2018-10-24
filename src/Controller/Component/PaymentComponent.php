<?php
namespace Payment\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Payment\Controller\Component\Interfaces\PaymentInterface;

class PaymentComponent extends Component
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        if (isset($config['gateway']) && ($gateway = Inflector::classify($config['gateway']))) {
            list(, $component) = pluginSplit($gateway);

            $config = array_merge(Configure::readOrFail('Payment.' . $component), $config);

            try {
                $this->getController()->loadComponent($gateway, $config);

                $this->component = $this->getController()->{$component};

                if (!$this->component instanceof PaymentInterface) {
                    throw new InterfaceException(__d('payment', 'Gateway should use PaymentInterface.'));
                }
            } catch (\Exception $e) {
                throw new \Exception('A: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('Brak wyboru serwisu płatności');
        }
    }

    public function transaction($transaction)
    {
        pr($this->component->transaction($transaction));
        exit;
    }

    public function callback()
    {
        pr($this->component->register());
        exit;
    }
}
