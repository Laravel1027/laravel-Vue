<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new table for comment files
        Schema::create('comment_files', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('comment_id')->unsigned()->index()->nullable();
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');

            $table->string('path');
            $table->string('thumb_path');
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });

        //Move exiting comments files to new table
        $comments = DB::table('comments')->select('*')->get();

        if ($comments) {
            foreach ($comments as $comment) {
                $file = DB::table('project_files')->where('id', $comment->project_files_id)->first();

                if ($file) {
                    DB::table('comment_files')->insert([
                        'comment_id' => $comment->id,
                        'path' => $file->path,
                        'thumb_path' => $file->thumb_path,
                        'user_id' => $file->user_id,
                        'created_at' => $file->created_at,
                        'updated_at' => $file->updated_at
                    ]);

                    DB::table('project_files')->where('id', $comment->project_files_id)->delete();
                }
            }
        }

        // Drop old file column
        Schema::table('comments', function($table) {
            $table->dropColumn('project_files_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_files');
    }
}
