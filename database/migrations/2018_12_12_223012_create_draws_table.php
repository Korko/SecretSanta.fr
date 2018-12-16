<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draws', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email_title');
            $table->longText('email_body');
            $table->string('organizer_name')->nullable();
            $table->string('organizer_email')->nullable();
            $table->date('expiration');
            $table->integer('dear_santa_draw_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('dear_santa_draw_id')
                ->references('id')->on('dear_santa_draws')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('draws');
    }
}
