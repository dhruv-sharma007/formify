<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateResponsesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table("responses")
            ->addColumn("form_id", "integer", ["signed" => false])
            ->addColumn("ip_address", "string", ["limit" => 150])
            ->addColumn("user_agent", "string")
            ->addTimestamps()
            ->create();
    }
}
