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
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('price_type')->default('package');
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('fixed_price', 8, 2)->nullable();
            $table->decimal('km_price', 8, 2)->nullable();
            $table->string('latitude',)->nullable();
            $table->string('longitude',)->nullable();
            $table->string('birthday',)->nullable();
            $table->string('code',)->nullable();
            $table->tinyText('fcm_token',)->nullable();
            $table->string('last_assigned_at',)->nullable();
            $table->boolean('online')->nullable()->default(1);
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('couriers');
    }
}
