(self["webpackChunk"] = self["webpackChunk"] || []).push([["/js/dashboard"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-4[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-4[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  props: {},
  data: function data() {
    return {};
  },
  created: function created() {
    var data = JSON.parse(window.localStorage.getItem('secretsanta')) || {};
    console.log(data);
  },
  computed: {},
  watch: {}
});

/***/ }),

/***/ "./resources/js/dashboard.js":
/*!***********************************!*\
  !*** ./resources/js/dashboard.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm.js");
/* harmony import */ var vue_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-i18n */ "./node_modules/vue-i18n/dist/vue-i18n.esm.js");
/* harmony import */ var _vue_i18n_locales_generated_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./vue-i18n-locales.generated.js */ "./resources/js/vue-i18n-locales.generated.js");
/* harmony import */ var _components_dashboard_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/dashboard.vue */ "./resources/js/components/dashboard.vue");


vue__WEBPACK_IMPORTED_MODULE_0__["default"].use(vue_i18n__WEBPACK_IMPORTED_MODULE_1__["default"]);
var lang = document.documentElement.lang.substr(0, 2);

var i18n = new vue_i18n__WEBPACK_IMPORTED_MODULE_1__["default"]({
  locale: lang,
  messages: _vue_i18n_locales_generated_js__WEBPACK_IMPORTED_MODULE_2__["default"]
});

window.app = new vue__WEBPACK_IMPORTED_MODULE_0__["default"]({
  el: '#content',
  components: {
    Dashboard: Dashboard
  },
  i18n: i18n,
  mounted: function mounted() {
    document.body.classList.add('cssLoading');
    setTimeout(function () {
      return document.body.classList.remove('cssLoading');
    }, 0);
  }
});

/***/ }),

/***/ "./resources/js/vue-i18n-locales.generated.js":
/*!****************************************************!*\
  !*** ./resources/js/vue-i18n-locales.generated.js ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  "fr": {
    "organizer": {
      "list": {
        "name": "Nom",
        "email": "Adresse Email",
        "status": "Status d'envoi de l'email",
        "caption": "Liste des participants"
      },
      "up_and_sent": "Modifié avec succès !",
      "deleted": "Toutes les données ont été supprimées",
      "download": {
        "button": "Télécharger le récapitulatif",
        "button-tooltip": "<h3>Récapitulatif</h3><p>Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici.</p>",
        "button_initial": "Télécharger le récapitulatif initial",
        "button_initial-tooltip": "<h3>Récapitulatif initial</h3><p>Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici.</p>",
        "button_final": "Télécharger le récapitulatif complété",
        "button_final-tooltip": "<h3>Récapitulatif complété</h3><p>Les données sont les mêmes que dans le récapitulatif initial mais ont été ajoutées aux exclusions de charque participant la cible qu'il a eu durant cet évènement. A moins que ceci amène à un blocage où on ne puisse plus trouver de cible à chaque participant pour la prochaine fois.</p><p class=\"border border-white border-1 rounded pl-2 pr-2 font-italic\">Compte tenu de la date de l'évènement définie, cette fonctionnalité n'est disponible que du {expires_at} au {deleted_at}.</p>"
      },
      "purge": {
        "button": "Supprimer tout",
        "confirm": {
          "title": "Êtes-vous sûr de vouloir supprimer la totalité des données avant le nettoyage automatique le {deletion} ?",
          "body_final": "Vous ne pourrez plus télécharger le récapitulatif des tirages de cet évènement et les participants ne pourront plus écrire à leur père noël secret. Cette action ne peut être annulée.",
          "body_expired": "Vous ne pourrez plus télécharger le récapitulatif de cet évènement. Cette action ne peut être annulée.",
          "body_nofinal": "Vous ne pourrez plus télécharger le récapitulatif de cet évènement et les participants ne pourront plus écrire à leur père noël secret. Cette action ne peut être annulée.",
          "value": "Supprimer toutes les données",
          "help": "Saisir \"[+:verification]\" en dessous pour confirmer.",
          "ok": "Ok",
          "cancel": "Annuler"
        }
      },
      "expired": "Votre évènement est passé ({expires_at}). Certaines actions ne sont plus disponibles, comme réenvoyer le nom de la cible à un participant."
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
          "Un des participants n'a pas reçu l'email, que faire ?": "Alors même que l'adresse renseignée est bonne, il arrive que l'email se perde, finisse en spam ou qu'un problème survienne faisant que l'email n'est pas arrivé. Depuis votre panneau d'organisateur, vous pouvez retrouver un bouton à côté du status de réception de l'email du participant pour pouvoir réenvoyer le-dit email. Comme ça, le participant pourra enfin savoir qui est sa cible et comment parler à son Santa.",
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
        "redo": "Ré-envoyer",
        "status": {
          "created": "En attente d'envoi",
          "sending": "Envoi en cours",
          "sent": "Envoyé",
          "error": "Erreur",
          "received": "Reçu"
        }
      }
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
        "dearSanta": {
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
    "form": {
      "nav": {
        "what": "Qu'est-ce que c'est ?",
        "how": "Comment faire ?",
        "go": "Allez, c'est parti !",
        "faq": "Foire Aux Questions"
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
      },
      "internalError": "Erreur interne"
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
    }
  }
});

/***/ }),

