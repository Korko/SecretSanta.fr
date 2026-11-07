<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('draws', 'organizer_name')) {
            Schema::table('draws', function (Blueprint $table): void {
                $table->longText('organizer_name')->nullable();
                $table->longText('organizer_email')->nullable();
            });

            DB::table('draws')
                ->join('participants as organizer', function (JoinClause $join): void {
                    $join
                        ->on('draws.id', '=', 'organizer.draw_id')
                        ->whereNotExists(function (Builder $query): void {
                            $query->selectRaw('1')
                                ->from('participants')
                                ->whereColumn('draws.id', '=', 'participants.draw_id')
                                ->whereColumn('organizer.id', '>', 'participants.id');
                        });
                })
                ->update(
                    [
                        'organizer_name' => DB::raw('organizer.name'),
                        'organizer_email' => DB::raw('organizer.email'),
                    ]
                );

            Schema::table('draws', function (Blueprint $table): void {
                $table->longText('organizer_name')->nullable(false)->change();
                $table->longText('organizer_email')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make separate calls for SQLite
        Schema::table('draws', function (Blueprint $table): void {
            $table->dropColumn('organizer_name');
        });

        Schema::table('draws', function (Blueprint $table): void {
            $table->dropColumn('organizer_email');
        });
    }
};
