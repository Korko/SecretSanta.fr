export default {
    "fr": {
        "validation": {
            "accepted": "Le champ {attribute} doit être accepté.",
            "active_url": "Le champ {attribute} n'est pas une URL valide.",
            "after": "Le champ {attribute} doit être une date postérieure au {date}.",
            "alpha": "Le champ {attribute} doit seulement contenir des lettres.",
            "alpha_dash": "Le champ {attribute} doit seulement contenir des lettres, des chiffres et des tirets.",
            "alpha_num": "Le champ {attribute} doit seulement contenir des chiffres et des lettres.",
            "array": "Le champ {attribute} doit être un tableau.",
            "before": "Le champ {attribute} doit être une date antérieure au {date}.",
            "between": {
                "numeric": "La valeur de {attribute} doit être comprise entre {min} et {max}.",
                "file": "Le fichier {attribute} doit avoir une taille entre {min} et {max} kilo-octets.",
                "string": "Le texte {attribute} doit avoir entre {min} et {max} caractères.",
                "array": "Le tableau {attribute} doit avoir entre {min} et {max} éléments."
            },
            "boolean": "Le champ {attribute} doit être vrai ou faux.",
            "confirmed": "Le champ de confirmation {attribute} ne correspond pas.",
            "date": "Le champ {attribute} n'est pas une date valide.",
            "date_format": "Le champ {attribute} ne correspond pas au format {format}.",
            "different": "Les champs {attribute} et {other} doivent être différents.",
            "digits": "Le champ {attribute} doit avoir {digits} chiffres.",
            "digits_between": "Le champ {attribute} doit avoir entre {min} et {max} chiffres.",
            "email": "Le champ {attribute} doit être une adresse email valide.",
            "exists": "Le champ {attribute} sélectionné est invalide.",
            "filled": "Le champ {attribute} est obligatoire.",
            "image": "Le champ {attribute} doit être une image.",
            "in": "Le champ {attribute} est invalide.",
            "integer": "Le champ {attribute} doit être un entier.",
            "ip": "Le champ {attribute} doit être une adresse IP valide.",
            "json": "Le champ {attribute} doit être un document JSON valide.",
            "max": {
                "numeric": "La valeur de {attribute} ne peut être supérieure à {max}.",
                "file": "Le fichier {attribute} ne peut être plus gros que {max} kilo-octets.",
                "string": "Le texte de {attribute} ne peut contenir plus de {max} caractères.",
                "array": "Le tableau {attribute} ne peut avoir plus de {max} éléments."
            },
            "mimes": "Le champ {attribute} doit être un fichier de type : {values}.",
            "min": {
                "numeric": "La valeur de {attribute} doit être supérieure à {min}.",
                "file": "Le fichier {attribute} doit être plus gros que {min} kilo-octets.",
                "string": "Le texte {attribute} doit contenir au moins {min} caractères.",
                "array": "Le tableau {attribute} doit avoir au moins {min} éléments."
            },
            "not_in": "Le champ {attribute} sélectionné n'est pas valide.",
            "numeric": "Le champ {attribute} doit contenir un nombre.",
            "regex": "Le format du champ {attribute} est invalide.",
            "required": "Le champ {attribute} est obligatoire.",
            "required_if": "Le champ {attribute} est obligatoire quand la valeur de {other} est {value}.",
            "required_unless": "Le champ {attribute} est obligatoire sauf si {other} est {values}.",
            "required_with": "Le champ {attribute} est obligatoire quand {values} est présent.",
            "required_with_all": "Le champ {attribute} est obligatoire quand {values} est présent.",
            "required_without": "Le champ {attribute} est obligatoire quand {values} n'est pas présent.",
            "required_without_all": "Le champ {attribute} est requis quand aucun de {values} n'est présent.",
            "same": "Les champs {attribute} et {other} doivent être identiques.",
            "size": {
                "numeric": "La valeur de {attribute} doit être {size}.",
                "file": "La taille du fichier de {attribute} doit être de {size} kilo-octets.",
                "string": "Le texte de {attribute} doit contenir {size} caractères.",
                "array": "Le tableau {attribute} doit contenir {size} éléments."
            },
            "string": "Le champ {attribute} doit être une chaîne de caractères.",
            "timezone": "Le champ {attribute} doit être un fuseau horaire valide.",
            "unique": "La valeur du champ {attribute} est déjà utilisée.",
            "url": "Le format de l'URL de {attribute} n'est pas valide.",
            "recaptcha": "Le captcha n'a pas pu être validé.",
            "custom": {
                "g-recaptcha-response": {
                    "required": "Le captcha est obligatoire",
                    "recaptcha": "Le captcha est invalide"
                },
                "randomform": {
                    "title": {
                        "required": "Le titre de l'email est requis."
                    },
                    "content": {
                        "required": "Le contenu de l'email est requis.",
                        "contains": "Le contenu de l'email doit contenir le mot {TARGET} pour indiquer la cible."
                    },
                    "expiration": {
                        "required": "La date d'expiration est requise.",
                        "min": "La date d'expiration ne peut pas précéder demain.",
                        "max": "La date d'expiration ne peut pas dépasser un an."
                    },
                    "participants": {
                        "length": "Il faut au moins 3 participants"
                    },
                    "participant": {
                        "name": {
                            "required": "Ce participant est requis (au moins 3 personnes).",
                            "distinct": "Ce participant n'a pas un nom unique."
                        },
                        "email": {
                            "required": "Cette adresse email est requise.",
                            "format": "Le format de cette adresse est invalide."
                        }
                    }
                },
                "dearsanta": {
                    "content": {
                        "required": "Le contenu du message est requis."
                    }
                },
                "organizer": {
                    "email": {
                        "required": "La nouvelle adresse est requise.",
                        "format": "Le format de l'adresse n'est pas valide."
                    }
                }
            },
            "attributes": {
                "name": "Nom",
                "username": "Pseudo",
                "email": "E-mail",
                "first_name": "Prénom",
                "last_name": "Nom",
                "password": "Mot de passe",
                "password_confirmation": "Confirmation du mot de passe",
                "city": "Ville",
                "country": "Pays",
                "address": "Adresse",
                "phone": "Téléphone",
                "mobile": "Portable",
                "age": "Age",
                "sex": "Sexe",
                "gender": "Genre",
                "day": "Jour",
                "month": "Mois",
                "year": "Année",
                "hour": "Heure",
                "minute": "Minute",
                "second": "Seconde",
                "title": "Titre",
                "content": "Contenu",
                "description": "Description",
                "excerpt": "Extrait",
                "date": "Date",
                "time": "Heure",
                "available": "Disponible",
                "size": "Taille",
                "g-recaptcha-response": "Recaptcha"
            }
        },
        "organizer": {
            "list": {
                "name": "Nom",
                "email": "Adresse Email",
                "status": "Status de réception de l'email"
            },
            "up_and_sent": "Modifié avec succès !"
        },
        "common": {
            "internal": "Une erreur est survenue",
            "fetcher": {
                "load": "Charger",
                "loading": "Chargement en cours..."
            },
            "form": {
                "send": "Envoyer",
                "sending": "Envoi en cours",
                "sent": "Envoyé"
            },
            "modal": {
                "close": "Fermer"
            }
        },
        "dearsanta": {
            "list": {
                "date": "Date d'envoi",
                "body": "Corps du message",
                "status": "Status de réception de l'email",
                "empty": "Aucun email envoyé pour le moment"
            },
            "content": {
                "label": "Contenu du mail",
                "placeholder": "Cher Papa Noël..."
            }
        },
        "form": {
            "nav": {
                "what": "Qu'est-ce que c'est ?",
                "how": "Comment faire ?",
                "go": "Allez, c'est parti !",
                "faq": "FAQ"
            },
            "title": "Secret Santa .fr",
            "subtitle": "Offrez-vous des cadeaux... secrètement !",
            "fyi": "Pour votre information",
            "section": {
                "what": {
                    "title": "Qu'est-ce que c'est ?",
                    "subtitle": "Description du Secret Santa",
                    "heading1": "Le principe",
                    "content1": "Secret Santa est un moyen drôle et original de s'offrir anonymement des cadeaux entre amis, collègues...\nLe déroulement est simple : chaque participant reçoit, de façon aléatoire, le nom de la personne à qui il devra faire un cadeau.\nLe montant du cadeau est généralement fixé au préalable (2€, 5€, 10€...)\nLe but n'est pas forcément de faire un beau cadeau mais d'être créatif !",
                    "notice": "secretsanta.fr est entièrement gratuit et sans publicité.\nTout est payé par le développeur lui-même.\nSi cet outil vous plait, pensez à faire un don.\n{button}"
                },
                "how": {
                    "title": "Comment faire ?",
                    "subtitle": "Vous allez voir, c'est simple !",
                    "heading1": "Première étape : spécifier le nombre et les noms des participants",
                    "content1": "Grâce aux boutons \"Ajouter un participant\" et \"Enlever un participant\", il est possible d'ajuster le nombre de personnes.\nPour chaque personne, indiquez un nom/prénom ou un pseudonyme. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.\nA noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.",
                    "heading2": "Deuxième étape : remplir les informations de contact et les exclusions",
                    "content2": "(Optionel) Ajoutez des exclusions. Si vous ne voulez pas que deux participants puissent se piocher l'un l'autre, remplissez le champ \"Exclusions\".",
                    "heading3": "Troisième étape : préparer l'e-mail",
                    "content3": "Il ne vous reste plus qu'à remplir le titre et le corps du courriel que les participants recevront.\nLe mot clef \"{TARGET}\" est obligatoire dans le corps du message afin de donner à chaque personne sa \"cible\".\n(Optionel) Vous pouvez aussi utiliser le mot clef \"{SANTA}\" qui sera remplacé par le nom du destinataire du message.",
                    "notice": "secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.\nCelles-ci sont chiffrées pour être inutilisables sans action de votre part.\nAucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.\nLe code source est disponible sur {link}"
                },
                "go": {
                    "title": "À vous de jouer !",
                    "subtitle": "Remplissez, cliquez et c'est parti !"
                }
            },
            "waiting": "Formulaire en cours de création. Si ce message reste affiché, essayez de rafraichir la page, sinon contactez moi par mail (<a href=\"mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;\">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) ou via <a href=\"https://github.com/Korko\">GitHub</a>. Merci.",
            "success": "Envoyé avec succès !",
            "participants": {
                "title": "Détails des participants",
                "import": "Importer depuis un fichier",
                "importing": "Import en cours"
            },
            "participant": {
                "organizer": "Organisateur",
                "name": {
                    "label": "Nom ou pseudonyme",
                    "placeholder": "exemple : Paul ou Korko"
                },
                "email": {
                    "label": "Adresse e-mail",
                    "placeholder": "exemple : michel@aol.com"
                },
                "exclusions": {
                    "label": "Exclusions",
                    "placeholder": "Aucune exclusion"
                },
                "remove": "Enlever",
                "add": "Ajouter un participant"
            },
            "csv": {
                "title": "Importer une liste de participants depuis un fichier CSV",
                "help": "Comment créer un fichier CSV avec {excel} Microsoft Office Excel {elink} ou {calc} Libre Office Calc {elink}",
                "format": "Afin que votre fichier CSV fonctionne, voici le format attendu :",
                "column1": "Nom du participant",
                "column2": "Adresse e-mail",
                "column3": "Exclusions (noms séparés par une virgule)",
                "warning": "Attention, l'import de ces données supprimera les participants déjà renseignés.",
                "cancel": "Annuler",
                "import": "Importer",
                "importError": "Une erreur est survenue lors de l'import.",
                "importSuccess": "L'import a été effectué avec succès."
            },
            "mail": {
                "title": {
                    "label": "Titre du mail",
                    "placeholder": "ex : Soirée secretsanta du 23 décembre chez Martin"
                },
                "content": {
                    "label": "Contenu du mail",
                    "placeholder": "ex : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !",
                    "tip1": "Utilisez \"{open}-santa{SANTA}{close}\" pour le nom de celui qui recevra le mail et \"{open}-target{TARGET}{close}\" pour le nom de sa cible.",
                    "tip2": "Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau."
                },
                "post": "----\nPour écrire à votre Secret Santa, allez sur la page suivante : {link}\nvia SecretSanta.fr"
            },
            "data-expiration": "Date limite de stockage des emails",
            "submit": "Lancez l'aléatoire !",
            "paypal": {
                "alt": "PayPal, le réflexe sécurité pour payer en ligne"
            }
        }
    }
}
