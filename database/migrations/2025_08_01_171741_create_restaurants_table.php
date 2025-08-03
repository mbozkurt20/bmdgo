<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('restaurant_code');
            $table->string('restaurant_name');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('password');
            $table->string('package_price');
            $table->tinyText('address')->nullable();
            $table->string('tax_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('yemeksepeti_email')->nullable();
            $table->string('yemeksepeti_password')->nullable();
            $table->string('yemeksepeti_token')->nullable();
            $table->string('yemeksepeti_tarih')->nullable();
            $table->string('adisyo_api_key')->nullable();
            $table->string('adisyo_secret_key')->nullable();
            $table->string('adisyo_consumer_adi')->nullable();
            $table->string('getir_client_id')->nullable();
            $table->string('getir_client_secret')->nullable();
            $table->string('getir_restaurant_id')->nullable();
            $table->string('getir_token')->nullable();
            $table->string('getir_tarih')->nullable();
            $table->string('trendyol_satici_id')->nullable();
            $table->string('trendyol_sube_id')->nullable();
            $table->string('trendyol_api_key')->nullable();
            $table->string('trendyol_secret_key')->nullable();
            $table->string('entegra_id')->nullable();
            $table->string('entegra_token')->nullable();
            $table->string('remember_token')->nullable();
            $table->boolean('email_verified_at')->nullable();
            $table->enum('status',['active','deactive'])->default('active');
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
        Schema::dropIfExists('restaurants');
    }
}
