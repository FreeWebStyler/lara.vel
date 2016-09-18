<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
       //View::composer('widgets.latest_comments', 'App\Http\ViewComposers\ProfileComposer');
       // \View::composer('widgets.latest_comments', 'App\Widgets\LatestComments');
        view()->composer('*', 'App\Http\ViewComposers\LatestComments');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}