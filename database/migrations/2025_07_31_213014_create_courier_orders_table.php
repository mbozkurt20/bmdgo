<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courier_id');
            $table->unsignedBigInteger('order_id');
            $table->text('reason')->nullable();
            $table->enum('status', ['accident', 'fault', 'other'])->nullable();
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
        Schema::dropIfExists('courier_orders');
    }
}
