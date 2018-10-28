<?php
namespace Payment\Controller;

use App\Controller\AppController;

class PaymentsController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        // Remove layout and view rendering
        $this->viewBuilder()->setLayout(false);
        $this->render(false);

        $this->Auth->allow([
            'callback',
        ]);
    }

    public function callback($gateway)
    {
        /*$gateway = Inflector::classify($gateway);

        $this->loadComponent('Payment.' . $gateway);

        return $this->{$gateway}->callback();*/
    }
}
