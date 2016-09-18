<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {   return view('welcome');  });
$router = new Route();dd($router);
Route::get('blog/3',['as'=>'blog','uses'=>'PostController@index']);*/

Route::group(['middleware' => ['admin']], function () {
    Route::get('admin/role',['as'=>'roles','uses'=>'RoleController@index']); // dd(0000);
    //Route::get('/admin',function(){ echo 'ADMIN!';}); 

    Route::get('post/create',['as'=>'post.create','uses'=>'PostController@create']);

    //Route::post('post', 'PostController@store')->name('post.store');
    Route::get('post/{post}/edit',  ['as'=>'post.show','uses'=>'PostController@edit']);
    Route::post('post', 'PostController@store');
    Route::post('post/delete', ['as'=>'post.delete', 'uses'=>'PostController@delete']);

    Route::get('role/creating', ['as'=>'role.creating','uses'=>'RoleController@creating']);



    Route::post('comment', ['as' => 'comment.store', 'uses' => 'CommentController@store']);
    Route::post('comment/delete', ['as' => 'comment.delete', 'uses' => 'CommentController@delete']);
    Route::post('comment/update', ['as' => 'comment.update', 'uses' => 'CommentController@update']);

    Route::post('comment/rate', ['as' => 'comment.rate', 'uses' => 'CommentController@rate']);

});

Route::post('comment/destroy', ['as' => 'comment.destroy', 'uses' => 'CommentController@destroy']);

Route::get('post/{id}', ['as' => 'blog', 'uses' => 'PostController@show']);

Route::get('blog/page/{page}',['as'=>'blog.page','uses'=>'PostController@index']);

//Route::any('comment', ['as' => 'comment.store'], function(){ echo 555; return;} ); //'CommentController@store'

Route::group(['middleware' => ['web']], function () {
    //Route::get('/',['as'=>'posts','uses'=>'PostController@index']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('unpublished',       ['as'=>'posts.unpublished','uses'=>'PostController@unpublished']);
    //Route::get('/', function (){   echo 3333;   }); // Uses Auth Middleware    

    Route::get('user/profile', function () {
     echo 5555;    // Uses Auth Middleware
    });
});

//View::composer('layouts.partials.sidebar', '\App\Composers\SidebarComposer');
//View::composer('layouts.partials.sidebar', 'App\Http\ViewComposers\MyViewComposer');
//View::composer('layouts2.partials.sidebar', compact(['tags'=>111,222]));
//View::composer('layouts.partials.sidebar',  new App\Composers\SidebarComposer());
//View::composer(‘sidebar’, function($view) {    $view->with(‘links’, [‘hello’,’world!’]);});

/*View::composer('layouts.partials.sidebar', function($view){
    //dd($view);
    //$view->with(['tags'=>111,222]);
    $view->with(['comments' => \App\Models\Comment::lastPublished()]);
});*/

/*View::composer('layouts.partials.sidebar', function($view){
    //dd($view);
    //$view->with(['tags'=>111,222]);
    $view->with(['comments' => \App\Models\Comment::lastPublished()]);
});*/

View::composer('layouts.partials.sidebar', function($view){
    //dd($view);
    //$view->with(['tags'=>111,222]);
    $view->with(['comments' => \App\Models\Comment::lastPublished()]);
});

/*View::composer('widgets.latest_news', function($view){
    //dd($view);
    //$view->with(['tags'=>111,222]);
    $view->with(['comments' => \App\Models\Comment::lastPublished()]);
});*/

//Route::get('/',                 ['as'=>'posts','uses'=>'PostController@index']);
Route::get('unpublished',       ['as'=>'posts.unpublished','uses'=>'PostController@unpublished']);
Route::get('/tag/{tag}',['as'=>'tag','uses'=>'TagController@index']); // dd(0000);
Route::get('/tag/{tag}/page/{page}',['as'=>'tag','uses'=>'TagController@index']); // dd(0000);
Route::get('/tags/{tag}',['as'=>'tags','uses'=>'TagController@index']); // dd(0000);
Route::get('/tags/{tag}/page/{page}',['as'=>'tags','uses'=>'TagController@index']); // dd(0000);

/*Route::get('post/create', 'PostController@create')->name('post.create');
Route::post('post', 'PostController@store')->name('post.store');
Route::get('post/{post}', 'PostController@show')->name('post.show');
Route::get('post/{post}/edit', 'PostController@edit')->name('post.edit');
Route::post('post/{post}', 'PostController@update')->name('post.update');*/

/*Route::get('post/create',       ['as'=>'post.create','uses'=>'PostController@create']);
Route::post('post',             ['as'=>'post.store','uses'=>'PostController@store']);
Route::get('post/{post}',       ['as'=>'post.show','uses'=>'PostController@show']);
Route::get('post/{post}/edit',  ['as'=>'post.show','uses'=>'PostController@edit']);
Route::get('post/{post}',       ['as'=>'post.update','uses'=>'PostController@update']);*/

$router->resource('post','PostController');
Route::get('/',['as'=>'posts','uses'=>'PostController@index']);
Route::get('/home', 'HomeController@index'); 
Route::auth();