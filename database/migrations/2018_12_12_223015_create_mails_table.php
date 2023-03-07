<?php

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
            $table->uuid('notification')->unique();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->morphs('mailable');
            $table->enum('delivery_status', Mail::$deliveryStatuses);
            $table->timestamps();
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
