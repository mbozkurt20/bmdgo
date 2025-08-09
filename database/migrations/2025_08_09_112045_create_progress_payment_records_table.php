<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressPaymentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_payment_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payable_id');
            $table->enum('payable_type', ['courier', 'restaurant']);
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('payment_date');
            $table->tinyText('note')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payable_type', 'payable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('progress_payment_records');
    }
}
