<?php

namespace App\Mail;

use App\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrganizerFinalRecap extends Mailable
{
    use Queueable, SerializesModels;

    public $organizerName;
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

        $participantNames = $draw->participants->pluck('name', 'id');
        $this->csv = $this->formatCsv($draw->participants->map(function ($participant) use ($participantNames) {
            return [
                $participant->name,
                $participant->email_address,
                collect($participant->exclusions)
                    ->map(function ($participantId) use ($participantNames) {
                        return $participantNames[$participantId];
                    })
                    ->implode(','),
            ];
        }));
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
