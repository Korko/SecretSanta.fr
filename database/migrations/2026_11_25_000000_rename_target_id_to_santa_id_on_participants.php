<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The column holds the id of the participant's santa (target() is the hasOne side), not of their target
return new class extends Migration
{
    public function up(): void
    {
        $this->rename('target_id', 'santa_id');
    }

    public function down(): void
    {
        $this->rename('santa_id', 'target_id');
    }

    private function rename(string $from, string $to): void
    {
        Schema::table('participants', function (Blueprint $table) use ($from): void {
            $table->dropForeign([$from]);
        });

        Schema::table('participants', function (Blueprint $table) use ($from, $to): void {
            $table->renameColumn($from, $to);
        });

        Schema::table('participants', function (Blueprint $table) use ($to): void {
            $table->foreign($to)->references('id')->on('participants')->nullOnDelete();
        });
    }
};
