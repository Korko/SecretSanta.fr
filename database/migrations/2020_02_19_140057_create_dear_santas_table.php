<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the table for the Dear Santas Letters.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dear_santas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('participants', 'id')->cascadeOnDelete();
            $table->longText('mail_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dear_santas');
    }
};
