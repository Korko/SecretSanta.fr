<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dear_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained('draws')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('participants')->cascadeOnDelete();
            $table->string('mail_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dear_targets');
    }
};