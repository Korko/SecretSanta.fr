<?php

namespace App\Mail;

use App;
use App\Models\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class OrganizerFinalRecap extends Mailable
{
    use Queueable, SerializesModels;

    public $organizerName;
    public $expirationDate;
    public $csv;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw)
    {
        $this->subject = __('emails.organizer_final_recap_title', ['draw' => $draw->id]);

        $this->organizerName = $draw->organizer->name;

        $this->expirationDate = $draw->expires_at->locale(App::getLocale())->isoFormat('LL');

        $this->csv = $draw->participants->toCsv(['name', 'email', 'exclusionsNames']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.organizer_final_recap')
                    ->text('emails.organizer_final_recap_plain')
                    ->attachData($this->csv, 'secretsanta.csv', [
                        'mime' => 'text/csv',
                    ]);
    }
}
