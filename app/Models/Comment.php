<?php

namespace App\Models;

use DB;
//use Config;
use Illuminate\Database\Eloquent\Model;
//use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Session; //use App\Models\Role;
use App\Helpers\RatesComments;

class Comment extends Model{
    use SoftDeletes;

    protected $fillable = ['id', 'post_id', 'parent_id', 'user_id', 'comment', 'published', 'published_at'];
    protected $dates = ['deleted_at'];

    public function author(){
        return $this->hasOne('App\User', 'id', 'user_id'); //, 'post_tag', 'post_id', 'tag_id'
    }

    /*public function author(){
        return $this->hasOne('User');
    }*/

    function build_comments_tree($array) {
        echo gettype($array); dd();
        $tree = array();
        $ar=array();
        if (!empty($array)) {
            /*foreach($array as $id => $row){
                echo $row['id'],$row['parent_id'],'<br>';
            }*/

            foreach($array as $id => &$row){
                //if($row['id']==8) dd(5);

                //echo $row['id'],$row['parent_id'],'<br>'; continue;

                if($row['parent_id'] == 0) { // со строгой типи3ацией с данными и3 БД работать не будет, т.к. там создаются строки цифры, а не просто цифры (0 !== '0') решение: либо == 0 либо (int)$row['parent_id'] ИЛИ if($parentId = $row['parent_id']) $array[$row['parent_id']]['children'][$id] = &$row; else $tree[$id] = &$row;
                    $ar[$row['id']] = $row;
                    $tree[$row['id']] = &$ar[$row['id']];
                } else {
                    $ar[$row['id']] = &$row;
                    $ar[$row['parent_id']]['children'][$row['id']] = &$row;
                }
            }
        }
        //dd($ar);
        //dd($tree);
        return $tree;
    }
    
    function getPublished2($post_id){
        $comments = Comment::latest('created_at') //$comments = Post::latest('published_at') - не будет ошибк0й! оО
                ->where('post_id','=',$post_id)
                ->published()->get();
        return $comments;
    }

    function getPublished3($post_id){
        $comments = Comment::latest('created_at') //$comments = Post::latest('published_at') - не будет ошибк0й! оО
        ->where('post_id','=',$post_id)
            ->published()->get();
        $comments = $this->build_comments_tree($comments);
        return $comments;
    }

