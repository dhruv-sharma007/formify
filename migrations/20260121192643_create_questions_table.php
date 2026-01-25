<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateQuestionsTable extends AbstractMigration
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
        $this->table('questions')
            ->addColumn('form_id', 'integer', ['signed' => false])
            ->addColumn('type', 'string', ['limit' => 50])
            ->addColumn('question_text', 'text')
            ->addColumn('is_required', 'boolean', ['default' => false])
            ->addColumn('position', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addForeignKey('form_id', 'forms', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['form_id'])
            ->addIndex(['form_id', 'position'])
            ->create();
    }
}
