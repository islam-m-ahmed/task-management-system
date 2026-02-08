<?php

namespace App\Providers;

use App\Architecture\Repositories\Classes\TaskDependencyRepository;
use App\Architecture\Repositories\Classes\TaskRepository;
use App\Architecture\Repositories\Interfaces\ITaskDependencyRepository;
use App\Architecture\Repositories\Interfaces\ITaskRepository;
use App\Architecture\Responder\ApiHttpResponder;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Classes\AuthService;
use App\Architecture\Services\Classes\TaskService;
use App\Architecture\Services\Interfaces\IAuthService;
use App\Architecture\Services\Interfaces\ITaskService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
