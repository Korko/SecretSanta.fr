<?php

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the table for the Dear Target Letters.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dear_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('participants', 'id')->cascadeOnDelete();
            $table->string('mail_type');
            $table->foreignId('draw_id')->constrained('draws', 'id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dear_targets');
    }
};
