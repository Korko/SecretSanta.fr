<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The state of a draw from the browser that sent the form (#117): the draw is solved by a job
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draw_trackings', function (Blueprint $table): void {
            $table->id();
            // Random (v4), in the page URL fragment: may be shared, only tells whether the draw went through
            $table->uuid()->unique();
            // Only kept in the browser that sent the form: gives the details
            $table->string('token_hash', 64);
            $table->string('state', 16);
            // Never anything about the participants: the draw isn't saved without a solution
            $table->string('failure_reason', 32)->nullable();
            // None until solved; deleted with the draw
            $table->foreignId('draw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
