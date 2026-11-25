<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Set on every new draw, null on older ones: only those fall back to the email check (see Draw::organizer)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->boolean('organizer_participates')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->dropColumn('organizer_participates');
        });
    }
};