/***/ "./resources/images/BMC Logo - Black.png":
/*!***********************************************!*\
  !*** ./resources/images/BMC Logo - Black.png ***!
  \***********************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = __webpack_require__.p + "images/BMC Logo - Black.png?9735372c55c8679a091ccac657b06bd9";

/***/ }),

/***/ "./resources/images/BMC Logo - Black.webp":
/*!************************************************!*\
  !*** ./resources/images/BMC Logo - Black.webp ***!
  \************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = __webpack_require__.p + "images/BMC Logo - Black.webp?37e6b6e3b2212d971f9ff3b3cefb821a";

/***/ }),

/***/ "./resources/images/logo.png":
/*!***********************************!*\
  !*** ./resources/images/logo.png ***!
  \***********************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = __webpack_require__.p + "images/logo.png?8b5c397831e0659fd4032153029b9533";

/***/ }),

/***/ "./resources/images/logo.webp":
/*!************************************!*\
  !*** ./resources/images/logo.webp ***!
  \************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = __webpack_require__.p + "images/logo.webp?d55daf5d9c3e68b4c7bdbde22567185b";

/***/ }),

/***/ "./resources/fonts/Cookie-Regular.ttf":
/*!********************************************!*\
  !*** ./resources/fonts/Cookie-Regular.ttf ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ("/fonts/Cookie-Regular.ttf?561be922e1551bfac194b0f364837e41");

/***/ }),

/***/ "./resources/fonts/Kreon-VariableFont_wght.ttf":
/*!*****************************************************!*\
  !*** ./resources/fonts/Kreon-VariableFont_wght.ttf ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ("/fonts/Kreon-VariableFont_wght.ttf?24b9456f88467b6c8f1b9abd8448d45f");

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js */ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_sass_layout_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! -!../../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!../../sass/layout.scss */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./resources/sass/layout.scss");
// Imports


var ___CSS_LOADER_EXPORT___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
___CSS_LOADER_EXPORT___.i(_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_sass_layout_scss__WEBPACK_IMPORTED_MODULE_1__["default"]);
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n\n", ""]);
// Exports
/* harmony default export */ __webpack_exports__["default"] = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./resources/sass/layout.scss":
/*!***************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./resources/sass/layout.scss ***!
  \***************************************************************************************************************************************/
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js */ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_bootstrap_dist_css_bootstrap_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! -!../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!../../node_modules/bootstrap/dist/css/bootstrap.min.css */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/bootstrap/dist/css/bootstrap.min.css");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_alertifyjs_build_css_alertify_min_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! -!../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!../../node_modules/alertifyjs/build/css/alertify.min.css */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/alertifyjs/build/css/alertify.min.css");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_alertifyjs_build_css_themes_bootstrap_min_css__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! -!../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!../../node_modules/alertifyjs/build/css/themes/bootstrap.min.css */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/alertifyjs/build/css/themes/bootstrap.min.css");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../node_modules/laravel-mix/node_modules/css-loader/dist/runtime/getUrl.js */ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/getUrl.js");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _fonts_Kreon_VariableFont_wght_ttf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../fonts/Kreon-VariableFont_wght.ttf */ "./resources/fonts/Kreon-VariableFont_wght.ttf");
/* harmony import */ var _images_logo_webp__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../images/logo.webp */ "./resources/images/logo.webp");
/* harmony import */ var _images_logo_webp__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_images_logo_webp__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _images_logo_png__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../images/logo.png */ "./resources/images/logo.png");
/* harmony import */ var _images_logo_png__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_images_logo_png__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _fonts_Cookie_Regular_ttf__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../fonts/Cookie-Regular.ttf */ "./resources/fonts/Cookie-Regular.ttf");
/* harmony import */ var _images_BMC_Logo_Black_webp__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../images/BMC Logo - Black.webp */ "./resources/images/BMC Logo - Black.webp");
/* harmony import */ var _images_BMC_Logo_Black_webp__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_images_BMC_Logo_Black_webp__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _images_BMC_Logo_Black_png__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../images/BMC Logo - Black.png */ "./resources/images/BMC Logo - Black.png");
/* harmony import */ var _images_BMC_Logo_Black_png__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_images_BMC_Logo_Black_png__WEBPACK_IMPORTED_MODULE_10__);
// Imports











