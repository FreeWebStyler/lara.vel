<?php

namespace App\Http\ViewComposers;

//use Illuminate\Routing\Route;
//use Illuminate\View\View;
use Illuminate\Support\Facades\Route;


class LatestComments
{
    public function compose($view) // View $view
    {
        //$items = \App\Models\Comment::getPublished;
        //dd($items);
        //$view->with('latest_news', \App\Models\Comment::getPublished);
//dd(Route::getCurrentRoute()->getPath());
        //dd($currentPath= Route::getFacadeRoot()->current()->uri());


        //dd(Route::current());
        /*Route::parameters();

// Before 4.1
        Route::getCurrentRoute();
        Route::getParameters();*/

        //$view->with(['comments' => \App\Models\Comment::lastPublished()]); - будет перекрывать всё!
        $view->with(['LatestComments' => \App\Models\Comment::lastPublished()]);
        //echo 'Route current uri: '.$currentPath= Route::getFacadeRoot()->current()->uri();
    }
}

//View::composer('latest_news', 'LatestNews');