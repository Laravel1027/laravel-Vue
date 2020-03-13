<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnreadCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unread_comments', function (Blueprint $table) {
            $table->increments('id');

            //User ID
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            //Project ID
            $table->integer('project_id')->unsigned()->index()->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            //Revision ID
            $table->integer('revision_id')->unsigned()->index()->nullable();
            $table->foreign('revision_id')->references('id')->on('revisions')->onDelete('cascade');

            //Issue ID
            $table->integer('issue_id')->unsigned()->index()->nullable();
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');

            //Comment ID
            $table->integer('comment_id')->unsigned()->index()->nullable();
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');

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
        Schema::dropIfExists('unread_comments');
    }
}