var ___CSS_LOADER_EXPORT___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
___CSS_LOADER_EXPORT___.i(_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_bootstrap_dist_css_bootstrap_min_css__WEBPACK_IMPORTED_MODULE_1__["default"]);
___CSS_LOADER_EXPORT___.i(_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_alertifyjs_build_css_alertify_min_css__WEBPACK_IMPORTED_MODULE_2__["default"]);
___CSS_LOADER_EXPORT___.i(_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_alertifyjs_build_css_themes_bootstrap_min_css__WEBPACK_IMPORTED_MODULE_3__["default"]);
var ___CSS_LOADER_URL_REPLACEMENT_0___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default()(_fonts_Kreon_VariableFont_wght_ttf__WEBPACK_IMPORTED_MODULE_5__["default"]);
var ___CSS_LOADER_URL_REPLACEMENT_1___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default()((_images_logo_webp__WEBPACK_IMPORTED_MODULE_6___default()));
var ___CSS_LOADER_URL_REPLACEMENT_2___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default()((_images_logo_png__WEBPACK_IMPORTED_MODULE_7___default()));
var ___CSS_LOADER_URL_REPLACEMENT_3___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default()(_fonts_Cookie_Regular_ttf__WEBPACK_IMPORTED_MODULE_8__["default"]);
var ___CSS_LOADER_URL_REPLACEMENT_4___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default()((_images_BMC_Logo_Black_webp__WEBPACK_IMPORTED_MODULE_9___default()), { needQuotes: true });
var ___CSS_LOADER_URL_REPLACEMENT_5___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_getUrl_js__WEBPACK_IMPORTED_MODULE_4___default()((_images_BMC_Logo_Black_png__WEBPACK_IMPORTED_MODULE_10___default()), { needQuotes: true });
// Module
___CSS_LOADER_EXPORT___.push([module.id, "#loadOverlay {\n    display: none;\n}\n.cssLoading * {\n  -webkit-transition: none !important;\n  -moz-transition: none !important;\n  -ms-transition: none !important;\n  -o-transition: none !important;\n  transition: none !important;\n}\n\n.alertify .ajs-dialog {\n    top: 50%;\n    transform: translateY(-50%);\n    margin: auto;\n}\n\n/*\nSticky Footer Solution\nby Steve Hatcher\nhttp://stever.ca\nhttp://www.cssstickyfooter.com\n*/\n\n* { margin: 0; padding: 0; }\n\n/* must declare 0 margins on everything, also for main layout components use padding, not\nvertical margins (top and bottom) to add spacing, else those margins get added to total height\nand your footer gets pushed down a bit more, creating vertical scroll bars in the browser */\n\nhtml,\nbody { height: 100%; }\n\n#wrap { min-height: 100%; }\n\n$footer-height: 90px !default;\n\n#main {\n    overflow: auto;\n    padding-bottom: $footer-height + 10px;\n}  /* must be same height as the footer */\n\nfooter {\n    position: relative;\n    margin-top: -$footer-height - 10px; /* negative value of footer height */\n    height: $footer-height + 10px;\n    font-size: 15px;\n    clear: both;\n}\n\nfooter .inner { padding-bottom: 10px; }\nfooter .row { height: $footer-height; }\n\n/* Opera Fix */\nbody::before {/* thanks to Maleika (Kohoutec) */\n    content: \"\";\n    height: 100%;\n    float: left;\n    width: 0;\n    margin-top: -32767px;/* thank you Erik J - negate effect of float */\n}\n\n/* IMPORTANT\n\nYou also need to include this conditional style in the <head> of your HTML file to feed this style to IE 6 and lower and 8 and higher.\n\n<!--[if !IE 7]>\n    <style type=\"text/css\">\n        #wrap {display: table;height: 100%}\n    </style>\n<![endif]-->\n\n*/\n\n/*\nTheme Name: MeatKing\nVersion:    1.14.20\nAuthor:     ThemeWagon\n\nTiny bug when width <780px, there an horizontal scrollbar\n\nTABLE OF CONTENTS\n    01 - General and Typography\n    02 - Header\n    03 - Navigation\n    04 - Parallax\n    05 - What/How\n    06 - Form\n    07 - Footer\n    08 - Responsive styles\n    09 - Ribbon\n    10 - Modal\n    11 - Readability\n    12 - Widgets\n    13 - Tooltip\n*/\n\n/* ==========================================================================\n    01. General and Typography\n========================================================================== */\n\n@font-face {\n    font-family: kreon;\n    src: url(" + ___CSS_LOADER_URL_REPLACEMENT_0___ + ") format('truetype');\n}\n\nhtml {\n    font-size: 14px;\n}\n\nbody {\n    font-family: kreon, serif;\n    -webkit-font-smoothing: antialiased;\n    -webkit-text-size-adjust: 100%;\n}\n\nimg {\n    max-height: 100%;\n    max-width: 100%;\n}\n\n.light-wrapper section {\n    position: relative;\n    padding: 0;\n    background: #f0a830;\n    color: #fff;\n    text-align: center;\n}\n\n.light-wrapper section::before,\n.light-wrapper section::after,\n.dark-wrapper section::before,\n.dark-wrapper section::after {\n    position: absolute;\n    content: '';\n}\n\n@-moz-document url-prefix() {\n    fieldset { display: table-cell; }\n}\n\n/* Separators Styles */\n.ss-style-top::before {\n    left: 0;\n    width: 100%;\n    height: 30px;\n    -webkit-background-size: 25px 100%;\n    -moz-background-size: 25px 100%;\n    -o-background-size: 25px 100%;\n    background-size: 25px 100%;\n    top: 0;\n    background-image: linear-gradient(315deg, #fff 50%, transparent 50%), linear-gradient(45deg, #fff 50%, transparent 50%);\n    margin-top: -30px;\n    z-index: 1;\n}\n\n.ss-style-bottom::before {\n    left: 0;\n    width: 100%;\n    height: 30px;\n    -webkit-background-size: 25px 100%;\n    -moz-background-size: 25px 100%;\n    -o-background-size: 25px 100%;\n    background-size: 25px 100%;\n    top: 0;\n    background-image: linear-gradient(583deg, #fff 50%, transparent 50%), linear-gradient(136deg, #fff 50%, transparent 50%);\n    margin-top: 0;\n    z-index: 1;\n}\n\n#main .light-wrapper:last-of-type .ss-style-bottom {\n    display: none;\n}\n\n[v-cloak] {\n    display: none;\n}\n\n.v-rcloak {\n    display: none;\n}\n\n[v-cloak] ~ .v-rcloak {\n    display: block;\n}\n\na.link {\n    cursor: pointer;\n}\n\n/* ==========================================================================\n    03. Navigation\n========================================================================== */\n#menu {\n    background: rgba(33, 45, 57, 0.8);\n    margin-bottom: 0;\n}\n\nnav#navbar {\n    width: 100%;\n    display: flex;\n}\n\n.navbar-brand h2 {\n    margin-top: 0;\n    font-weight: bold;\n    color: white;\n}\n\n.navbar-brand {\n    padding-top: 8px;\n    padding-bottom: 0;\n}\n\n.navbar .navbar-nav > li > a {\n    -webkit-transition: all 0.4s;\n    -moz-transition: all 0.4s;\n    -o-transition: all 0.4s;\n    transition: all 0.4s;\n    color: white;\n    font-weight: bold;\n    padding: 15px;\n    line-height: 20px;\n    display: block;\n}\n\n.navbar .navbar-nav > li > a:hover,\n.navbar .navbar-nav > .active > a {\n    background: #ed0121;\n    color: white;\n    text-shadow: none;\n}\n\n.nav.navbar-nav-left {\n    display: inline-flex;\n    justify-content: flex-end;\n    flex: 1;\n    margin-right: 60px;\n}\n\n.nav.navbar-nav-right {\n    display: inline-flex;\n    justify-content: flex-start;\n    flex: 1;\n    margin-left: 60px;\n}\n\n.navbar a[href]:not(:empty)::before {\n    content: \"↪\";\n    padding-right: .2em;\n    color: #ccc;\n    display: inline-block;\n    opacity: .4;\n}\n.navbar a[href]:not(:empty):hover::before {\n    text-decoration: none;\n    color: #333;\n}\n.navbar a[href^=\"#\"]:not(:empty)::before {\n    content: \"#\";\n}\n\n#logo {\n    position: absolute;\n    display: block !important;\n    width: 110px;\n    left: 50%;\n    margin-left: calc(-55px - 0.5rem);/* Don't forget half the padding of .navbar */\n    background: black;\n    -webkit-border-radius: 0 0 100% 100%;\n    -moz-border-radius: 0 0 100% 100%;\n    border-radius: 0 0 100% 100%;\n    padding: 12px;\n    top: 0;\n    background-image: url(" + ___CSS_LOADER_URL_REPLACEMENT_1___ + ");\n    background-size: 86px;\n    background-repeat: no-repeat;\n    background-position: center center;\n    height: 110px;\n}\nbody.nowebp #logo {\n    background-image: url(" + ___CSS_LOADER_URL_REPLACEMENT_2___ + ");\n}\n\n/* ==========================================================================\n    06. Content\n========================================================================== */\n#content {\n    padding-top: 140px;\n    padding-bottom: 60px;\n}\n\n/* ==========================================================================\n    07. Footer\n========================================================================== */\n.dark-wrapper {\n    background: #b50119;\n    color: white;\n    font-weight: bold;\n}\n\n.dark-wrapper .ss-style-top::before {\n    background-image: linear-gradient(315deg, #b50119 50%, transparent 50%), linear-gradient(45deg, #b50119 50%, transparent 50%);\n}\n\n.themeBy {\n    color: #ed0121;\n    background: white;\n}\n\nfooter .row {\n    display: flex;\n}\n\n/* ==========================================================================\n    08. Responsive styles\n========================================================================== */\n@media (max-width: 767px) {\n    .navbar {\n        min-height: 30px;\n    }\n\n    #logo {\n        -webkit-transition: all 0.3s ease;\n        transition: all 0.3s ease;\n    }\n\n    body.scrolled #logo {\n        width: 60px;\n        margin-left: -30px;\n    }\n\n    .navbar-nav > li.btn-group {\n        margin: 0 10px;\n    }\n}\n\n@media (min-width: 768px) {\n    html .navbar-nav {\n        float: none !important;\n        width: 100%;\n        text-align: center;\n        margin-left: -16px;\n    }\n\n    html:lang(en) .navbar-nav {\n        margin-left: 13px;\n    }\n\n    .navbar-nav > li {\n        display: inline-block;\n        float: none;\n    }\n\n    .navbar-nav > li.btn-group {\n        margin: 0 15px;\n    }\n}\n\n/* ==========================================================================\n    09. Ribbon\n========================================================================== */\n#ribbon {\n    display: none;\n}\n\n@media screen and (min-width: 768px) {\n    #ribbon {\n        position: fixed;\n        display: block;\n        top: -30px;\n        right: 0;\n        width: 105px;\n        overflow: visible;\n        height: 135px;\n        z-index: 9999;\n    }\n\n    #ribbon a {\n        background: #a00;\n        color: #fff;\n        text-decoration: none;\n        font-family: arial, sans-serif;\n        text-align: center;\n        font-weight: bold;\n        padding: 5px 40px;\n        line-height: 20px;\n        transition: background 0.5s, color 0.5s;\n        width: 200px;\n        position: absolute;\n        top: 55px;\n        right: -50px;\n        transform: rotate(45deg);\n        -webkit-transform: rotate(45deg);\n        -ms-transform: rotate(45deg);\n        -moz-transform: rotate(45deg);\n        -o-transform: rotate(45deg);\n        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.8);\n    }\n\n    #ribbon a:hover {\n        background: #c00;\n        color: #fff;\n    }\n\n    #ribbon a::before,\n    #ribbon a::after {\n        content: \"\";\n        width: 100%;\n        display: block;\n        position: absolute;\n        top: 1px;\n        left: 0;\n        height: 1px;\n        background: #fff;\n    }\n\n    #ribbon a::after {\n        bottom: 1px;\n        top: auto;\n    }\n}\n\n/* ==========================================================================\n    10. Modal\n========================================================================== */\n.modal-mask {\n    position: fixed;\n    z-index: 9998;\n    top: 0;\n    left: 0;\n    width: 100%;\n    height: 100%;\n    background-color: rgba(0, 0, 0, 0.5);\n    display: table;\n    transition: opacity 0.3s ease;\n}\n\n.modal-wrapper {\n    display: table-cell;\n    vertical-align: middle;\n}\n\n.modal-container {\n    width: 500px;\n    margin: 0 auto;\n    padding: 20px 30px;\n    background-color: #fff;\n    border-radius: 2px;\n    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);\n    transition: all 0.3s ease;\n    font-family: Helvetica, Arial, sans-serif;\n}\n\n.modal-header h3 {\n    margin-top: 0;\n    color: #42b983;\n}\n\n.modal-body {\n    margin: 20px 0;\n}\n\n.modal-default-button {\n    float: right;\n}\n\n/*\n * The following styles are auto-applied to elements with\n * transition=\"modal\" when their visibility is toggled\n * by Vue.js.\n *\n * You can easily play with the modal transition by editing\n * these styles.\n */\n\n.modal-enter {\n    opacity: 0;\n}\n\n.modal-leave-active {\n    opacity: 0;\n}\n\n.modal-enter .modal-container,\n.modal-leave-active .modal-container {\n    -webkit-transform: scale(1.1);\n    transform: scale(1.1);\n}\n\n/* ==========================================================================\n    11. Readability\n========================================================================== */\n.btn-outline-success {\n    color: #2c642c;\n    border-color: #2c642c;\n}\n.btn-success,.btn-outline-success:not([disabled]):hover {\n    background-color: #2c642c;\n    border-color: #2c642c;\n}\n\n.btn-outline-danger {\n    color: #a82824;\n    border-color: #a82824;\n}\n.btn-danger,.btn-outline-danger:not([disabled]):hover {\n    background-color: #a82824;\n    border-color: #a82824;\n}\n\n.btn-outline-warning {\n    color: #9c5a05;\n    border-color: #9c5a05;\n}\n.btn-warning,.btn-outline-warning:not([disabled]):hover {\n    background-color: #9c5a05;\n    border-color: #9c5a05;\n    color: #fff;\n}\n\n.btn-primary,.btn-outline-primary:not([disabled]):hover {\n    background-color: #275c8b;\n    border-color: #275c8b;\n}\n\n.btn[disabled] {\n    opacity: 0.35;\n}\n\n.table-hover tbody tr:focus-within {\n    color: #212529;\n    background-color: rgba(0, 0, 0, 0.175);\n}\n\n/* ==========================================================================\n    12. Widgets\n========================================================================== */\n.card-title {\n    font-weight: bold;\n    text-transform: uppercase;\n}\n\n/* ==========================================================================\n    13. Tooltip\n========================================================================== */\n.tip-wrapper {\n    display: inline-block;\n    position: relative;\n    text-align: left;\n}\n.tip-wrapper .tip-handler {\n    text-decoration: underline dotted;\n}\n\n.tip-content h3 {margin: 12px 0;}\n\n.tip-content {\n    min-width: 300px;\n    max-width: 400px;\n    color: #EEEEEE;\n    background-color: #444444;\n    font-weight: normal;\n    font-size: 13px;\n    border-radius: 8px;\n    position: absolute;\n    z-index: 99999999;\n    box-sizing: border-box;\n    box-shadow: 0 1px 8px rgba(0,0,0,0.5);\n    visibility: hidden; opacity: 0; transition: opacity 0.8s;\n    padding: 0;\n}\n.tip-content.right {\n    top: 50%;\n    left: 100%;\n    margin-left: 20px;\n    transform: translate(0, -50%);\n}\n.tip-content.left {\n    top: 50%;\n    right: 100%;\n    margin-right: 20px;\n    transform: translate(0, -50%);\n}\n.tip-content.top {\n    top: -20px;\n    left: 50%;\n    transform: translate(-30%,-100%);\n}\n.tip-content.bottom {\n    top: 40px;\n    left: 50%;\n    transform: translate(-50%, 0);\n}\n\n.tip-wrapper:hover .tip-content {\n    visibility: visible; opacity: 1;\n}\n\n.tip-content img {\n    width: 400px;\n    border-radius: 8px 8px 0 0;\n}\n.tip-content .text-content {\n    padding: 10px 20px;\n}\n\n.tip-content.right i {\n    position: absolute;\n    top: 50%;\n    right: 100%;\n    margin-top: -12px;\n    width: 12px;\n    height: 24px;\n    overflow: hidden;\n}\n.tip-content.right i::after {\n    content: '';\n    position: absolute;\n    width: 12px;\n    height: 12px;\n    left: 0;\n    top: 50%;\n    transform: translate(50%,-50%) rotate(-45deg);\n    background-color: #444444;\n    box-shadow: 0 1px 8px rgba(0,0,0,0.5);\n}\n\n.tip-content.left i {\n    position: absolute;\n    top: 50%;\n    left: 100%;\n    margin-top: -12px;\n    width: 12px;\n    height: 24px;\n    overflow: hidden;\n}\n.tip-content.left i::after {\n    content: '';\n    position: absolute;\n    width: 12px;\n    height: 12px;\n    left: 0;\n    top: 50%;\n    transform: translate(-50%,-50%) rotate(-45deg);\n    background-color: #444444;\n    box-shadow: 0 1px 8px rgba(0,0,0,0.5);\n}\n\n.tip-content.top i {\n    position: absolute;\n    top: 100%;\n    left: 30%;\n    margin-left: -12px;\n    width: 24px;\n    height: 12px;\n    overflow: hidden;\n}\n.tip-content.top i::after {\n    content: '';\n    position: absolute;\n    width: 15px;\n    height: 15px;\n    left: 50%;\n    transform: translate(-50%,-50%) rotate(45deg);\n    background-color: #444444;\n    box-shadow: 0 1px 8px rgba(0,0,0,0.5);\n}\n\n.tip-content.bottom i {\n    position: absolute;\n    bottom: 100%;\n    left: 50%;\n    margin-left: -12px;\n    width: 24px;\n    height: 12px;\n    overflow: hidden;\n}\n.tip-content.bottom i::after {\n    content: '';\n    position: absolute;\n    width: 12px;\n    height: 12px;\n    left: 50%;\n    transform: translate(-50%,50%) rotate(45deg);\n    background-color: #444444;\n    box-shadow: 0 1px 8px rgba(0,0,0,0.5);\n}\n\n@font-face {\n    font-family: Cookie;\n    src: url(" + ___CSS_LOADER_URL_REPLACEMENT_3___ + ") format('truetype');\n}\n\n.bmc-button {\n    height: 60px;\n    display: inline-flex;\n    align-items: center;\n    color: #ffffff;\n    background-color: #FF813F;\n    border-radius: 12px;\n    border: 1px solid transparent;\n    padding: 0 24px 0 calc(24px + 25px + 8px);\n    font-size: 22px;\n    letter-spacing: 0.6px;\n    box-shadow: 0px 1px 2px rgba(190, 190, 190, 0.5);\n    -webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5);\n    font-family: 'Cookie', cursive;\n    -webkit-box-sizing: border-box;\n    box-sizing: border-box;\n    -o-transition: 0.3s all linear;\n    -webkit-transition: 0.3s all linear;\n    -moz-transition: 0.3s all linear;\n    -ms-transition: 0.3s all linear;\n    transition: 0.3s all linear;\n    background-image: \"../images/BMC Logo - Black.png\";\n    background-image: image-set(" + ___CSS_LOADER_URL_REPLACEMENT_4___ + ", " + ___CSS_LOADER_URL_REPLACEMENT_5___ + ");\n    background-size: 25px 33px;\n    background-repeat: no-repeat;\n    background-position: 24px;\n}", ""]);
// Exports
/* harmony default export */ __webpack_exports__["default"] = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js":
/*!******************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js ***!
  \******************************************************************************/
