<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        //Schema::drop('post_tags');
        if(!Schema::hasTable('tags')) Schema::create('tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->mediumInteger('id')->unsigned()->autoIncrement()->nullable(false); //autoIncrement() - уже делает Primary Key
            $table->string('tag',50)->nullable(false);
            //$table->primary('id');
            $table->unique('tag');
            $table->index('tag');
            //$table->dropTimestamps();
            //$table->dropSoftDeletes();
        });
        //Schema::drop('tag2post');
        if(!Schema::hasTable('post_tag')) Schema::create('tag2post',function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->mediumInteger('tag_id')->unsigned()->nullable(false);
            $table->mediumInteger('post_id')->unsigned()->nullable(false);
            $table->unique('post_id','tag_id');
            $table->index('post_id');
            //$table->dropTimestamps();
            //$table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){        
        Schema::drop('tag2post');
        Schema::drop('tags');
    }
}
