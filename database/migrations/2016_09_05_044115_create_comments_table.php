<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('comments')) Schema::create('comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->mediumInteger('id')->unsigned()->autoIncrement()->nullable(false); //autoIncrement() - уже делает Primary Key
            $table->mediumInteger('post_id')->unsigned()->nullable(false);
            $table->mediumInteger('parent_id')->unsigned()->nullable(false);
            $table->mediumInteger('user_id')->unsigned()->nullable(false);
            $table->string('comment', 5000)->nullable(false);
            $table->mediumInteger('rate')->default(0);
            $table->boolean('published')->default(false);
            //$table->timestamp('deleted_at')->default(null);
            $table->index('post_id');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
