<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the table for the participants.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained('draws', 'id')->cascadeOnDelete();
            $table->longText('name');
            $table->longText('email');
            $table->foreignId('target_id')->nullable()->constrained('participants', 'id')->nullOnDelete();
            $table->boolean('redraw')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
