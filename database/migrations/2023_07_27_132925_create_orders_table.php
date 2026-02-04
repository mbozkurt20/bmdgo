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
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('courier_id')->nullable();
            $table->string('tracking_id');
            $table->string('full_name');
            $table->string('phone');
            $table->string('pid')->nullable(); // entegra order id
            $table->tinyInteger('status_request_count')->default(0); // entegra durum güncelleme istek sayısı
            $table->text('address');
            $table->string('verify_code')->nullable();
            $table->tinyText('notes')->nullable();
            $table->string('payment_method');
            $table->decimal('amount', 9, 2);
            $table->decimal('sub_amount', 9, 2)->nullable();
            $table->decimal('discount', 9, 2)->nullable();
            $table->json('items')->nullable();
            $table->json('promotions')->nullable();
            $table->json('coupon')->nullable();
            $table->string('platform_date')->nullable();
            $table->string('message')->nullable(); //iptal mesajı
            $table->string('distance')->nullable();
            $table->string('status');
            $table->string('assigned_at')->nullable();
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
