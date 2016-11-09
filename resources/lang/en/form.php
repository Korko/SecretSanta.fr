<?php

return [
    'nav.what' => "What is it?",
    'nav.how'  => "How?",
    'nav.go'   => "Let's go!",

    'subtitle'   => "Treat yourself gifts... secretly!",

    'section.what.title' => "What is it?",
    'section.what.subtitle' => "Description of Secret Santa",
    'section.what.heading1' => "The principle",
    'section.what.content1' => "Secret Santa is a funny and original way to anonymously give gifts between friends, colleagues ...
The process is simple: each participant receives, randomly, the name of the person to whom he will have a gift.
The amount of the gift is usually set in advance (£2, £5, £10 ...)
The goal is not necessarily to make a gift but to be creative!",

    'section.how.title' => "How?",
    'section.how.subtitle' => "You'll see, it's really simple !",
    'section.how.heading1' => "First step: specifying how many participants and their names",
    'section.how.content1' => "With buttons \"Add Participant\" and \"Removing a participant\" it is possible to adjust the number of people.
For each person, specify a name / first name or a pseudonym. Two participants may have the same name, otherwise it is impossible to differentiate them.
Note that secretsanta.io is designed so that a person can not draw itself.",
    'section.how.heading2' => "Second step: filling their contact and the exclusions",
    'section.how.content2' => "You can choose whether participants will receive the name of their target email, text message, or both.
To do this, specify for each e-mail address and / or mobile phone number.
(Optional) Add exclusions. If you do not want spouses are able to draw each other, fill the \"partner\".",
    'section.how.heading3' => "Third step: preparing the mail or the sms",
    'section.how.content3' => "It'll just fill in the title and body of the email or SMS that participants receive.
The key word \"{TARGET}\" is mandatory in the message body to give each person his \"target\".
(Optional) You can also use the key word \"{SANTA}\" which will be replaced with the name of the message recipient.",
    'section.how.notice' => "For your information : secretsanta.io doesn't save any of your data nor does it use them in other ways. The source code is available on :link",

    'section.go.title' => "Your turn!",
    'section.go.subtitle' => "Fill, click and let's go!",

    'success' => "Successfully sent!",

    'participants' => "Participants details",

    'participant.name' => "Name or alias",
    'participant.email' => "Email adress",
    'participant.phone' => "Phone",
    'participant.partner' => "Partner",

    'name.placeholder' => "e.g. Paul or Korko",
    'email.placeholder' => "e.g. michel@aol.com",
    'phone.placeholder' => "e.g. 01632 960283",

    'partner.none' => "None",
    'partner.remove' => "Remove",
    'partner.add' => "Add a participant",

    'mail.title' => "Email title",
    'mail.title.placeholder' => "e.g. Secret santa evening of December 23rd at Martin's",

    'mail.content' => "Email content",
    'mail.content.placeholder' => "e.g. Hi {SANTA}, for the secret santa party, your target is {TARGET}. As a reminder, the amount of the gift is £3!",
    'mail.content.tip1' => "Use \"{SANTA}\" for the receiver of the email and \"{TARGET}\" for the name of the target.",
    'mail.content.tip2' => "Tip: Remember to remind the date, location and the amount of the gift.",

    'sms.content' => "Sms content <span class=\"tip\">(max 130 chars)</span>",
    'sms.content.placeholder' => "e.g. Hi {SANTA}, for the secret santa party, your target is {TARGET}. As a reminder, the amount of the gift is £3!",
    'sms.content.tip1' => "Use \"{SANTA}\" for the receiver of the sms and \"{TARGET}\" for the name of the target.",
    'sms.content.tip2' => "Tip: Remember to remind the date, location and the amount of the gift.",

    'submit' => "Start randomizing!",
];
