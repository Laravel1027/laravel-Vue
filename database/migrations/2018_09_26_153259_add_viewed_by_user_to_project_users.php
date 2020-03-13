<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewedByUserToProjectUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add Column
        Schema::table('project_user', function($table) {
            $table->boolean('viewed_by_user')->default(0)->after('role');
        });

        //Change column value for exiting projects
        $projects = DB::table('projects')->select('*')->get();
        foreach ($projects as $project) {
            DB::table('project_user')->where('project_id', $project->id)->update(['viewed_by_user' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_user', function($table) {
            $table->dropColumn('viewed_by_user');
        });
    }
}
