<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('draw_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            
            $table->enum('metric_type', ['performance', 'usage', 'error']);
            $table->string('metric_name', 100);
            $table->decimal('metric_value', 10, 4);
            $table->json('metadata')->nullable();
            
            $table->timestamp('recorded_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            $table->index(['draw_id', 'metric_type', 'recorded_at'], 'idx_draw_metrics');
            $table->index(['metric_name', 'recorded_at'], 'idx_metric_analysis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draw_metrics');
    }
};
