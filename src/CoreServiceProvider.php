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

    private function enablePublishing() {
        if ( !\App::runningInConsole() ) {
            return;
        }

        //migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => base_path('database/migrations/lumi-core'),
        ], 'lumi-core:migrations');
    }
}
