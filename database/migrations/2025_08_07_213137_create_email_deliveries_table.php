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
        Schema::create('email_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            
            // Polymorphic relation pour participant ou organizer
            $table->string('recipient_type'); // Participant::class ou Organizer::class
            $table->unsignedBigInteger('recipient_id');
            $table->index(['recipient_type', 'recipient_id'], 'idx_recipient');
            
            // Détails de l'email
            $table->string('email_type', 50); // 'invitation', 'reminder', 'results', etc.
            $table->string('email_address');
            $table->string('subject');
            $table->uuid('message_id')->unique(); // ID unique pour tracer les bounces
            
            // Statut et timestamps
            $table->enum('status', ['pending', 'sent', 'delivered', 'bounced'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            
            // Gestion des erreurs et tentatives
            $table->text('bounce_reason')->nullable();
            $table->integer('attempts_count')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            
            // Métadonnées pour informations supplémentaires
            $table->json('metadata')->nullable();
            
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            
            // Indexes pour performance
            $table->index(['draw_id', 'status'], 'idx_draw_status');
            $table->index(['status', 'created_at'], 'idx_status_date');
            $table->index(['email_address', 'status'], 'idx_email_status');
            $table->index(['last_attempt_at'], 'idx_last_attempt');
            $table->index(['sent_at'], 'idx_sent_at');
            $table->index(['bounced_at'], 'idx_bounced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_deliveries');
    }
};