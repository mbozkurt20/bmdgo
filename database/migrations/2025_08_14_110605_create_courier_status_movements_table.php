<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierStatusMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_status_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courier_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->enum('status', ['active', 'passive', 'break', 'service']);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->foreign('courier_id')->references('id')->on('couriers')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courier_status_movements');
    }
}
