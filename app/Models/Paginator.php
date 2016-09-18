<?php

namespace App\Models;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\PaginationPresenter;
use Illuminate\Contracts\Pagination\Presenter;
//use Illuminate\Contracts\Pagination\Presenter;//use Illuminate\Pagination\Presenter as Presenter; //Illuminate\Contracts\Pagination\Presenter $presenter = NULL
//class Paginator extends \Illuminate\Pagination\LengthAwarePaginator

class Paginator extends LengthAwarePaginator
{
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        //$parameters = [$this->pageName => $page];

        /*if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }*/
        //dd($this->pageName); //dd($parameters);
        return //$this->path
            $this->pageName //.(Str::contains($this->path, '?') ? '&' : '').'/'
            .$page //.http_build_query($parameters, '', '&')
            .$this->buildFragment();

        //return $this->path
        //                .(Str::contains($this->path, '?') ? '&' : '?')
        //                .http_build_query($parameters, '', '&')
        //                .$this->buildFragment();
    }

    public function pages()
    {
        //dd($this);
        $presenter = new PaginationPresenter($this);
        return $presenter->render();
        //PaginationPresenter $presenter = null
        //dd(999);
        /*if (is_null($presenter) && static::$presenterResolver) {
            $presenter = call_user_func(static::$presenterResolver, $this);
        }

        $presenter = $presenter ?: new BootstrapThreePresenter($this);

        return $presenter->render();*/
    }

}