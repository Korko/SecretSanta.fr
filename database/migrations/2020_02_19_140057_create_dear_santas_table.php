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
            $table->id();
            $table->foreignId('sender_id')->constrained('participants')->cascadeOnDelete();
            $table->longText('mail_body');
            $table->foreignId('mail_id')->constrained()->cascadeOnDelete();
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
