<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDearSantasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dear_santas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('draw_id')->unsigned();
            $table->string('santa_name');
            $table->string('santa_email');
            $table->string('challenge');
            $table->string('public_key');

            $table->foreign('draw_id')->references('id')->on('dear_santa_draws')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dear_santas');
    }
}
