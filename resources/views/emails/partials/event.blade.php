{{-- "Le jour J": the date, then the optional place and budget, only when given --}}
<x-mail.rows :rows="[
    __('emails.event.heading') => $eventDate,
    __('emails.event.location') => $location,
    __('emails.event.budget') => $budget,
]" />
