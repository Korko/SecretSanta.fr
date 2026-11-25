<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Prod has mails.notification indexed twice (`mails_id_unique` and `notification`) instead of `mails_notification_unique`
return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(Schema::getIndexes('mails'))
            ->filter(fn (array $index): bool => $index['unique'] && ! $index['primary'] && $index['columns'] === ['notification'])
            ->map(fn (array $index): string => $index['name'])->values();

        if ($indexes->all() === ['mails_notification_unique']) {
            return;
        }

        Schema::table('mails', function (Blueprint $table) use ($indexes): void {
            $indexes->each(fn (string $name) => $table->dropUnique($name));
            $table->unique('notification');
        });
    }
};
