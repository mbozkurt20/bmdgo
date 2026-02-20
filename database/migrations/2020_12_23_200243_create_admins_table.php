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
            $table->string('created_by_type')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('auto_orders')->default(0);
            $table->string('phone')->nullable();
            $table->integer('code')->nullable();
            $table->string('address')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('top_up_balance')->default(0);
            $table->decimal('top_up_price',8,2)->default(0.00);
            $table->tinyInteger('check_courier_timeout')->default(2);
            $table->string('vatan_sms_customer')->nullable();
            $table->string('vatan_sms_username')->nullable();
            $table->string('vatan_sms_password')->nullable();
            $table->string('vatan_sms_orginator')->nullable();
            $table->integer('distance_limit_km')->default(50);
            $table->integer('max_package_limit')->default(4);
            $table->boolean('is_sms')->default(0);
            $table->boolean( 'is_active',)->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
