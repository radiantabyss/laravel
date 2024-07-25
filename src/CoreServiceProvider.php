<?php
namespace Lumi\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //enable publishing
        $this->enablePublishing();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //load configs
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'lumi-core');
    }

    private function enablePublishing() {
        if ( !\App::runningInConsole() ) {
            return;
        }

        //config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('lumi-core.php'),
        ], 'lumi-core:config');

        //migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => base_path('database/migrations/lumi-core'),
        ], 'lumi-core:migrations');
    }
}
