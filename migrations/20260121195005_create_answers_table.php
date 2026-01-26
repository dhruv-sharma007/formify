<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAnswersTable extends AbstractMigration
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
        $this->table("answers")
            ->addColumn("response_id", "integer", ["signed" => false, "null" => false])
            ->addColumn("question_id", "integer", ["signed" => false, "null" => false])
            ->addColumn("answerText", "string", ["signed" => false, "null" => false])
            ->addColumn("option_id", "integer", ["signed" => false, "null" => false])
            ->addForeignKey("response_id", "responses", ['id'], ["delete" => "CASCADE"])
            ->addForeignKey("question_id", "questions", ['id'], ["delete" => "CASCADE"])
            ->addForeignKey("option_id", "options", ["id"], ["delete" => "CASCADE"])
            ->addIndex(["response_id", "question_id", "option_id"])
            ->create();
    }
}
