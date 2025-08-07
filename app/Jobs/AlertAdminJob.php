<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchable;
use Illuminate\Foadation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to alert administrators
 */
class AlertAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $level;
    public string $message;
    public array $context;

    public function __construct(string $level, string $message, array $context = [])
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;

        $this->onQueue('alerts');
    }

    public function handle(): void
    {
        // Send alert via different channels based on level
        switch ($this->level) {
            case 'critical':
                $this->sendSMS();
                $this->sendSlack();
                $this->sendEmail();
                break;
            case 'warning':
                $this->sendSlack();
                $this->sendEmail();
                break;
            default:
                $this->sendSlack();
                break;
        }

        // Log in database
        \DB::table('admin_alerts')->insert([
            'level' => $this->level,
            'message' => $this->message,
            'context' => json_encode($this->context),
            'created_at' => now(),
        ]);
    }

    protected function sendSMS(): void
    {
        // Twilio integration or other SMS service
        // ...
    }

    protected function sendSlack(): void
    {
        // Slack notification
        \Notification::route('slack', config('services.slack.webhook_url'))
            ->notify(new \App\Notifications\AdminAlert($this->level, $this->message, $this->context));
    }

    protected function sendEmail(): void
    {
        // Email to admins
        $admins = config('app.admin_emails', []);
        foreach ($admins as $email) {
            \Mail::to($email)->queue(new \App\Mail\AdminAlert($this->level, $this->message, $this->context));
        }
    }
}
