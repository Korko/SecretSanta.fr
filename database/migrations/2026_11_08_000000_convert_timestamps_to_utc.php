<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Dates used to be written in the app timezone through a UTC session, so MySQL stored local times as if they were UTC.
    // Each one is converted with its own offset (summer or winter time). failed_jobs.failed_at was set by MySQL: already UTC
    private const array COLUMNS = [
        'draws' => ['created_at', 'updated_at', 'organizer_received_at', 'targets_revealed_at'],
        'participants' => ['created_at', 'updated_at'],
        'mails' => ['created_at', 'updated_at'],
    ];

    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        // Read and write the raw stored values
        DB::statement("SET time_zone = '+00:00'");

        foreach (self::COLUMNS as $table => $columns) {
            DB::table($table)->select(['id', ...$columns])->chunkById(1000, function ($rows) use ($table, $columns): void {
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($columns as $column) {
                        if (is_string($row->$column)) {
                            $values[$column] = CarbonImmutable::parse($row->$column, config()->string('app.timezone'))->utc()->toDateTimeString();
                        }
                    }

                    if ($values !== []) {
                        DB::table($table)->where('id', $row->id)->update($values);
                    }
                }
            });
        }

        DB::statement('SET time_zone = ?', [date('P')]);
    }
};
