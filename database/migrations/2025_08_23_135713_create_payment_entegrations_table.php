<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentEntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_entegrations', function (Blueprint $table) {
            $table->id();
            $table->string('tami_env')->nullable();
            $table->string('tami_merchant_number')->nullable();
            $table->string('tami_terminal_number')->nullable();
            $table->string('tami_secret_key')->nullable();
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
        Schema::dropIfExists('payment_entegrations');
    }
}
