<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// null: not computed yet (ComputeNextSolvable, after the draw or a change of participants).
// The existing draws keep their value
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->boolean('next_solvable')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        DB::table('draws')->whereNull('next_solvable')->update(['next_solvable' => true]);

        Schema::table('draws', function (Blueprint $table): void {
            $table->boolean('next_solvable')->default(true)->change();
        });
    }
};
