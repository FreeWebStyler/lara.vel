<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\Role;
use App\Models\Tag;
use App\Models\PostTag;
use DB;
use App\Models\Paginator;
use App\Models\PaginationPresenter;

class PostController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $postModel, Request $request, $page=1){ // если нет данных о странице, то по-умолчанию - первая

        $perpage=2; // сколько выводить на странице
//echo 555;dd($p);

        $user = Auth::user();

        $total = DB::table('posts')->where('published', '=', '1')->count(); // кол-во новостей

        if($page > ceil($total/$perpage)) return redirect()->route('posts');
        //DB::setFetchMode(\PDO::FETCH_ASSOC); //DB::connection()->setFetchMode(PDO::FETCH_ASSOC);

        $posts = DB::table('posts')
            ->select('posts.id', 'posts.excerpt', 'posts.title', 'posts.user_id', 'posts.published_at', 'users.name as author',DB::raw('GROUP_CONCAT(tags.tag) as tags'))
            ->leftjoin('post_tag', 'post_tag.post_id', '=', 'posts.id')
            ->leftjoin('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->where('published', '=', '1')
            ->groupBy('posts.id')
            ->orderBy('published_at', 'desc')->skip(($page-1)*$perpage)->take($perpage)->get();

        //dd($posts);
        //$pag=new \Illuminate\Pagination\LengthAwarePaginator([1=>'333'],1,5,1);
        //$pag=new LengthAwarePaginator([1=>'333'],1,5,1);

        /*$posts = DB::table('posts')
            ->select(DB::raw('COUNT(id)'))
            ->where('published', '=', '1')
            ->first(); var_dump(get_object_vars($posts)); dd($posts->{"COUNT(id)"});*/

        if(strpos($request->getPathInfo(),'blog/page')) $pageName=''; else $pageName='blog/page/';

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
            'canDeleteAll'=>$canDeleteAll, 'userid'=>$user['id'],
            'canEditAll'=>$canEditAll]);
    }

    function unpublished(){    //Post $postModel
     $user = Auth::user();
     if($user==null || !$user->hasRole('admin')) return 'Acces denied!'; else $canEditAll=true;
        $total = Post::latest('published_at')->unpublished()->count();
        Post::latest('published_at')->unpublished()->get();

        dd($total);
        //$posts = Post::latest('published_at')->unpublished()->get();
     return view('post.index', ['posts'=>$posts,'can'=>$canEditAll]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('post.create',['post'=>new Post]);
        $post=new Post();
        $post->hours=date('H');
        $post->minutes=date("i");
        $post->seconds=date("s");

        return view('post.create',['post'=>$post]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Post $postModel, Request $request) {

        $token = Session::get('_token');
        if($token != $request->_token) dd('FAIL!');

        //$data = Session::all();
        $user = Auth::user(); //dd();
        $editId = Session::get('editid');
        if($editId==null) { $postModel->setAttribute('user_id',$user['id']); // $request['author_id']=$user['id'];
        }
        else {
            $postModel = $postModel->where('id', '=', $editId)->first(); //$request['author_id']=$postModel->user_id;// Находим в какую запись сохранять //dd($editId);
        }

        //$postModel->setAttribute('user_id',$request['author_id']);

        $this->validate($request, [ 
            'title' => 'required|max:255', // 'title' => 'required|unique:posts|max:255',            
            'slug' => 'required|max:255', //'slug' => 'required|unique:posts|max:255',
            'excerpt' => 'required|max:255',
            'content' => 'required|max:50000',
            'published_at' => 'required|max:255']);

        $postModel = $postModel->fill($request->all());
        $postModel->setAttribute('published_at',date("Y-m-d", strtotime($request->published_at)).' '.$request->hours.':'.$request->minutes.':'.$request->seconds);
        if($postModel->published == "on"){ $postModel->published = 1; } else { $postModel->published = 0; } //echo $editId; dd($postModel);        
        $postModel->save(); 

        $tags=explode('  ',$request['tags']); //dd($tags);
        foreach($tags as $tag){
            $ntag=new Tag();
            $ntag=$ntag->firstOrCreate(['tag'=>$tag]); //'save_to_table'=>'tags',    //dd($tags);
            $postTag=new PostTag();
            $postTag=$postTag->firstOrCreate(['tag_id'=>$ntag['attributes']['id'],
                'post_id'=>$postModel['attributes']['id']]); //'save_to_table'=>'tag2post',
        }
        if($editId==null) return redirect()->route('posts'); else return redirect()->route('blog', ['id' => $editId]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $user = Auth::user();

        $post = DB::table('posts')
            ->select('posts.*','users.name as author',DB::raw('GROUP_CONCAT(tags.tag) as tags'))
            ->leftjoin('post_tag', 'post_tag.post_id', '=', 'posts.id')
            ->leftjoin('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->where('published', '=', '1')
            ->where('posts.id', '=', $id)
            ->groupBy('posts.id')
            ->orderBy('published_at', 'desc')->first();

        $cans=[];
        if($user!=null && $user->can('create-post')) $cans['create-post']=true;
        if($user!=null && $user->can('editall-posts')) $cans['editall-posts']=true;
        if($user!=null && $user->can('deleteAll-posts')) $cans['deleteAll-posts']=true;

        $comments = new Comment();
        $comments = $comments->getPublished($post->id);

        //dd($comments);

        if(Session::get('delUnpostedComment')) $delUnpostedComment = 1; else $delUnpostedComment=0;
        Session::remove('delUnpostedComment');

        //return view('post.show', ['post'=>$post, 'comments'=>$comments, 'userid'=>$user['id'], 'cans'=>$cans, 'delUnPostedComment' => $delUnPostedComment]);
        $userid = $user['id'];
        $post->tags = trim($post->tags, ",");

        return view('post.show', compact('post', 'comments', 'userid', 'cans', 'delUnpostedComment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){ //, Post $postModel
        //$data = Session::all(); dd($data);
        Session::flash('editid', $id);
        $tags='';
        $post = Post::where('id','=',$id)->first(); //dd($post);
        foreach($post->tags as $tag) $tags.=$tag->tag.'  ';

        $user = Auth::user();       
        //if($user!=null && $user->hasRole('admin')) echo 222; else echo 333;

        $post['hours']=date("H",strtotime($post['published_at']));
        $post['minutes']=date("i",strtotime($post['published_at']));
        $post['seconds']=date("s",strtotime($post['published_at']));
        $post['publish_date']=date("d.m.Y",strtotime($post['published_at']));

        return view('post.edit', ['post'=>$post,'tags'=>$tags]); //echo $id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete(Post $postModel, Request $request, $id=null)
    {
     $data = Session::get('_token');
        //$post = Post::where('id','=',$id)->delete();
        $post = Post::find($request->post_id)->delete();
        dd($post);

        //$postModel->where($request->post_id);
     //if($data==$request->_token) dd($request->post_id); else dd($data);

     //dd(555);

     //if($data===$request->_token) echo Post::findOrFail($request->post_id)->delete(); else echo 'Err0r!';

     //echo 1;

     //dd($post);
     //dd($post);
     //dd($post);
     //dd($request->_token); //_token dd($request);
    }
}
