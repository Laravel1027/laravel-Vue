<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignFromProjectFinalFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_final_files', function (Blueprint $table) {
            $table->dropForeign('project_final_files_project_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_final_files', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }
}
