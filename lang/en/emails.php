<?php

return [
    'common' => [
        'regards' => 'Best regards',
    ],

    'draw_completed' => [
        'subject' => 'Secret Santa Draw Completed - :title',
        'greeting' => 'Hello :name!',
        'body' => 'The Secret Santa draw ":title" has been successfully completed.',
        'participants_count' => 'All :count participants have been assigned their Secret Santa recipients.',
        'next_steps' => 'What happens next:',
        'step1' => 'Each participant will receive an email with their assigned recipient',
        'step2' => 'Participants can access their secure link to see their assignment',
        'step3' => 'Anonymous messaging is now enabled between Secret Santas and recipients',
        'button' => 'View Draw Details',
        'footer' => 'Thank you for organizing this Secret Santa with us!',
    ],

    'participant_invitation' => [
        'subject' => 'You\'re invited to Secret Santa - :title',
        'greeting' => 'Hello :name!',
        'body' => ':organizer has invited you to participate in the Secret Santa draw ":title".',
        'event_details' => 'Event Details:',
        'date' => 'Event Date: :date',
        'budget' => 'Suggested Budget: :budget',
        'instructions' => 'To join this Secret Santa draw, click the button below and enter your secure access key.',
        'access_key' => 'Your Access Key: :key',
        'button' => 'Join Secret Santa',
        'footer' => 'Keep your access key safe - you\'ll need it to access your Secret Santa assignment.',
    ],

    'participant_draw_ready' => [
        'subject' => 'Your Secret Santa Assignment - :title',
        'greeting' => 'Hello :name!',
        'body' => 'The draw for ":title" has been completed and you have been assigned your Secret Santa recipient!',
        'target_intro' => 'You will be the Secret Santa for:',
        'exclusions_notice' => 'Note: This draw had :count exclusion rule(s) applied.',
        'button' => 'View Assignment Details',
        'footer' => 'Remember to keep your assignment secret and have fun!',
    ],

    'draw_failed' => [
        'subject' => 'Draw Failed - :title',
        'greeting' => 'Hello :name,',
        'body' => 'Unfortunately, the Secret Santa draw for ":title" could not be completed.',
        'error_message' => 'Error details:',
        'suggestion' => 'Please review your exclusion rules and try again. You may need to adjust the exclusions to make a valid draw possible.',
        'button' => 'Manage Draw',
        'footer' => 'If you continue to experience issues, please contact support.',
    ],

    'registration_request' => [
        'subject' => 'New Registration Request - :title',
        'greeting' => 'Hello :name,',
        'body' => ':participant has requested to join your Secret Santa draw ":title".',
        'participant_info' => 'Participant Information:',
        'field' => 'Field',
        'value' => 'Value',
        'name' => 'Name',
        'email' => 'Email',
        'date' => 'Request Date',
        'action_required' => 'Please review and approve or reject this registration request.',
        'button' => 'Review Request',
        'footer' => 'Participants cannot join the draw until you approve their registration.',
    ],

    'registration_accepted' => [
        'subject' => 'Registration Approved - :title',
        'greeting' => 'Hello :name!',
        'body' => 'Good news! Your registration for the Secret Santa draw ":title" has been approved by :organizer.',
        'next_steps' => 'Next steps:',
        'step1' => 'Wait for the draw to be completed',
        'step2' => 'You\'ll receive an email with your Secret Santa assignment',
        'step3' => 'Use your secure link to access gift ideas and send anonymous messages',
        'footer' => 'Get ready for some Secret Santa fun!',
    ],

    'registration_rejected' => [
        'subject' => 'Registration Update - :title',
        'greeting' => 'Hello :name,',
        'body' => 'Your registration request for the Secret Santa draw ":title" has been reviewed.',
        'reason_intro' => 'Additional information:',
        'footer' => 'If you have questions, please contact the organizer.',
    ],

    'message_notification' => [
        'subject' => 'New Message - :title',
        'greeting' => 'Hello :name!',
        'body' => 'You have received a new message in the Secret Santa draw ":title".',
        'question_received' => 'Your Secret Santa has asked you a question!',
        'thank_you_received' => 'You\'ve received a thank you message!',
        'message_received' => 'You have a new message waiting.',
        'button' => 'View Message',
        'footer' => 'Messages are anonymous to keep the Secret Santa surprise!',
    ],

    'organizer_notification' => [
        'subject' => 'Update - :title',
        'greeting' => 'Hello :name,',
        'body' => 'There\'s an update for your Secret Santa draw ":title".',
        'participant_joined' => 'A new participant has joined the draw.',
        'message_sent' => 'A message has been sent between participants.',
        'draw_completed' => 'The draw has been successfully completed!',
        'general_update' => 'There\'s been an activity in your draw.',
        'button' => 'View Details',
        'footer' => 'Stay informed about your Secret Santa event.',
    ],

    'admin_alert' => [
        'subject' => 'System Alert [:level]',
        'greeting' => 'System Administrator Alert',
        'level' => 'Alert Level: :level',
        'context' => 'Additional Context:',
        'key' => 'Key',
        'value' => 'Value',
        'timestamp' => 'Timestamp:',
    ],
];