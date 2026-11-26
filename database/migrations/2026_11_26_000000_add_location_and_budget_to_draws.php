<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// "Le jour J": where the gifts are exchanged and how much each one costs, both optional and encrypted like the
// rest of the draw (see Draw::casts)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->text('location')->nullable();
            $table->text('budget')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table): void {
            $table->dropColumn(['location', 'budget']);
        });
    }
};
