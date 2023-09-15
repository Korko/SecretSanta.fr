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
        Schema::create('dear_santas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Participant::class, 'sender_id')->constrained('participants')->cascadeOnDelete();
            $table->blob('mail_body');
            $table->foreignIdFor(Draw::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dear_santas');
    }
};
