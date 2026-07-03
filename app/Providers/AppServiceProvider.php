<?php

namespace App\Providers;

use App\Actions\TaskControllerActions;
use App\Concerns\TaskControllerActionsInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskControllerActionsInterface::class, TaskControllerActions::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
