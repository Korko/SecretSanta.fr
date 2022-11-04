<?php

return [
    'nav' => [
        'contact' => "J'ai encore une question",
    ],

    'categories' => [
        'general' => 'Générales',
        'technical' => 'Techniques',
        'news' => 'Dernières Nouveautés',
    ],

    'questions' => [
        'general' => [
            'Pourquoi avoir développé SecretSanta.fr ?' => "Le développeur faisait fréquemment des soirées secret santa avec des amis ou des collègues avec les prénoms de chacun dans un chapeau. Chacun piochait un papier et c'est arrivé plusieurs fois que quelqu'un tombe sur son prénom. Parfois on relançait le tirage, parfois la personne ne le disais pas et je trouvais ça dommage. Alors il a eu l'idée d'en faire un outils. Le but était de faire en sorte que tout soit automatique et que personne ne se pioche soit même. Même l'organisateur/organisatrice participait comme tout le monde puisqu'il ne pouvait pas savoir qui avait pioché qui. Après sont venus les exclusions etc.",
            'Comment ce site peut fonctionner en étant gratuit ?' => "SecretSanta.fr est complètement gratuit pour les utilisateurs, pas pour le développeur qui paye les différents frais. Il n'y a aucune publicité ni revente d'informations.",
            "Je me suis trompé dans mon adresse email quand j'ai organisé mon secret santa." => "Pour corriger ce problème, soit vous organizez un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son/sa père/mère noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.",
            "J'ai supprimé mon email d'accès au panneau d'organisateur/organisatrice." => "Pour corriger ce problème, soit vous organizez un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son/sa père/mère noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.",
            "Je me suis trompé dans l'adresse d'un participant." => "Lorsque vous avez organisé votre secret santa, vous avez dû recevoir un email avec un lien vers un panneau d'organisateur/organisatrice. Vous pourrez modifier l'adresse email de chaque participant depuis cette interface.",
            "Un des participants n'a pas reçu l'email." => "Alors même que l'adresse renseignée est bonne, il arrive que l'email se perde, finisse en spam ou qu'un problème survienne faisant que l'email n'est pas arrivé. Depuis votre panneau d'organisateur/organisatrice, vous pouvez retrouver un bouton à côté du status de réception de l'email du participant pour pouvoir réenvoyer le-dit email. Comme ça, le participant pourra enfin savoir qui est sa cible et comment parler à son Santa.",
            'Un des participants ne souhaite plus participer.' => "Depuis le panneau d'organisateur, tant que vous êtes plus de 3 participants, vous pouvez retirer un participant de l'évènement. Attention cela est définitif, il n'existe aucun moyen de rajouter quelqu'un. Le santa de la personne qui abandonne sera prévenu du nom de sa nouvelle cible puis recevra la totalité des messages que celle-ci aurait écrit à son ancien santa (la personne qui a abandonnée). Il n'y a donc rien d'autre à faire.",
            'Je suis organisateur et je ne voudrais pas participer.' => "Depuis le formulaire de création de l'évènement, il est possible de préciser que l'organisateur ne participe pas. Vous recevrez quand même le lien vers le panneau d'organisateur mais pas de cible (et personne ne vous piochera). Si jamais vous avez déjà organisé l'évènement, depuis le panneau d'organisateur, vous pouvez vous retirer vous même de la liste des participants (si vous êtes 4 ou plus). Vous resterez l'organisateur mais vous ne serez plus participant.",
            'Est-il possible de parler à sa cible ?' => 'Afin de conserver son identité secrête, un santa ne peut pas écrire à sa cible.',
            "A quoi correspondent les status d'envoi des emails ?" => "Les emails peuvent avoir 5 états : tout d'abord ils sont \"En attente d'envoi\" c'est à dire qu'ils sont sur la piste de décolage. Ensuite c'est \"Envoi en cours\", cette étape est tellement rapide que personne ne devrait l'observer. Viennent les 3 principaux status : \"Envoyé\" c'est à dire que le mail est bien parti, je n'ai pas encore de retour d'erreur encore, \"Reçu\", le destinataire a bien confirmé l'avoir reçu et enfin \"Erreur\", quelque chose ne va pas. Soit l'email est invalide, soit il faut retenter un peu plus tard, un problème passager peut arriver quand une trop grande quantité de mails sont envoyés en peu de temps.",
            'Quand sont supprimés mes données personnelles ?' => "Toutes vos données d'un tirage sont supprimées 7 jours après la date de fin d'un tirage au sort ou 3 mois après l'envoi du dernier email. Ce délai a été fixé afin de laisser le temps à l'organisateur/organisatrice de télécharger depuis son interface la liste des participants avec leur cible piochée. Cela permettra d'aider à l'organisation d'un secretsanta avec les mêmes personnes plus tard en évitant de retomber sur les mêmes cibles.",
            "Comment est définie la date de fin d'un tirage au sort ? A quoi ça sert ?" => 'Un organisateur peut à tout moment décider via son panneau dédié, de terminer un tirage au sort. A ce moment là, plus aucun email ne peut être envoyé, les cibles de tous les participants sont affichées et un fichier final peut être téléchargé pour préparer plus facilement le prochain tirage au sort avec les mêmes participants. Cette action est définitive.',
            'Comment rajouter un participant à un tirage déjà effectué ?' => "Malheureusement, de par sa conception, SecretSanta.fr ne permet pas de rajouter quelqu'un une fois le tirage effectué. Il vous reste cependant une option, autre que relancer le tirage : vous pouvez attribuez à cette personne votre propre cible et offrir vous même un cadeau à ce nouveau participant.",
            'Qui peut savoir la liste des cibles ?' => "Pour faire court : personne. Pour faire long : cette liste n'est affichée nulle part tant que l'évènement n'est pas terminé.",
        ],

        'technical' => [
            'Quelles données sont stockées et pourquoi ?' => "Sont conservés pour chaque participant : nom et adresse email, pour chaque organisation, le titre et le contenu du mail envoyé ainsi que chaque message envoyé entre participant via le lien reçu par mail (appelé 'cher papa noël'). Elles sont conservées pour deux raisons : d'abord pour permettre cette dernière fonctionnalité qui permet d'écrire à son/sa père/mère noël secret. Ensuite pour permettre de réenvoyer les emails en cas d'erreur d'adresse.",
            'Comment sont stockées les données ?' => "Chaque élement est chiffré en AES-256 avec une clef unique par organisation. Cette clef n'est pas stockée et est envoyée à chaque participant. L'administrateur ne peut donc jamais accéder aux données sans action de votre part. Chaque fois que vous effectuez une action, vous utilisez automatiquement cette clef qui n'est que prétée à SecretSanta pour faire l'action demandée sans jamais l'enregistrer.",
            'Je voudrais supprimer mes données personnelles.' => "De part la façon dont sont stockées les données, l'administrateur est dans l'incapacité de savoir quelles données appartiennent à qui. Seul l'organisateur/organisatrice est en capacité de supprimer les données d'un participant en le retirant de l'évènement ou en supprimant l'évènement entièrement. Sinon, ces données sont automatiquement supprimées peu après la fin de l'évènement ou quelques mois après le dernier message échangé via la plateforme.",
            "J'aimerais vérifier par moi même le code source." => "Avec grand plaisir ! Le code source se trouve à l'adresse suivante : https://framagit.com/Korko/SecretSanta. Vous pouvez aussi trouver le lien en haut à droite de la page principale, dans le petit ruban rouge.",
        ],

        'news' => [
            'Noël 2022' => [
                'Limitation du nombre de participants au total pour une meilleure stabilité du site',
                'Amélioration du tirage au sort avec un nouvel algorithme plus rapide',
                'Suppression de la date de suppression, il s\'agit maintenant d\'une expiration indiquée sur les différents panneaux',
                'Ajout dans le panneau participant de la possibilité de recevoir de nouveau l\'email contenant le nom de la personne à qui faire un cadeau',
                'Ajout d\'une validation de l\'email de l\'organisateur avant le tirage au sort',
                'Ajout de la possibilité pour un organisateur de changer le nom d\'un participant',
            ],
            'Noël 2021' => [
                'Ajout de la possibilité pour un organisateur de ne pas être participant',
                'Ajout de la possibilité pour un organisateur de retirer un participant durant l\'évènement',
                'Amélioration du système de suivi des emails en respectant la vie privée',
                'Ajout pour les participants du système de réenvoi d\'un email pour leur père noël secrêt',
                'Ajout d\'une page légale pour préciser les intentions du projet en terme de données personnelles',
                'Chiffrement de la liste des emails à envoyer pour améliorer encore plus la sécurité des données personnelles',
                'Ajout pour l\'organisateur du réenvoi d\'email même si celui-ci est sensé avoir été reçu',
                'Limitation de la date de suppression a 6 mois',
            ],
            'Noël 2020' => [
                'Ajout dans le panneau d\'organisateur d\'un bouton pour télécharger la liste des participants, ainsi qu\'une version finalisée avec les cibles dans les exclusions',
                'Suivi des emails revenus en erreur et affichage en direct dans le panneau d\'organisateur sans avoir besoin de rafraichir la page',
                'Amélioration de réception des emails en ajoutant une signature numérique à chacun',
                'Amélioration de la sécurité des données personnelles dans les liens vers les différents panneaux',
                'Ajout de tooltips sur la date d\'expiration et sur les boutons de téléchargements',
                'Suppression du captcha, Google pouvait tracer les utilisateurs',
                'Ajout dans le panneau d\'organisateur d\'un bouton pour supprimer la totalité des données du tirage au sort peu importe la date d\'expiration',
                'Ajout d\'une foire aux questions',
                'Ajout du suivi des emails envoyés par les participants à leur père noël secrêt, directement dans leur panneau respectif',
            ],
            'Noël 2018' => [
                'Ajout pour les organisateurs d\'un panneau pour voir la liste des participants',
                'Suppression de l\'envoi de SMS, coût beaucoup trop important',
                'Utilisation d\'un captcha invisible pour plus de fluidité',
            ],
            'Noël 2017' => [
                'Ajout de la possibilité pour chaque participant d\'écrire à leur père noël sans dévoiler son identité',
                'Amélioration de la vitesse de résolution du tirage au sort',
            ],
            'Noël 2016' => [
                'Ajout de la possibilité d\'ajouter plusieurs exclusions par participant au lieu d\'une seule',
                'Ajout de la traduction pour permettre une version anglaise',
                'Ajout de la possibilité d\'envoyer la cible de chaque participant par sms',
            ],
            'Noël 2015' => [
                'Ajout d\'un captcha pour se protéger des bots',
                'Développement initial du projet',
            ],
        ],
    ],
];
