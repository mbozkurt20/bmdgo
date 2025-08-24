<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopupMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topup_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_user_id');
            $table->enum('created_type',['dealer','admin','superadmin']);
            $table->unsignedBigInteger('admin_id');
            $table->bigInteger('top_up')->default(0); //kontör
            $table->string('top_up_price')->default(0); //kontör ücreti
            $table->string('total_amount');
            $table->enum('type',['talep','yükleme'])->default('talep');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->json('payment_details')->nullable();
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
        Schema::dropIfExists('topup_movements');
    }
}
