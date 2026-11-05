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
        Schema::table('participants', function (Blueprint $table) {
            $table->integer('email_resent_count')->default(0)->after('email_opened_at');
            $table->timestamp('email_updated_at')->nullable()->after('email_resent_count');
            $table->string('temp_new_email')->nullable()->after('email_updated_at')->comment('Temporary field for testing');
            $table->foreignId('target_participant_id')->nullable()->after('temp_new_email')->constrained('participants')->nullOnDelete();
            $table->string('target_name_encrypted')->nullable()->after('target_participant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['target_participant_id']);
            $table->dropColumn([
                'email_resent_count',
                'email_updated_at', 
                'temp_new_email',
                'target_participant_id',
                'target_name_encrypted'
            ]);
        });
    }
};