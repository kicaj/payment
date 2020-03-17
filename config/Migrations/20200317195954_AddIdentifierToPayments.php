<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddIdentifierToPayments extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('payment_payments')
            ->addColumn('identifier', 'string', [
                'default' => null,
                'limit' => 255,
                'after' => 'gateway',
            ])
            ->update();
    }
}
