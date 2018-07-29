<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('email_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('confirm_opt_in')->defautl(true);
            $table->boolean('has_welcome_message')->default(true);
            $table->boolean('has_goodbye_message')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('email_lists');
    }
}
