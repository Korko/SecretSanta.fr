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
        Schema::table('dear_santas', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained('draws', 'id')->cascadeOnDelete();
        });

        DB::table('dear_santas')
            ->join('participants', function ($join) {
                $join->on('dear_santas.sender_id', '=', 'participants.id');
            })
            ->update(
                array(
                    'dear_santas.draw_id' => DB::raw('participants.draw_id')
                )
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dear_santas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('draw_id');
        });
    }
};
