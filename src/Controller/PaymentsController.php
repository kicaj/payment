<?php
namespace Payment\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class PaymentsController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent($this->plugin . '.Payment');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated([
            'status',
        ]);

        $this->Security->setConfig('unlockedActions', [
            'status',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        // Disable layout
        $this->viewBuilder()->disableAutoLayout();
    }

    /**
     * Payment status.
     *
     * @param null|string $gateway Payment gateway name.
     */
    public function status($gateway = null)
    {
        $this->Payment->status($gateway);
    }
}
