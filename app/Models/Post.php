<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model{
    protected $fillable = ['id', 'slug', 'title', 'excerpt', 'content', 'user_id', 'published', 'published_at'];

    public function tags(){
        return $this->belongsToMany('App\Models\Tag'); //, 'post_tag', 'post_id', 'tag_id'
    }

    public function author(){
        return $this->hasOne('App\User', 'id'); //, 'post_tag', 'post_id', 'tag_id'
    }

    /*public function author(){
        return $this->hasOne('User');
    }*/
    
    function getPublishedPosts(){
        $posts = Post::latest('published_at')
                ->where('published_at','<=',Carbon::now())
                ->published()->get();
        return $posts;
    }
    //
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
