<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Adds "delayed" (a server reported a delay and keeps trying). The table is created from DeliveryStatus::cases(),
// so only existing MySQL/MariaDB enum columns need it; SQLite (tests) already has it from the create migration
return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE mails MODIFY delivery_status ENUM('created','sending','sent','error','delayed','received') NOT NULL");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::table('mails')->where('delivery_status', 'delayed')->update(['delivery_status' => 'sent']);
            DB::statement("ALTER TABLE mails MODIFY delivery_status ENUM('created','sending','sent','error','received') NOT NULL");
        }
    }
};
