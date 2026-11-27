export default {
    "en": {
        "common": {
            "internal": "An error occurred",
            "success": "Success",
            "copy_failed": "Could not copy: copy {text} by hand",
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
                    "delayed": "Delayed, retrying",
                    "received": "Received"
                },
                "recent": "You cannot resend the same email so quickly",
                "bounce": {
                    "address": "This address doesn't exist: fix it",
                    "full": "Mailbox full: send it again later",
                    "refused": "Refused by the recipient's server, the address isn't the problem"
                }
            },
            "csv": {
                "generated": "File generated on {date} on {app} ({url})",
                "usage": "This file can be used to prefill the participants and their exclusions"
            },
            "confirm": {
                "type": "To confirm, type “{word}”",
                "cancel": "Cancel"
            }
        },
        "dearsanta": {
            "report": {
                "question": "A problem?",
                "identity": "Are you {name}?",
                "not_me": "No, this email isn't for me",
                "its_me": "Yes, it's me",
                "not_participating": "I never asked to take part",
                "cancel": "Cancel the report",
                "back": "Back",
                "sent": {
                    "not_me": "{organizer} was told by email that this email wasn't for you, to fix the address.",
                    "not_participating": "{organizer} was told by email that you are not taking part."
                }
            },
            "targets": {
                "revealed": "The organizer has viewed the list of targets",
                "secret": "The list of targets has not been viewed by the organizer",
                "ended": "The event is over: the list of targets is now visible to the organizer"
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
            },
            "email": {
                "current": "Your emails go to {email}.",
                "open": "Change address",
                "new": "New address",
                "send_code": "Get a code",
                "code": "Code sent to {email}:",
                "confirm": "Confirm",
                "code_sent": "A code has just been sent to {email}. Type it here to confirm the change.",
                "changed": "Your emails will now go to {email}. Your target's name has just been sent there again.",
                "wrong_code": "This code is not the right one.",
                "expired": "This code is no longer valid, ask for a new one.",
                "same": "This is already your address."
            },
            "withdraw": {
                "button": "I no longer want to take part",
                "done": "You no longer take part in this event. Your secret santa got a new target and the organizer has been told.",
                "confirm": {
                    "title": "Withdraw from the event?",
                    "body": "You will leave the draw: your secret santa will get a new target, {organizer} will be told by email and this page will stop working. Only {organizer} will be able to bring you back. If this email was not meant for you or you never asked to take part, use the report instead.",
                    "help": "Type your name, \"{name}\", below to confirm.",
                    "ok": "Withdraw",
                    "cancel": "Cancel"
                },
                "blocked": "Your withdrawal would give someone a person they excluded. Ask {organizer} to remove you from their organizer page."
            },
            "suggestion": {
                "open": "Suggest a gift idea for someone else",
                "help": "Your idea will be emailed to the secret santa of the person you choose, without you knowing who they are. It stays anonymous: sign it in the text if you wish.",
                "recipient": "For whom?",
                "choose": "Choose a participant",
                "content": "Your idea",
                "placeholder": "She loves board games...",
                "send": "Send the idea",
                "list": "Ideas sent",
                "received": {
                    "title": "Gift ideas received for your target"
                }
            }
        },
        "faq": {
            "categories": {
                "general": "General",
                "technical": "Technical"
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
                    "noResult": "No result",
                    "both": "Both ways: click to apply one way only",
                    "oneWay": "One way only: click to apply both ways",
                    "derived": "Added from {name}'s row",
                    "remove": "Remove the exclusion {name}",
                    "help": "An exclusion applies both ways: the arrow on each tag limits it to one way (imported ones keep the file's direction)."
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
                    "tip1": "Use \"{santa}\" for the name of the email recipient and \"{target}\" for the name of their target.",
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
            "internalError": "Internal error",
            "k": {
                "stepsNav": "Go to a step of the form",
                "stepsShort": {
                    "one": "Participants",
                    "two": "Message",
                    "three": "Big day"
                },
                "steps": {
                    "one": "The participants",
                    "two": "The message",
                    "three": "The big day"
                },
                "you": "You, the organizer",
                "person": "Person {n}",
                "participate": {
                    "label": "I take part in the draw too",
                    "help": "You are then drawn like everyone else. Uncheck if you organize without taking part: you will only get the summary."
                },
                "remove": "Remove {name}",
                "removeN": "Remove participant {n}",
                "nameOf": "Name or nickname of participant {n}",
                "emailOf": "Email address of participant {n}",
                "exclusionsOf": "Exclusions of participant {n}",
                "eventLabel": "Name of your event",
                "eventPlaceholder": "e.g. Family Christmas at Martin's",
                "messageLabel": "Message to the participants",
                "messagePlaceholder": "Hi {SANTA}! For Christmas at Martin's, your target is {TARGET}. Can't wait to see your gifts!",
                "insert": "Insert:",
                "insertSanta": "the name of the person receiving the email",
                "insertTarget": "the name of their target",
                "inserted": "{word} inserted in the message",
                "date": "Date of the event",
                "location": "Location",
                "budget": "Budget per gift, in €",
                "optional": "(optional)",
                "locationPlaceholder": "e.g. at Martin's",
                "dayHelp": "The date can be changed later. Until then, a dedicated page gives you the summary of the participants. All stored data is deleted one month later.",
                "submit": "Start the draw!",
                "sending": "Drawing…",
                "newDraw": "Organize another draw",
                "seal": "Kept secret by SecretSanta.fr: your data is encrypted and never shared.",
                "success": "Here we go! Every participant gets their email within the next few minutes."
            }
        },
        "organizer": {
            "url": "Link received by a participant by email",
            "fix": "Fix",
            "sections": {
                "participation": "Your participation",
                "organization": "Organizing the event"
            },
            "eventDetails": {
                "location": "Location (optional)",
                "locationPlaceholder": "E.g. at Grandma's",
                "budget": "Budget per gift, in € (optional)",
                "saved": "Saved"
            },
            "date": {
                "label": "Event date",
                "help": "The event data will be deleted on {deleted_at}."
            },
            "reveal": {
                "button": "Reveal targets",
                "done": "Targets revealed on {date}. Participants can see that the list has been viewed.",
                "expired": "The event is over: every participant's target is now visible.",
                "confirm": {
                    "title": "Reveal who gives to whom?",
                    "body": "You will see each participant's target, useful in case of delivery issues or to organize other events. This is permanent, and participants will see on their page that the list has been revealed to the organizer.",
                    "ok": "Reveal",
                    "cancel": "Cancel",
                    "value": "Reveal targets",
                    "help": "Type \"[+:verification]\" below to confirm."
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
                "received_count": "{received}/{total} participants confirmed receiving their email (reply, page visit or acknowledgement)",
                "wrote_count": "{wrote}/{total} participants wrote to their Secret Santa",
                "wrote": "Wrote to their Santa",
                "wrote_help": "This person sent at least one message to their Secret Santa from their page"
            },
            "up_and_sent": "Successfully updated!",
            "renamed": "Name changed, their Santa was told",
            "rename": {
                "button": "Change the name",
                "title": "Rename {name}",
                "body": "Their Santa will be told the new name by email. Targets do not change.",
                "ok": "Rename",
                "cancel": "Cancel",
                "done": "Renamed to {name}"
            },
            "cannot_withdraw": "Unable to remove {name} without giving someone a person they excluded",
            "cannot_restore": "Unable to bring {name} back without giving someone a person they excluded",
            "withdrawn": {
                "title": "Withdrawals",
                "help": "These people withdrew from their page. If someone else had got their email, bring them back with the right address: they will get their target.",
                "date": "On {date}",
                "button": "Bring back",
                "prompt_title": "Bring {name} back?",
                "prompt_body": "{name} will be back in the draw with their exclusions and get their target at this address (fix it if someone else had got their email). Another person will change target.",
                "ok": "Bring back",
                "cancel": "Cancel",
                "done": "{name} takes part again"
            },
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
                "computing": "Computing: the completed summary will be offered here if possible.",
                "button_final": "Download the completed summary",
                "button_final-tooltip": {
                    "title": "Completed summary",
                    "explain": "The data is the same as in the initial summary, but the target each participant had during this event has been added to their exclusions. Unless this would make it impossible to find a target for every participant next time.",
                    "limit": "Given the event date, this feature is only available after {expires_at}, until {deleted_at}, or as soon as you have revealed the targets."
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
            "expired": "Your event is over ({expires_at}). Some actions are no longer available, such as resending the target name to a participant.",
            "force": {
                "title": "Force the removal?",
                "body": "You can force it: the exclusions will be ignored for this change, and a secret santa may get a person they had excluded."
            }
        },
        "pages": {
            "on": "on {date}",
            "send": "Send",
            "aside": "Alongside",
            "status": {
                "waiting": "Waiting",
                "sent": "Sent",
                "received": "Received",
                "error": "Failed",
                "error_unknown": "Address to check",
                "not_opened": "not opened yet"
            },
            "gift": {
                "for": "For:",
                "from": "From:",
                "anonymous": "From: anonymous"
            },
            "event": {
                "label": "The big day",
                "budget": "{budget} per gift",
                "today": "It's today!",
                "left": "{n} day left | {n} days left",
                "left_long": "{n} day left before the big day | {n} days left before the big day",
                "count_before": "Only",
                "count_after": "day left | days left"
            },
            "participant": {
                "eyebrow": "Hello {name}, here is your Secret Santa page",
                "you": "You",
                "target": {
                    "title": "You are giving a gift to…",
                    "show": "Show my target",
                    "hide": "Hide",
                    "hidden_label": "For: hidden target",
                    "text": "You are giving a gift to {name}. They do not know it is you.",
                    "text_hidden": "Your target is hidden on this device.",
                    "thread": "What {name} wrote to you",
                    "thread_hidden": "What your target wrote to you",
                    "someone": "Your target",
                    "empty": "{name} has not written to you yet. Their messages arrive here and by email.",
                    "empty_hidden": "Your target has not written to you yet. Their messages arrive here and by email.",
                    "resend": "Get this conversation by email"
                },
                "ideas": {
                    "title": "Ideas for {name}",
                    "text": "The other participants give you ideas, without knowing you are the one giving.",
                    "empty": "No idea from the other participants yet."
                },
                "santa": {
                    "title": "Your Secret Santa",
                    "tag_label": "From: your Secret Santa, anonymous",
                    "surprise": "Someone drew you. Surprise on {date}!",
                    "text": "Give them hints: a wish, a size, an allergy. Your messages reach them by email.",
                    "empty": "You have not written to them yet.",
                    "write": "Write to your Secret Santa",
                    "placeholder": "A wish, a size, an allergy…",
                    "hint": "Your Secret Santa sees your name: they already know who to give to."
                },
                "suggest": {
                    "title": "An idea for someone else?",
                    "text": "It goes to the Secret Santa of the chosen person, without you knowing who that is.",
                    "for": "For {name}"
                },
                "secret": {
                    "secret": "The secret is well kept",
                    "secret_text": "{organizer}, the organizer, has not looked at the list of targets. If they had, you would see it here.",
                    "revealed": "The list was looked at",
                    "revealed_text": "{organizer}, the organizer, looked at the list of targets.",
                    "ended": "The event is over",
                    "ended_text": "The list of targets is now visible to {organizer}, the organizer."
                },
                "address": {
                    "title": "Your address",
                    "current": "Your emails arrive at {email}."
                },
                "problem": {
                    "not_me": "This email was not meant for me"
                }
            },
            "withdraw": {
                "title": "Withdraw?",
                "body": "You will leave the draw: your Secret Santa will get a new target, {organizer} will be told by email and this page will stop working.",
                "warning": "Only {organizer}, the organizer, can bring you back. If this email was not meant for you, choose “This email was not meant for me” instead.",
                "ok": "Withdraw"
            },
            "organizer": {
                "eyebrow": "Your organizer dashboard",
                "received": "received their email | received their email",
                "received_label": "Emails received",
                "received_help": "Received = a reply, a visit to their page or a delivery receipt.",
                "tabs": "Your two roles",
                "tab_organization": "Organization",
                "tab_participation": "My participation (my target, my messages)",
                "todo": "To do",
                "todo_error": "The email to {name} could not be delivered to {email}: {reason}",
                "todo_not_me": "{name}: the person who got the email says it was not meant for them. Check the address.",
                "todo_not_participating": "{name} says they are not taking part. You can remove them from the draw.",
                "todo_withdrawn": "{name} withdrew {date}. Their Secret Santa already got a new target.",
                "participants": "The participants",
                "targets_secret": "The targets stay secret as long as you do not reveal them.",
                "targets_shown": "The targets are visible: the list was revealed, or the event is over.",
                "number": "Number",
                "email": "Email address",
                "reception": "Delivery",
                "actions": "Actions",
                "actions_for": "Actions for {name}",
                "you": "(you)",
                "secret": "Secret",
                "change_help": "Adding, removing or bringing someone back changes the target of a randomly drawn person, told by email. The older the draw, the more awkward: their gift may already be bought.",
                "multi_help": "Several changes at once: add and remove, then validate. Each person whose target changes gets a single email.",
                "aside": "Event settings"
            },
            "menu": {
                "rename": "Rename",
                "resend": "Send the email again",
                "resent": "Email sent again",
                "remove": "Remove from the draw"
            },
            "fix": {
                "button": "Fix the address",
                "title": "Fix the address of {name}",
                "body": "The draw email goes to the new address within the next few minutes.",
                "ok": "Fix and send again"
            },
            "add": {
                "title": "Add a participant",
                "body": "This person will get their target by email, with no exclusion, and someone else will get a new target (they will be told).",
                "body_multi": "Added to the list of changes, sent when you validate."
            },
            "remove": {
                "title": "Remove {name} from the draw?",
                "body": "Their Secret Santa will get a new target, drawn at random, and will be told by email. The target of {name} will get a new Secret Santa, who will receive the messages already written.",
                "warning": "A randomly drawn person changes target. The draw dates from {date}: their gift may already be bought.",
                "ok": "Remove {name}"
            },
            "force": {
                "ok": "Force it"
            },
            "reveal": {
                "title": "Reveal who gives to whom?",
                "body": "You will see the target of each participant: useful for a delivery problem, or to organize other events with the same people.",
                "warning": "This is final. Everyone will see on their page that the list was looked at.",
                "word": "reveal",
                "ok": "Reveal the targets"
            },
            "purge": {
                "title": "Delete all the data?",
                "body": "The event, the participants and all the messages will be erased right now, before the automatic cleanup on {deletion}.",
                "warning": "Irreversible. You will no longer be able to download the summaries and nobody will be able to write to their Secret Santa.",
                "warning_expired": "Irreversible. You will no longer be able to download the summaries.",
                "word": "delete",
                "ok": "Delete everything",
                "card": "Delete everything",
                "card_text": "Erases the event and all its data before the automatic cleanup on {date}. The participants will no longer be able to write to their Secret Santa.",
                "card_text_expired": "Erases the event and all its data before the automatic cleanup on {date}.",
                "button": "Delete all the data…"
            },
            "day": {
                "title": "The big day",
                "location": "Location",
                "budget": "Budget per gift, in €",
                "save": "Save",
                "help": "The participants see this information on their page. All the data will be deleted on {date}.",
                "deletion": "All the data will be deleted on {date}."
            },
            "recaps": {
                "title": "Summaries",
                "init": "List of participants (CSV)",
                "init_help": "As filled in at the start, with the addresses fixed since.",
                "final": "Summary for next year",
                "final_help": "Everyone's target is added to their exclusions, so as not to get the same pairs again. Available after {date}, or as soon as you reveal the targets."
            },
            "secret": {
                "title": "The secret",
                "text": "Nobody, not even you, knows who gives to whom. If needed, you can reveal the list: it is final, and everyone will see it on their page.",
                "button": "Reveal the targets…"
            }
        },
        "validation": {
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
                    "location": {
                        "max": "The location cannot exceed 255 characters."
                    },
                    "budget": {
                        "format": "The budget must be an amount in euros, e.g. 20.",
                        "max": "The budget cannot exceed €100,000."
                    },
                    "expiration": {
                        "required": "The expiration date is required.",
                        "min": "The expiration date cannot be before tomorrow.",
                        "max": "The expiration date cannot be more than a year away.",
                        "format": "The expiration date must use the year-month-day format, e.g. 2022-02-05."
                    },
                    "participants": {
                        "length": "At least 3 participants are required",
                        "max": "The number of participants is limited to {max}. For a bigger draw, contact the administrator: {email}"
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
                    },
                    "email": {
                        "required": "The new address is required.",
                        "format": "The address format is not valid."
                    },
                    "code": {
                        "required": "The code is required."
                    },
                    "recipient": {
                        "required": "Choose who the idea is for."
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
            }
        }
    }
}
