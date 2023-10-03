<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreorderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorder_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('preorder_category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title', 400);
            $table->string('barcode')->nullable();
            $table->string('price')->nullable();
            $table->string('multiplicity')->nullable();
            $table->string('multiplicity_tu')->nullable();
            $table->string('container')->nullable();
            $table->string('country')->nullable();
            $table->string('packaging')->nullable();
            $table->string('package_type')->nullable();
            $table->string('weight')->nullable();
            $table->string('season')->nullable();
            $table->string('r_i')->nullable();
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->string('seasonality')->nullable();
            $table->string('plant_height')->nullable();
            $table->string('packaging_type')->nullable();
            $table->string('package_amount')->nullable();
            $table->string('culture_type')->nullable();
            $table->string('frost_resistance')->nullable();
            $table->text('additional_1')->nullable();
            $table->text('additional_2')->nullable();
            $table->text('additional_3')->nullable();
            $table->text('additional_4')->nullable();
            $table->string('sku', 500)->nullable();
            $table->integer('cell_number')->nullable();
            $table->unsignedInteger('soft_limit')->nullable();
            $table->unsignedInteger('hard_limit')->nullable();
            $table->string('merch_price')->nullable();

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
        Schema::dropIfExists('preorder_products');
    }
}
