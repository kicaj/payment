<?php
namespace Payment\Model\Table;

use Cake\ORM\Table;

class PaymentsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('payment_payments');
        $this->setDisplayField('transaction');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }
}
