<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_user', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->char('user_id', 36);
            $table->char('number_id', 36);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('number_id')->references('id')->on('numbers');
            $table->string('permission', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
