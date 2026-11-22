<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A withdrawal waits a while before taking effect: the organizer fixing the address cancels it (see ApplyWithdrawal)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table): void {
            $table->timestamp('withdraws_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table): void {
            $table->dropColumn('withdraws_at');
        });
    }
};
