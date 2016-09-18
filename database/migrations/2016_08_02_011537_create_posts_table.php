<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('posts')) Schema::create('posts', function (Blueprint $table) {
            $table->mediumInteger('user_id')->unsigned()->nullable(false); //$table->increments('user_id');
            $table->string('title')->nullable();
            $table->string('slug')->nullable(false);;//->unique();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            //$table->mediumInteger('author_id')->unsigned()->nullable(false); //ALTER TABLE `posts` ADD `author_id` MEDIUMINT(6) NOT NULL AFTER `content`; ALTER TABLE `posts` CHANGE `author_id` `author_id` MEDIUMINT(6) UNSIGNED NOT NULL;
            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
