<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'id' => 1,
                'title' => 'Design Database Schema',
                'description' => 'Create the initial database schema for the project including all tables and relationships.',
                'status' => TaskStatus::COMPLETED->value,
                'due_date' => now()->addDays(5),
                'assigned_user_id' => 3, // Bob User
                'created_by' => 1,       // John Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Implement User Authentication',
                'description' => 'Set up user authentication using Laravel Sanctum with JWT tokens.',
                'status' => TaskStatus::COMPLETED->value,
                'due_date' => now()->addDays(7),
                'assigned_user_id' => 3, // Bob User
                'created_by' => 1,       // John Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'title' => 'Create API Endpoints',
                'description' => 'Develop RESTful API endpoints for task management.',
                'status' => TaskStatus::IN_PROGRESS->value,
                'due_date' => now()->addDays(10),
                'assigned_user_id' => 4, // Alice User
                'created_by' => 1,       // John Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'title' => 'Write Unit Tests',
                'description' => 'Write comprehensive unit tests for all services and repositories.',
                'status' => TaskStatus::PENDING->value,
                'due_date' => now()->addDays(14),
                'assigned_user_id' => 4, // Alice User
                'created_by' => 2,       // Jane Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'title' => 'Documentation',
                'description' => 'Create API documentation and README file.',
                'status' => TaskStatus::PENDING->value,
                'due_date' => now()->addDays(20),
                'assigned_user_id' => 5, // Charlie User
                'created_by' => 2,       // Jane Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'title' => 'Code Review',
                'description' => 'Review all code and ensure coding standards are followed.',
                'status' => TaskStatus::PENDING->value,
                'due_date' => now()->addDays(18),
                'assigned_user_id' => null,
                'created_by' => 1,       // John Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tasks as $taskData) {
            DB::table('tasks')->updateOrInsert(
                ['id' => $taskData['id']],
                $taskData
            );
        }
    }
}
