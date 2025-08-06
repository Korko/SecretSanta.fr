<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('draw_results_cache', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            
            $table->binary('encrypted_result');
            
            $table->unsignedInteger('accessed_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('expires_at');
            
            $table->primary(['draw_id', 'participant_id']);
            $table->index('expires_at', 'idx_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draw_results_cache');
    }
};
