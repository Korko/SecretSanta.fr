<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id');
            $table->longText('name');
            $table->longText('address');
            $table->foreignId('target_id')->nullable();
            $table->foreignId('mail_id');
            $table->timestamps();

            $table->foreign('draw_id')
                ->references('id')->on('draws')->onDelete('cascade');

            $table->foreign('target_id')
                ->references('id')->on('participants')->onDelete('cascade');

            $table->foreign('mail_id')
                ->references('id')->on('mails')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
}
