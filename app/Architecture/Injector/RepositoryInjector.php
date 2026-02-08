<?php

namespace App\Architecture\Injector;

use App\Architecture\Repositories\Classes\TaskDependencyRepository;
use App\Architecture\Repositories\Classes\TaskRepository;
use App\Architecture\Repositories\Classes\UserRepository;
use App\Architecture\Repositories\Interfaces\ITaskDependencyRepository;
use App\Architecture\Repositories\Interfaces\ITaskRepository;
use App\Architecture\Repositories\Interfaces\IUserRepository;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class RepositoryInjector extends ServiceProvider
{
    /**
     * Register repository bindings
     */
    public function register(): void
    {
        $this->app->singleton(IUserRepository::class, function ($app) {
            return new UserRepository($app->make(User::class));
        });

        $this->app->singleton(ITaskRepository::class, function ($app) {
            return new TaskRepository($app->make(Task::class));
        });

        $this->app->singleton(ITaskDependencyRepository::class, function ($app) {
            return new TaskDependencyRepository($app->make(TaskDependency::class));
        });
    }
}
