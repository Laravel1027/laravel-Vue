<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create new table for issue files
        Schema::create('issue_files', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('issue_id')->unsigned()->index()->nullable();
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');

            $table->string('path');
            $table->string('thumb_path');
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });

        //Move exiting issues files to new table
        $issues = DB::table('issues')->select('*')->get();

        if ($issues) {
            foreach ($issues as $issue) {
                $file = DB::table('project_files')->where('id', $issue->project_files_id)->first();

                if ($file) {
                    DB::table('issue_files')->insert([
                        'issue_id' => $issue->id,
                        'path' => $file->path,
                        'thumb_path' => $file->thumb_path,
                        'user_id' => $file->user_id,
                        'created_at' => $file->created_at,
                        'updated_at' => $file->updated_at
                    ]);

                    DB::table('project_files')->where('id', $issue->project_files_id)->delete();
                }
            }
        }

        // Drop old file column
        Schema::table('issues', function($table) {
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
        Schema::dropIfExists('issue_files');
    }
}
