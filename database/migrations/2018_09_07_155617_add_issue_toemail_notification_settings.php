<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIssueToemailNotificationSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_notification_settings', function($table) {
            $table->boolean('approved_issue')->default(1)->after('new_comment');
            $table->boolean('new_issue')->default(1)->after('new_comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_notification_settings', function($table) {
            $table->dropColumn('approved_issue');
            $table->dropColumn('new_issue');
        });
    }
}
