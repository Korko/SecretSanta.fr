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
                "status": "Status d'envoi de l'email",
                "caption": "Liste des participants"
            },
            "up_and_sent": "Modifié avec succès !",
            "deleted": "Toutes les données ont été supprimées",
            "purge": {
                "button": "Supprimer tout",
                "confirm": {
                    "title": "Êtes-vous sûr de vouloir supprimer la totalité des données avant leur expiration le {expiration} ?",
                    "body": "Vous ne recevrez pas le récapitulatif des tirages de cet évènement et les participants ne pourront plus écrire à leur père noël secrêt. Cette action ne peut être annulée.",
                    "value": "Supprimer toutes les données",
                    "help": "Entrez \"[+:verification]\" en dessous pour confirmer",
                    "ok": "Purger",
                    "cancel": "Annuler"
                }
            }
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
                "sent": "Envoyé",
                "reset": "Recommencer"
            },
            "modal": {
                "close": "Fermer"
            },
            "email": {
                "status": {
                    "created": "En attente d'envoi",
                    "sent": "Envoyé",
                    "received": "Reçu",
                    "error": "Erreur"
                }
            }
        },
        "faq": {
            "nav": {
                "go": "Allez, c'est parti !",
                "contact": "J'ai encore une question"
            },
            "categories": {
                "general": "Générales",
                "technical": "Techniques"
            },
            "questions": {
                "general": {
                    "Pourquoi avoir développé SecretSanta.fr ?": "Le développeur faisait fréquemment des soirées secret santa avec des amis ou des collègues avec les prénoms de chacun dans un chapeau. Chacun piochait un papier et c'est arrivé plusieurs fois que quelqu'un tombe sur son prénom. Parfois on relançait le tirage, parfois la personne ne le disais pas et je trouvais ça dommage. Alors il a eu l'idée d'en faire un outils. Le but était de faire en sorte que tout soit automatique et que personne ne se pioche soit même. Même l'organisateur participait comme tout le monde puisqu'il ne pouvait pas savoir qui avait pioché qui. Après sont venus les exclusions etc.",
                    "Comment ce site peut fonctionner en étant gratuit ?": "SecretSanta.fr est complètement gratuit pour les utilisateurs, pas pour le développeur qui paye les différents frais. Il n'y a aucune publicité ni revente d'informations.",
                    "Je me suis trompé dans mon adresse email quand j'ai organisé mon secret santa, comment faire ?": "Pour corriger ce problème, soit vous organizer un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son père noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.",
                    "J'ai supprimé mon email d'accès au panneau d'organisateur, comment faire ?": "Pour corriger ce problème, soit vous organizer un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son père noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.",
                    "Je me suis trompé dans l'adresse d'un participant": "Lorsque vous avez organisé votre secret santa, vous avez dû recevoir un email avec un lien vers un panneau d'organisateur. Vous pourrez modifier l'adresse email de chaque participant depuis cette interface.",
                    "Quand sont supprimés mes données personnelles ?": "Toutes vos données d'un tirage sont supprimées 7 jours après la date d'expiration. Ce délai a été fixé afin d'envoyer à l'organisateur la liste des participants avec leur cible piochée par mail afin d'aider à l'organisation d'un secretsanta avec les mêmes personnes plus tard en évitant de retomber sur les mêmes cibles.",
                    "J'ai oublié un participant, comment je peux le rajouter ?": "Malheureusement, de par sa conception, SecretSanta.fr ne permet pas de rajouter quelqu'un une fois le tirage effectué. Il vous reste cependant une option, autre que relancer le tirage : vous pouvez attribuez à cette personne votre propre cible et offrir vous même un cadeau à ce nouveau participant.",
                    "Qui peut savoir la liste des cibles ?": "Pour faire court : personne. Pour faire long : "
                },
                "technical": {
                    "Quelles données sont stockées et pourquoi ?": "Sont conservés pour chaque participant : nom et adresse email, pour chaque organisation, le titre et le contenu du mail envoyé ainsi que chaque message envoyé entre participant via le lien reçu par mail (appelé 'cher papa noël'). Elles sont conservées pour deux raisons : d'abord pour permettre cette dernière fonctionnalité qui permet d'écrire à son père noël secret. Ensuite pour permettre de réenvoyer les emails en cas d'erreur d'adresse.",
                    "Comment sont stockées les données ?": "Chaque élement est chiffré en AES-256 avec une clef unique par organisation. Cette clef n'est pas stockée et est envoyée à chaque participant. L'administrateur ne peut donc jamais accéder aux données sans action de votre part. Chaque fois que vous effectuez une action, vous utilisez automatiquement cette clef qui n'est que prétée à SecretSanta pour faire l'action demandée sans jamais l'enregistrer.",
                    "Je voudrais supprimer mes données, comment faire ?": "De part la façon dont sont stockées les données, l'administrateur est dans l'incapacité de savoir quelle donnée appartient à qui. Seul l'organisateur est en capacité de supprimer les données de tous les participants d'un coup. Sinon, ces données sont automatiquement supprimées peu après la fin de l'évènement.",
                    "J'aimerais vérifier par moi même le code source, où puis-je le trouver ?": "Avec grand plaisir ! Le code source se trouve à l'adresse suivante : https://framagit.com/Korko/SecretSanta. Vous pouvez aussi trouver le lien en haut à droite de la page principale, dans le petit ruban rouge."
                }
            }
        },
        "dearsanta": {
            "list": {
                "date": "Date d'envoi",
                "body": "Corps du message",
                "status": "Status de réception de l'email",
                "empty": "Aucun email envoyé pour le moment",
                "caption": "Liste des emails envoyés au Père Noël"
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
                "faq": "Foire aux questions"
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
                    "subtitle": "Vous allez voir, c'est très simple !",
                    "heading1": "Première étape : lister les participants",
                    "content1": "Grâce aux boutons \"Ajouter un participant\" et \"Enlever un participant\", il est possible d'ajuster le nombre de personnes.\nPour chaque personne, indiquez un nom/prénom ou un pseudonyme, et une adresse email. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.\nA noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.",
                    "heading2": "Deuxième étape : préciser les exclusions",
                    "content2": "Ajoutez des exclusions. Si vous ne voulez pas que deux participants puissent se piocher l'un l'autre, remplissez le champ \"Exclusions\".",
                    "heading3": "Troisième étape : préparer l'e-mail",
                    "content3": "Il ne vous reste plus qu'à remplir le titre et le corps du courriel que les participants recevront.\nLe mot clef \"{TARGET}\" est obligatoire dans le corps du message afin de donner à chaque personne sa \"cible\".\n(Optionel) Vous pouvez aussi utiliser le mot clef \"{SANTA}\" qui sera remplacé par le nom du destinataire du message.",
                    "notice": "secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.\nCelles-ci sont chiffrées pour être inutilisables sans action de votre part.\nAucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.\nLe code source est disponible sur {link}",
                    "heading4": "Et après ?",
                    "content4": "Jusqu'au jour de l'évènement spécifiée à la fin, les participants peuvent écrire un mot à leur Santa depuis un lien qu'ils reçoivent par email. Mais celui-ci ne peut pas répondre, au risque de dévoiler son identité.\nL'organisateur dispose aussi d'une interface dédiée pour retrouver le récapitulatif des participants et des exclusions."
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
                "importing": "Import en cours",
                "caption": "Liste des participats"
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
                    "placeholder": "Aucune exclusion",
                    "noOptions": "Liste vide",
                    "noResult": "Aucun résultat"
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
                    "placeholder": "ex : Soirée secretsanta du 23 décembre chez Martin, {SANTA} ta cible est..."
                },
                "content": {
                    "label": "Contenu du mail",
                    "placeholder": "ex : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !",
                    "tip1": "Utilisez \"{santa}&#123;SANTA&#125;{close}\" pour le nom de celui qui recevra le mail et \"{target}&#123;TARGET&#125;{close}\" pour le nom de sa cible.",
                    "tip2": "Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau."
                },
                "post": "----\nPour écrire à votre Secret Santa, allez sur la page suivante : {link}\nvia SecretSanta.fr"
            },
            "data-expiration": "Date de l'évènement : ",
            "data-expiration-tooltip": "<h3>Date de l'évènement</h3><ul><li>Une interface dédiée vous permettra d'accéder à un récapitulatif des participants jusqu'au jour de l'évènement.</li><li>Toutes les données stockées seront supprimées une semaine après.</li></ul>",
            "submit": "Lancez l'aléatoire !",
            "paypal": {
                "alt": "PayPal, le réflexe sécurité pour payer en ligne"
            }
        }
    }
}