/***/ (function(module) {

"use strict";


/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (cssWithMappingToString) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item);

      if (item[2]) {
        return "@media ".concat(item[2], " {").concat(content, "}");
      }

      return content;
    }).join("");
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names


  list.i = function (modules, mediaQuery, dedupe) {
    if (typeof modules === "string") {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, ""]];
    }

    var alreadyImportedModules = {};

    if (dedupe) {
      for (var i = 0; i < this.length; i++) {
        // eslint-disable-next-line prefer-destructuring
        var id = this[i][0];

        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }

    for (var _i = 0; _i < modules.length; _i++) {
      var item = [].concat(modules[_i]);

      if (dedupe && alreadyImportedModules[item[0]]) {
        // eslint-disable-next-line no-continue
        continue;
      }

      if (mediaQuery) {
        if (!item[2]) {
          item[2] = mediaQuery;
        } else {
          item[2] = "".concat(mediaQuery, " and ").concat(item[2]);
        }
      }

      list.push(item);
    }
  };

  return list;
};

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/getUrl.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/getUrl.js ***!
  \*********************************************************************************/
/***/ (function(module) {

"use strict";


module.exports = function (url, options) {
  if (!options) {
    // eslint-disable-next-line no-param-reassign
    options = {};
  } // eslint-disable-next-line no-underscore-dangle, no-param-reassign


  url = url && url.__esModule ? url.default : url;

  if (typeof url !== "string") {
    return url;
  } // If url is already wrapped in quotes, remove them


  if (/^['"].*['"]$/.test(url)) {
    // eslint-disable-next-line no-param-reassign
    url = url.slice(1, -1);
  }

  if (options.hash) {
    // eslint-disable-next-line no-param-reassign
    url += options.hash;
  } // Should url be wrapped?
  // See https://drafts.csswg.org/css-values-3/#urls


  if (/["'() \t\n]/.test(url) || options.needQuotes) {
    return "\"".concat(url.replace(/"/g, '\\"').replace(/\n/g, "\\n"), "\"");
  }

  return url;
};

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./dashboard.vue?vue&type=style&index=0&lang=css& */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css&");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ __webpack_exports__["default"] = (_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var isOldIE = function isOldIE() {
  var memo;
  return function memorize() {
    if (typeof memo === 'undefined') {
      // Test for IE <= 9 as proposed by Browserhacks
      // @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
      // Tests for existence of standard globals is to allow style-loader
      // to operate correctly into non-standard environments
      // @see https://github.com/webpack-contrib/style-loader/issues/177
      memo = Boolean(window && document && document.all && !window.atob);
    }

    return memo;
  };
}();

var getTarget = function getTarget() {
  var memo = {};
  return function memorize(target) {
    if (typeof memo[target] === 'undefined') {
      var styleTarget = document.querySelector(target); // Special case to return head of iframe instead of iframe itself

      if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
        try {
          // This will throw an exception if access to iframe is blocked
          // due to cross-origin restrictions
          styleTarget = styleTarget.contentDocument.head;
        } catch (e) {
          // istanbul ignore next
          styleTarget = null;
        }
      }

      memo[target] = styleTarget;
    }

    return memo[target];
  };
}();

var stylesInDom = [];

function getIndexByIdentifier(identifier) {
  var result = -1;

  for (var i = 0; i < stylesInDom.length; i++) {
    if (stylesInDom[i].identifier === identifier) {
      result = i;
      break;
    }
  }

  return result;
}

function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];

  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var index = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3]
    };

    if (index !== -1) {
      stylesInDom[index].references++;
      stylesInDom[index].updater(obj);
    } else {
      stylesInDom.push({
        identifier: identifier,
        updater: addStyle(obj, options),
        references: 1
      });
    }

    identifiers.push(identifier);
  }

  return identifiers;
}

