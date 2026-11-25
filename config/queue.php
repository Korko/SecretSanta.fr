<?php

declare(strict_types=1);

return [

    // Failed jobs are not stored: their payload holds the draw secret in clear (data.secret, see AppServiceProvider::bootJobs),
    // only lent for the time of the sending. The worker still logs the exception; the organizer resends from the panel
    'failed' => [
        'driver' => 'null',
    ],

];
