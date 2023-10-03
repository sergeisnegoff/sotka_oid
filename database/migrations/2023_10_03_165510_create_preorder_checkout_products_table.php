<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreorderCheckoutProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorder_checkout_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_checkout_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('preorder_product_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('qty');
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
        Schema::dropIfExists('preorder_checkout_products');
    }
}
