<?php

use App\Models\Draw;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->timestamp('finished_at')->nullable();
        });

        if (config('app.env') !== 'testing') {
            DB::table('draws')
                ->update(
                    [
                        'updated_at' => DB::raw('GREATEST(updated_at, DATE_SUB(expires_at, INTERVAL '.Draw::MONTHS_BEFORE_EXPIRATION.' MONTH))'),
                        'finished_at' => DB::raw('IF(expires_at >= NOW(), expires_at, NULL)'),
                    ]
                );
        }

        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable();
        });

        if (config('app.env') !== 'testing') {
            DB::table('draws')
                ->update(
                    [
                        'expires_at' => DB::raw('GREATEST(finished_at, DATE_ADD(updated_at, INTERVAL '.Draw::MONTHS_BEFORE_EXPIRATION.' MONTH))'),
                    ]
                );
        }

        Schema::table('draws', function (Blueprint $table) {
            $table->timestamp('expires_at')->change();
            $table->dropColumn('finished_at');
        });
    }
};
