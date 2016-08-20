<?php

namespace Wppd\Form;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.wppd.form', function ($app) {
            return $app['Wppd\Form\Commands\Form'];
        });

        $this->commands('command.wppd.form');
    }
}
