<?php

namespace App\Jobs;

use App\Models\Draw\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour envoyer les rappels
 */
class SendReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $reminderType;
    private array $parameters;

    public int $timeout = 300;

    public function __construct(string $reminderType, array $parameters = [])
    {
        $this->reminderType = $reminderType;
        $this->parameters = $parameters;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            match ($this->reminderType) {
                'registration_deadline' => $this->sendRegistrationDeadlineReminders(),
                'draw_pending' => $this->sendDrawPendingReminders(),
                'message_response' => $this->sendMessageResponseReminders(),
                default => throw new \InvalidArgumentException("Unknown reminder type: {$this->reminderType}")
            };

        } catch (\Exception $e) {
            Log::error("Failed to send reminders", [
                'type' => $this->reminderType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Rappels de date limite d'inscription
     */
    private function sendRegistrationDeadlineReminders(): void
    {
        $draws = Draw::where('status', 'open_registration')
            ->whereNotNull('registration_deadline')
            ->where('registration_deadline', '>', now())
            ->where('registration_deadline', '<=', now()->addDays(2))
            ->get();

        foreach ($draws as $draw) {
            $organizer = $draw->participants()->where('is_organizer', true)->first();
            if ($organizer) {
                NotifyOrganizer::dispatch($organizer, 'registration_deadline_reminder', [
                    'deadline' => $draw->registration_deadline->format('d/m/Y H:i'),
                    'participants_count' => $draw->acceptedParticipants()->count()
                ]);
            }
        }
    }

    /**
     * Rappels de tirages en attente
     */
    private function sendDrawPendingReminders(): void
    {
        $draws = Draw::where('status', 'closed_registration')
            ->where('updated_at', '<', now()->subDays(3))
            ->get();

        foreach ($draws as $draw) {
            $organizer = $draw->participants()->where('is_organizer', true)->first();
            if ($organizer) {
                NotifyOrganizer::dispatch($organizer, 'draw_pending_reminder', [
                    'days_waiting' => $draw->updated_at->diffInDays(now()),
                    'participants_count' => $draw->acceptedParticipants()->count()
                ]);
            }
        }
    }

    /**
     * Rappels de réponse aux messages
     */
    private function sendMessageResponseReminders(): void
    {
        // TODO: Implémenter selon les besoins métier
        Log::info("Message response reminders not implemented yet");
    }
}
