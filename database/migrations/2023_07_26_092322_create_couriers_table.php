<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('situation')->default('Aktif');
            $table->string('price_type')->default('package');
            $table->decimal('price', 8, 2)->default('0.00');
            $table->decimal('fixed_price', 8, 2)->nullable();
            $table->decimal('km_price', 8, 2)->nullable();
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
        Schema::dropIfExists('couriers');
    }
}
