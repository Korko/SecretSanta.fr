export default {
    "fr": {
        "organizer": {
            "list": {
                "name": "Nom",
                "email": "Adresse Email",
                "status": "Status d'envoi de l'email",
                "target": "Nom de la cible",
                "caption": "Liste des participant(e)s",
                "withdraw": "Retirer"
            },
            "changed": "Modifié avec succès !",
            "withdrawn": "{name} ne participe plus à l'évènement.",
            "deleted": "Toutes les données ont été supprimées",
            "download": {
                "button": "Télécharger le récapitulatif",
                "button-tooltip": {
                    "title": "Récapitulatif",
                    "content": "Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici."
                },
                "button_initial": "Télécharger le récapitulatif initial",
                "button_initial-tooltip": {
                    "title": "Récapitulatif initial",
                    "content": "Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici."
                },
                "button_final": "Télécharger le récapitulatif complété",
                "button_final-tooltip": {
                    "title": "Récapitulatif complété",
                    "explain": "Les données sont les mêmes que dans le récapitulatif initial mais ont été ajoutées aux exclusions de chaque participant(e) la cible qu'il a eu durant cet évènement. A moins que ceci amène à un blocage où on ne puisse plus trouver de cible à chaque participant(e) pour la prochaine fois."
                }
            },
            "end": {
                "button": "Terminer l'évènement",
                "button-tooltip": {
                    "title": "Terminer l'évènement",
                    "content": "Une fois l'évènement terminé, les cibles de chaque participant sera affiché et vous pourrez aussi télécharger le récapitulatif complété. Par ailleurs les interfaces pour communiquer avec son père noël secret seront désactivées et les données du tirage au sort seront supprimées dans 7 jours. Cette action est définitive."
                }
            },
            "purge": {
                "button": "Supprimer tout",
                "button-tooltip": {
                    "title": "Supprimer toutes les données",
                    "content": "Sans action de votre part, toutes les données seront automatiquement supprimées 3 mois après le dernier email échangé. Cette action supprime immédiatement la totalité des données de votre tirage au sort (noms des participants, adresses emails, messages échangés etc). Cette action est définitive."
                },
                "confirm": {
                    "title": "Êtes-vous sûr de vouloir supprimer la totalité des données avant le nettoyage automatique le {deletes_at} ?",
                    "body_final": "Vous ne pourrez plus télécharger le récapitulatif des tirages de cet évènement et les participant(e)s ne pourront plus écrire à leur père/mère noël secret. Cette action ne peut être annulée.",
                    "body_finished": "Vous ne pourrez plus télécharger le récapitulatif de cet évènement. Cette action ne peut être annulée.",
                    "body_nofinal": "Vous ne pourrez plus télécharger le récapitulatif de cet évènement et les participant(e)s ne pourront plus écrire à leur père/mère noël secret. Cette action ne peut être annulée.",
                    "value": "Supprimer toutes les données",
                    "help": "Saisir \"[+:verification]\" en dessous pour confirmer.",
                    "ok": "Ok",
                    "cancel": "Annuler"
                }
            },
            "withdraw": {
                "button": "Retirer",
                "confirm": {
                    "title": "Êtes-vous sûr de vouloir retirer {name} de l'évènement ?",
                    "body": "Tous les messages reçu de sa cible seront transmis à son nouveau père/mère noël secret. Cette action ne peut être annulée.",
                    "value": "Annuler la participation",
                    "help": "Saisir \"[+:verification]\" en dessous pour confirmer.",
                    "ok": "Ok",
                    "cancel": "Annuler"
                }
            },
            "finished": "Votre évènement a été marqué comme terminé ({finished_at}). Certaines actions ne sont plus disponibles, comme réenvoyer le nom de la cible à un(e) participant(e)."
        },
        "faq": {
            "nav": {
                "go": "Allez, c'est parti !",
                "contact": "J'ai encore une question"
            },
            "categories": {
                "general": "Générales",
                "technical": "Techniques",
                "news": "Dernières Nouveautés"
            },
            "questions": {
                "general": {
                    "Pourquoi avoir développé SecretSanta.fr ?": "Le développeur faisait fréquemment des soirées secret santa avec des amis ou des collègues avec les prénoms de chacun dans un chapeau. Chacun piochait un papier et c'est arrivé plusieurs fois que quelqu'un tombe sur son prénom. Parfois on relançait le tirage, parfois la personne ne le disais pas et je trouvais ça dommage. Alors il a eu l'idée d'en faire un outils. Le but était de faire en sorte que tout soit automatique et que personne ne se pioche soit même. Même l'organisateur/organisatrice participait comme tout le monde puisqu'il ne pouvait pas savoir qui avait pioché qui. Après sont venus les exclusions etc.",
                    "Comment ce site peut fonctionner en étant gratuit ?": "SecretSanta.fr est complètement gratuit pour les utilisateurs, pas pour le développeur qui paye les différents frais. Il n'y a aucune publicité ni revente d'informations.",
                    "Je me suis trompé dans mon adresse email quand j'ai organisé mon secret santa.": "Pour corriger ce problème, soit vous organizez un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son/sa père/mère noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.",
                    "J'ai supprimé mon email d'accès au panneau d'organisateur/organisatrice.": "Pour corriger ce problème, soit vous organizez un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son/sa père/mère noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.",
                    "Je me suis trompé dans l'adresse d'un participant.": "Lorsque vous avez organisé votre secret santa, vous avez dû recevoir un email avec un lien vers un panneau d'organisateur/organisatrice. Vous pourrez modifier l'adresse email de chaque participant depuis cette interface.",
                    "Un des participants n'a pas reçu l'email.": "Alors même que l'adresse renseignée est bonne, il arrive que l'email se perde, finisse en spam ou qu'un problème survienne faisant que l'email n'est pas arrivé. Depuis votre panneau d'organisateur/organisatrice, vous pouvez retrouver un bouton à côté du status de réception de l'email du participant pour pouvoir réenvoyer le-dit email. Comme ça, le participant pourra enfin savoir qui est sa cible et comment parler à son Santa.",
                    "Un des participants ne souhaite plus participer.": "Depuis le panneau d'organisateur, tant que vous êtes plus de 3 participants, vous pouvez retirer un participant de l'évènement. Attention cela est définitif, il n'existe aucun moyen de rajouter quelqu'un. Le santa de la personne qui abandonne sera prévenu du nom de sa nouvelle cible puis recevra la totalité des messages que celle-ci aurait écrit à son ancien santa (la personne qui a abandonnée). Il n'y a donc rien d'autre à faire.",
                    "Je suis organisateur et je ne voudrais pas participer.": "Depuis le formulaire de création de l'évènement, il est possible de préciser que l'organisateur ne participe pas. Vous recevrez quand même le lien vers le panneau d'organisateur mais pas de cible (et personne ne vous piochera). Si jamais vous avez déjà organisé l'évènement, depuis le panneau d'organisateur, vous pouvez vous retirer vous même de la liste des participants (si vous êtes 4 ou plus). Vous resterez l'organisateur mais vous ne serez plus participant.",
                    "Est-il possible de parler à sa cible ?": "Afin de conserver son identité secrête, un santa ne peut pas écrire à sa cible.",
                    "A quoi correspondent les status d'envoi des emails ?": "Les emails peuvent avoir 5 états : tout d'abord ils sont \"En attente d'envoi\" c'est à dire qu'ils sont sur la piste de décolage. Ensuite c'est \"Envoi en cours\", cette étape est tellement rapide que personne ne devrait l'observer. Viennent les 3 principaux status : \"Envoyé\" c'est à dire que le mail est bien parti, je n'ai pas encore de retour d'erreur encore, \"Reçu\", le destinataire a bien confirmé l'avoir reçu et enfin \"Erreur\", quelque chose ne va pas. Soit l'email est invalide, soit il faut retenter un peu plus tard, un problème passager peut arriver quand une trop grande quantité de mails sont envoyés en peu de temps.",
                    "Quand sont supprimés mes données personnelles ?": "Toutes vos données d'un tirage sont supprimées 7 jours après la date de fin d'un tirage au sort ou 3 mois après l'envoi du dernier email. Ce délai a été fixé afin de laisser le temps à l'organisateur/organisatrice de télécharger depuis son interface la liste des participants avec leur cible piochée. Cela permettra d'aider à l'organisation d'un secretsanta avec les mêmes personnes plus tard en évitant de retomber sur les mêmes cibles.",
                    "Comment est définie la date de fin d'un tirage au sort ? A quoi ça sert ?": "Un organisateur peut à tout moment décider via son panneau dédié, de terminer un tirage au sort. A ce moment là, plus aucun email ne peut être envoyé, les cibles de tous les participants sont affichées et un fichier final peut être téléchargé pour préparer plus facilement le prochain tirage au sort avec les mêmes participants. Cette action est définitive.",
                    "Comment rajouter un participant à un tirage déjà effectué ?": "Malheureusement, de par sa conception, SecretSanta.fr ne permet pas de rajouter quelqu'un une fois le tirage effectué. Il vous reste cependant une option, autre que relancer le tirage : vous pouvez attribuez à cette personne votre propre cible et offrir vous même un cadeau à ce nouveau participant.",
                    "Qui peut savoir la liste des cibles ?": "Pour faire court : personne. Pour faire long : cette liste n'est affichée nulle part tant que l'évènement n'est pas terminé."
                },
                "technical": {
                    "Quelles données sont stockées et pourquoi ?": "Sont conservés pour chaque participant : nom et adresse email, pour chaque organisation, le titre et le contenu du mail envoyé ainsi que chaque message envoyé entre participant via le lien reçu par mail (appelé 'cher papa noël'). Elles sont conservées pour deux raisons : d'abord pour permettre cette dernière fonctionnalité qui permet d'écrire à son/sa père/mère noël secret. Ensuite pour permettre de réenvoyer les emails en cas d'erreur d'adresse.",
                    "Comment sont stockées les données ?": "Chaque élement est chiffré en AES-256 avec une clef unique par organisation. Cette clef n'est pas stockée et est envoyée à chaque participant. L'administrateur ne peut donc jamais accéder aux données sans action de votre part. Chaque fois que vous effectuez une action, vous utilisez automatiquement cette clef qui n'est que prétée à SecretSanta pour faire l'action demandée sans jamais l'enregistrer.",
                    "Je voudrais supprimer mes données personnelles.": "De part la façon dont sont stockées les données, l'administrateur est dans l'incapacité de savoir quelles données appartiennent à qui. Seul l'organisateur/organisatrice est en capacité de supprimer les données d'un participant en le retirant de l'évènement ou en supprimant l'évènement entièrement. Sinon, ces données sont automatiquement supprimées peu après la fin de l'évènement ou quelques mois après le dernier message échangé via la plateforme.",
                    "J'aimerais vérifier par moi même le code source.": "Avec grand plaisir ! Le code source se trouve à l'adresse suivante : https://framagit.com/Korko/SecretSanta. Vous pouvez aussi trouver le lien en haut à droite de la page principale, dans le petit ruban rouge."
                },
                "news": {
                    "La date d'expiration": "Fin 2022 : Plus besoin de préciser la date de l'évènement lors de la création. Maintenant elle est manuellement déclenchée dans la panneau d'organisateur. Dans le cas où plus aucun message n'est échangé (d'un participant à un autre ou une relance de nom de cible) pendant 3 mois, toutes les données seront automatiquement supprimées.",
                    "Un nouvel algorithme": "Fin 2022 : Un nouveau système de résolution du tirage au sort à été mis en place. Plus rapide et plus efficace, il devrait éviter au maximum les personnes isolées qui s'offrent l'une à l'autre et privilégier plutôt les chaînes de cadeau, en plus de permettre de plus grandes chaînes !",
                    "La relance du nom de la cible": "Fin 2021 : Quand on a supprimé son email qui donne le nom de sa cible mais qu'on a encore le lien vers le panneau, on peut maintenant redemander soit-même le réenvoie de cet email depuis ce même panneau.",
                    "Le retrait de participant": "Fin 2021 : On peut désormais retirer un participant d'un tirage au sort. Il faut être au minimum 4.",
                    "Organisateur non participant": "Fin 2021 : Un organisateur peut maintenant ne plus participer. Il garde accès à son panneau de supervision mais n'est plus tiré au sort et ne reçoit pas de cible non plus."
                }
            }
        },
        "common": {
            "internal": "Une erreur est survenue",
            "success": "Succès",
            "copied": "Copié dans le presse papier",
            "fetcher": {
                "load": "Charger",
                "loading": "Chargement en cours..."
            },
            "form": {
                "send": "Envoyer",
                "sending": "Envoi en cours",
                "sent": "Envoyé",
                "reset": "Commencer un nouveau tirage"
            },
            "modal": {
                "close": "Fermer"
            },
            "email": {
                "redo": "Ré-envoyer",
                "status": {
                    "created": "En attente d'envoi",
                    "sending": "Envoi en cours",
                    "sent": "Envoyé",
                    "error": "Erreur",
                    "received": "Reçu"
                },
                "recent": "Vous ne pouvez pas relancer un même email trop rapidement"
            },
            "nav": {
                "go": "Organiser un tirage",
                "dashboard": "Tableau de bord",
                "faq": "Foire Aux Questions"
            }
        },
        "bmc": {
            "button": "Offrez moi un café"
        },
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
                    "participant-organizer": {
                        "required": "Vous devez spécifier si l'organisateur participe ou non à l'évènement."
                    },
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
                        "max": "La date d'expiration ne peut pas dépasser un an.",
                        "format": "La date d'expiration doit respecter le format année-mois-jour exemple: 2022-02-05."
                    },
                    "participants": {
                        "length": "Il faut au moins 3 participant(e)s"
                    },
                    "organizer": {
                        "name": {
                            "required": "Le nom de l'organisateur/organisatrice est requis."
                        },
                        "email": {
                            "required": "Cette adresse email est requise.",
                            "format": "Le format de cette adresse est invalide."
                        }
                    },
                    "participant": {
                        "name": {
                            "required": "Ce/Cette participant(e) est requis (au moins 3 personnes).",
                            "distinct": "Ce/Cette participant(e) n'a pas un nom unique."
                        },
                        "email": {
                            "required": "Cette adresse email est requise.",
                            "format": "Le format de cette adresse est invalide."
                        }
                    }
                },
                "dearSanta": {
                    "content": {
                        "required": "Le contenu du message est requis."
                    }
                },
                "organizer": {
                    "email": {
                        "required": "La nouvelle adresse est requise.",
                        "format": "Le format de l'adresse n'est pas valide."
                    },
                    "name": {
                        "required": "Le nom du participant est requis.",
                        "not_in": "Le nom du participant doit être unique."
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
        "form": {
            "nav": {
                "what": "Qu'est-ce que c'est ?",
                "how": "Comment faire ?",
                "go": "Allez, c'est parti !"
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
                    "notice": "secretsanta.fr est entièrement gratuit et sans publicité.\nTout est payé par le développeur lui-même.\nSi cet outil vous plait, pensez à faire un don."
                },
                "how": {
                    "title": "Comment faire ?",
                    "subtitle": "Vous allez voir, c'est très simple !",
                    "heading1": "Première étape : lister les participant(e)s",
                    "content1": "Grâce aux boutons \"Ajouter un(e) participant(e)\" et \"Enlever un(e) participant(e)\", il est possible d'ajuster le nombre de personnes.\nPour chaque personne, indiquez un nom/prénom ou un pseudonyme, et une adresse email. Deux participant(e)s ne peuvent avoir le même nom, sinon il est impossible de les différencier.\nA noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.",
                    "heading2": "Deuxième étape : préciser les exclusions",
                    "content2": "Ajoutez des exclusions. Si vous ne voulez pas que deux participant(e)s puissent se piocher l'un l'autre, remplissez le champ \"Exclusions\".",
                    "heading3": "Troisième étape : préparer l'e-mail",
                    "content3": "Il ne vous reste plus qu'à remplir le titre et le corps du courriel que les participant(e)s recevront.\nLe mot clef \"{TARGET}\" est obligatoire dans le corps du message afin de donner à chaque personne sa \"cible\".\n(Optionel) Vous pouvez aussi utiliser le mot clef \"{SANTA}\" qui sera remplacé par le nom du destinataire du message.",
                    "heading4": "Et après ?",
                    "content4": "Jusqu'au jour de l'évènement spécifiée à la fin, les participant(e)s peuvent écrire un mot à leur Santa depuis un lien qu'ils reçoivent par email. Mais celui-ci ne peut pas répondre, au risque de dévoiler son identité.\nL'organisateur/organisatrice dispose aussi d'une interface dédiée pour retrouver le récapitulatif des participant(e)s et des exclusions.",
                    "notice": "secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.\nCelles-ci sont chiffrées pour être inutilisables sans action de votre part.\nAucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.\nLe code source est disponible sur <a href=\"https://github.com/Korko/SecretSanta\">GitHub</a>"
                },
                "go": {
                    "title": "À vous de jouer !",
                    "subtitle": "Remplissez, cliquez et c'est parti !"
                }
            },
            "waiting": "Formulaire en cours de création. Si ce message reste affiché, essayez de rafraichir la page, sinon contactez moi par mail ({email}) ou via {github}. Merci.",
            "success": "Envoyé avec succès !",
            "organizerIn": "L'organisateur/organisatrice participe",
            "organizerOut": "L'organisateur/organisatrice ne participe pas",
            "organizer": {
                "title": "Détails de l'organisateur/organisatrice",
                "name": "Nom ou pseudonyme de l'organisateur/organisatrice",
                "email": "Adresse e-mail de l'organisateur/organisatrice"
            },
            "participants": {
                "title": "Détails des participant(e)s",
                "import": "Importer depuis un fichier",
                "importing": "Import en cours",
                "caption": "Liste des participats"
            },
            "participant": {
                "organizer": "Organisateur·rice",
                "name": {
                    "label": "Nom ou pseudonyme",
                    "placeholder": "exemple : Paul ou Korko"
                },
                "email": {
                    "label": "Adresse e-mail",
                    "placeholder": "exemple : michel{'@'}aol.com"
                },
                "exclusions": {
                    "label": "Exclusions",
                    "placeholder": "Aucune exclusion",
                    "noOptions": "Liste vide",
                    "noResult": "Aucun résultat"
                },
                "remove": "Enlever",
                "add": "Ajouter un(e) participant(e)"
            },
            "csv": {
                "title": "Importer une liste de participant(e)s depuis un fichier CSV",
                "help": "Comment créer un fichier CSV avec {excel} Microsoft Office Excel {elink} ou {calc} Libre Office Calc {elink}",
                "format": "Afin que votre fichier CSV fonctionne, voici le format attendu :",
                "column1": "Nom du/de la participant(e)",
                "column2": "Adresse e-mail",
                "column3": "Exclusions (noms séparés par une virgule)",
                "warning": "Attention, l'import de ces données supprimera les participant(e)s déjà renseignés.",
                "cancel": "Annuler",
                "import": "Importer",
                "importError": "Une erreur est survenue lors de l'import.",
                "importSuccess": "L'import a été effectué avec succès.",
                "analyzing": "Chargement en cours..."
            },
            "mail": {
                "title": {
                    "label": "Titre du mail",
                    "placeholder": "ex : Soirée secretsanta du 23 décembre chez Martin, {SANTA} ta cible est..."
                },
                "content": {
                    "label": "Contenu du mail",
                    "placeholder": "ex : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !",
                    "tip1": "Utilisez {santa} pour le nom de celui qui recevra le mail et {target} pour le nom de sa cible.",
                    "tip2": "Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau."
                },
                "post": "----\nPour écrire à votre Secret Santa, allez sur la page suivante : {link}\nvia SecretSanta.fr"
            },
            "data-expiration": "Date de l'évènement : ",
            "data-expiration-tooltip": {
                "title": "Date de l'évènement",
                "interface": "Une interface dédiée vous permettra d'accéder à un récapitulatif des participant(e)s jusqu'au jour de l'évènement.",
                "deletion": "Toutes les données stockées seront supprimées une semaine après."
            },
            "submit": "Lancez l'aléatoire !",
            "paypal": {
                "alt": "PayPal, le réflexe sécurité pour payer en ligne"
            },
            "internalError": "Erreur interne"
        },
        "dearsanta": {
            "list": {
                "date": "Date d'envoi",
                "body": "Corps du message",
                "status": "Status de réception de l'email",
                "empty": "Aucun email envoyé pour le moment",
                "caption": "Liste des emails envoyés au/à la Père/Mère Noël"
            },
            "content": {
                "label": "Contenu du mail",
                "placeholder": "Cher Papa Noël..."
            },
            "resend": {
                "button": "Me ré-envoyer les mails que j'ai reçu de ma cible"
            },
            "finished": "Votre évènement a été marqué comme terminé ({finished_at}). Le nom de votre cible a été révélé à l'organisateur et certaines actions ne sont plus disponibles, comme envoyer des emails."
        }
    }
}
