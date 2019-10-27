<?php
namespace Payment\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Payment\Exception\StatusUnauthorizedException;

class PaymentsController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent($this->plugin . '.Payment');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated([
            'status',
        ]);

        $this->Security->setConfig('unlockedActions', [
            'status',
        ]);

        // Disable layout and rendering view
        $this->viewBuilder()->setLayout(false);
        $this->render(false);
    }

    /**
     * Payment status.
     *
     * @param null|string $gateway Payment gateway name.
     * @throws StatusUnauthorizedException
     * @throws NotFoundException
     */
    public function status($gateway = null)
    {
        $this->Payment->status($gateway);
    }
}
