<?php

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Even if Laravel has doctrine/dbal, please ignore it and use the native schema builder
        // We need it for the renameTo method
        SchemaBuilder::$alwaysUsesNativeSchemaOperationsIfPossible = true;
        Schema::table('draws', function (Blueprint $table) {
            $table->blob('mail_title')->renameTo('title')->change();
            $table->blob('mail_body')->nullable()->renameTo('description')->change();

            $table->boolean('participant_organizer');
            $table->foreignId('organizer_id')->nullable()->constrained('participants')->nullOnDelete();

            $table->string('budget', 55)->nullable();
            $table->date('event_date')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('drawn_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->enum('status', DrawStatus::values());
        });

        DB::table('draws')
            ->leftJoin('participants as organizer', function ($join) {
                $join
                    ->on('draws.id', '=', 'organizer.draw_id')
                    ->whereColumn('draws.organizer_name', '=', 'organizer.name')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('participants')
                            ->whereColumn('draws.id', '=', 'participants.draw_id')
                            ->whereColumn('organizer.id', '>', 'participants.id');
                });
            })
            ->update(
                array(
                    'organizer_id' => '`organizer`.`id`',
                    'participant_organizer' => '`organizer`.`id` IS NOT NULL',
                    'budget' => NULL,
                    'event_date' => '`expires_at`',
                    'ready_at' => '`created_at`',
                    'drawn_at' => '`created_at`',
                    'finished_at' => '`expires_at` < NOW() ? NOW() : NULL',
                    'status' => '`expires_at` < NOW() ? "' . DrawStatus::FINISHED->value . '" : "' . DrawStatus::STARTED->value . '"',
                )
            );

        Schema::table('draws', function (Blueprint $table) {
            $table->dropColumn('expires_at');
            $table->dropColumn('next_solvable');
            $table->dropColumn('organizer_email');
            $table->dropColumn('organizer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('draws', function (Blueprint $table) {
            $table->longText('title')->renameTo('mail_title')->change();
            $table->longText('description')->nullable(false)->renameTo('mail_body')->change();

            $table->date('expires_at');
            $table->boolean('next_solvable');
            $table->longText('organizer_email');
            $table->longText('organizer_name');
        });

        DB::table('draws')
            ->leftJoin('participants as organizer', function ($join) {
                $join->on('draws.organizer_id', '=', 'organizer.id');
            })
            ->update(
                array(
                    'expires_at' => '`event_date`',
                    'next_solvable' => TRUE, // Won't recompute for each draw, considere true, it's the case for most of them
                    'organizer_email' => '`organizer`.`email`',
                    'organizer_name' => '`organizer`.`name`',
                )
            );

        Schema::table('draws', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organizer_id');
            $table->dropColumn('participant_organizer');
            $table->dropColumn('budget');
            $table->dropColumn('event_date');
            $table->dropColumn('ready_at');
            $table->dropColumn('drawn_at');
            $table->dropColumn('finished_at');
            $table->dropColumn('status');
        });
    }
};
