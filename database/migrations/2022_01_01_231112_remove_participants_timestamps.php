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
        if (Schema::hasColumn('participants', 'created_at')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('participants', 'created_at')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }
};
