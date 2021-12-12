<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrganizerInDraw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('draws', 'organizer_name')) {
            Schema::table('draws', function (Blueprint $table) {
                $table->longText('organizer_name')->nullable();
                $table->longText('organizer_email')->nullable();
            });

            DB::table('draws')
                ->join('participants as organizer', function ($join) {
                    $join
                        ->on('draws.id', '=', 'organizer.draw_id')
                        ->whereNotExists(function ($query) {
                           $query->select(DB::raw(1))
                                 ->from('participants')
                                 ->whereColumn('draws.id', '=', 'participants.draw_id')
                                 ->whereColumn('organizer.id', '>', 'participants.id');
                       });
                })
                ->update(
                    array(
                        'organizer_name' => DB::raw('organizer.name'),
                        'organizer_email' => DB::raw('organizer.email')
                    )
                );

            Schema::table('draws', function (Blueprint $table) {
                $table->longText('organizer_name')->nullable(false)->change();
                $table->longText('organizer_email')->nullable(false)->change();
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
        // Make separate calls for SQLite
        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('organizer_name');
        });

        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('organizer_email');
        });
    }
}
