<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('subject');
            $table->string('from_name');
            $table->string('from_address');
            $table->string('reply_address');
            $table->string('template');
            $table->boolean('is_scheduled');
            $table->dateTime('schedule')->nullable();
            $table->boolean('has_sent')->default(false);
            $table->softDeletes();
            $table->timestamps();

            //Relation
            $table->unsignedInteger('email_list_id')->nullable();
            $table->foreign('email_list_id')->references('id')
                  ->on('email_lists');

            $table->unsignedInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')
                  ->on('roles');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('email_campaigns');
    }
}
