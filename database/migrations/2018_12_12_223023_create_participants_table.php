<?php

use App\Models\Draw;
use App\Models\Participant;
use App\Models\User;
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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->foreignIdFor(Draw::class)->constrained()->cascadeOnDelete();
            $table->tinyBlob('name');
            $table->blob('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignIdFor(Participant::class, 'target_id')->nullable()->constrained('participants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
