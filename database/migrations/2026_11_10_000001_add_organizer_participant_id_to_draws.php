<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Null when the organizer doesn't take part. Not backfilled (emails are encrypted with each draw's IV):
// existing draws keep being detected by comparing the first participant's email (see Draw::organizer)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->foreignId('organizer_participant_id')->nullable()->constrained('participants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('organizer_participant_id');
        });
    }
};
