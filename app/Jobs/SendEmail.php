<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Generic job for sending emails
 */
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $emailType;
    private array $data;

    public int $timeout = 60;
    public int $tries = 5;

    public function __construct(string $emailType, array $data)
    {
        $this->emailType = $emailType;
        $this->data = $data;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            match ($this->emailType) {
                'participant_draw_ready' => $this->sendParticipantDrawReady(),
                'draw_completed' => $this->sendDrawCompthanded(),
                'draw_failed' => $this->sendDrawFailed(),
                'registration_request' => $this->sendRegistrationRequest(),
                'registration_accepted' => $this->sendRegistrationAccepted(),
                'registration_rejected' => $this->sendRegistrationRejected(),
                'message_notification' => $this->sendMessageNotification(),
                'organizer_notification' => $this->sendOrganizerNotification(),
                default => throw new \InvalidArgumentException("Unknown email type: {$this->emailType}")
            };

            Log::info("Email sent successfully", [
                'type' => $this->emailType,
                'recipient' => $this->data['participant_email'] ?? $this->data['email'] ?? 'aknown'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send email", [
                'type' => $this->emailType,
                'error' => $e->getMessage(),
                'data' => $this->data
            ]);
            throw $e;
        }
    }

    /**
     * Draw ready email for participant
     */
    private function sendParticipantDrawReady(): void
    {
        $email = new \App\Mail\ParticipantDrawReady(
            $this->data['participant_name'],
            $this->data['draw_title'],
            $this->data['participant_link']
        );

        \Mail::to($this->data['participant_email'])->send($email);
    }

    /**
     * Draw completed email for organizer
     */
    private function sendDrawCompthanded(): void
    {
        $email = new \App\Mail\DrawCompthanded(
            $this->data['organizer_name'],
            $this->data['draw_title'],
            $this->data['message'],
            $this->data['organizer_link']
        );

        \Mail::to($this->data['organizer_email'])->send($email);
    }

    /**
     * Draw failed email for organizer
     */
    private function sendDrawFailed(): void
    {
        $email = new \App\Mail\DrawFailed(
            $this->data['organizer_name'],
            $this->data['draw_title'],
            $this->data['reason'],
            $this->data['organizer_link']
        );

        \Mail::to($this->data['organizer_email'])->send($email);
    }

    /**
     * Registration request email
     */
    private function sendRegistrationRequest(): void
    {
        $email = new \App\Mail\RegistrationRequest(
            $this->data['organizer_name'],
            $this->data['participant_name'],
            $this->data['participant_email'],
            $this->data['draw_title'],
            $this->data['management_link']
        );

        \Mail::to($this->data['organizer_email'])->send($email);
    }

    /**
     * Registration accepted email
     */
    private function sendRegistrationAccepted(): void
    {
        $email = new \App\Mail\RegistrationAccepted(
            $this->data['participant_name'],
            $this->data['draw_title'],
            $this->data['organizer_name']
        );

        \Mail::to($this->data['participant_email'])->send($email);
    }

    /**
     * Registration rejected email
     */
    private function sendRegistrationRejected(): void
    {
        $email = new \App\Mail\RegistrationRejected(
            $this->data['participant_name'],
            $this->data['draw_title'],
            $this->data['reason'] ?? 'No reason provided'
        );

        \Mail::to($this->data['participant_email'])->send($email);
    }

    /**
     * Message notification email
     */
    private function sendMessageNotification(): void
    {
        $email = new \App\Mail\MessageNotification(
            $this->data['recipient_name'],
            $this->data['draw_title'],
            $this->data['message_type'],
            $this->data['participant_link']
        );

        \Mail::to($this->data['recipient_email'])->send($email);
    }

    /**
     * Organizer notification email
     */
    private function sendOrganizerNotification(): void
    {
        $email = new \App\Mail\OrganizerNotification(
            $this->data['organizer_name'],
            $this->data['draw_title'],
            $this->data['notification_type'],
            $this->data['message'],
            $this->data['organizer_link']
        );

        \Mail::to($this->data['organizer_email'])->send($email);
    }
}
