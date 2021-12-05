"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[619],{3612:function(e,t,a){var n=a(538),r=a(7152),i=a(1832),s=(a(1249),a(2707),a(2479),a(9601),a(9826),a(4916),a(5306),a(8620)),o=a(379),u=a(8424),l=a(2376),c=a(3067),d=a(2457);n.Z.use(s.ZP);var p={components:{EmailStatus:c.Z},extends:d.Z,props:{data:{type:Object,default:function(){return{}}},routes:{type:Object,required:!0}},data:function(){return{content:"",draw:this.data.draw,emails:this.data.emails,resendEmailUrls:this.data.resendEmailUrls}},computed:{emailsByDate:function(){return Object.values(this.emails).map((function(e){return Object.assign(e,e.mail)})).sort((function(e,t){return new Date(e.created_at)>new Date(t.created_at)?-1:1})).map((function(e){return e.created_at=new Date(e.created_at).toLocaleString("fr-FR"),e}))||[]},checkUpdates:function(){return!!Object.values(this.emails).find((function(e){return"error"!==e.mail.delivery_status}))}},created:function(){var e=this;l.Z.channel("draw."+this.draw).listen(".mail.update",(function(t){e.emails[t.id]&&(e.emails[t.id].mail.delivery_status=t.delivery_status,e.emails[t.id].mail.updated_at=t.updated_at)}))},validations:{content:{required:o.C1}},methods:{success:function(e){e.email.updated_at||(e.email.updated_at=new Date),this.$set(this.emails,e.email.id,e.email)},reset:function(){this.content=""},resend:function(e){return this.emails[e].mail.delivery_status="created",this.emails[e].mail.updated_at=(new Date).getTime(),(0,u.Z)(this.resendEmailUrls[e])},nl2br:function(e){return e.replace("\n","<br />")}}},m=a(3379),f=a.n(m),v=a(4686),h={insert:"head",singleton:!1},b=(f()(v.Z,h),v.Z.locals,(0,a(1900).Z)(p,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("ajax-form",{attrs:{action:e.routes.contactUrl,$v:e.$v,autoReset:!0},on:{success:e.success,reset:e.reset}},[a("fieldset",[a("div",{staticClass:"form-group"},[a("label",{attrs:{for:"mailContent"}},[e._v(e._s(e.$t("dearsanta.content.label")))]),e._v(" "),a("div",{staticClass:"input-group"},[a("textarea",{directives:[{name:"model",rawName:"v-model",value:e.content,expression:"content"}],class:{"form-control":!0,"is-invalid":e.$v.content.$error},attrs:{id:"mailContent",name:"content",placeholder:e.$t("dearsanta.content.placeholder"),"aria-invalid":e.$v.content.$error},domProps:{value:e.content},on:{blur:function(t){return e.$v.content.$touch()},input:function(t){t.target.composing||(e.content=t.target.value)}}}),e._v(" "),e.$v.content.required?e._e():a("div",{staticClass:"invalid-tooltip"},[e._v(e._s(e.$t("validation.custom.dearSanta.content.required")))])])])])]),e._v(" "),a("table",{staticClass:"table table-hover"},[a("caption",[e._v(e._s(e.$t("dearsanta.list.caption")))]),e._v(" "),a("thead",[a("tr",{staticClass:"table-active"},[a("th",{attrs:{scope:"col"}},[e._v("\n                    "+e._s(e.$t("dearsanta.list.date"))+"\n                ")]),e._v(" "),a("th",{attrs:{scope:"col"}},[e._v("\n                    "+e._s(e.$t("dearsanta.list.body"))+"\n                ")]),e._v(" "),a("th",{attrs:{scope:"col"}},[e._v("\n                    "+e._s(e.$t("dearsanta.list.status"))+"\n                ")])])]),e._v(" "),a("tbody",[e._l(e.emailsByDate,(function(t){return a("tr",{key:t.id,staticClass:"email"},[a("td",[e._v(e._s(t.created_at))]),e._v(" "),a("td",[a("p",[e._v(e._s(e.nl2br(t.mail_body)))])]),e._v(" "),a("td",[a("email-status",{attrs:{delivery_status:t.mail.delivery_status,last_update:t.mail.updated_at},on:{redo:function(a){return e.resend(t.id)}}})],1)])})),e._v(" "),0===e.emails.length?a("tr",{staticClass:"no-email"},[a("td",{attrs:{colspan:"3"}},[e._v("\n                    "+e._s(e.$t("dearsanta.list.empty"))+"\n                ")])]):e._e()],2)])],1)}),[],!1,null,null,null).exports),g=a(3181);n.Z.use(r.Z);var y=document.documentElement.lang.substr(0,2),x=new r.Z({locale:y,messages:i.Z});window.app=new n.Z({mixins:[(0,g.F)(b)],i18n:x,data:{routes:window.global.routes},mounted:function(){document.body.classList.add("cssLoading"),setTimeout((function(){return document.body.classList.remove("cssLoading")}),0)}})},5374:function(e,t,a){a(7042);t.Z={data:function(){return{data:null,states:{},previousState:null,state:null}},methods:{send:function(e,t){this.data=t;var a=this.states[this.state][e]||this.states[this.state]["*"];a&&(this.previousState=this.state,this.state=a,(this["state"+this.state[0].toUpperCase()+this.state.slice(1)]||function(){})())}}}},3181:function(e,t,a){a.d(t,{F:function(){return p}});a(3371);var n={},r=a(1900),i=(0,r.Z)(n,(function(){var e=this,t=e.$createElement;return(e._self._c||t)("span",[e._v(e._s(e.$t("common.internal")))])}),[],!1,null,null,null).exports,s=(a(1539),a(8674),a(7727),a(8424)),o=(a(9653),{props:{delay:{type:Number,required:!0,default:100}},data:function(){return{show:!1}},mounted:function(){var e=this;setTimeout((function(){e.show=!0}),this.delay)}}),u={components:{Timer:(0,r.Z)(o,(function(){var e=this,t=e.$createElement;return(e._self._c||t)("span",[e.show?e._t("default"):e._e()],2)}),[],!1,null,null,null).exports},props:{fetchurl:{type:String,required:!0}},data:function(){return{loading:!1}},mounted:function(){var e=this;this.loading=!0,(0,s.Z)(this.fetchurl).then((function(t){e.$emit("success",t)})).catch((function(t){e.$emit("error")})).finally((function(){e.loading=!1}))}},l=(0,r.Z)(u,(function(){var e=this.$createElement;return(this._self._c||e)("timer",{attrs:{delay:2e3}})}),[],!1,null,null,null).exports,c=a(2457),d=a(5374),p=function(e){return e=e||c.Z,{mixins:[d.Z],components:{Failure:i,Fetcher:l,Form:e},el:"#wrap",data:{state:"Fetcher",states:Object.freeze({Fetcher:{success:"Form",failure:"Failure"}})}}};p()},7105:function(e,t,a){var n=a(1832),r=a(8307);r.defaults.transition="slide",r.defaults.theme.ok="btn btn-primary",r.defaults.theme.cancel="btn btn-danger",r.defaults.theme.input="form-control",r.defaults.notifier.position="top-right";var i=document.documentElement.lang.substr(0,2);r.errorAlert||r.dialog("errorAlert",(function(){return{setup:function(){return{options:{frameless:!1,movable:!1,closableByDimmer:!1,maximizable:!1,resizable:!1},buttons:[{text:r.defaults.glossary.ok,key:27,invokeOnClose:!0,className:r.defaults.theme.ok}],focus:{element:0,select:!1}}},build:function(){var e='<span class="fa fa-times-circle fa-2x" style="vertical-align:middle;color:#e10000;"></span> '+n.Z[i].common.internal;this.setHeader(e)}}}),!0,"alert"),t.Z=r},2376:function(e,t,a){var n=a(4554);window.Pusher=a(6606),t.Z=new n.Z({broadcaster:"pusher",key:window.global.pusher.key,wsHost:window.global.pusher.host,wsPort:443,disableStats:!0,enabledTransports:["ws","wss"]})},8424:function(e,t,a){a.d(t,{Z:function(){return l}});a(4747),a(9720),a(6992),a(1539),a(8783),a(3948),a(1637),a(8674),a(2526),a(1817),a(2165),a(7042),a(8309),a(1038),a(7941),a(7327),a(5003),a(9337);var n=a(8261);function r(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function i(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?r(Object(a),!0).forEach((function(t){s(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):r(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}function s(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}function o(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var a=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null==a)return;var n,r,i=[],s=!0,o=!1;try{for(a=a.call(e);!(s=(n=a.next()).done)&&(i.push(n.value),!t||i.length!==t);s=!0);}catch(e){o=!0,r=e}finally{try{s||null==a.return||a.return()}finally{if(o)throw r}}return i}(e,t)||function(e,t){if(!e)return;if("string"==typeof e)return u(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);"Object"===a&&e.constructor&&(a=e.constructor.name);if("Map"===a||"Set"===a)return Array.from(e);if("Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a))return u(e,t)}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function u(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,n=new Array(t);a<t;a++)n[a]=e[a];return n}function l(e,t,a,r){var s=void 0;return(0,n.Kn)(a)||(0,n.kJ)(a)?(s=new FormData,Object.entries(a).forEach((function(e){var t=o(e,2),a=t[0],n=t[1];return s.append(a,n)}))):a&&(s=new URLSearchParams(a)),window.fetch(new Request(e,{method:t||"GET",headers:i({"X-HASH-IV":window.location.hash.substr(1),"X-Requested-With":"XMLHttpRequest"},r||{}),body:s})).then((function(e){var t;return t=(r||{}).responseType?e.text():e.json(),e.ok?t:t.then((function(e){return Promise.reject(e)}))}))}},8261:function(e,t,a){a.d(t,{LR:function(){return n},Kn:function(){return r},kJ:function(){return i}});a(7042),a(6992),a(1539),a(8783),a(3948),a(285),a(1637);var n=function(e,t,a){var n=new Blob([e]);n=n.slice(0,n.size,a);var r=window.URL.createObjectURL(n),i=document.createElement("a");i.href=r,i.setAttribute("download",t),document.body.appendChild(i),i.click()},r=function(e){return"[object Object]"===Object.prototype.toString.call(e)},i=function(e){return"[object Array]"===Object.prototype.toString.call(e)}},1832:function(e,t){t.Z={fr:{organizer:{list:{name:"Nom",email:"Adresse Email",status:"Status d'envoi de l'email",caption:"Liste des participants",withdraw:"Retirer"},up_and_sent:"Modifié avec succès !",withdrawn:"{name} ne participe plus à l'évènement.",deleted:"Toutes les données ont été supprimées",download:{button:"Télécharger le récapitulatif","button-tooltip":{title:"Récapitulatif",content:"Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici."},button_initial:"Télécharger le récapitulatif initial","button_initial-tooltip":{title:"Récapitulatif initial",content:"Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici."},button_final:"Télécharger le récapitulatif complété","button_final-tooltip":{title:"Récapitulatif complété",explain:"Les données sont les mêmes que dans le récapitulatif initial mais ont été ajoutées aux exclusions de charque participant la cible qu'il a eu durant cet évènement. A moins que ceci amène à un blocage où on ne puisse plus trouver de cible à chaque participant pour la prochaine fois.",limit:"Compte tenu de la date de l'évènement définie, cette fonctionnalité n'est disponible que du {expires_at} au {deleted_at}."}},purge:{button:"Supprimer tout",confirm:{title:"Êtes-vous sûr de vouloir supprimer la totalité des données avant le nettoyage automatique le {deletion} ?",body_final:"Vous ne pourrez plus télécharger le récapitulatif des tirages de cet évènement et les participants ne pourront plus écrire à leur père noël secret. Cette action ne peut être annulée.",body_expired:"Vous ne pourrez plus télécharger le récapitulatif de cet évènement. Cette action ne peut être annulée.",body_nofinal:"Vous ne pourrez plus télécharger le récapitulatif de cet évènement et les participants ne pourront plus écrire à leur père noël secret. Cette action ne peut être annulée.",value:"Supprimer toutes les données",help:'Saisir "[+:verification]" en dessous pour confirmer.',ok:"Ok",cancel:"Annuler"}},withdraw:{button:"Retirer",confirm:{title:"Êtes-vous sûr de vouloir retirer {name} de l'évènement ?",body:"Tous les messages reçu de sa cible seront transmis à son nouveau père noël secret. Cette action ne peut être annulée.",value:"Annuler la participation",help:'Saisir "[+:verification]" en dessous pour confirmer.',ok:"Ok",cancel:"Annuler"}},expired:"Votre évènement est passé ({expires_at}). Certaines actions ne sont plus disponibles, comme réenvoyer le nom de la cible à un participant."},faq:{nav:{go:"Allez, c'est parti !",contact:"J'ai encore une question"},categories:{general:"Générales",technical:"Techniques"},questions:{general:{"Pourquoi avoir développé SecretSanta.fr ?":"Le développeur faisait fréquemment des soirées secret santa avec des amis ou des collègues avec les prénoms de chacun dans un chapeau. Chacun piochait un papier et c'est arrivé plusieurs fois que quelqu'un tombe sur son prénom. Parfois on relançait le tirage, parfois la personne ne le disais pas et je trouvais ça dommage. Alors il a eu l'idée d'en faire un outils. Le but était de faire en sorte que tout soit automatique et que personne ne se pioche soit même. Même l'organisateur participait comme tout le monde puisqu'il ne pouvait pas savoir qui avait pioché qui. Après sont venus les exclusions etc.","Comment ce site peut fonctionner en étant gratuit ?":"SecretSanta.fr est complètement gratuit pour les utilisateurs, pas pour le développeur qui paye les différents frais. Il n'y a aucune publicité ni revente d'informations.","Je me suis trompé dans mon adresse email quand j'ai organisé mon secret santa, comment faire ?":"Pour corriger ce problème, soit vous organizer un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son père noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.","J'ai supprimé mon email d'accès au panneau d'organisateur, comment faire ?":"Pour corriger ce problème, soit vous organizer un autre tirage, soit vous demandez à un des participant de vous transmettre l'adresse web qui lui permet d'écrire à son père noël secret et vous l'envoyez par mail à l'adresse suivante : help@secretsanta.fr. Après vérification et durant son temps libre, le développeur pourra peut-être vous aider.","Je me suis trompé dans l'adresse d'un participant":"Lorsque vous avez organisé votre secret santa, vous avez dû recevoir un email avec un lien vers un panneau d'organisateur. Vous pourrez modifier l'adresse email de chaque participant depuis cette interface.","Un des participants n'a pas reçu l'email, que faire ?":"Alors même que l'adresse renseignée est bonne, il arrive que l'email se perde, finisse en spam ou qu'un problème survienne faisant que l'email n'est pas arrivé. Depuis votre panneau d'organisateur, vous pouvez retrouver un bouton à côté du status de réception de l'email du participant pour pouvoir réenvoyer le-dit email. Comme ça, le participant pourra enfin savoir qui est sa cible et comment parler à son Santa.","Quand sont supprimés mes données personnelles ?":"Toutes vos données d'un tirage sont supprimées 7 jours après la date d'expiration. Ce délai a été fixé afin d'envoyer à l'organisateur la liste des participants avec leur cible piochée par mail afin d'aider à l'organisation d'un secretsanta avec les mêmes personnes plus tard en évitant de retomber sur les mêmes cibles.","J'ai oublié un participant, comment je peux le rajouter ?":"Malheureusement, de par sa conception, SecretSanta.fr ne permet pas de rajouter quelqu'un une fois le tirage effectué. Il vous reste cependant une option, autre que relancer le tirage : vous pouvez attribuez à cette personne votre propre cible et offrir vous même un cadeau à ce nouveau participant.","Qui peut savoir la liste des cibles ?":"Pour faire court : personne. Pour faire long : "},technical:{"Quelles données sont stockées et pourquoi ?":"Sont conservés pour chaque participant : nom et adresse email, pour chaque organisation, le titre et le contenu du mail envoyé ainsi que chaque message envoyé entre participant via le lien reçu par mail (appelé 'cher papa noël'). Elles sont conservées pour deux raisons : d'abord pour permettre cette dernière fonctionnalité qui permet d'écrire à son père noël secret. Ensuite pour permettre de réenvoyer les emails en cas d'erreur d'adresse.","Comment sont stockées les données ?":"Chaque élement est chiffré en AES-256 avec une clef unique par organisation. Cette clef n'est pas stockée et est envoyée à chaque participant. L'administrateur ne peut donc jamais accéder aux données sans action de votre part. Chaque fois que vous effectuez une action, vous utilisez automatiquement cette clef qui n'est que prétée à SecretSanta pour faire l'action demandée sans jamais l'enregistrer.","Je voudrais supprimer mes données, comment faire ?":"De part la façon dont sont stockées les données, l'administrateur est dans l'incapacité de savoir quelle donnée appartient à qui. Seul l'organisateur est en capacité de supprimer les données de tous les participants d'un coup. Sinon, ces données sont automatiquement supprimées peu après la fin de l'évènement.","J'aimerais vérifier par moi même le code source, où puis-je le trouver ?":"Avec grand plaisir ! Le code source se trouve à l'adresse suivante : https://framagit.com/Korko/SecretSanta. Vous pouvez aussi trouver le lien en haut à droite de la page principale, dans le petit ruban rouge."}}},common:{internal:"Une erreur est survenue",fetcher:{load:"Charger",loading:"Chargement en cours..."},form:{send:"Envoyer",sending:"Envoi en cours",sent:"Envoyé",reset:"Recommencer"},modal:{close:"Fermer"},email:{redo:"Ré-envoyer",status:{created:"En attente d'envoi",sending:"Envoi en cours",sent:"Envoyé",error:"Erreur",received:"Reçu"}}},validation:{accepted:"Le champ {attribute} doit être accepté.",active_url:"Le champ {attribute} n'est pas une URL valide.",after:"Le champ {attribute} doit être une date postérieure au {date}.",alpha:"Le champ {attribute} doit seulement contenir des lettres.",alpha_dash:"Le champ {attribute} doit seulement contenir des lettres, des chiffres et des tirets.",alpha_num:"Le champ {attribute} doit seulement contenir des chiffres et des lettres.",array:"Le champ {attribute} doit être un tableau.",before:"Le champ {attribute} doit être une date antérieure au {date}.",between:{numeric:"La valeur de {attribute} doit être comprise entre {min} et {max}.",file:"Le fichier {attribute} doit avoir une taille entre {min} et {max} kilo-octets.",string:"Le texte {attribute} doit avoir entre {min} et {max} caractères.",array:"Le tableau {attribute} doit avoir entre {min} et {max} éléments."},boolean:"Le champ {attribute} doit être vrai ou faux.",confirmed:"Le champ de confirmation {attribute} ne correspond pas.",date:"Le champ {attribute} n'est pas une date valide.",date_format:"Le champ {attribute} ne correspond pas au format {format}.",different:"Les champs {attribute} et {other} doivent être différents.",digits:"Le champ {attribute} doit avoir {digits} chiffres.",digits_between:"Le champ {attribute} doit avoir entre {min} et {max} chiffres.",email:"Le champ {attribute} doit être une adresse email valide.",exists:"Le champ {attribute} sélectionné est invalide.",filled:"Le champ {attribute} est obligatoire.",image:"Le champ {attribute} doit être une image.",in:"Le champ {attribute} est invalide.",integer:"Le champ {attribute} doit être un entier.",ip:"Le champ {attribute} doit être une adresse IP valide.",json:"Le champ {attribute} doit être un document JSON valide.",max:{numeric:"La valeur de {attribute} ne peut être supérieure à {max}.",file:"Le fichier {attribute} ne peut être plus gros que {max} kilo-octets.",string:"Le texte de {attribute} ne peut contenir plus de {max} caractères.",array:"Le tableau {attribute} ne peut avoir plus de {max} éléments."},mimes:"Le champ {attribute} doit être un fichier de type : {values}.",min:{numeric:"La valeur de {attribute} doit être supérieure à {min}.",file:"Le fichier {attribute} doit être plus gros que {min} kilo-octets.",string:"Le texte {attribute} doit contenir au moins {min} caractères.",array:"Le tableau {attribute} doit avoir au moins {min} éléments."},not_in:"Le champ {attribute} sélectionné n'est pas valide.",numeric:"Le champ {attribute} doit contenir un nombre.",regex:"Le format du champ {attribute} est invalide.",required:"Le champ {attribute} est obligatoire.",required_if:"Le champ {attribute} est obligatoire quand la valeur de {other} est {value}.",required_unless:"Le champ {attribute} est obligatoire sauf si {other} est {values}.",required_with:"Le champ {attribute} est obligatoire quand {values} est présent.",required_with_all:"Le champ {attribute} est obligatoire quand {values} est présent.",required_without:"Le champ {attribute} est obligatoire quand {values} n'est pas présent.",required_without_all:"Le champ {attribute} est requis quand aucun de {values} n'est présent.",same:"Les champs {attribute} et {other} doivent être identiques.",size:{numeric:"La valeur de {attribute} doit être {size}.",file:"La taille du fichier de {attribute} doit être de {size} kilo-octets.",string:"Le texte de {attribute} doit contenir {size} caractères.",array:"Le tableau {attribute} doit contenir {size} éléments."},string:"Le champ {attribute} doit être une chaîne de caractères.",timezone:"Le champ {attribute} doit être un fuseau horaire valide.",unique:"La valeur du champ {attribute} est déjà utilisée.",url:"Le format de l'URL de {attribute} n'est pas valide.",recaptcha:"Le captcha n'a pas pu être validé.",custom:{"g-recaptcha-response":{required:"Le captcha est obligatoire",recaptcha:"Le captcha est invalide"},randomform:{title:{required:"Le titre de l'email est requis."},content:{required:"Le contenu de l'email est requis.",contains:"Le contenu de l'email doit contenir le mot {TARGET} pour indiquer la cible."},expiration:{required:"La date d'expiration est requise.",min:"La date d'expiration ne peut pas précéder demain.",max:"La date d'expiration ne peut pas dépasser un an.",format:"La date d'expiration doit respecter le format année-mois-jour exemple: 2022-02-05."},participants:{length:"Il faut au moins 3 participants"},participant:{name:{required:"Ce participant est requis (au moins 3 personnes).",distinct:"Ce participant n'a pas un nom unique."},email:{required:"Cette adresse email est requise.",format:"Le format de cette adresse est invalide."}}},dearSanta:{content:{required:"Le contenu du message est requis."}},organizer:{email:{required:"La nouvelle adresse est requise.",format:"Le format de l'adresse n'est pas valide."}}},attributes:{name:"Nom",username:"Pseudo",email:"E-mail",first_name:"Prénom",last_name:"Nom",password:"Mot de passe",password_confirmation:"Confirmation du mot de passe",city:"Ville",country:"Pays",address:"Adresse",phone:"Téléphone",mobile:"Portable",age:"Age",sex:"Sexe",gender:"Genre",day:"Jour",month:"Mois",year:"Année",hour:"Heure",minute:"Minute",second:"Seconde",title:"Titre",content:"Contenu",description:"Description",excerpt:"Extrait",date:"Date",time:"Heure",available:"Disponible",size:"Taille","g-recaptcha-response":"Recaptcha"}},form:{nav:{what:"Qu'est-ce que c'est ?",how:"Comment faire ?",go:"Allez, c'est parti !",faq:"Foire Aux Questions"},title:"Secret Santa .fr",subtitle:"Offrez-vous des cadeaux... secrètement !",fyi:"Pour votre information",section:{what:{title:"Qu'est-ce que c'est ?",subtitle:"Description du Secret Santa",heading1:"Le principe",content1:"Secret Santa est un moyen drôle et original de s'offrir anonymement des cadeaux entre amis, collègues...\nLe déroulement est simple : chaque participant reçoit, de façon aléatoire, le nom de la personne à qui il devra faire un cadeau.\nLe montant du cadeau est généralement fixé au préalable (2€, 5€, 10€...)\nLe but n'est pas forcément de faire un beau cadeau mais d'être créatif !",notice:"secretsanta.fr est entièrement gratuit et sans publicité.\nTout est payé par le développeur lui-même.\nSi cet outil vous plait, pensez à faire un don.\n{button}"},how:{title:"Comment faire ?",subtitle:"Vous allez voir, c'est très simple !",heading1:"Première étape : lister les participants",content1:'Grâce aux boutons "Ajouter un participant" et "Enlever un participant", il est possible d\'ajuster le nombre de personnes.\nPour chaque personne, indiquez un nom/prénom ou un pseudonyme, et une adresse email. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.\nA noter que secretsanta.fr est conçu de façon à ce qu\'une personne ne puisse pas se piocher elle-même.',heading2:"Deuxième étape : préciser les exclusions",content2:"Ajoutez des exclusions. Si vous ne voulez pas que deux participants puissent se piocher l'un l'autre, remplissez le champ \"Exclusions\".",heading3:"Troisième étape : préparer l'e-mail",content3:'Il ne vous reste plus qu\'à remplir le titre et le corps du courriel que les participants recevront.\nLe mot clef "{TARGET}" est obligatoire dans le corps du message afin de donner à chaque personne sa "cible".\n(Optionel) Vous pouvez aussi utiliser le mot clef "{SANTA}" qui sera remplacé par le nom du destinataire du message.',notice:"secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.\nCelles-ci sont chiffrées pour être inutilisables sans action de votre part.\nAucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.\nLe code source est disponible sur {link}",heading4:"Et après ?",content4:"Jusqu'au jour de l'évènement spécifiée à la fin, les participants peuvent écrire un mot à leur Santa depuis un lien qu'ils reçoivent par email. Mais celui-ci ne peut pas répondre, au risque de dévoiler son identité.\nL'organisateur dispose aussi d'une interface dédiée pour retrouver le récapitulatif des participants et des exclusions."},go:{title:"À vous de jouer !",subtitle:"Remplissez, cliquez et c'est parti !"}},waiting:'Formulaire en cours de création. Si ce message reste affiché, essayez de rafraichir la page, sinon contactez moi par mail (<a href="mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) ou via <a href="https://github.com/Korko">GitHub</a>. Merci.',success:"Envoyé avec succès !",participants:{title:"Détails des participants",import:"Importer depuis un fichier",importing:"Import en cours",caption:"Liste des participats"},participant:{organizer:"Organisateur",name:{label:"Nom ou pseudonyme",placeholder:"exemple : Paul ou Korko"},email:{label:"Adresse e-mail",placeholder:"exemple : michel@aol.com"},exclusions:{label:"Exclusions",placeholder:"Aucune exclusion",noOptions:"Liste vide",noResult:"Aucun résultat"},remove:"Enlever",add:"Ajouter un participant"},csv:{title:"Importer une liste de participants depuis un fichier CSV",help:"Comment créer un fichier CSV avec {excel} Microsoft Office Excel {elink} ou {calc} Libre Office Calc {elink}",format:"Afin que votre fichier CSV fonctionne, voici le format attendu :",column1:"Nom du participant",column2:"Adresse e-mail",column3:"Exclusions (noms séparés par une virgule)",warning:"Attention, l'import de ces données supprimera les participants déjà renseignés.",cancel:"Annuler",import:"Importer",importError:"Une erreur est survenue lors de l'import.",importSuccess:"L'import a été effectué avec succès."},mail:{title:{label:"Titre du mail",placeholder:"ex : Soirée secretsanta du 23 décembre chez Martin, {SANTA} ta cible est..."},content:{label:"Contenu du mail",placeholder:"ex : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !",tip1:'Utilisez "{santa}&#123;SANTA&#125;{close}" pour le nom de celui qui recevra le mail et "{target}&#123;TARGET&#125;{close}" pour le nom de sa cible.',tip2:"Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau."},post:"----\nPour écrire à votre Secret Santa, allez sur la page suivante : {link}\nvia SecretSanta.fr"},"data-expiration":"Date de l'évènement : ","data-expiration-tooltip":{title:"Date de l'évènement",interface:"Une interface dédiée vous permettra d'accéder à un récapitulatif des participants jusqu'au jour de l'évènement.",deletion:"Toutes les données stockées seront supprimées une semaine après."},submit:"Lancez l'aléatoire !",paypal:{alt:"PayPal, le réflexe sécurité pour payer en ligne"},internalError:"Erreur interne"},dearsanta:{list:{date:"Date d'envoi",body:"Corps du message",status:"Status de réception de l'email",empty:"Aucun email envoyé pour le moment",caption:"Liste des emails envoyés au Père Noël"},content:{label:"Contenu du mail",placeholder:"Cher Papa Noël..."}}}}},4686:function(e,t,a){var n=a(1519),r=a.n(n)()((function(e){return e[1]}));r.push([e.id,"#form form{margin-bottom:20px}.email td p{background:linear-gradient(#fff 30%,rgba(255,255,255,0)),linear-gradient(rgba(255,255,255,0),#fff 70%) 0 100%,radial-gradient(50% 0,farthest-side,rgba(0,0,0,.2),transparent),radial-gradient(50% 100%,farthest-side,rgba(0,0,0,.2),transparent) 0 100%;background:linear-gradient(#fff 30%,rgba(255,255,255,0)),linear-gradient(rgba(255,255,255,0),#fff 70%) 0 100%,radial-gradient(farthest-side at 50% 0,rgba(0,0,0,.2),transparent),radial-gradient(farthest-side at 50% 100%,rgba(0,0,0,.2),transparent) 0 100%;background-attachment:local,local,scroll,scroll;background-repeat:no-repeat;background-size:100% 40px,100% 40px,100% 14px,100% 14px;max-height:10em;overflow:auto}.email:hover td p{background:rgba(0,0,0,.075),rgba(0,0,0,.075),radial-gradient(50% 0,farthest-side,rgba(0,0,0,.2),transparent),radial-gradient(50% 100%,farthest-side,rgba(0,0,0,.2),transparent) 0 100%;background:rgba(0,0,0,.075),rgba(0,0,0,.075),radial-gradient(farthest-side at 50% 0,rgba(0,0,0,.2),transparent),radial-gradient(farthest-side at 50% 100%,rgba(0,0,0,.2),transparent) 0 100%}.no-email{text-align:center}table{table-layout:fixed}table th:first-child,table th:last-child{width:12em}table caption{display:none}",""]),t.Z=r},5852:function(e,t,a){var n=a(1519),r=a.n(n)()((function(e){return e[1]}));r.push([e.id,".input-group-append[data-v-48455282]{display:inline;margin-left:10px}.error[data-v-48455282]{color:red}.sent[data-v-48455282]{color:orange}.received[data-v-48455282]{color:green}",""]),t.Z=r},9640:function(e,t,a){a.d(t,{Z:function(){return l}});a(1539),a(8674),a(7727),a(9601);var n=a(9755),r=a.n(n),i=a(8424),s=a(7105),o={props:{action:{type:String,default:""},button:{type:Boolean,default:!0},buttonSend:{type:String,default:""},buttonSending:{type:String,default:""},buttonSent:{type:String,default:""},buttonReset:{type:String,default:""},$v:{type:Object,default:null},sendIcon:{type:String,default:"paper-plane"},autoReset:{type:Boolean,default:!1}},data:function(){return{fieldErrors:[],sending:!1,sent:!1}},watch:{sending:function(){this.$emit("change",this.sending)}},methods:{fieldError:function(e){return this.fieldErrors[e]?this.fieldErrors[e][0]:null},call:function(e,t){var a=this;if(!this.sending&&!this.sent)return this.$v&&this.$v.$touch(),(!this.$v||!this.$v.$invalid)&&(this.sending=!0,(0,i.Z)(e,"POST",t.data).then((function(e){a.fieldErrors=[],a.sending=!1,a.autoReset||(a.sent=!0),(t.success||t.then||function(){})(e),(t.complete||t.finally||function(){})(),a.$emit("success",e),a.autoReset&&a.onReset()})).catch((function(e){var n,r;e&&e.errors&&(a.fieldErrors=e.errors),a.sending=!1,(n=t.error||t.catch)&&n(e),(r=t.complete||t.finally)&&r(),!n&&!r&&a.fieldErrors.length>0&&s.Z.errorAlert(a.$t("form.internalError")),a.$emit("error")})))},onSubmit:function(){this.submit()},onReset:function(){this.$emit("reset"),this.fieldErrors=[],this.$v.$reset(),this.sending=!1,this.sent=!1},submit:function(e,t){this.$emit("beforeSubmit"),e=e||r()(this.$el).serialize();var a=this.call(this.action,Object.assign({data:e},t));return this.$emit("afterSubmit"),a}}},u=(0,a(1900).Z)(o,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("form",{attrs:{action:e.action,method:"post",autocomplete:"off"},on:{submit:function(t){return t.preventDefault(),e.onSubmit.apply(null,arguments)},reset:function(t){return t.preventDefault(),e.onReset.apply(null,arguments)}}},[a("fieldset",{attrs:{disabled:e.sending||e.sent}},[e._t("default",null,null,{sending:e.sending,sent:e.sent,submit:e.submit,onSubmit:e.onSubmit,onReset:e.onReset,fieldError:e.fieldError})],2),e._v(" "),e.button?a("fieldset",[a("button",{staticClass:"btn btn-primary btn-lg",attrs:{type:"submit",disabled:e.sent||e.sending}},[e.sent?a("span",[a("span",{staticClass:"fas fa-check-circle"}),e._v(" "+e._s(e.buttonSent||e.$t("common.form.sent")))]):e.sending?a("span",[a("span",{staticClass:"fas fa-spinner"}),e._v(" "+e._s(e.buttonSending||e.$t("common.form.sending")))]):a("span",[a("span",{class:"fas fa-"+e.sendIcon}),e._v(" "+e._s(e.buttonSend||e.$t("common.form.send")))])]),e._v(" "),e.sent?a("button",{staticClass:"btn btn-primary btn-lg",attrs:{type:"reset"}},[a("span",[a("span",{staticClass:"fas fa-backward"}),e._v(" "+e._s(e.buttonReset||e.$t("common.form.reset")))])]):e._e()]):e._e()])}),[],!1,null,null,null),l=u.exports},3067:function(e,t,a){a.d(t,{Z:function(){return u}});a(9653);var n={props:{can_redo:{type:Boolean,default:!0},delivery_status:{type:String,required:!0},last_update:{type:[String,Number],default:null},disabled:{type:Boolean,default:!1},rateLimit:{type:Number,default:3e5}},data:function(){return{recent:!0,recentUpdateTimeout:null}},computed:{icon:function(){return{created:"fas fa-spinner",sending:"fas fa-spinner",sent:"fas fa-check",received:"fas fa-check",error:"fas fa-exclamation-triangle"}[this.delivery_status]}},watch:{last_update:{immediate:!0,handler:function(){var e=this;this.recent=this.isRecent(),this.recent&&(this.recentUpdateTimeout&&clearTimeout(this.recentUpdateTimeout),this.recentUpdateTimeout=setTimeout((function(){e.recent=e.isRecent()}),this.rateLimit-this.getDelay()))}}},methods:{getDelay:function(){return(new Date).getTime()-new Date(this.last_update).getTime()},isRecent:function(){return this.getDelay()<this.rateLimit}}},r=a(3379),i=a.n(r),s=a(5852),o={insert:"head",singleton:!1},u=(i()(s.Z,o),s.Z.locals,(0,a(1900).Z)(n,(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("span",[e._v(e._s(e.$t("common.email.status."+e.delivery_status))+" "),a("i",{class:[e.icon,e.delivery_status]})]),e._v(" "),e.can_redo||"error"===e.delivery_status?a("button",{staticClass:"btn btn-outline-secondary",attrs:{disabled:e.recent||e.disabled,type:"button"},on:{click:function(t){return e.$emit("redo")}}},[a("i",{staticClass:"fas fa-redo"}),e._v("\n        "+e._s(e.$t("common.email.redo"))+"\n    ")]):e._e()])}),[],!1,null,"48455282",null).exports)},2457:function(e,t,a){a.d(t,{Z:function(){return r}});var n={components:{AjaxForm:a(9640).Z}},r=(0,a(1900).Z)(n,(function(){var e=this.$createElement;return(this._self._c||e)("ajax-form")}),[],!1,null,null,null).exports}},function(e){e.O(0,[898],(function(){return t=3612,e(e.s=t);var t}));e.O()}]);