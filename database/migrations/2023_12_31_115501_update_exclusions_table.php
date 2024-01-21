<?php

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
        Schema::table('exclusions', function (Blueprint $table) {
            $table->unique(['participant_id', 'exclusion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exclusions', function (Blueprint $table) {
            $table->dropUnique(['participant_id', 'exclusion_id']);
        });
    }
};
