<?php

use App\Models\Draw;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exclusions', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained('draws', 'id')->cascadeOnDelete();
        });

        DB::table('exclusions')
            ->join('participants', function (JoinClause $join) {
                $join->on('exclusions.participant_id', '=', 'participants.id');
            })
            ->update(
                array(
                    'exclusions.draw_id' => DB::raw('participants.draw_id')
                )
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exclusions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('draw_id');
        });
    }
};
