<?php

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
        Schema::table('dear_santas', function (Blueprint $table) {
            $table->blob('mail_body')->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->blob('name')->change();
            $table->blob('email')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dear_santas', function (Blueprint $table) {
            $table->longText('mail_body')->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->longText('name')->change();
            $table->longText('email')->change();
        });
    }
};
