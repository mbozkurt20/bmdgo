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
            $table->bigInteger('admin_id');
            $table->integer('restaurant_id');
            $table->string('name');
            $table->string('phone');
            $table->string('password');
            $table->string('situation')->default('Aktif');
            $table->decimal('price', 8, 2);
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
