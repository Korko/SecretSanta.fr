<?php

use App\Enums\EmailAddressStatus;
use App\Enums\PendingDrawStatus;
use App\Models\PendingDraw;
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
        Schema::disableForeignKeyConstraints();
        Schema::create('pending_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pending_draw_id')->constrained()->cascadeOnDelete();
            $table->binary('name');
            $table->binary('email')->nullable();
            $table->enum('email_status', EmailAddressStatus::values());
            $table->json('exclusions')->nullable();
            $table->timestamps();
        });

        Schema::create('pending_draws', function (Blueprint $table) {
            $table->id();
            $table->binary('organizer_name');
            $table->binary('organizer_email');
            $table->binary('title');
            $table->foreignId('organizer_id')->nullable()->constrained('pending_participants')->nullOnDelete();
            $table->enum('email_status', EmailAddressStatus::values());
            $table->enum('status', PendingDrawStatus::values());
            $table->foreignId('draw_id')->nullable()->constrained('draws')->nullOnDelete();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_draws');
        Schema::dropIfExists('pending_participants');
    }
};
