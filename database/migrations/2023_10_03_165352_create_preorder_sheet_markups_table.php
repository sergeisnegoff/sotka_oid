<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreorderSheetMarkupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorder_sheet_markups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_table_sheet_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title')->nullable();
            $table->string('barcode')->nullable();
            $table->string('price')->nullable();
            $table->string('multiplicity')->nullable();
            $table->string('multiplicity_tu')->nullable();
            $table->string('container')->nullable();
            $table->string('hard_limit')->nullable();
            $table->string('soft_limit')->nullable();
            $table->string('country')->nullable();
            $table->string('packaging')->nullable();
            $table->string('package_type')->nullable();
            $table->string('weight')->nullable();
            $table->string('season')->nullable();
            $table->string('r_i')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->string('description')->nullable();
            $table->string('seasonality')->nullable();
            $table->string('plant_height')->nullable();
            $table->string('packaging_type')->nullable();
            $table->string('package_amount')->nullable();
            $table->string('culture_type')->nullable();
            $table->string('frost_resistance')->nullable();
            $table->string('additional_1')->nullable();
            $table->string('additional_2')->nullable();
            $table->string('additional_3')->nullable();
            $table->string('additional_4')->nullable();
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
        Schema::dropIfExists('preorder_sheet_markups');
    }
}
