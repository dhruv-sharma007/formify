<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOptionsTable extends AbstractMigration
{

    public function change(): void
    {
        $this->table("options")
            ->addColumn("question_id", "integer", ["signed" => false])
            ->addColumn("option_text", "string", ["signed" => false, "limit" => 150])
            ->addColumn("position", "integer")
            ->addForeignKey("question_id", "questions", 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
