<?php

use App\Models\Draw;
use App\Models\Participant;
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
        Schema::table('participants', function (Blueprint $table) {
            $table->ulid()->after('id')->unique();
        });

        // Generate ulids
        Participant::all()->map(function ($participant) {
            $participant->update([
                'ulid' => Str::ulid()
            ]);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('ulid');
        });
    }
};
