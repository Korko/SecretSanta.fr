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
        Schema::create('organizers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            
            // Encryption fields
            $table->string('individual_key_hash', 255);
            $table->text('master_key_encrypted');
            
            // Encrypted personal information
            $table->tinyInteger('encryption_version')->unsigned()->default(2);
            $table->text('name_encrypted');
            $table->text('email_encrypted');
            
            // Email tracking
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('email_opened_at')->nullable();
            $table->timestamp('last_access_at')->nullable();
            
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            
            // Indexes
            $table->index(['draw_id'], 'idx_organizer_draw');
            $table->index(['email_sent_at'], 'idx_email_tracking');
            $table->index(['last_access_at'], 'idx_access_tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizers');
    }
};