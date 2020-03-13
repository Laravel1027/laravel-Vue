<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstOpenToProjectUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add column
        Schema::table('project_user', function (Blueprint $table) {
            $table->boolean('first_open')->after('viewed_by_user')->default(1);
        });

        //Change column value for exiting project users
        $project_user = DB::table('project_user')->select('*')->get();
        foreach ($project_user as $user) {
            DB::table('project_user')->where('user_id', $user->user_id)->update(['first_open' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_user', function (Blueprint $table) {
            $table->dropColumn('first_open');
        });
    }
}
