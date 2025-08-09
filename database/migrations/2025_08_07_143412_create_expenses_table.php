<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->enum('type',['admin','restaurant']);
            $table->string('title')->nullable();
            $table->string('expense_type')->nullable();
            $table->tinyText('description')->nullable();
            $table->string('date')->nullable(); //giderin tarihi
            $table->string('payment_method')->nullable();
            $table->decimal('amount',10,2)->nullable();
            $table->enum('status',['active','deactive'])->default('active');
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
        Schema::dropIfExists('expenses');
    }
}
