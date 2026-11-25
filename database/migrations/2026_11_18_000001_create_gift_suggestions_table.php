<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A participant's gift idea for someone else than their own target, emailed to that person's santa
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_suggestions', function (Blueprint $table): void {
            $table->id();
            // Never shown: only keeps their own ideas from them. Kept without it when they leave the draw
            $table->foreignId('sender_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('recipient_id')->constrained('participants')->cascadeOnDelete();
            $table->longText('mail_body');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_suggestions');
    }
};
