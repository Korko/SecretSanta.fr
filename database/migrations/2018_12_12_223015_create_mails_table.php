<?php

use App\Models\Draw;
use App\Models\Mail;
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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->morphs('mailable');
            $table->enum('delivery_status', Mail::$deliveryStatuses);
            $table->timestamps();
            $table->foreignIdFor(Draw::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
