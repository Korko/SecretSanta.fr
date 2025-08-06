<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            
            $table->tinyInteger('encryption_version')->default(2);
            $table->string('organizer_key_hash', 64);
            $table->text('master_key_encrypted');
            $table->json('key_rotation_data')->nullable();
            
            $table->text('title_encrypted');
            $table->text('description_encrypted')->nullable();
            $table->text('organizer_name_encrypted');
            $table->text('organizer_email_encrypted');
            
            $table->enum('status', ['draft', 'open_registration', 'closed_registration', 'processing', 'drawn', 'revealed', 'archived'])->default('draft');
            $table->boolean('auto_accept_participants')->default(false);
            $table->boolean('allow_target_messages')->default(true);
            $table->json('config')->nullable();
            
            $table->json('stats_cache')->nullable();
            $table->timestamp('stats_updated_at')->nullable();
            
            $table->timestamp('registration_deadline')->nullable();
            $table->timestamp('drawn_at')->nullable();
            $table->timestamp('revealed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draws');
    }
};