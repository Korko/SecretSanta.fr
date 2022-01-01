<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReduceColumnsSizes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->string('organizer_name')->change();
            $table->text('organizer_email')->change();
            $table->text('mail_title')->change();
            $table->text('mail_body')->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('name')->change();
            $table->text('email')->change();
        });

        Schema::table('dear_santas', function (Blueprint $table) {
            $table->text('mail_body')->change();
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
}
