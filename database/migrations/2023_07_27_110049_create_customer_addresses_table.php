<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_id');
            $table->bigInteger('customer_id');
            $table->string('name');
            $table->string('sokak_cadde');
            $table->string('bina_no');
            $table->string('kat');
            $table->string('daire_no');
            $table->string('mahalle');
            $table->text('adres_tarifi')->nullable();
            $table->enum('status', ['active', 'deactive'])->default('active');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
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
        Schema::dropIfExists('customer_addresses');
    }
}
