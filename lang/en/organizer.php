<?php

declare(strict_types=1);

return [
    'url' => 'Link received by a participant by email',
    'fix' => 'Fix',
    'date' => [
        'label' => 'Event date',
        'help' => 'The event data will be deleted on {deleted_at}.',
    ],
    'reveal' => [
        'button' => 'Reveal targets',
        'done' => 'Targets revealed on :date. Participants can see that the list has been viewed.',
        'confirm' => [
            'title' => 'Reveal who gives to whom?',
            'body' => 'You will see each participant\'s target, useful in case of delivery issues or to organize other events. This is permanent, and participants will see on their page that the list has been revealed to the organizer.',
            'ok' => 'Reveal',
            'cancel' => 'Cancel',
        ],
    ],
    'list' => [
        'target' => 'Target',
        'name' => 'Name',
        'email' => 'Email address',
        'status' => 'Email delivery status',
        'caption' => 'List of participants',
        'withdraw' => 'Remove',
        'resend_failed' => 'Resend failed emails (:count)',
        'received_count' => ':received/:total participants confirmed receiving their email (reply, page visit or acknowledgement)',
    ],
    'up_and_sent' => 'Successfully updated!',
    'cannot_withdraw' => 'Unable to remove :name',
    'withdrawn' => ':name no longer takes part in the event.',
    'deleted' => 'All data has been deleted',
    'download' => [
        'button' => 'Download the summary',
        'button-tooltip' => [
            'title' => 'Summary',
            'content' => 'This is the data as you entered it when the event was generated. Only email addresses may have changed, to reflect the changes you made here.',
        ],
        'button_initial' => 'Download the initial summary',
        'button_initial-tooltip' => [
            'title' => 'Initial summary',
            'content' => 'This is the data as you entered it when the event was generated. Only email addresses may have changed, to reflect the changes you made here.',
        ],
        'button_final' => 'Download the completed summary',
        'button_final-tooltip' => [
            'title' => 'Completed summary',
            'explain' => 'The data is the same as in the initial summary, but the target each participant had during this event has been added to their exclusions. Unless this would make it impossible to find a target for every participant next time.',
            'limit' => 'Given the event date, this feature is only available from {expires_at} to {deleted_at}, or as soon as you have revealed the targets.',
        ],
    ],
    'purge' => [
        'button' => 'Delete everything',
        'confirm' => [
            'title' => 'Are you sure you want to delete all data before the automatic cleanup on :deletion?',
            // Final recap available and draw not expired yet
            'body_final' => 'You will no longer be able to download the draw summary of this event and participants will no longer be able to write to their secret santa. This action cannot be undone.',
            // Draw expired, final recap may be available or not
            'body_expired' => 'You will no longer be able to download the summary of this event. This action cannot be undone.',
            // No final recap available and draw not expired yet
            'body_nofinal' => 'You will no longer be able to download the summary of this event and participants will no longer be able to write to their secret santa. This action cannot be undone.',
            'value' => 'Delete all data',
            // Use !: not to be transformed by vue-i18n-generator
            'help' => 'Type "[+!:verification]" below to confirm.',
            'ok' => 'Ok',
            'cancel' => 'Cancel',
        ],
    ],
    'report' => [
        'refused_email' => 'This address reported not taking part in this event: enter another one.',
        'not_me' => 'Wrong recipient',
        'not_participating' => 'Not taking part',
        'help' => [
            'not_me' => 'Whoever received this email says it wasn\'t for them: fix the address (they saw the target, you can also remove and add them again).',
            'not_participating' => 'This person says they are not taking part: you can remove them from the event.',
        ],
    ],
    'edit' => [
        'warning' => 'A randomly chosen participant may get a new target. The draw took place {days} day(s) ago: the older it is, the more inconvenient for that person, who may already have bought their gift.',
        'add' => 'Add',
        'new' => 'new',
        'undo' => 'Undo',
        'multi' => 'Change several participants at once',
        'validate' => 'Confirm ({add} added, {remove} removed)',
        'cancel' => 'Discard changes',
        'done' => 'Changes saved, the participants concerned have been notified.',
        'confirm_add' => [
            'title' => 'Add {name} to the event?',
            'body' => '{name} will receive their target by email, and a single person will change target (they will be notified). This action cannot be undone.',
        ],
        'confirm_multi' => [
            'title' => 'Confirm the changes?',
            'body' => '{add} added and {remove} removed. Each person whose target changes will receive a single email. This action cannot be undone.',
        ],
    ],
    'withdraw' => [
        'button' => 'Remove',
        'confirm' => [
            'title' => 'Are you sure you want to remove {name} from the event?',
            'body' => 'All messages received from their target will be forwarded to their new secret santa. This action cannot be undone.',
            'value' => 'Cancel participation',
            // Use !: not to be transformed by vue-i18n-generator
            'help' => 'Type "[+!:verification]" below to confirm.',
            'ok' => 'Ok',
            'cancel' => 'Cancel',
        ],
    ],
    'expired' => 'Your event is over ({expires_at}). Some actions are no longer available, such as resending the target name to a participant.',
];
