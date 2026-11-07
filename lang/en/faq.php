<?php

declare(strict_types=1);

return [
    'nav' => [
        'go' => 'Let\'s go!',
        'contact' => 'I still have a question',
    ],

    'categories' => [
        'general' => 'General',
        'technical' => 'Technical',
    ],

    'questions' => [
        'general' => [
            'Why was SecretSanta developed?' => "The developer often held secret santa parties with friends or colleagues, with everyone's name in a hat. Everyone drew a slip of paper and several times someone drew their own name. Sometimes the draw was redone, sometimes the person kept quiet, which was a shame. So he came up with the idea of building a tool. The goal was to make everything automatic so that nobody could draw themselves. Even the organizer could take part like everyone else since they could not know who drew whom. Exclusions and other features came later.",
            'How can this site work while being free?' => 'SecretSanta is completely free for users, but not for the developer who pays the various costs. There are no ads and no data is sold.',
            'We are an odd number of participants, is that a problem?' => 'Not at all, whether you are an even or odd number. Taking an example with 3 participants: Anna, Betty and Cedric, Anna could give to Betty, Betty to Cedric and Cedric to Anna. Everyone gets a gift and nobody drew themselves.',
            'I made a mistake in my email address when organizing my secret santa, what can I do?' => "To fix this, either organize another draw, or ask one of the participants to send you the web address that lets them write to their secret santa, then go to https://secretsanta.io/fix. If you still cannot make the change, you can email your participant's link to help@secretsanta.fr. After checking, and in their free time, the developer may be able to help you.",
            'I deleted the email giving access to the organizer panel, what can I do?' => 'To fix this, either organize another draw, or ask one of the participants to send you the web address that lets them write to their secret santa, then go to https://secretsanta.io/fix.',
            'I made a mistake in a participant\'s address' => "When you organized your secret santa, you should have received an email with a link to an organizer panel. You can change each participant's email address from there.",
            'One of the participants did not receive the email, what can I do?' => "Even when the address is correct, an email can get lost, end up in spam, or fail to arrive for some other reason. From your organizer panel, there is a button next to each participant's email delivery status to resend that email. That way the participant can finally find out who their target is and how to talk to their Santa.",
            'Is it possible to talk to your target?' => 'Not for now, for anonymity reasons. Only the target can write to their secret santa. But it is planned.',
            'When is my personal data deleted?' => 'All the data of a draw is deleted 3 weeks after the event date. This delay lets the organizer download, from their organizer panel, the list of participants with their drawn target, to help organize a future secret santa with the same people while avoiding the same targets.',
            'I forgot a participant, how can I add them?' => 'From your organizer panel, you can add a participant (or remove one). The new participant gets their target by email. To make room for them, another person gets a new target, picked at random so no one can guess who gives to whom, and they are notified by email. To keep adding simple, the new participant has no exclusions: they can draw anyone and be drawn by anyone.',
            'Who can know the list of targets?' => 'By default, nobody: the list is encrypted and appears nowhere. The organizer can however reveal it from their organizer page (email delivery issue, other events to organize with the same pairs...). This is permanent, and each participant sees on their page whether the list has been revealed.',
        ],

        'technical' => [
            'What data is stored and why?' => "For each participant: name and email address; for each draw: the title and content of the email sent, as well as each message sent between participants through the link received by email (called 'dear santa'). They are kept for two reasons: first to allow that last feature, which lets you write to your secret santa. Then to allow resending emails in case of a wrong address.",
            'How is the data stored?' => 'Each item is encrypted with AES-256 using a unique key per draw. This key is not stored and is sent to each participant. The administrator can therefore never access the data without action on your part. Every time you perform an action, you automatically use this key, which is only lent to SecretSanta to perform the requested action without ever being saved.',
            'I would like to delete my data, how can I do it?' => 'Because of the way the data is stored, the administrator cannot know which data belongs to whom. Only the organizer can delete the data of all participants at once. Otherwise, the data is automatically deleted shortly after the end of the event.',
            'I would like to check the source code myself, where can I find it?' => 'With pleasure! The source code is available at https://framagit.org/Korko/SecretSanta. You will also find the link at the bottom of every page.',
        ],
    ],
];
