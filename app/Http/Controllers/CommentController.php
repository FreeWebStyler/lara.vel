<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;
use App\Helpers\RatesComments;

class CommentController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

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

    public function store(Comment $commentModel, Request $request)
    {   //dd($request);

        $token = Session::get('_token');
        if($token != $request->_token) dd('FAIL!');
        //dd($request);
        //$data = Session::all();
        $user = Auth::user(); //dd();
        $editId = Session::get('editid');

        /*if($editId==null) { $commentModel->setAttribute('user_id',$user['id']); // $request['author_id']=$user['id'];
        }
        else {
            $commentModel = $commentModel->where('id', '=', $editId)->first(); //$request['author_id']=$postModel->user_id;// Находим в какую запись сохранять //dd($editId);
        }*/

        $commentModel->setAttribute('user_id',$user['id']);
        //if(!isset($request->parent_id)) $commentModel->setAttribute('parent_id',0);
        //$postModel->setAttribute('user_id',$request['author_id']);
        $this->validate($request, ['comment' => 'required|max:5000']);
        $commentModel = $commentModel->fill($request->all());

        //dd($commentModel);

        //$commentModel->setAttribute('published_at',date("Y-m-d", strtotime($request->published_at)).' '.$request->hours.':'.$request->minutes.':'.$request->seconds);
        //if($commentModel->published == "on"){ $commentModel->published = 1; } else { $commentModel->published = 0; } //echo $editId; dd($commentModel);
        $commentModel->published = 1;
        $commentModel = $commentModel->save();
        if(isset($request->parent_id)) { echo 'true'; return;}

        if($commentModel) Session::set('delUnpostedComment', 1); //if($commentModel);

        return redirect()->route('blog', ['id' => $request->post_id]);

        //return redirect()->route( 'blog' )->with( 'id', $request->post_id );
        //return Redirect::route( 'blog' )->with( 'id', $request->post_id );
        //return redirect()->route('posts');
        //return redirect()->route('blog', $request->post_id);
        //return redirect()->route( 'blog' )->with( 'id', $request->post_id );
        //return Redirect::route('blog', array('id' => $request->post_id));
        //return redirect()->route('blog', ['id' => $request->post_id]);
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

        if($user!=null && $user->can('create-post')) $cans['create-post']=true;
        if($user!=null && $user->can('editall-posts')) $cans['editall-posts']=true;
        if($user!=null && $user->can('deleteAll-posts')) $cans['deleteAll-posts']=true;

        return view('post.show', ['post'=>$post, 'userid'=>$user['id'], 'cans'=>$cans]);
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

        return view('post.edit',['post'=>$post,'tags'=>$tags]); //echo $id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $commentModel)
    {
        $token = Session::get('_token');
        if($token != $request->_token) dd('FAIL!');

        //$commentModel = $commentModel->where('id', '=', $request->id)->first();
        $commentModel = $commentModel->find($request->id);

        $this->validate($request, ['comment' => 'required|max:5000']);

        $commentModel = $commentModel->fill($request->all());
        $commentModel->save(); echo 'location.reload()';
        //dd($commentModel);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $postModel, Request $request)
    {
        $token = Session::get('_token');
        if($token != $request->_token) dd('FAIL!');
        //dd($request);

        $comments = DB::table('comments') //SELECT comments.*, users.name as author from comments JOIN users ON users.id = comments.user_id WHERE post_id=4
        //$comments = $comments->setFetchMode('PDO::FETCH_NAMED')
        ->select('comments.*', 'users.name as author')
            ->join('users', 'users.id', '=', 'user_id')
            ->where('post_id', '=', $request->post_id) // ->where('deleted_at', '=', null)
            ->where('published', '=', '1')->toSql();
        //dd($comments);
        //$stmt = $dbh->query('SELECT * from comments WHERE post_id=40'); while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ $c++; if($c>100) die();
        //Config::set('database.fetch', 'PDO::FETCH_ASSOC');
        $db = DB::connection()->getPdo();
        //DB::setFetchMode('PDO::FETCH_ASSOC');
        //echo DB::getFetchMode();
        $query = $db->prepare($comments);
        $query->execute([$request->post_id, 1]);

        $comments=[]; $tree=[];

        while ($res = $query->fetch()) { //$query->fetchColumn()
            $id=$res['id'];
            $comments[$id] = $res;
            if((int)$comments[$id]['parent_id'] === 0) $tree[$id] = &$comments[$id]; else $comments[$comments[$id]['parent_id']]['children'][$id] = &$comments[$id];
        }

            $i=1; $citem=[]; $ids=[$request->id=>1];
            array_walk_recursive($tree,  function ($item, $key) use(&$i, &$citem, &$ids)
            {   // citem - current item; lil - latest item level; lid - latest item id;

                if($key == 'id') $citem['id']=$item;
                if($key == 'parent_id') $citem['par']=$item;

                if($i%20==0) {
                    foreach($ids as $key => $val){
                        if($key == $citem['par']) $ids[$citem['id']]=1;
                    }
                }

                $i++;
            });
        //dd(array_flip($ids));
        //dd($ids);
        //dd(array_flip($ids));
        DB::table('comments')->whereIn('id', array_keys($ids))->delete();
        echo 'location.reload()';
    }

    public function delete(Comment $commentModel, Request $request)
    {   //dd($request);
        //$data = Session::get('_token');
        $token = Session::get('_token');
        if($token != $request->_token) dd('FAIL!');
        //$post = Post::where('id','=',$id)->delete();
        //$comment = Comment::find($request->id)->first(); dd($comment);
        $comment = Comment::find($request->id)->delete();
        echo 'location.reload()';
    }

    public function rate(Request $request){
        $user = Auth::user();
        RatesComments::setRate($request->id, $request->sign, $user->id);
    }
}
