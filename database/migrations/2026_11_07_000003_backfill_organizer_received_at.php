<?php

declare(strict_types=1);

use App\Enums\DeliveryStatus;
use App\Models\Participant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // A participating organizer is the first participant, with the same email: emails were encrypted with a fixed IV
    // per draw (before DrawEncrypter), so the ciphertexts match. Their target email carries the panel link, its
    // reception proves both. Only valid for data written in that legacy format, which is all this backfill targets
    public function up(): void
    {
        DB::update(
            'UPDATE draws SET organizer_received_at = (
                SELECT mails.updated_at FROM participants
                JOIN mails ON mails.mailable_type = ? AND mails.mailable_id = participants.id
                WHERE participants.id = (SELECT MIN(id) FROM participants WHERE draw_id = draws.id)
                    AND participants.email = draws.organizer_email
                    AND mails.delivery_status = ?
            ) WHERE organizer_received_at IS NULL',
            [Participant::class, DeliveryStatus::Received->value],
        );
    }
};
