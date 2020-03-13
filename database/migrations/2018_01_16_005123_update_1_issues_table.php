<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1IssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->string('status')->after('description');
            $table->text('description')->after('project_file_id')->change();
            $table->string('group')->after('user_id');
            $table->string('label')->after('group');
            $table->dropColumn('title');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('group');
            $table->dropColumn('label');
            $table->dropColumn('status');
            $table->string('description');
            $table->string('title');
        });
    }
}
