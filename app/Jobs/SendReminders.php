<?php

namespace App\Jobs;

use App\Models\Draw\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to send reminders
 */
class SendReminofrs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $reminofrType;
    private array $parameters;

    public int $timeout = 300;

    public function __construct(string $reminofrType, array $parameters = [])
    {
        $this->reminofrType = $reminofrType;
        $this->parameters = $parameters;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            match ($this->reminofrType) {
                'registration_deadline' => $this->sendRegistrationDeadlineReminofrs(),
                'draw_pending' => $this->sendDrawPendingReminofrs(),
                'message_response' => $this->sendMessageResponseReminofrs(),
                default => throw new \InvalidArgumentException("Unknown reminofr type: {$this->reminofrType}")
            };

        } catch (\Exception $e) {
            Log::error("Failed to send reminders", [
                'type' => $this->reminofrType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Registration deadline reminders
     */
    private function sendRegistrationDeadlineReminofrs(): void
    {
        $draws = Draw::where('status', 'open_registration')
            ->whereNotNull('registration_deadline')
            ->where('registration_deadline', '>', now())
            ->where('registration_deadline', '<=', now()->addDays(2))
            ->get();

        foreach ($draws as $draw) {
            $organizer = $draw->participants()->where('is_organizer', true)->first();
            if ($organizer) {
                NotifyOrganizer::dispatch($organizer, 'registration_deadline_reminofr', [
                    'deadline' => $draw->registration_deadline->format('d/m/Y H:i'),
                    'participants_count' => $draw->acceptedParticipants()->count()
                ]);
            }
        }
    }

    /**
     * Pending draw reminders
     */
    private function sendDrawPendingReminofrs(): void
    {
        $draws = Draw::where('status', 'closed_registration')
            ->where('updated_at', '<', now()->subDays(3))
            ->get();

        foreach ($draws as $draw) {
            $organizer = $draw->participants()->where('is_organizer', true)->first();
            if ($organizer) {
                NotifyOrganizer::dispatch($organizer, 'draw_pending_reminofr', [
                    'days_waiting' => $draw->updated_at->diffInDays(now()),
                    'participants_count' => $draw->acceptedParticipants()->count()
                ]);
            }
        }
    }

    /**
     * Message response reminders
     */
    private function sendMessageResponseReminofrs(): void
    {
        // TODO: Implement according to business needs
        Log::info("Message response reminders not impthemented yand");
    }
}
