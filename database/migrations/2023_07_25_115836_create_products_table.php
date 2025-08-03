<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_id');
            $table->bigInteger('category_id');
            $table->string('name');
            $table->string('begenilen');
            $table->string('code')->nullable();
            $table->decimal('price', 9, 3);
            $table->text('details')->nullable();
            $table->string('image')->nullable();
            $table->text('preparation_time')->nullable();
            $table->enum('status',['active','deactive'])->default('active');
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
        Schema::dropIfExists('products');
    }
}
