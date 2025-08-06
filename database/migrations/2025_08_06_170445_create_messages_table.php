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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('to_participant_id')->constrained('participants')->cascadeOnDelete();
            
            $table->tinyInteger('encryption_version')->unsigned()->default(2);
            $table->text('content_encrypted');
            $table->string('content_hash', 64)->nullable();
            
            $table->enum('type', ['to_secret_santa', 'to_target', 'system']);
            $table->enum('status', ['sent', 'read', 'deleted'])->default('sent');
            
            $table->enum('moderation_status', ['ok', 'reported', 'reviewing', 'removed'])->default('ok');
            $table->json('moderation_data')->nullable();
            
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            
            $table->index(['draw_id', 'created_at'], 'idx_draw_messages');
            $table->index(['to_participant_id', 'status', 'created_at'], 'idx_participant_inbox');
            $table->index(['from_participant_id', 'created_at'], 'idx_participant_sent');
            $table->index(['moderation_status', 'created_at'], 'idx_moderation');
            $table->index('content_hash', 'idx_content_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
