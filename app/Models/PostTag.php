<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PostTag extends Model
{
    protected $fillable = ['tag_id','post_id'];
    protected $table='post_tag';
    public $timestamps = false;
    /*public function __construct(array $attributes = [])
    { // $attributes = []
        parent::__construct($attributes);        
        //if (isset($attributes) && isset($attributes['save_to_table'])) {
    $this->table=$attributes['save_to_table'];  } //unset['save_to_table')
        $this->table='tag_post';
    }*/
}
