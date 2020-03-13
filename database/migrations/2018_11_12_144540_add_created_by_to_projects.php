<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByToProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add column
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('created_by')->after('company');
        });

        //Change column value for exiting projects
        $projects = DB::table('projects')->select('*')->get();
        if ($projects) {
            foreach ($projects as $project) {
                $owner = DB::table('project_user')->where('project_id', $project->id)->orderBy('created_at', 'ASC')->first();
                if ($owner) {
                    DB::table('projects')->where('id', $project->id)->update(['created_by' => $owner->user_id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}
