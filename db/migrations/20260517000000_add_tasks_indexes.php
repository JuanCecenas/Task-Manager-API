<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTasksIndexes extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('tasks');
        $table->addIndex(['status'], ['name' => 'idx_tasks_status'])
            ->addIndex(['created_at'], ['name' => 'idx_tasks_created_at'])
            ->update();
    }
}
