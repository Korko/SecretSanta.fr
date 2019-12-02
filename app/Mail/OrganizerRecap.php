<?php

namespace App\Mail;

use App\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrganizerRecap extends Mailable
{
    use Queueable, SerializesModels;

    public $panelLink;

    public $draw;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw, $panelLink)
    {
        $this->draw = $draw;
        $this->panelLink = $panelLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $csv = $this->formatCsv($this->draw->participants->map(function ($participant) {
            return [
                $participant->name,
                $participant->email_address,
            ];
        }));

        return $this->subject(__('emails.organizer.title', ['draw' => $this->draw->id]))
                    ->view('emails.organizer_recap')
                    ->text('emails.organizer_recap_plain')
                    ->attachData($csv, 'secretsanta.csv', [
                        'mime' => 'text/csv',
                    ]);
    }

    protected function formatCsv(iterable $data, $delimiter = ',', $enclosure = '"', $escape_char = '\\')
    {
        $f = fopen('php://memory', 'r+');
        foreach ($data as $fields) {
            if (fputcsv($f, $fields, $delimiter, $enclosure, $escape_char) === false) {
                return false;
            }
        }
        rewind($f);
        $csv_line = stream_get_contents($f);

        return rtrim($csv_line);
    }
}
