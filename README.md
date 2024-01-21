## SecretSanta.fr / SecretSanta.io

[![Latest Version](https://badge.fury.io/gh/korko%2Fsecretsanta.svg)](https://framagit.org/Korko/SecretSanta/tags)

SecretSanta is a PHP tool to help people organize secret santa draws without all the problems of drawing from a hat. It requires no registration and always tries to keep the least possible data possible. When it's needed, SecretSanta encrypt those data not to be usuable without a user action.

Once participants data are given to SecretSanta, a random target is assigned to each santa according to exclusions. An email is then sent to each one to inform about the said target. In case of dead end in the resolution of each target, a new try is done twice. It then may happen in very special situations where a solution is possible but the application doesn't find it.

SecretSanta allows the organizer to consult a special panel to check for good reception of the emails for each participant. In case of an error, they can modify the wrong email address to send back the target to the participant whom they have to offer a present.

SecretSanta also allows each participant to send an email to their santa, not knowing who they are. The santa cannot answer back as they may be recognizable via the writing style or an unwanted signature.

SecretSanta let the possibility to the organizer to define an expiration date at which point all data about a draw will be removed from the database. A link in the organizer panel can aldo be used to immediatly remove them. If wanted, the organizer can totally disable the recording of personal data but this implies a total absence of possibility to fix a draw whithout making a new one (e.g. in case of a spelling mistake in an email).

When SecretSanta stores personal data for each participant (name, email) and draw data (email title, email content), it encrypts them with an AES 256 key which is never stored but sent to each participant (to write to their santa) and to the organizer (for the panel). That key is given to the user browser via the url hash, preventing it to be logged in the web server logs. Each participant also have a different IV for the common key for harder decipher in case of a leak of the database.

Via a cron task, SecretSanta checks the application mailbox (used as email sender) for Mailer-Daemon emails indicating an error in the address. As those are encrypted in the database, in order to link this error to an actual participant, the id is appended to the return path address and a catch-all filter is enabled on all addresses like sender+\*@domain where sender@domain is the actual application mailbox. That kind of catch-all is enabled by default on some email providers like Google GMail.

SecretSanta really wants to keep the link santa-target a secret so event the organizer will not be able to know who is the santa of who.

Some stats are stored for analytics purposes but are totally anonymized, resulting only on integer increments. Those stats are the following:

* Amount of draws done
* Amount of participants
* Amount of email sent to santa by targets (called "dear santa")
* Amount of draws with no records in database wanted
* Amount of email errors linked to a participant
* Amount of email errors not linked to a participant