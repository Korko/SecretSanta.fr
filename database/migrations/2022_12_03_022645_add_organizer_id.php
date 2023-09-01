<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::table('draws', function (Blueprint $table) {
            $table->foreignId('organizer_id')->nullable()->constrained('participants')->nullOnDelete();
        });

        DB::update('UPDATE draws
            LEFT JOIN
                (SELECT id, draw_id, email FROM participants WHERE id IN (SELECT MIN(id) FROM participants GROUP BY draw_id)) AS organizer
                ON organizer.draw_id = draws.id AND organizer.email = draws.organizer_email
            SET organizer_id = organizer.id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('organizer_id');
        });
    }
};
