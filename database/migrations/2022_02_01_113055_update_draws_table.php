<?php

use App\Enums\DrawStatus;
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
        Schema::disableForeignKeyConstraints();

        Schema::table('participants', function (Blueprint $table) {
            $table->binary('email')->nullable()->change();
            $table->timestamp('email_verified_at')->nullable();

            $table->dropColumn('redraw');
        });

        // TODO ->renameTo is just... ignored????
        Schema::table('draws', function (Blueprint $table) {
            $table->binary('mail_body')->nullable()->change();
        });

        Schema::table('draws', function (Blueprint $table) {
            $table->renameColumn('mail_title', 'title');
            $table->renameColumn('mail_body', 'description');

            $table->enum('status', DrawStatus::values());

            $table->foreignId('organizer_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamp('organizer_email_verified_at')->nullable();

            $table->timestamp('ready_at')->nullable();
            $table->timestamp('drawn_at')->nullable();

            $table->dropColumn('next_solvable');
        });

        DB::update('UPDATE draws
            LEFT JOIN
                (SELECT id, draw_id, email FROM participants WHERE id IN (SELECT MIN(id) FROM participants GROUP BY draw_id)) AS organizer
                ON organizer.draw_id = draws.id AND organizer.email = draws.organizer_email
            SET organizer_id = organizer.id, status = "drawn", ready_at = NOW(), drawn_at = NOW()');

        Schema::enableForeignKeyConstraints();
    }
};
