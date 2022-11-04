<?php

use App\Models\PendingDraw;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_draws', function (Blueprint $table) {
            $table->id();
            $table->binary('organizer_name');
            $table->binary('organizer_email');
            $table->longText('data')->nullable(); // Should be better with longBinary but not yet implemented
            $table->enum('status', PendingDraw::$statuses);
            $table->foreignId('draw_id')->nullable()->constrained('draws')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_draws');
    }
};
