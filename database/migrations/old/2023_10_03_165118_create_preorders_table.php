<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorders', function (Blueprint $table) {
            $table->id();
            $table->text('file');
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('end_date');
            $table->string('title');
            $table->unsignedInteger('min_order');
            $table->string('background_image');
            $table->string('code');
            $table->text('slide_images')->nullable();
            $table->text('short_description')->nullable();
            $table->unsignedTinyInteger('prepay_percent')->nullable();
            $table->string('default_image');
            $table->boolean('file_processed')->default(false);
            $table->text('client_file')->nullable();
            $table->string('client_qty_field')->nullable();
            $table->text('merch_file')->nullable();
            $table->string('merch_qty_field')->nullable();
            $table->boolean('is_finished')->default(false);
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
        Schema::dropIfExists('preorders');
    }
}
