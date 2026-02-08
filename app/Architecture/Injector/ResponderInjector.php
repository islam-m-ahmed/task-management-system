<?php

namespace App\Architecture\Injector;

use App\Architecture\Responder\ApiHttpResponder;
use App\Architecture\Responder\IApiHttpResponder;
use Illuminate\Support\ServiceProvider;

class ResponderInjector extends ServiceProvider
{
    /**
     * Register responder bindings
     */
    public function register(): void
    {
        $this->app->singleton(IApiHttpResponder::class, ApiHttpResponder::class);
    }
}
