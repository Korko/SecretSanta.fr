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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            
            $table->tinyInteger('encryption_version')->unsigned()->default(2);
            $table->string('individual_key_hash', 64);
            $table->text('master_key_encrypted');
            
            $table->text('name_encrypted');
            $table->text('email_encrypted');
            $table->string('name_hash', 64)->nullable();
            
            $table->enum('status', ['pending', 'accepted', 'rejected', 'removed'])->default('pending');
            $table->boolean('is_organizer')->default(false);
            
            $table->foreignId('assigned_to_participant_id')->nullable()->references('id')->on('participants')->nullOnDelete();
            $table->json('assignment_metadata')->nullable();
            
            $table->timestamps();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('last_access_at')->nullable();
            $table->unsignedInteger('access_count')->default(0);
            
            $table->index('uuid', 'idx_uuid');
            $table->unique(['draw_id', 'name_hash'], 'idx_draw_name_hash');
            $table->index(['draw_id', 'status', 'is_organizer'], 'idx_draw_status');
            $table->index(['draw_id', 'assigned_to_participant_id'], 'idx_assignment');
            $table->index('last_access_at', 'idx_last_access');
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