    function getPublished($post_id){

        $user = Auth::user();

        if($user!=null && $user->can('create-post')) $cans['create-post']=true;
        if($user!=null && $user->can('editall-posts')) $cans['editall-posts']=true;
        if($user!=null && $user->can('deleteAll-posts')) $cans['deleteAll-posts']=true;

        //DB::setFetchMode('PDO::FETCH_NAMED'); //DB::setFetchMode('PDO::FETCH_CLASS'); //DB::connection()->setFetchMode('PDO::FETCH_ASSOC');
        //Config::set('database.fetch', 'PDO::FETCH_ASSOC'); //echo DB::getFetchMode(); //dd(33);
        DB::setFetchMode(\PDO::FETCH_ASSOC); //DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $comments = DB::table('comments') //SELECT comments.*, users.name as author from comments JOIN users ON users.id = comments.user_id WHERE post_id=4
        //$comments = $comments->setFetchMode('PDO::FETCH_NAMED')
            ->select('comments.*', 'users.name as author')
            ->join('users', 'users.id', '=', 'user_id')
            ->where('post_id', '=', $post_id) // ->where('deleted_at', '=', null)
            ->where('published', '=', '1')->toSql();
        //dd($comments);
        //$stmt = $dbh->query('SELECT * from comments WHERE post_id=40'); while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ $c++; if($c>100) die();
        //Config::set('database.fetch', 'PDO::FETCH_ASSOC');
        $db = DB::connection()->getPdo();
        //DB::setFetchMode(\PDO::FETCH_ASSOC);
        //DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        //echo DB::getFetchMode();
        $query = $db->prepare($comments);
        $query->execute([$post_id, 1]);

        $comments=[]; $tree=[];

        //while ($res = $query->fetch()) { //$query->fetchColumn()
        while($res= $query->fetch(\PDO::FETCH_ASSOC)){
            //dd($res);
            $id=$res['id'];
            $comments[$id] = $res;

            if((int)$comments[$id]['parent_id'] === 0){
                $tree[$id] = &$comments[$id]; //$comments[$id]['root']=$id;
            } else {
                $comments[$comments[$id]['parent_id']]['children'][$id] = &$comments[$id]; //$comments[$id]['root']=$comments[$comments[$id]['parent_id']]['root'];
            }

        }

        //echo '<plaintext>';  // dd($tree);   //echo '<pre>';  //print_r($tree);  //dd();

        $id=0; $coms=''; $i=1; $citem=[]; $levels=[]; $level=0; $lid=''; $ldif=0;
        array_walk_recursive($tree, function ($item, $key) use(&$coms, &$id, &$i, &$citem, &$levels, &$level, &$lid, &$lil, &$user, &$ldif, &$comments)
        {   // citem - current item; lil - latest item level; lid - latest item id;

            if($key == 'id') $citem['id']=$item;
            if($key == 'comment') $citem['comment']=$item;
            if($key == 'parent_id') $citem['par']=$item;
            if($key == 'user_id') $citem['user_id']=$item;
            if($key == 'author') $citem['author']=$item;
            if($key == 'rate') $citem['rate']=$item;
            if($key == 'created_at') $citem['created_at']=$item;
            if($key == 'deleted_at' && $item != null) $citem['comment']='Комментарий удалён';

            if($i%11==0) {

                if($citem['par']==0){ $levels[$citem['id']]=0; $level=0; }
                if($lid === $citem['par']) { $level++; $levels[$citem['id']]=$level; }
                if($lid!='' && $lid != $citem['par'] && $citem['par']!=0) $level=$levels[$citem['par']];
                if(isset($levels[$citem['id']])) $level=$levels[$citem['id']]; else { $level=$levels[$citem['par']]+1; $levels[$citem['id']]=$level;}
                if($lid=='') $lil=0;

                $dif = $lil-$level; $divs='';

                if($lil == $level && $lid != '') { $coms.='</div>'; }
                if($lil > $level){ if($dif >= 1) $dif++; for($k=0; $k < $dif; $k++) $divs.='</div>'.PHP_EOL; $coms.=$divs;}

                if($level < 10) $class='com'; else $class='comb';
                $class='com';

                //echo 'SIGN '.$rate['sign'].' _SIGN';
                //echo 'ID_'. $citem['id'];

                if(isset($user->id) && $citem['user_id'] != $user->id) $sign = RatesComments::getRateStatus($citem, $user->id); else $sign = 2; // Сделать чтение файла 1 ра3, вместо постоянного //echo $citem['id'].' SIGN_ '. $sign .' _SIGN<br>';
                if($sign === '+') $sign=1;
                if($sign === '-') $sign='-1';
                if($sign == 0) $sign=0;

                switch($sign){
                    case '1': //echo '| SIGN_ '.$rate['sign'].' _SIGN |';
                        $red = '<span class="grey" onclick="rate(this, '.$citem['id'].', \'-\')">👎</span>';
                        $lime = '<span class="lime" onclick="rate(this, '.$citem['id'].', \'+\')">👍</span>';
                        break;
                    case '-1':
                        $red = '<span class="red" onclick="rate(this, '.$citem['id'].', \'-\')">👎</span>';
                        $lime = '<span class="grey" onclick="rate(this, '.$citem['id'].', \'+\')">👍</span>';
                        break;
                    case 2:
                        $red = '';
                        $lime = '';
                        break;
                    default:
                        $red = '<span class="grey" onclick="rate(this, '.$citem['id'].', \'-\')">👎</span>';
                        $lime = '<span class="grey" onclick="rate(this, '.$citem['id'].', \'+\')">👍</span>';
                        break;
                }

                //$coms.='<div id='.$citem['id'].' class=com>'.$citem['comment'];
                //$coms.=PHP_EOL.'<div id='.$citem['id'].' class='.$class.'>'.$citem['comment'];
                if($citem['par']!=0 ) $to = '<span onmouseover="showOrig(this, event, '.$citem['par'].')" onmouseleave=showOrig("destr")> -> </span>'.$comments[$citem['par']]['author']; else $to=''; // && $citem['author'] != $comments[$citem['par']]['author']
                $coms.= '<div class='.$class.'><span id=com'.$citem['id'].'><span title="'.$citem['par'].' '.$citem['id'] . '">'.$citem['comment'].'</span> '.$citem['author'].$to.' '.$red.' '.$citem['rate'].' '.$lime;
                $coms.= '<button type="button" class="btn-link btn-xs" commentid=' . $citem['id'] . ' onclick="setReplyForm(this,\'' . $citem['id'] . '\')">Ответить</button>';
                if ($user != null && ($user->id == $citem['user_id']) || isset($cans['editall-posts'])) $coms .= '<button type=button class="btn-link btn-xs" onclick="setEditForm(this,\'' . $citem['id'] . '\')">Edit</button>';
                if ($user != null && ($user->id == $citem['user_id']) || isset($cans['editall-posts'])) $coms .= '<button type="button" postid=' . $citem['id'] . ' class="btn btn-link btn-xs" onclick="confirmDelete(this, ' . $citem['id'] . ', \'комментарий\')" >Delete</button>';
                if ($user != null && ($user->id == $citem['user_id']) || isset($cans['editall-posts'])) $coms .= '<button type="button" postid=' . $citem['id'] . ' class="btn btn-link btn-xs" onclick="confirmDelete(this, ' . $citem['id'] . ', \'комментарий\', \'destroy\')" >Destroy</button>';
                $coms.= '</span>';

                $lid=$citem['id']; $lil=$level;
            }
              $i++;
        });

        $divs='';

        //for($k=0; $k < $level; $k++){ echo 'ks'.$level; $divs.='</div>'; } $coms.=$divs;

        if($coms !='') $coms.='</div>';

        //echo '<plaintext>'; print_r($coms); die;

        return $coms; //return view('post.show', ['post'=>$post, 'userid'=>$user['id'], 'cans'=>$cans]);
    }

    static function lastPublished(){
        $comments = Comment::latest('created_at') //$comments = Post::latest('published_at') - не будет ошибк0й! оО
            ->published()->limit(5)->get();
        return $comments;
    }

    function scopePublished($query){
        $query->where('published','=',1);
        return $query;
    }
    
    function scopeUnPublished($query){
        $query->where('published','=',0);
        return $query;
    }

    function scopeUnPublished2($query){
        $query->where('published','=',0);
        return $query;
    }
    
}
