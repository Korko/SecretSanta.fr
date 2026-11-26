<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A gift idea can be signed: its sender's name is then shown to the recipient's santa ("De : Inès"), never otherwise
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gift_suggestions', function (Blueprint $table): void {
            $table->boolean('signed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('gift_suggestions', function (Blueprint $table): void {
            $table->dropColumn('signed');
        });
    }
};
