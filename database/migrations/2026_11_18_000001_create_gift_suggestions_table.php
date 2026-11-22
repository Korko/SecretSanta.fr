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
            $table->foreignId('sender_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('participants')->cascadeOnDelete();
            $table->longText('mail_body');
            $table->boolean('signed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_suggestions');
    }
};
