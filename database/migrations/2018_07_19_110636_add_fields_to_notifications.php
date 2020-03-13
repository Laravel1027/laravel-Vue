<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function($table) {
            $table->string('type')->nullable()->after('created_by');
            $table->string('proofer')->nullable()->after('action_url');
            $table->string('project')->nullable()->after('action_url');
            $table->string('company')->nullable()->after('action_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function($table) {
            $table->dropColumn('type');
            $table->dropColumn('proofer');
            $table->dropColumn('project');
            $table->dropColumn('company');
        });
    }
}