function insertStyleElement(options) {
  var style = document.createElement('style');
  var attributes = options.attributes || {};

  if (typeof attributes.nonce === 'undefined') {
    var nonce =  true ? __webpack_require__.nc : 0;

    if (nonce) {
      attributes.nonce = nonce;
    }
  }

  Object.keys(attributes).forEach(function (key) {
    style.setAttribute(key, attributes[key]);
  });

  if (typeof options.insert === 'function') {
    options.insert(style);
  } else {
    var target = getTarget(options.insert || 'head');

    if (!target) {
      throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
    }

    target.appendChild(style);
  }

  return style;
}

function removeStyleElement(style) {
  // istanbul ignore if
  if (style.parentNode === null) {
    return false;
  }

  style.parentNode.removeChild(style);
}
/* istanbul ignore next  */


var replaceText = function replaceText() {
  var textStore = [];
  return function replace(index, replacement) {
    textStore[index] = replacement;
    return textStore.filter(Boolean).join('\n');
  };
}();

function applyToSingletonTag(style, index, remove, obj) {
  var css = remove ? '' : obj.media ? "@media ".concat(obj.media, " {").concat(obj.css, "}") : obj.css; // For old IE

  /* istanbul ignore if  */

  if (style.styleSheet) {
    style.styleSheet.cssText = replaceText(index, css);
  } else {
    var cssNode = document.createTextNode(css);
    var childNodes = style.childNodes;

    if (childNodes[index]) {
      style.removeChild(childNodes[index]);
    }

    if (childNodes.length) {
      style.insertBefore(cssNode, childNodes[index]);
    } else {
      style.appendChild(cssNode);
    }
  }
}

