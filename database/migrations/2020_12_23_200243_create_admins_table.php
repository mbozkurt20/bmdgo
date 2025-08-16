<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_id')->nullable(); //bayi id user
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('auto_orders')->default(0);
            $table->string('phone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('top_up_balance')->default(0);
            $table->string('vatan_sms_customer')->nullable();
            $table->string('vatan_sms_username')->nullable();
            $table->string('vatan_sms_password')->nullable();
            $table->string('vatan_sms_orginator')->nullable();
            $table->boolean('is_sms')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
