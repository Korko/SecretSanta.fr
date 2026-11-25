<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single use codes from the administrator to go over MAX_PARTICIPANTS (secretsanta:allow-participants)
        Schema::create('participant_limits', function (Blueprint $table): void {
            $table->id();
            $table->char('code_hash', 64)->unique();
            $table->unsignedInteger('max');
            $table->timestamp('used_at')->nullable();
            // Gone with its draw: a used code can't be used again anyway (only its hash is kept)
            $table->foreignId('draw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // The limit a draw was created with, kept for the participants added later from the panel
        Schema::table('draws', function (Blueprint $table): void {
            $table->unsignedInteger('max_participants')->nullable();
        });
    }
};
