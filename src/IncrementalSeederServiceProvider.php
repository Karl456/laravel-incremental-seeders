<?php

namespace Karl456\IncrementalSeeders;

use Illuminate\Support\ServiceProvider;
use Karl456\IncrementalSeeders\Commands\IncrementalSeederCommand;
use Karl456\IncrementalSeeders\Commands\MakeIncrementalSeederCommand;

class IncrementalSeederServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                IncrementalSeederCommand::class,
                MakeIncrementalSeederCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/incremental-seeders.php' => config_path('incremental-seeders.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/incremental-seeders.php', 'incremental-seeders'
        );
    }
}
