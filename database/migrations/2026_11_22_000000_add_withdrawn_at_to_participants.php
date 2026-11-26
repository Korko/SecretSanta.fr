<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A participant who withdrew from their page is kept aside rather than deleted: the organizer can bring them back,
// when someone else got their email and withdrew in their place (see ParticipantsEditor::withdraw)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table): void {
            $table->timestamp('withdrawn_at')->nullable();
        });
    }
};
