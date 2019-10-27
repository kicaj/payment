<?php
namespace Payment\Model\Table;

use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;

class PaymentsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('payment_payments');
        $this->setDisplayField('transaction');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }
}
