<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->comment('SKU名称');
            $table->text('description')->comment('SKU详情');
            $table->decimal('price', 10, 2)->comment('SKU价格');
            $table->unsignedInteger('stock')->comment('SKU库存');
            $table->unsignedBigInteger('product_id')->comment('SKU对应SPU的id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('product_skus');
    }
}
