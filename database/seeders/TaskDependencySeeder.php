<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskDependencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dependencies = [
            ['task_id' => 5, 'depends_on_task_id' => 6],
            ['task_id' => 6, 'depends_on_task_id' => 3],
            ['task_id' => 6, 'depends_on_task_id' => 2],
            ['task_id' => 6, 'depends_on_task_id' => 1],
        ];

        foreach ($dependencies as $dep) {
            DB::table('task_dependencies')->updateOrInsert(
                ['task_id' => $dep['task_id'], 'depends_on_task_id' => $dep['depends_on_task_id']],
                array_merge($dep, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
