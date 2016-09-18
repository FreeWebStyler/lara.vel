<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\Presenter;

class PaginationPresenter implements Presenter
{
    public function __construct(Paginator $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
    }

    public function render(){
        //dd($this->paginator->url(77));

        //dd($this->paginator->lastPage());
        $cur=$this->paginator->currentPage();
        $max=$this->paginator->lastPage();
        $totalRecords=$this->paginator->total();
        $perPage=$this->paginator->perPage();



        // dd($totalRecords);        dd($perPage);

        if($totalRecords>$perPage) {
            $mid=ceil($max/2);

            $f=''; $lp1=''; $lp2='';
            if($cur>1) $f=1;
            if($cur>6 && $cur<11) {$f=1;$lp1=$cur-5;} // else
            if($cur>=11) {$f=1;$lp1=$cur-10;$lp2=$cur-5;} // else
            if($lp1!=$f) $f="<a class=swchItem href=".$f.">".$f."</a>";
            //if($lp1==$f) $f!=''; else $f="<span class=swchItem onclick='search(".$f.",1);'>".$f."</span>";

            if($lp1!='') $lp1="<a class=swchItem href=".$this->paginator->url($lp1).">".$lp1."</a>";
            if($lp2!='') $lp2="<a class=swchItem href=".$this->paginator->url($lp2).">".$lp2."</a>";
            //$pager=$f.' '.$lp1.' '.$lp2.' <span style=color:green>'.$cur.'</span><br>';       //echo $pager;
            $rp2=''; $rp1='';
            if(($cur+10)<$max) {$rp1=$cur+5; $rp2=$cur+10;} else if(($cur+5)<$max) $rp1=$cur+5; else if($cur<($max-1)) $rp1=$max-1;
            if($cur==$max) $max=''; else $max="<a class=swchItem href=".$this->paginator->url($max).">".$max."</a>";
            if($rp1!='') $rp1="<a class=swchItem href=".$this->paginator->url($rp1).">".$rp1."</a>";
            if($rp2!='') $rp2="<a class=swchItem href=".$this->paginator->url($rp2).">".$rp2."</a>";
            $mid="<a class=swchItem href=".$this->paginator->url($mid).">".$mid."</a>";
            $pr = $cur-1;
            $ne = $cur+1;
            if(!$pr>0) $prev="<span class=swchItemA>Предыдущая</span>"; else $prev="<a class=swchItem href=".$this->paginator->url($pr).">Предыдущая</a>";
            if(!$ne<$max) $next="<a class=swchItem href=".$this->paginator->url($ne).">Следущая</a>"; else $next="<span class=swchItemA>Следущая</span>";
            $cur="<span class=swchItemA>".$cur."</span>";
            if($cur+10<$mid && $mid!=$lp1 && $mid!=$lp2 && $mid!=$rp1 && $mid!=$rp2 && $mid!=$cur) // если середина больше
                $pager=$prev.' '.$f.' '.$lp1.' '.$lp2.' '.$cur.' '.$rp1.' '.$rp2.' '.$mid.' '.$max.' '.$next.'<br>';
            if($cur-10>$mid && $mid!=$lp1 && $mid!=$lp2 && $mid!=$rp1 && $mid!=$rp2 && $mid!=$cur) // если меньше
                $pager=$prev.' '.$f.' '.$mid.' '.$lp1.' '.$lp2.' '.$cur.' '.$rp1.' '.$rp2.' '.$max.' '.$next.'<br>';
            if($cur-10<$mid && $cur+10>$mid) // если она мешает
                $pager=$prev.' '.$f.' '.$lp1.' '.$lp2.' '.$cur.' '.$rp1.' '.$rp2.' '.$max.' '.$next.'<br>';
            if($mid==$lp1 || $mid==$lp2 || $mid==$rp1 || $mid==$rp2) // если она мешает
                $pager=$prev.' '.$f.' '.$lp1.' '.$lp2.' '.$cur.' '.$rp1.' '.$rp2.' '.$max.' '.$next.'<br>';

            echo $pager;

        }

        return '';
    }


    public function hasPages(){
        echo 55;
    }
}