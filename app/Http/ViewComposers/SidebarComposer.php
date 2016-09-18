<?php

namespace App\Composers;

class SidebarComposer {

    public function compose($view)
    {
        dd(33); $view->with(['tags'=>'333','111',222]);
    }
}