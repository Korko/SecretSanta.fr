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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->tinyInteger('encryption_version')->unsigned()->default(2);
            $table->text('name_encrypted');
            $table->text('email_encrypted');
            
            $table->string('name_hash', 64)->nullable();
            $table->string('email_hash', 64)->nullable();
            
            $table->boolean('is_default')->default(false);
            
            $table->timestamps();
            
            $table->index(['user_id', 'is_default'], 'idx_user_profiles');
            $table->index('name_hash', 'idx_name_hash');
            $table->index('email_hash', 'idx_email_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
