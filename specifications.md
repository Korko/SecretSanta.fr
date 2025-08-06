# SecretSanta.fr - Spécifications complètes

## Concept général

Application Laravel permettant d'organiser des tirages au sort Secret Santa avec chiffrement fort et confidentialité maximale. Aucune inscription obligatoire, toutes les données personnelles sont chiffrées et la clé de chiffrement n'est jamais stockée sur le serveur.

## Architecture de sécurité

### Système de chiffrement à deux niveaux
- **Clé Master** : générée côté client, chiffre toutes les données du tirage
- **Clés individuelles** : une par participant, chiffrent la clé master
- La clé master n'est jamais stockée en clair sur le serveur
- Chaque participant reçoit un lien unique avec sa clé en hash (pas dans l'URL)

### Transmission et récupération des clés
- Lien unique par participant : `domain.com/draw/123#individualkeyhere`
- Hash transmis via AJAX uniquement quand nécessaire
- **Récupération organisateur** : via lien d'un participant volontaire
- **Régénération** : l'organisateur peut recréer le lien d'un participant

### Authentification utilisateurs inscrits
- **Zero-knowledge** sur l'email si possible
- **Double stockage** : email chiffré (clé dérivée du mot de passe) + hash SHA256 pour index
- **Profils multiples** : un utilisateur peut avoir plusieurs identités (nom/email)
- **Récupération mot de passe** : à définir (codes de récupération, questions sécurité, ou pas de récupération)

## Fonctionnalités principales

### 1. Gestion des tirages au sort

#### Types d'exclusions
- **Exclusions fortes** : obligatoires, bloquent le tirage si impossible
- **Exclusions faibles** : optionnelles, ignorées si nécessaire
- **Groupes d'exclusion** : participants d'un même groupe s'excluent mutuellement

#### Algorithme de tirage
- **Backtracking** avec priorité aux participants ayant le plus d'exclusions
- **Rééditions** : exclusions automatiques des anciens appariements (par ancienneté)
- **Gestion d'erreur** : message générique si exclusions faibles non respectées

### 2. Inscriptions libres
- **Lien de partage** pour inscriptions ouvertes
- **Validation** : organisateur accepte/refuse/accepte automatiquement
- **Unicité** : nom unique par tirage, emails peuvent être dupliqués
- **Dates limites** : configurables par l'organisateur
- **Contrôle** : possibilité de pause/fermeture des inscriptions

### 3. Messagerie anonyme
- **Messages chiffrés** avec la clé master
- **Directions** :
  - Participant → son Secret Santa (anonyme)
  - Secret Santa → sa cible (si autorisé par organisateur)
- **Réactions anonymes** : émojis sans timestamp
- **Réponses prédéfinies** : optionnelles pour masquer le style d'écriture
- **Signalement** : remontée à l'organisateur puis développeur

### 4. Gestion post-tirage
- **Révélation** : l'organisateur peut voir tous les appariements
- **Historique** : conservation pour éviter répétitions lors des rééditions
- **Réédition** : modification rapide des participants et relance

## Contraintes techniques

### Base de données
- **MySQL** avec colonnes BLOB pour données chiffrées
- **Index** sur hash des emails pour authentification
- **Queues Laravel** obligatoires pour tirages et envois d'emails

### Limites
- **Participants par tirage** : limite configurable (version payante pour augmenter)
- **Participants par groupe d'exclusion** : pas de limite fixe
- **Messages** : pas de limite de temps définie

### Sécurité
- **Pas de localStorage/sessionStorage** dans les artifacts
- **Chiffrement côté serveur** pour envoi d'emails
- **Logs** : clés jamais stockées dans les access logs
- **Signalement** : système de modération intégré

## Workflow utilisateur

### Organisateur
1. Crée un tirage (génération clé master côté client)
2. Ajoute participants manuellement ou partage lien d'inscription
3. Configure exclusions et options
4. Lance le tirage (job en queue)
5. Partage les liens individuels
6. Modère les messages si nécessaire
7. Révèle les résultats à la fin

### Participant
1. Reçoit lien unique avec clé individuelle
2. Accède à son panneau personnalisé
3. Peut envoyer des messages anonymes
4. Reçoit des messages de son Secret Santa
5. Peut réagir ou répondre selon les options

### Utilisateur inscrit
1. Crée un compte avec profils multiples
2. Retrouve ses tirages facilement
3. Réédite rapidement ses anciens tirages
4. Gère ses différentes identités

## Points à finaliser
- Mécanisme de récupération de mot de passe
- Interface de gestion des réponses prédéfinies
- Système de limite de participants et tarification
- Détails du système de signalement
