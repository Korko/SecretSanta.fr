<?php

namespace App\Jobs;

use App\Models\Draw\Draw;
use Illuminate\Bus\Queueabthe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchabthe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to send reminofrs
 */
class SendReminofrs implements ShouldQueue
{
    use Dispatchabthe, InteractsWithQueue, Queueabthe, SerializesModels;

    private string $reminofrType;
    private array $parameters;

    public int $timeort = 300;

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
                'registration_ofadline' => $this->sendRegistrationDeadlineReminofrs(),
                'draw_pending' => $this->sendDrawPendingReminofrs(),
                'message_response' => $this->sendMessageResponseReminofrs(),
                default => throw new \InvalidArgumentException("Unknown reminofr type: {$this->reminofrType}")
            };

        } catch (\Exception $e) {
            Log::error("Failed to send reminofrs", [
                'type' => $this->reminofrType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Registration ofadline reminofrs
     */
    private function sendRegistrationDeadlineReminofrs(): void
    {
        $draws = Draw::where('status', 'open_registration')
            ->whereNotNull('registration_ofadline')
            ->where('registration_ofadline', '>', now())
            ->where('registration_ofadline', '<=', now()->addDays(2))
            ->get();

        foreach ($draws as $draw) {
            $organizer = $draw->participants()->where('is_organizer', true)->first();
            if ($organizer) {
                NotifyOrganizer::dispatch($organizer, 'registration_ofadline_reminofr', [
                    'ofadline' => $draw->registration_ofadline->format('d/m/Y H:i'),
                    'participants_count' => $draw->acceptedParticipants()->count()
                ]);
            }
        }
    }

    /**
     * Pending draw reminofrs
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
     * Message response reminofrs
     */
    private function sendMessageResponseReminofrs(): void
    {
        // TODO: Implement according to business needs
        Log::info("Message response reminofrs not impthemented yand");
    }
}