function applyToTag(style, options, obj) {
  var css = obj.css;
  var media = obj.media;
  var sourceMap = obj.sourceMap;

  if (media) {
    style.setAttribute('media', media);
  } else {
    style.removeAttribute('media');
  }

  if (sourceMap && typeof btoa !== 'undefined') {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  } // For old IE

  /* istanbul ignore if  */


  if (style.styleSheet) {
    style.styleSheet.cssText = css;
  } else {
    while (style.firstChild) {
      style.removeChild(style.firstChild);
    }

    style.appendChild(document.createTextNode(css));
  }
}

var singleton = null;
var singletonCounter = 0;

function addStyle(obj, options) {
  var style;
  var update;
  var remove;

  if (options.singleton) {
    var styleIndex = singletonCounter++;
    style = singleton || (singleton = insertStyleElement(options));
    update = applyToSingletonTag.bind(null, style, styleIndex, false);
    remove = applyToSingletonTag.bind(null, style, styleIndex, true);
  } else {
    style = insertStyleElement(options);
    update = applyToTag.bind(null, style, options);

    remove = function remove() {
      removeStyleElement(style);
    };
  }

  update(obj);
  return function updateStyle(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) {
        return;
      }

      update(obj = newObj);
    } else {
      remove();
    }
  };
}

