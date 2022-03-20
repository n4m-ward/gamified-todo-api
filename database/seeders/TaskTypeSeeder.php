<?php

namespace Database\Seeders;

use App\Models\TaskType;
use Illuminate\Database\Seeder;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        TaskType::query()
            ->firstOrCreate([
                'id' => TaskType::TASK_DIARIA,
                'description' => TaskType::TASK_TYPE_DESCRIPTION[TaskType::TASK_DIARIA],
            ]);
        TaskType::query()
            ->firstOrCreate([
                'id' => TaskType::TASK_SEMANAL,
                'description' => TaskType::TASK_TYPE_DESCRIPTION[TaskType::TASK_SEMANAL],
            ]);
        TaskType::query()
            ->firstOrCreate([
                'id' => TaskType::TASK_MENSAL,
                'description' => TaskType::TASK_TYPE_DESCRIPTION[TaskType::TASK_MENSAL],
            ]);
    }
}
