<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->bigInteger('restaurant_id');
            $table->bigInteger('courier_id')->nullable();
            $table->bigInteger('tracking_id');
            $table->string('full_name');
            $table->string('phone');
            $table->text('address');
            $table->string('verify_code')->nullable();
            $table->tinyText('notes')->nullable();
            $table->string('payment_method');
            $table->decimal('amount', 9, 2);
            $table->decimal('sub_amount', 9, 2)->nullable();
            $table->json('items')->nullable();
            $table->json('promotions')->nullable();
            $table->json('coupon')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('orders');
    }
}
