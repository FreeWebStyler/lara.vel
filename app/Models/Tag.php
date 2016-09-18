<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tag extends Model{
    protected $fillable = ['tag'];
    //protected $table;    
    public $timestamps = false;
    /*protected static $_table;

    public static function SsetTable($table){
        //$this->table=$table;
        static::$_table = $table;
    }*/    
    
    public function __construct(array $attributes = []){ // $attributes = []

        //print_r($attributes);
        parent::__construct($attributes);
        
        /*if(isset($attributes) && isset($attributes['save_to_table'])){
          $this->table=$attributes['save_to_table'];  //unset['save_to_table'){
        }*/
        
        //print_r($attributes);
        //$this->table='post_tags'; //$this->table=$table;        
    }

    function create2(){
        $posts = Tag::latest('published_at')
                ->where('published_at','<=',Carbon::now())
                ->published()->get();
        return $posts;
    }
    
    function setTable($table){
        $this->table=$table;
        //$this->setAttribute('table','post_tags'); 
    }
    
    static function saveTag($tag,$table){ // dd($table); // $this->table=$table; $tag=new Tag; dd($tag);
        $tags=explode('  ',$tag); //dd($tags);
        foreach($tags as $tag){
            //$ntag=new Tag('post_tags'); 
            
            //$ntag=Tag::fromTable("post_tags");
            //DB::table('index_slider')->get();
            //$ntag->setAttribute('table','post_tags'); 
            //$ntag->setTable('post_tags');
            //$ntag->table='post_tags';
            //$ntag->tag=$tag;
            //$ntag->updateOrCreate(); //firstOrCreate
            
            
            // $ntag->updateOrCreate(['tag'=>$tag]);
            //$ntag=new Tag();
            //Tag::SsetTable('post_tags');
            //$ntag=$ntag->updateOrCreate(['tag'=>$tag]);
            
            //$ntag=static::SsetTable('post_tags');
            
            //$ntag=static::SsetTable("post_tags");   
            //$ntag=$ntag->firstOrCreate(['tag'=>$tag]);  
            
            //$ntag=
            static::updateOrCreate(['tag'=>$tag]); //['save_to_table'=>'post_tags',
    
            //dd($ntag);

            //->firstOrCreate(['tag'=>$tag,'table'=>'post_tags']); 
            dd($ntag);
        }
        
        $posts = Tag::latest('published_at')
                ->where('published_at','<=',Carbon::now())
                ->published()->get();
        return $posts;
    }

}
