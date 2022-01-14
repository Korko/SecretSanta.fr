<?php

use App\Models\Draw;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDrawExpiration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('draws', 'finished_at')) {
            Schema::table('draws', function (Blueprint $table) {
                $table->date('finished_at')->nullable();
            });
        }

        if (Schema::hasColumn('draws', 'expires_at')) {
            if(config('app.env') !== 'testing') {
                DB::table('draws')
                    ->update(
                        array(
                            'updated_at' => DB::raw('GREATEST(updated_at, DATE_SUB(expires_at, INTERVAL '.Draw::MONTHS_BEFORE_EXPIRATION.' MONTH))'),
                            'finished_at' => DB::raw('IF(expires_at >= NOW(), expires_at, NULL)')
                        )
                    );
            }

            Schema::table('draws', function (Blueprint $table) {
                $table->dropColumn('expires_at');
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
        Schema::table('draws', function (Blueprint $table) {
            $table->date('expires_at')->nullable();
        });

        if(config('app.env') !== 'testing') {
            DB::table('draws')
                ->update(
                    array(
                        'expires_at' => DB::raw('GREATEST(finished_at, DATE_ADD(updated_at, INTERVAL '.Draw::MONTHS_BEFORE_EXPIRATION.' MONTH))'),
                    )
                );
        }

        Schema::table('draws', function (Blueprint $table) {
            $table->date('expires_at')->change();
            $table->dropColumn('finished_at');
        });
    }
}
