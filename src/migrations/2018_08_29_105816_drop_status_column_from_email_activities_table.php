<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropStatusColumnFromEmailActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasColumn('email_activities', 'status')) {
            Schema::table('email_activities', function ($table) {
                $table->dropColumn(['status']);
            });
        }
        if (!Schema::hasColumn('email_activities', 'campaign_id')) {
            Schema::table('email_activities', function ($table) {
                $table->unsignedInteger('campaign_id');
            });
        }
        if (!Schema::hasColumn('email_activities', 'recipient_id')) {
            Schema::table('email_activities', function ($table) {
                $table->unsignedInteger('recipient_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