module.exports = function (list, options) {
  options = options || {}; // Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
  // tags it will allow on a page

  if (!options.singleton && typeof options.singleton !== 'boolean') {
    options.singleton = isOldIE();
  }

  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];

    if (Object.prototype.toString.call(newList) !== '[object Array]') {
      return;
    }

    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDom[index].references--;
    }

    var newLastIdentifiers = modulesToDom(newList, options);

    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];

      var _index = getIndexByIdentifier(_identifier);

      if (stylesInDom[_index].references === 0) {
        stylesInDom[_index].updater();

        stylesInDom.splice(_index, 1);
      }
    }

    lastIdentifiers = newLastIdentifiers;
  };
};

/***/ }),

/***/ "./resources/js/components/dashboard.vue":
/*!***********************************************!*\
  !*** ./resources/js/components/dashboard.vue ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _dashboard_vue_vue_type_template_id_57220a4e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dashboard.vue?vue&type=template&id=57220a4e& */ "./resources/js/components/dashboard.vue?vue&type=template&id=57220a4e&");
/* harmony import */ var _dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./dashboard.vue?vue&type=script&lang=js& */ "./resources/js/components/dashboard.vue?vue&type=script&lang=js&");
/* harmony import */ var _dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./dashboard.vue?vue&type=style&index=0&lang=css& */ "./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");



