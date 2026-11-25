<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Failed jobs are not stored anymore (config/queue.php): the ones already there still hold their draw secret
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
