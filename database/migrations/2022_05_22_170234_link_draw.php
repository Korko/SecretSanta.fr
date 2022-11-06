<?php

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
        Schema::table('dear_santas', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained('draws')->cascadeOnDelete()->after('id');
        });

        Schema::table('mails', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained('draws')->cascadeOnDelete()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dear_santas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('draw_id');
        });

        Schema::table('mails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('draw_id');
        });
    }
};
