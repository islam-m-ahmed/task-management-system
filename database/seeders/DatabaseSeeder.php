<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolePermissionSeeder::class,
            TaskSeeder::class,
            TaskDependencySeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Manager: manager@example.com / password123');
        $this->command->info('User: user@example.com / password123');
    }
}
