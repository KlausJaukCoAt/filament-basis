<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
            if ($this->app->runningInConsole()) {
        $this->commands([
            \App\Console\Commands\ModuleInitCommand::class,
            \App\Console\Commands\ModuleDestroyCommand::class,
            \App\Console\Commands\ModuleListCommand::class,
            \App\Console\Commands\ModuleSyncCommand::class,
        ]);
    }
    }
}
