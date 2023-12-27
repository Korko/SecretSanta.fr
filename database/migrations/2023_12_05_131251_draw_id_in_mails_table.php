<?php

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\Participant;
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
        Schema::table('mails', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained('draws', 'id')->cascadeOnDelete();
        });

        DB::table('mails')
            ->join('participants', function (JoinClause $join) {
                $join
                    ->on('mails.mailable_id', '=', 'participants.id')
                    ->where('mails.mailable_type', '=', Participant::class);
            })
            ->update(
                array(
                    'mails.draw_id' => DB::raw('participants.draw_id')
                )
            );

        DB::table('mails')
            ->join('dear_santas', function (JoinClause $join) {
                $join
                    ->on('mails.mailable_id', '=', 'dear_santas.id')
                    ->where('mails.mailable_type', '=', DearSanta::class);
            })
            ->update(
                array(
                    'mails.draw_id' => DB::raw('dear_santas.draw_id')
                )
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('draw_id');
        });
    }
};
