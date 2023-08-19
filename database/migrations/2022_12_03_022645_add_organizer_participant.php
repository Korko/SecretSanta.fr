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
            $table->boolean('organizer_participant')->default(true);
        });

        // Set organizer_participant to false for draws where organizer_email is different from the email of the participant with the lowest id (organizer)
        DB::update('UPDATE draws SET organizer_participant = 0 WHERE organizer_email != (SELECT email FROM participants WHERE draw_id = draws.id ORDER BY id ASC LIMIT 1)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('organizer_participant');
        });
    }
};
