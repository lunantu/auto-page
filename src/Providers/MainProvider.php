<?php

namespace Lunantu\AutoPage\Providers;

use Lunantu\AutoPage\Helpers\Route;
use Illuminate\Support\ServiceProvider;

class MainProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('auto_page_router', function(){
            return new Route();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
