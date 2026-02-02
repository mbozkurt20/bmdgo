<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->string('status');
            $table->integer('duration_seconds')->nullable(); // bir sonraki status'e kadar geçen süre
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
            $table->index(['order_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status_logs');
    }
}
