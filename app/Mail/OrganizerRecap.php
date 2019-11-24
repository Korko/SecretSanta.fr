<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrganizerRecap extends Mailable
{
    use Queueable, SerializesModels;

    public $participants;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($participants)
    {
        $this->participants = $participants;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $csv = $this->formatCsv(array_map(function ($participant) {
            return [
                $participant['name'],
                $participant['email']
            ];
        }, $this->participants));

        return $this->view('emails.organizer_recap')
                    ->text('emails.organizer_recap_plain')
                    ->attachData($csv, 'secretsanta.csv', [
                        'mime' => 'text/csv',
                    ]);
    }

    protected function formatCsv($data, $delimiter = ",", $enclosure = '"', $escape_char = "\\")
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
