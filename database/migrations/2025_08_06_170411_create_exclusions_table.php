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
        Schema::create('exclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('excluded_participant_id')->constrained('participants')->cascadeOnDelete();
            
            $table->enum('type', ['strong', 'weak'])->default('strong');
            $table->enum('source', ['manual', 'group', 'history', 'auto'])->default('manual');
            
            $table->json('metadata')->nullable();
            
            $table->timestamp('created_at');
            
            $table->unique(['participant_id', 'excluded_participant_id'], 'idx_unique_exclusion');
            $table->index(['draw_id', 'type'], 'idx_draw_exclusions');
            $table->index(['participant_id', 'type', 'source'], 'idx_participant_exclusions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclusions');
    }
};
