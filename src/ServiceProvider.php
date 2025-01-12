<?php
namespace RA;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
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
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'ra');
    }

    private function enablePublishing() {
        if ( !\App::runningInConsole() ) {
            return;
        }

        //config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('ra.php'),
        ], 'ra:config');

        //migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => base_path('database/migrations/ra'),
        ], 'ra:migrations');
    }
}
