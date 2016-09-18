<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Paginator;

class TagController extends Controller
{
    public function notin(Request $request, $tags=null, $page=1)
    {
        $route = $request->route()->getName(); $perpage = 2;

        $user = Auth::user();
        if($user!=null && $user->can('create-post')) $canCreatePost=true; else $canCreatePost=false;
        if($user!=null && $user->can('editall-posts')) $canEditAll=true; else $canEditAll=false;
        if($user!=null && $user->can('deleteAll-posts')) $canDeleteAll=true; else $canDeleteAll=false;

        //dd(count($tag));
       /*$tags_id = DB::table('tags')->select('id');
        if (count($tags) > 1) {
            foreach ($tags as $key => $value) if($value[0]=='!' && $value[1]=='=') $tagsin[] = substr($value,2); else $tagsin[] = $value;
            $tags_id->whereIn('tag', $tagsin);
        } else {$tags_id->where('tag', '=', $tags); $route='tag';} //dd($tagsin);
        $tags_id = $tags_id->get(); //dd($tags_id);*/

        $tags_id = DB::table('tags')->select('*');//toSql(); // 0 is just "empty" value, it doesn't matter here yet
        foreach ($tags as $key => $value) if($value[0]=='!' && $value[1]=='=') $tagsin[] = substr($value,2); else $tagsin[] = $value;
        $tags_id=$tags_id->whereIn('tag', $tagsin)->toSql();
        //dd($tagsin);
        $db = DB::connection()->getPdo();

        $query = $db->prepare($tags_id);
        $query->execute($tagsin);

         while ($res = $query->fetch()) { //$query->fetchColumn()
            if(in_array('!='.$res[1],$tags)) $tagsNotIn[]=$res['id']; else $tagsIn[]=$res['id'];
        }

        if(!isset($tagsNotIn)) return 0;

        //dd($tagsIn,$tagsNotIn);
        // END TAGS_ID
        // POSTS_ID
        DB::enableQueryLog();

        $posts_id = DB::table(DB::raw('`posts`, `post_tag`'));
        if($route == 'tags') $posts_id->select(DB::raw('posts.id, post_tag.tag_id')); else $posts_id->select(DB::raw('DISTINCT posts.id'));
        $posts_id->where(DB::raw('posts.id'), '=', DB::raw('post_tag.post_id')); //->where('posts.id', '=', '1');

        //$posts_id=$posts_id->whereIn('post_tag.tag_id',array_merge($tagsIn,$tagsNotIn))->toSql();
        $posts_id=$posts_id->whereIn('post_tag.tag_id',$tagsIn)->toSql();
        //$posts_id=$posts_id->whereNotIn('post_tag.tag_id',$tagsNotIn)->toSql();

        $db = DB::connection()->getPdo();
        $query = $db->prepare($posts_id);
        $query->execute(array_merge($tagsIn,$tagsNotIn));
        //$query->execute($tagsNotIn);
        //dd(array_merge($tagsIn,$tagsNotIn));
        $posts_id=[];
        //dd($query);

        while ($res = $query->fetch()) { //$query->fetchColumn()
            $posts_id[$res['id']][$res['tag_id']] = 1;
            //$posts_id[$res['id']][] =
            //$newArray = $res['tag_id'];
            if(in_array($res['tag_id'], $tagsNotIn)) {
               foreach($posts_id as $post_idk => $post_idv){
                  foreach($post_idv as $key => $val){
                    if(in_array($key, $tagsNotIn)) unset($posts_id[$post_idk]);
                  }
               }
            }
           } //$posts_id[$res['id']][]=$res['tag_id']; //echo $res['tag_id'];


       //dd($tagsNotIn);

        //$posts_id = $posts_id->get();//->toSql();//->get();//->toSql();
       //dd($posts_id);
        //$queries = DB::getQueryLog(); dd($queries);
        //$posts_id=[$posts_id[0]->id,$posts_id[1]->id];
        $posts_ids=[];
        //$posts_ids=$posts_id;
        /*if ($route == 'tag') foreach ($posts_id as $key => $value) $posts_id[$key] = $value->id; else {
            foreach ($posts_id as $key => $value) $posts_ids[] = $value->id;
            $posts_id=$posts_ids;
        }*/

        if ($route == 'tag') foreach ($posts_id as $key => $value) $posts_id[$key] = $value->id; else {
          foreach ($posts_id as $key => $value) $posts_ids[] = $key;
          $posts_id=$posts_ids;
        }

        //$posts_id=array_unique($posts_id);
        //dd($posts_id);

        $total = count($posts_id); // кол-во новостей
        if ($page > ceil($total / $perpage)) return view('errors.404'); //retrdfvurn redirect()->route('posts');

        //dd($posts_id);
        // END POSTS_ID
        // POSTS

        $posts = DB::table('posts')
            ->select('posts.*','users.name as author',DB::raw('GROUP_CONCAT(tags.tag) as tags'))
            ->leftjoin('post_tag', 'post_tag.post_id', '=', 'posts.id')
            ->leftjoin('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->where('published', '=', '1');
        $posts->whereIn('posts.id',$posts_id);

        $posts=$posts->groupBy('posts.id')
            ->orderBy('published_at', 'desc')->skip(($page-1)*$perpage)->take($perpage)->get();
        //dd($posts);
        // $queries = DB::getQueryLog(); dd($queries);
        if(strpos($request->getPathInfo(),'/page')) $pageName=''; else $pageName=$request->getRequestUri().'/page/';
        $posts=new Paginator($posts, $total, $perpage, $page, ['path'=>'path','pageName'=>$pageName]);

        //dd($posts);
        return $posts;
         //return view('post.index', ['posts'=>$posts,'canCreatePost'=>$canCreatePost,
            //'canDeleteAll'=>$canDeleteAll,'userid'=>$user['id'],
            //'canEditAll'=>$canEditAll]);
    }

    public function index(Request $request, $tag=null, $page=1)
    { // если нет данных о странице, то по-умолчанию - первая

        $route = $request->route()->getName();
        //dd($request->route()->getName());

        $perpage = 2; // сколько выводить на странице
        //echo 555;dd($p);

        $user = Auth::user();

        if ($user != null && $user->can('create-post')) $canCreatePost = true; else $canCreatePost = false;
        if ($user != null && $user->can('editall-posts')) $canEditAll = true; else $canEditAll = false;
        if ($user != null && $user->can('deleteAll-posts')) $canDeleteAll = true; else $canDeleteAll = false;

        //$total = DB::table('posts')->where('published', '=', '1')->count(); // кол-во новостей
        //if($page > ceil($total/$perpage)) return redirect()->route('posts');

        //dd($request->getRequestUri());
        $tags = substr($request->getRequestUri(), strlen($route) + 2);
        $tags = explode("/", $tags);
        $tags = urldecode($tags[0]);
        $tags = explode("  ", $tags);

        foreach($tags as $tag){
            if($tag[0]=='!' && $tag[1]=='='){ $ret = $this->notin($request, $tags, $page);
                if($ret!=0) return view('post.index', ['posts'=>$ret,'canCreatePost'=>$canCreatePost,
                'canDeleteAll'=>$canDeleteAll,'userid'=>$user['id'],
                'canEditAll'=>$canEditAll]); else return view('post.empty', ['tags' => $tags]);
            }
        }

        //dd($tags);
        //Config::set('database.fetch', PDO::FETCH_ASSOC);
        //$tag = DB::table('tags')->select('id')->where('tag', '=', $tag)->first();
        $tags_id = DB::table('tags')->select('id');
        //$tags_id = DB::connection()->fetch(PDO::FETCH_ASSOC)->table('tags')->select('id');

        if (count($tags) > 1) {
            foreach ($tags as $key => $value) $tagsin[] = $value;
            $tags_id->whereIn('tag', $tagsin);
        } else {$tags_id->where('tag', '=', $tags); $route='tag';}

        $tags_id = $tags_id->get();

        //dd($tags_id);

        //$tags_id=[0=>29,1=>28];

        if(empty($tags_id)) return view('post.empty', ['tags' => $tags]);
        //dd($tags_id); //SELECT id FROM tags WHERE tag = 'yyy'; //dd($tag);

        //SELECT DISTINCT posts.id FROM posts, post_tag WHERE posts.id = post_tag.post_id AND (post_tag.tag_id = 28 OR post_tag.tag_id = 29);

        //$posts = DB::select('SELECT DISTINCT posts.id FROM posts, post_tag WHERE posts.id = post_tag.post_id AND (post_tag.tag_id = ? OR post_tag.tag_id = ?)',$tags_id);
        DB::enableQueryLog();
        //$posts = DB::select('SELECT DISTINCT posts.* FROM posts WHERE id=1');

        // Then to retrieve everything since you enabled the logging:
        //$queries = DB::getQueryLog();
        /*foreach($queries as $i=>$query)
        {
            Log::debug("Query $i: " . json_encode($query));
        }*/

        //dd($posts);

        // END TAGS_ID
        // POSTS_ID

        $posts_id = DB::table(DB::raw('`posts`, `post_tag`'));
        if ($route == 'tags') $posts_id->select(DB::raw('posts.id')); else $posts_id->select(DB::raw('DISTINCT posts.id'));
        //->select('posts.id')
        //->where('posts.id', '=', '`post_tag`.`post_id`');
        $posts_id->where(DB::raw('posts.id'), '=', DB::raw('post_tag.post_id')); //->where('posts.id', '=', '1');

        // $tags_id=[0=>'yyy',1=>'uuu']; $tags_id=(array)$tags_id;
        //$tags_id = json_decode(json_encode($tags_id), true);
        //$tags_id = collect($tags_id)->map(function($x){ return (array) $x; });

        $posts_id->where(function ($posts_id) use ($tags_id, $route) {
            foreach ($tags_id as $key => $value) //(array)$tags_id
            {
                //you can use orWhere the first time, dosn't need to be ->where
                //select posts.id from `posts`, `post_tag` where `posts`.`id` = post_tag.post_id and `post_tag`.`tag_id` IN(28,29)
                //if($route=='tags') $posts_id->where('post_tag.tag_id','=',$value->id); else
                $posts_id->orWhere('post_tag.tag_id', '=', $value->id);
            }
        });

        $posts_id = $posts_id->get();//->toSql();
        //$queries = DB::getQueryLog(); dd($queries);
        //dd($posts_id);
        //$posts_id=[$posts_id[0]->id,$posts_id[1]->id];

        if ($route == 'tag') foreach ($posts_id as $key => $value) $posts_id[$key] = $value->id; else {
            foreach ($posts_id as $key => $value) if (isset($tposts_id[$value->id])) $posts_ids[] = $value->id; else $tposts_id[$value->id] = 1;
            $posts_id=$posts_ids;
        }

        $total = count($posts_id); // кол-во новостей
        if ($page > ceil($total / $perpage)) return view('errors.404'); //retrdfvurn redirect()->route('posts');

        //dd($posts_id);

        // END POSTS_ID
        // POSTS

        $posts = DB::table('posts')
            ->select('posts.*','users.name as author',DB::raw('GROUP_CONCAT(tags.tag) as tags'))
            ->leftjoin('post_tag', 'post_tag.post_id', '=', 'posts.id')
            ->leftjoin('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->where('published', '=', '1');
        $posts->whereIn('posts.id',$posts_id);

       /* $posts->where(function ($posts) use ($posts_id)
        {
            foreach ($posts_id as $key => $value)
            {
                //you can use orWhere the first time, dosn't need to be ->where
                $posts->orWhere('posts.id','=',$value->id);
            }
        });*/

        $posts=$posts->groupBy('posts.id')
            ->orderBy('published_at', 'desc')->skip(($page-1)*$perpage)->take($perpage)->get();

        //dd($posts);

        //$pag=new \Illuminate\Pagination\LengthAwarePaginator([1=>'333'],1,5,1);
        //$pag=new LengthAwarePaginator([1=>'333'],1,5,1);

        /*$posts = DB::table('posts')
            ->select(DB::raw('COUNT(id)'))
            ->where('published', '=', '1')
            ->first(); var_dump(get_object_vars($posts)); dd($posts->{"COUNT(id)"});*/


        if(strpos($request->getPathInfo(),'/page')) $pageName=''; else $pageName=$request->getRequestUri().'/page/';

        //echo $p;

        //$posts=new Pages($posts,5,2,$p,['path'=>'path','pageName'=>$pageName]);

        $posts=new Paginator($posts, $total, $perpage, $page, ['path'=>'path','pageName'=>$pageName]);

        //$posts=$posts->setPageName('page');

        //$pag=new \Illuminate\Pagination\LengthAwarePaginator();
        //$pag->make();
        //dd($posts->toArray());
        //if($p!=null)
        //->toSql()

        //dd($posts);

        if($user!=null && $user->can('create-post')) $canCreatePost=true; else $canCreatePost=false;
        if($user!=null && $user->can('editall-posts')) $canEditAll=true; else $canEditAll=false;
        if($user!=null && $user->can('deleteAll-posts')) $canDeleteAll=true; else $canDeleteAll=false;
        //echo $can;

        return view('post.index', ['posts'=>$posts,'canCreatePost'=>$canCreatePost,
            'canDeleteAll'=>$canDeleteAll,'userid'=>$user['id'],
            'canEditAll'=>$canEditAll]);
    }
}