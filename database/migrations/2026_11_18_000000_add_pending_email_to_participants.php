<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A participant moving to another address proves they own it with a code sent there (see DearSantaController::changeEmail)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table): void {
            $table->text('pending_email')->nullable();
            $table->string('pending_email_code', 64)->nullable();
            $table->timestamp('pending_email_at')->nullable();
            $table->unsignedTinyInteger('pending_email_attempts')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table): void {
            $table->dropColumn(['pending_email', 'pending_email_code', 'pending_email_at', 'pending_email_attempts']);
        });
    }
};
