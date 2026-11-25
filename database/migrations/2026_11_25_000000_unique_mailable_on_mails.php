<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// One mail per mailable (morphOne): two simultaneous Draw::recapMail() on an old draw could create two
return new class extends Migration
{
    public function up(): void
    {
        // Keep the latest of each duplicate
        $keep = DB::table('mails')->selectRaw('MAX(id)')->groupBy('mailable_type', 'mailable_id');
        $duplicates = DB::table('mails')->whereNotIn('id', $keep)->pluck('id');
        $duplicates->chunk(1000)->each(fn ($ids) => DB::table('mails')->whereIn('id', $ids)->delete());

        Schema::table('mails', function (Blueprint $table): void {
            $table->unique(['mailable_type', 'mailable_id']);
            $table->dropIndex(['mailable_type', 'mailable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('mails', function (Blueprint $table): void {
            $table->index(['mailable_type', 'mailable_id']);
            $table->dropUnique(['mailable_type', 'mailable_id']);
        });
    }
};
