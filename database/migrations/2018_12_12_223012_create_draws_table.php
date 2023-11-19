<?php

use App\Enums\DrawStatus;
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
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->foreignIdFor(Participant::class, 'organizer_id')->nullable()->constrained('draw_users');
            $table->boolean('participant_organizer');
            $table->blob('title');
            $table->blob('description')->nullable();
            $table->string('budget', 55)->nullable();
            $table->date('event_date')->nullable();
            $table->timestamps();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('drawn_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->enum('status', DrawStatus::values());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draws');
    }
};