;


/* normalize component */

var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _dashboard_vue_vue_type_template_id_57220a4e___WEBPACK_IMPORTED_MODULE_0__.render,
  _dashboard_vue_vue_type_template_id_57220a4e___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/dashboard.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/dashboard.vue?vue&type=script&lang=js&":
/*!************************************************************************!*\
  !*** ./resources/js/components/dashboard.vue?vue&type=script&lang=js& ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_4_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-4[0].rules[0].use[0]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./dashboard.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-4[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=script&lang=js&");
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_4_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css&":
/*!********************************************************************************!*\
  !*** ./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css& ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_7_0_rules_0_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./dashboard.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/dist/cjs.js!./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-7[0].rules[0].use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=style&index=0&lang=css&");


/***/ }),

/***/ "./resources/js/components/dashboard.vue?vue&type=template&id=57220a4e&":
/*!******************************************************************************!*\
  !*** ./resources/js/components/dashboard.vue?vue&type=template&id=57220a4e& ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": function() { return /* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_template_id_57220a4e___WEBPACK_IMPORTED_MODULE_0__.render; },
/* harmony export */   "staticRenderFns": function() { return /* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_template_id_57220a4e___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns; }
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_dashboard_vue_vue_type_template_id_57220a4e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./dashboard.vue?vue&type=template&id=57220a4e& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=template&id=57220a4e&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=template&id=57220a4e&":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/dashboard.vue?vue&type=template&id=57220a4e& ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": function() { return /* binding */ render; },
/* harmony export */   "staticRenderFns": function() { return /* binding */ staticRenderFns; }
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [_vm._v("\n    test\n")])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ normalizeComponent; }
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        )
      }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ var __webpack_exec__ = function(moduleId) { return __webpack_require__(__webpack_require__.s = moduleId); }
/******/ __webpack_require__.O(0, ["js/vendors-jquery","js/vendors-vue","js/vendors-ui"], function() { return __webpack_exec__("./resources/js/dashboard.js"); });
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);