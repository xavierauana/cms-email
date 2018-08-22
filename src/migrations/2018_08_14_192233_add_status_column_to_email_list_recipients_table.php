<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToEmailListRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('email_list_recipients', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->string('token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasColumn('email_list_recipients', 'status')) {
            Schema::table('email_list_recipients', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        if (Schema::hasColumn('email_list_recipients', 'token')) {
            Schema::table('email_list_recipients', function (Blueprint $table) {
                $table->dropColumn('token');
            });
        }

    }
}
