<?php

declare(strict_types=1);

return [
    'validation' => 'The submitted data is not valid',
    'solution' => 'The exclusions you chose make it impossible to assign a target to every participant.',
    'csrf' => 'The form expired due to inactivity. Please try again.',
    'signature' => 'The requested action could not be performed. Please refresh the page before trying again.',
    'email' => 'An error occurred while sending the email. Please try again later.',
    'internal' => 'An internal error occurred. Please try again later.',
    'resend' => 'You must wait before resending this email.',
    'withdraw' => 'You cannot have fewer than 3 participants.',
    'expired' => 'This event is not over yet',
    'solvable' => 'This event does not allow this generation',
    'fixOrganizer' => [
        'drawNotFoundOrExpired' => 'The draw linked to this address does not exist or has ended.',
        'distanceEmailTooBig' => 'The email address entered is too different from the original one, please contact the site developer.',
        'resendDelayTooShort' => 'You recently changed this email address already. Please wait a little.',
    ],
];
