<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Why a mail bounced: the enhanced status code of the delivery report (RFC 3463, "5.1.1"), not its text, which
// quotes the address and the remote server's words while the address itself is only stored encrypted
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mails', function (Blueprint $table): void {
            $table->string('error_code', 9)->nullable()->after('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::table('mails', function (Blueprint $table): void {
            $table->dropColumn('error_code');
        });
    }
};
