<?php

namespace App\Mail;

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

        $participantNames = $draw->participants->pluck('name', 'id');
        $this->csv = $this->formatCsv($draw->participants->map(function ($participant) use ($participantNames) {
            return [
                $participant->name,
                $participant->email,
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

    protected function formatCsv(iterable $data, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
    {
        $fileHandler = fopen('php://memory', 'r+');
        foreach ($data as $fields) {
            if (fputcsv($fileHandler, $fields, $delimiter, $enclosure, $escapeChar) === false) {
                return false;
            }
        }
        rewind($fileHandler);
        $csvLine = stream_get_contents($fileHandler);

        return rtrim($csvLine);
    }
}
