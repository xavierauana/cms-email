<?php

use Anacreation\CmsEmail\Models\Recipient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToAllColumnToEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->text("to_status")
                  ->default(serialize([Recipient::StatusTypes['confirmed']]));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn("to_status");
        });
    }
}
