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
        Schema::table('draws', function (Blueprint $table) {
            $table->tinyBlob('organizer_name')->change();
            $table->blob('organizer_email')->change();
            $table->blob('mail_title')->change();
            $table->blob('mail_body')->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->tinyBlob('name')->change();
            $table->blob('email')->change();
        });

        Schema::table('dear_santas', function (Blueprint $table) {
            $table->blob('mail_body')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->longText('organizer_name')->change();
            $table->longText('organizer_email')->change();
            $table->longText('mail_title')->change();
            $table->longText('mail_body')->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->longText('name')->change();
            $table->longText('email')->change();
        });

        Schema::table('dear_santas', function (Blueprint $table) {
            $table->longText('mail_body')->change();
        });
    }
};
