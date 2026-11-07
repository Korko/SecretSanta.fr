export default {
    "en": {
        "common": {
            "internal": "An error occurred",
            "success": "Success",
            "copied": "Copied to clipboard",
            "fetcher": {
                "load": "Load",
                "loading": "Loading..."
            },
            "form": {
                "send": "Send",
                "sending": "Sending",
                "sent": "Sent",
                "reset": "Start a new draw"
            },
            "modal": {
                "close": "Close"
            },
            "email": {
                "redo": "Resend",
                "status": {
                    "created": "Waiting to be sent",
                    "sending": "Sending",
                    "sent": "Sent",
                    "error": "Error",
                    "received": "Received"
                },
                "recent": "You cannot resend the same email so quickly"
            }
        },
        "dearsanta": {
            "reminder": "Remember that your Secret Santa cannot reply to you! Make sure to give them as much information as possible.",
            "report": {
                "question": "A problem?",
                "not_me": "This email isn't for me",
                "not_participating": "I'm not taking part",
                "cancel": "Cancel the report",
                "sent": {
                    "not_me": "{organizer} will see on their page that this email wasn't for you.",
                    "not_participating": "{organizer} will see on their page that you are not taking part."
                }
            },
            "targets": {
                "revealed": "The organizer has viewed the list of targets",
                "secret": "The list of targets has not been viewed by the organizer"
            },
            "list": {
                "date": "Sent on",
                "body": "Message body",
                "status": "Email delivery status",
                "empty": "No email sent yet",
                "caption": "List of emails sent to your Santa"
            },
            "content": {
                "label": "Email content",
                "placeholder": "Dear Santa..."
            },
            "resend": {
                "button": "Resend me the emails I received from my target"
            }
        },
        "faq": {
            "nav": {
                "go": "Let's go!",
                "contact": "I still have a question"
            },
            "categories": {
                "general": "General",
                "technical": "Technical"
            },
            "questions": {
                "general": {
                    "Why was SecretSanta developed?": "The developer often held secret santa parties with friends or colleagues, with everyone's name in a hat. Everyone drew a slip of paper and several times someone drew their own name. Sometimes the draw was redone, sometimes the person kept quiet, which was a shame. So he came up with the idea of building a tool. The goal was to make everything automatic so that nobody could draw themselves. Even the organizer could take part like everyone else since they could not know who drew whom. Exclusions and other features came later.",
                    "How can this site work while being free?": "SecretSanta is completely free for users, but not for the developer who pays the various costs. There are no ads and no data is sold.",
                    "We are an odd number of participants, is that a problem?": "Not at all, whether you are an even or odd number. Taking an example with 3 participants: Anna, Betty and Cedric, Anna could give to Betty, Betty to Cedric and Cedric to Anna. Everyone gets a gift and nobody drew themselves.",
                    "I made a mistake in my email address when organizing my secret santa, what can I do?": "To fix this, either organize another draw, or ask one of the participants to send you the web address that lets them write to their secret santa, then go to https://secretsanta.io/fix. If you still cannot make the change, you can email your participant's link to help@secretsanta.fr. After checking, and in their free time, the developer may be able to help you.",
                    "I deleted the email giving access to the organizer panel, what can I do?": "To fix this, either organize another draw, or ask one of the participants to send you the web address that lets them write to their secret santa, then go to https://secretsanta.io/fix.",
                    "I made a mistake in a participant's address": "When you organized your secret santa, you should have received an email with a link to an organizer panel. You can change each participant's email address from there.",
                    "One of the participants did not receive the email, what can I do?": "Even when the address is correct, an email can get lost, end up in spam, or fail to arrive for some other reason. From your organizer panel, there is a button next to each participant's email delivery status to resend that email. That way the participant can finally find out who their target is and how to talk to their Santa.",
                    "Is it possible to talk to your target?": "Not for now, for anonymity reasons. Only the target can write to their secret santa. But it is planned.",
                    "When is my personal data deleted?": "All the data of a draw is deleted 3 weeks after the event date. This delay lets the organizer download, from their organizer panel, the list of participants with their drawn target, to help organize a future secret santa with the same people while avoiding the same targets.",
                    "I forgot a participant, how can I add them?": "From your organizer panel, you can add a participant (or remove one). The new participant gets their target by email. To make room for them, another person gets a new target, picked at random so no one can guess who gives to whom, and they are notified by email. To keep adding simple, the new participant has no exclusions: they can draw anyone and be drawn by anyone.",
                    "Who can know the list of targets?": "By default, nobody: the list is encrypted and appears nowhere. The organizer can however reveal it from their organizer page (email delivery issue, other events to organize with the same pairs...). This is permanent, and each participant sees on their page whether the list has been revealed."
                },
                "technical": {
                    "What data is stored and why?": "For each participant: name and email address; for each draw: the title and content of the email sent, as well as each message sent between participants through the link received by email (called 'dear santa'). They are kept for two reasons: first to allow that last feature, which lets you write to your secret santa. Then to allow resending emails in case of a wrong address.",
                    "How is the data stored?": "Each item is encrypted with AES-256 using a unique key per draw. This key is not stored and is sent to each participant. The administrator can therefore never access the data without action on your part. Every time you perform an action, you automatically use this key, which is only lent to SecretSanta to perform the requested action without ever being saved.",
                    "I would like to delete my data, how can I do it?": "Because of the way the data is stored, the administrator cannot know which data belongs to whom. Only the organizer can delete the data of all participants at once. Otherwise, the data is automatically deleted shortly after the end of the event.",
                    "I would like to check the source code myself, where can I find it?": "With pleasure! The source code is available at https://framagit.org/Korko/SecretSanta. You can also find the link at the top right of the main page, in the small red ribbon."
                }
            }
        },
        "form": {
            "inputEdit": {
                "update": "Edit",
                "updating": "Saving",
                "updated": "Change saved",
                "submit": "Confirm",
                "cancel": "Cancel",
                "error": "Error: {error}",
                "redo": "Try again"
            },
            "nav": {
                "what": "What is it?",
                "how": "How does it work?",
                "go": "Let's go!",
                "faq": "Frequently Asked Questions"
            },
            "title": "Secret Santa",
            "subtitle": "Give each other gifts... secretly!",
            "fyi": "For your information",
            "section": {
                "what": {
                    "title": "What is it?",
                    "subtitle": "Secret Santa explained",
                    "heading1": "The idea",
                    "content1": "Secret Santa is a fun and original way to exchange gifts anonymously between friends, colleagues...\nIt's simple: each participant randomly receives the name of the person they will give a gift to.\nThe gift amount is usually set beforehand ($2, $5, $10...)\nThe goal is not necessarily to give a fancy gift but to be creative!",
                    "notice": "SecretSanta is completely free and ad-free.\nEverything is paid for by the developer himself.\nIf you like this tool, consider making a donation.\n{button}"
                },
                "how": {
                    "title": "How does it work?",
                    "subtitle": "You'll see, it's very simple!",
                    "heading1": "Step one: list the participants",
                    "content1": "With the \"Add a participant\" and \"Remove\" buttons, you can adjust the number of people.\nFor each person, enter a name or nickname, and an email address.\nTwo participants cannot have the same name, otherwise they cannot be told apart.\nNote that SecretSanta is designed so that nobody can draw themselves.",
                    "heading2": "Step two: set the exclusions",
                    "content2": "Add exclusions. If you don't want two participants to be able to draw each other, fill in the \"Exclusions\" field.",
                    "heading3": "Step three: prepare the email",
                    "content3": "All that's left is to fill in the title and body of the email the participants will receive.\nThe \"{TARGET}\" keyword is mandatory in the message body, it will automatically be replaced by the name of each participant's \"target\".\n(Optional) You can also use the \"{SANTA}\" keyword, which will be replaced by the name of the email recipient.\nFor example, you could include the sentence \"Hello {SANTA}, you will give a gift to {TARGET}\".",
                    "heading4": "And then?",
                    "content4": "Until the event date set at the end, participants can write a note to their Santa from a link they receive by email.\nSanta, however, cannot reply, as that would reveal their identity.\nThe organizer also has a dedicated interface to find the summary of participants and exclusions.",
                    "notice": "SecretSanta only stores your data when required.\nIt is encrypted so that it is unusable without action on your part.\nNone of this data will be shared and you have full control over it.\nThe source code is available on {link}"
                },
                "go": {
                    "title": "Your turn!",
                    "subtitle": "Fill in, click and off you go!"
                }
            },
            "waiting": "Form loading. If this message stays displayed, try refreshing the page, otherwise contact me by email (<a href=\"mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;\">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) or via <a href=\"https://github.com/Korko\">GitHub</a>. Thank you.",
            "success": "Sent successfully!",
            "organizerIn": "The organizer takes part",
            "organizerOut": "The organizer does not take part",
            "organizer": {
                "title": "Organizer details",
                "name": "Organizer name or nickname",
                "email": "Organizer email address"
            },
            "participants": {
                "title": "Participant details",
                "import": "Import from a file",
                "importing": "Importing",
                "caption": "List of participants"
            },
            "participant": {
                "organizer": "Organizer",
                "name": {
                    "label": "Name or nickname",
                    "placeholder": "e.g. Paul or Korko"
                },
                "email": {
                    "label": "Email address",
                    "placeholder": "e.g. michael@aol.com",
                    "suggest": "Did you mean {email}?"
                },
                "exclusions": {
                    "label": "Exclusions",
                    "placeholder": "No exclusion",
                    "noOptions": "Empty list",
                    "noResult": "No result"
                },
                "remove": "Remove",
                "add": "Add a participant"
            },
            "csv": {
                "title": "Import a list of participants from a CSV file",
                "help": "How to create a CSV file with {excel} Microsoft Office Excel {elink} or {calc} LibreOffice Calc {elink}",
                "format": "For your CSV file to work, here is the expected format:",
                "column1": "Participant name",
                "column2": "Email address",
                "column3": "Exclusions (comma-separated names)",
                "warning": "Warning, importing this data will remove the participants already entered.",
                "cancel": "Cancel",
                "import": "Import",
                "importError": "An error occurred during the import.",
                "importSuccess": "The import was successful.",
                "analyzing": "Loading..."
            },
            "mail": {
                "title": {
                    "label": "Email title",
                    "placeholder": "e.g. Secret santa party on December 23rd at Martin's, {SANTA} your target is..."
                },
                "content": {
                    "label": "Email content",
                    "placeholder": "e.g. Hi {SANTA}, for the secret santa party, your target is {TARGET}. Reminder: the gift amount is $3!",
                    "tip1": "Use \"{santa}&#123;SANTA&#125;{close}\" for the name of the email recipient and \"{target}&#123;TARGET&#125;{close}\" for the name of their target.",
                    "tip2": "Tip: remember to mention the date, the place and the gift amount."
                },
                "post": "----\nTo write to your Secret Santa, go to the following page: {link}\nvia SecretSanta"
            },
            "data-expiration": "Event date: ",
            "data-expiration-tooltip": {
                "title": "Event date",
                "interface": "A dedicated interface will let you access a summary of the participants until the day of the event.",
                "deletion": "All stored data will be deleted one week later."
            },
            "submit": "Draw!",
            "paypal": {
                "alt": "PayPal, the safer way to pay online"
            },
            "internalError": "Internal error"
        },
        "organizer": {
            "url": "Link received by a participant by email",
            "fix": "Fix",
            "date": {
                "label": "Event date",
                "help": "The event data will be deleted on {deleted_at}."
            },
            "reveal": {
                "button": "Reveal targets",
                "done": "Targets revealed on {date}. Participants can see that the list has been viewed.",
                "confirm": {
                    "title": "Reveal who gives to whom?",
                    "body": "You will see each participant's target, useful in case of delivery issues or to organize other events. This is permanent, and participants will see on their page that the list has been revealed to the organizer.",
                    "ok": "Reveal",
                    "cancel": "Cancel"
                }
            },
            "list": {
                "target": "Target",
                "name": "Name",
                "email": "Email address",
                "status": "Email delivery status",
                "caption": "List of participants",
                "withdraw": "Remove",
                "resend_failed": "Resend failed emails ({count})",
                "received_count": "{received}/{total} participants confirmed receiving their email (reply, page visit or acknowledgement)"
            },
            "up_and_sent": "Successfully updated!",
            "cannot_withdraw": "Unable to remove {name}",
            "withdrawn": "{name} no longer takes part in the event.",
            "deleted": "All data has been deleted",
            "download": {
                "button": "Download the summary",
                "button-tooltip": {
                    "title": "Summary",
                    "content": "This is the data as you entered it when the event was generated. Only email addresses may have changed, to reflect the changes you made here."
                },
                "button_initial": "Download the initial summary",
                "button_initial-tooltip": {
                    "title": "Initial summary",
                    "content": "This is the data as you entered it when the event was generated. Only email addresses may have changed, to reflect the changes you made here."
                },
                "button_final": "Download the completed summary",
                "button_final-tooltip": {
                    "title": "Completed summary",
                    "explain": "The data is the same as in the initial summary, but the target each participant had during this event has been added to their exclusions. Unless this would make it impossible to find a target for every participant next time.",
                    "limit": "Given the event date, this feature is only available from {expires_at} to {deleted_at}, or as soon as you have revealed the targets."
                }
            },
            "purge": {
                "button": "Delete everything",
                "confirm": {
                    "title": "Are you sure you want to delete all data before the automatic cleanup on {deletion}?",
                    "body_final": "You will no longer be able to download the draw summary of this event and participants will no longer be able to write to their secret santa. This action cannot be undone.",
                    "body_expired": "You will no longer be able to download the summary of this event. This action cannot be undone.",
                    "body_nofinal": "You will no longer be able to download the summary of this event and participants will no longer be able to write to their secret santa. This action cannot be undone.",
                    "value": "Delete all data",
                    "help": "Type \"[+:verification]\" below to confirm.",
                    "ok": "Ok",
                    "cancel": "Cancel"
                }
            },
            "report": {
                "refused_email": "This address reported not taking part in this event: enter another one.",
                "not_me": "Wrong recipient",
                "not_participating": "Not taking part",
                "help": {
                    "not_me": "Whoever received this email says it wasn't for them: fix the address (they saw the target, you can also remove and add them again).",
                    "not_participating": "This person says they are not taking part: you can remove them from the event."
                }
            },
            "edit": {
                "warning": "A randomly chosen participant may get a new target. The draw took place {when}: the older it is, the more inconvenient for that person, who may already have bought their gift.",
                "add": "Add",
                "new": "new",
                "undo": "Undo",
                "multi": "Change several participants at once",
                "validate": "Confirm ({add} added, {remove} removed)",
                "cancel": "Discard changes",
                "done": "Changes saved, the participants concerned have been notified.",
                "confirm_add": {
                    "title": "Add {name} to the event?",
                    "body": "{name} will receive their target by email, and another person will change target (they will be notified). {name} will have no exclusions. This action cannot be undone."
                },
                "confirm_multi": {
                    "title": "Confirm the changes?",
                    "body": "{add} added and {remove} removed. Each person whose target changes will receive a single email. This action cannot be undone."
                }
            },
            "withdraw": {
                "button": "Remove",
                "confirm": {
                    "title": "Are you sure you want to remove {name} from the event?",
                    "body": "All messages received from their target will be forwarded to their new secret santa. This action cannot be undone.",
                    "value": "Cancel participation",
                    "help": "Type \"[+:verification]\" below to confirm.",
                    "ok": "Ok",
                    "cancel": "Cancel"
                }
            },
            "expired": "Your event is over ({expires_at}). Some actions are no longer available, such as resending the target name to a participant."
        },
        "validation": {
            "accepted": "The {attribute} field must be accepted.",
            "accepted_if": "The {attribute} field must be accepted when {other} is {value}.",
            "active_url": "The {attribute} field must be a valid URL.",
            "after": "The {attribute} field must be a date after {date}.",
            "after_or_equal": "The {attribute} field must be a date after or equal to {date}.",
            "alpha": "The {attribute} field must only contain letters.",
            "alpha_dash": "The {attribute} field must only contain letters, numbers, dashes, and underscores.",
            "alpha_num": "The {attribute} field must only contain letters and numbers.",
            "any_of": "The {attribute} field is invalid.",
            "array": "The {attribute} field must be an array.",
            "array_keys": "The {attribute} field must only contain the following keys: {values}.",
            "ascii": "The {attribute} field must only contain single-byte alphanumeric characters and symbols.",
            "base64": "The {attribute} field must be a valid Base64 string.",
            "before": "The {attribute} field must be a date before {date}.",
            "before_or_equal": "The {attribute} field must be a date before or equal to {date}.",
            "between": {
                "array": "The {attribute} field must have between {min} and {max} items.",
                "file": "The {attribute} field must be between {min} and {max} kilobytes.",
                "numeric": "The {attribute} field must be between {min} and {max}.",
                "string": "The {attribute} field must be between {min} and {max} characters."
            },
            "boolean": "The {attribute} field must be true or false.",
            "can": "The {attribute} field contains an unauthorized value.",
            "confirmed": "The {attribute} field confirmation does not match.",
            "contains": "The {attribute} field is missing a required value.",
            "current_password": "The password is incorrect.",
            "date": "The {attribute} field must be a valid date.",
            "date_equals": "The {attribute} field must be a date equal to {date}.",
            "date_format": "The {attribute} field must match the format {format}.",
            "decimal": "The {attribute} field must have {decimal} decimal places.",
            "declined": "The {attribute} field must be declined.",
            "declined_if": "The {attribute} field must be declined when {other} is {value}.",
            "different": "The {attribute} field and {other} must be different.",
            "digits": "The {attribute} field must be {digits} digits.",
            "digits_between": "The {attribute} field must be between {min} and {max} digits.",
            "dimensions": "The {attribute} field has invalid image dimensions.",
            "distinct": "The {attribute} field has a duplicate value.",
            "doesnt_contain": "The {attribute} field must not contain any of the following: {values}.",
            "doesnt_end_with": "The {attribute} field must not end with one of the following: {values}.",
            "doesnt_start_with": "The {attribute} field must not start with one of the following: {values}.",
            "email": "The {attribute} field must be a valid email address.",
            "encoding": "The {attribute} field must be encoded in {encoding}.",
            "ends_with": "The {attribute} field must end with one of the following: {values}.",
            "enum": "The selected {attribute} is invalid.",
            "exists": "The selected {attribute} is invalid.",
            "extensions": "The {attribute} field must have one of the following extensions: {values}.",
            "file": "The {attribute} field must be a file.",
            "filled": "The {attribute} field must have a value.",
            "gt": {
                "array": "The {attribute} field must have more than {value} items.",
                "file": "The {attribute} field must be greater than {value} kilobytes.",
                "numeric": "The {attribute} field must be greater than {value}.",
                "string": "The {attribute} field must be greater than {value} characters."
            },
            "gte": {
                "array": "The {attribute} field must have {value} items or more.",
                "file": "The {attribute} field must be greater than or equal to {value} kilobytes.",
                "numeric": "The {attribute} field must be greater than or equal to {value}.",
                "string": "The {attribute} field must be greater than or equal to {value} characters."
            },
            "hex_color": "The {attribute} field must be a valid hexadecimal color.",
            "image": "The {attribute} field must be an image.",
            "in": "The selected {attribute} is invalid.",
            "in_array": "The {attribute} field must exist in {other}.",
            "in_array_keys": "The {attribute} field must contain at least one of the following keys: {values}.",
            "integer": "The {attribute} field must be an integer.",
            "ip": "The {attribute} field must be a valid IP address.",
            "ipv4": "The {attribute} field must be a valid IPv4 address.",
            "ipv6": "The {attribute} field must be a valid IPv6 address.",
            "json": "The {attribute} field must be a valid JSON string.",
            "list": "The {attribute} field must be a list.",
            "lowercase": "The {attribute} field must be lowercase.",
            "lt": {
                "array": "The {attribute} field must have less than {value} items.",
                "file": "The {attribute} field must be less than {value} kilobytes.",
                "numeric": "The {attribute} field must be less than {value}.",
                "string": "The {attribute} field must be less than {value} characters."
            },
            "lte": {
                "array": "The {attribute} field must not have more than {value} items.",
                "file": "The {attribute} field must be less than or equal to {value} kilobytes.",
                "numeric": "The {attribute} field must be less than or equal to {value}.",
                "string": "The {attribute} field must be less than or equal to {value} characters."
            },
            "mac_address": "The {attribute} field must be a valid MAC address.",
            "max": {
                "array": "The {attribute} field must not have more than {max} items.",
                "file": "The {attribute} field must not be greater than {max} kilobytes.",
                "numeric": "The {attribute} field must not be greater than {max}.",
                "string": "The {attribute} field must not be greater than {max} characters."
            },
            "max_digits": "The {attribute} field must not have more than {max} digits.",
            "mimes": "The {attribute} field must be a file of type: {values}.",
            "mimetypes": "The {attribute} field must be a file of type: {values}.",
            "min": {
                "array": "The {attribute} field must have at least {min} items.",
                "file": "The {attribute} field must be at least {min} kilobytes.",
                "numeric": "The {attribute} field must be at least {min}.",
                "string": "The {attribute} field must be at least {min} characters."
            },
            "min_digits": "The {attribute} field must have at least {min} digits.",
            "missing": "The {attribute} field must be missing.",
            "missing_if": "The {attribute} field must be missing when {other} is {value}.",
            "missing_unless": "The {attribute} field must be missing unless {other} is {value}.",
            "missing_with": "The {attribute} field must be missing when {values} is present.",
            "missing_with_all": "The {attribute} field must be missing when {values} are present.",
            "multiple_of": "The {attribute} field must be a multiple of {value}.",
            "not_in": "The selected {attribute} is invalid.",
            "not_regex": "The {attribute} field format is invalid.",
            "numeric": "The {attribute} field must be a number.",
            "password": {
                "letters": "The {attribute} field must contain at least one letter.",
                "mixed": "The {attribute} field must contain at least one uppercase and one lowercase letter.",
                "numbers": "The {attribute} field must contain at least one number.",
                "symbols": "The {attribute} field must contain at least one symbol.",
                "uncompromised": "The given {attribute} has appeared in a data leak. Please choose a different {attribute}."
            },
            "present": "The {attribute} field must be present.",
            "present_if": "The {attribute} field must be present when {other} is {value}.",
            "present_unless": "The {attribute} field must be present unless {other} is {value}.",
            "present_with": "The {attribute} field must be present when {values} is present.",
            "present_with_all": "The {attribute} field must be present when {values} are present.",
            "prohibited": "The {attribute} field is prohibited.",
            "prohibited_if": "The {attribute} field is prohibited when {other} is {value}.",
            "prohibited_if_accepted": "The {attribute} field is prohibited when {other} is accepted.",
            "prohibited_if_declined": "The {attribute} field is prohibited when {other} is declined.",
            "prohibited_unless": "The {attribute} field is prohibited unless {other} is in {values}.",
            "prohibits": "The {attribute} field prohibits {other} from being present.",
            "regex": "The {attribute} field format is invalid.",
            "required": "The {attribute} field is required.",
            "required_array_keys": "The {attribute} field must contain entries for: {values}.",
            "required_if": "The {attribute} field is required when {other} is {value}.",
            "required_if_accepted": "The {attribute} field is required when {other} is accepted.",
            "required_if_declined": "The {attribute} field is required when {other} is declined.",
            "required_unless": "The {attribute} field is required unless {other} is in {values}.",
            "required_with": "The {attribute} field is required when {values} is present.",
            "required_with_all": "The {attribute} field is required when {values} are present.",
            "required_without": "The {attribute} field is required when {values} is not present.",
            "required_without_all": "The {attribute} field is required when none of {values} are present.",
            "same": "The {attribute} field must match {other}.",
            "size": {
                "array": "The {attribute} field must contain {size} items.",
                "file": "The {attribute} field must be {size} kilobytes.",
                "numeric": "The {attribute} field must be {size}.",
                "string": "The {attribute} field must be {size} characters."
            },
            "starts_with": "The {attribute} field must start with one of the following: {values}.",
            "string": "The {attribute} field must be a string.",
            "timezone": "The {attribute} field must be a valid timezone.",
            "unique": "The {attribute} has already been taken.",
            "uploaded": "The {attribute} failed to upload.",
            "uppercase": "The {attribute} field must be uppercase.",
            "url": "The {attribute} field must be a valid URL.",
            "ulid": "The {attribute} field must be a valid ULID.",
            "uuid": "The {attribute} field must be a valid UUID.",
            "custom": {
                "g-recaptcha-response": {
                    "required": "The captcha is required",
                    "recaptcha": "The captcha is invalid"
                },
                "randomform": {
                    "participant-organizer": {
                        "required": "You must specify whether the organizer takes part in the event."
                    },
                    "title": {
                        "required": "The email title is required."
                    },
                    "content": {
                        "required": "The email content is required.",
                        "contains_text": "The email content must contain the word {TARGET} to indicate the target."
                    },
                    "expiration": {
                        "required": "The expiration date is required.",
                        "min": "The expiration date cannot be before tomorrow.",
                        "max": "The expiration date cannot be more than a year away.",
                        "format": "The expiration date must use the year-month-day format, e.g. 2022-02-05."
                    },
                    "participants": {
                        "length": "At least 3 participants are required"
                    },
                    "organizer": {
                        "name": {
                            "required": "The organizer name is required."
                        },
                        "email": {
                            "required": "This email address is required.",
                            "format": "The format of this address is invalid."
                        }
                    },
                    "participant": {
                        "name": {
                            "required": "This participant is required (at least 3 people).",
                            "distinct": "This participant does not have a unique name."
                        },
                        "email": {
                            "required": "This email address is required.",
                            "format": "The format of this address is invalid."
                        }
                    }
                },
                "dearSanta": {
                    "content": {
                        "required": "The message content is required."
                    }
                },
                "organizer": {
                    "email": {
                        "required": "The new address is required.",
                        "format": "The address format is invalid."
                    }
                },
                "fixOrganizer": {
                    "url": {
                        "required": "The URL is required.",
                        "format": "The URL is invalid."
                    },
                    "email": {
                        "required": "The email address is required.",
                        "format": "The address format is invalid."
                    }
                }
            },
            "attributes": {
                "g-recaptcha-response": "Recaptcha"
            }
        }
    }
}
