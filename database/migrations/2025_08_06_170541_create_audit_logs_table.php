<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('draw_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('action', 100);
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('session_id', 100)->nullable();
            
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            $table->index(['user_id', 'created_at'], 'idx_user_audit');
            $table->index(['entity_type', 'entity_id', 'created_at'], 'idx_entity_audit');
            $table->index(['draw_id', 'created_at'], 'idx_draw_audit');
            $table->index(['action', 'created_at'], 'idx_action_audit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
