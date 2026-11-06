<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

it('leaves a single unique index on mails.notification', function (): void {
    Schema::table('mails', function (Blueprint $table): void {
        $table->dropUnique('mails_notification_unique');
        $table->unique('notification', 'mails_id_unique');
        $table->unique('notification', 'notification');
    });

    (require database_path('migrations/2026_11_06_000000_dedupe_mails_notification_unique.php'))->up();

    $names = collect(Schema::getIndexes('mails'))
        ->filter(fn (array $index): bool => $index['columns'] === ['notification'])
        ->pluck('name')->all();
    expect($names)->toBe(['mails_notification_unique']);
});
