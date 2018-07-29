<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampaignRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('email_list_recipients',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email');
                $table->timestamps();

                //Relation
                $table->unsignedInteger('email_list_id');
                $table->foreign('email_list_id')->references('id')
                      ->on('email_lists');
                $table->unsignedInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')
                      ->on('users');

                // Constraints
                $table->unique(['email_list_id', 'email']);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('email_list_recipients');
    }
}
