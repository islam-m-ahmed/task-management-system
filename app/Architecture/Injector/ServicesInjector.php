<?php

namespace App\Architecture\Injector;

use App\Architecture\Services\Classes\AuthService;
use App\Architecture\Services\Classes\TaskService;
use App\Architecture\Services\Interfaces\IAuthService;
use App\Architecture\Services\Interfaces\ITaskService;
use Illuminate\Support\ServiceProvider;

class ServicesInjector extends ServiceProvider
{
    /**
     * Register service bindings
     */
    public function register(): void
    {
        $this->app->singleton(IAuthService::class, AuthService::class);
        $this->app->singleton(ITaskService::class, TaskService::class);
    }
}
