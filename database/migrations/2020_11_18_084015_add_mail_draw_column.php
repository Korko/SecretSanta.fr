<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMailDrawColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('mails', function (Blueprint $table) {
            $table->foreignId('draw_id')->constrained();
            $table->unsignedTinyInteger('version')->default(0);
        });

        DB::table('mails')
            ->join('participants', 'participants.mail_id', '=', 'mails.id')
            ->update(['mails.draw_id' => DB::raw('participants.draw_id')]);

        DB::table('mails')
            ->join('dear_santas', 'dear_santas.mail_id', '=', 'mails.id')
            ->join('participants', 'participants.id', '=', 'dear_santas.sender_id')
            ->update(['mails.draw_id' => DB::raw('participants.draw_id')]);

        DB::table('mails')
            ->where('draw_id', '=', 0)
            ->delete();

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mails', function (Blueprint $table) {
            $table->dropForeign(['draw_id']);
            $table->dropColumn(['draw_id', 'version']);
        });
    }
}
