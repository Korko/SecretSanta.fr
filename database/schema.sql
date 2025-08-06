-- =====================================================
-- SECRETSANTA.FR - SCHÉMA DE BASE DE DONNÉES MYSQL
-- =====================================================

-- Table des utilisateurs inscrits
CREATE TABLE users (
                       id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                       email_hash VARCHAR(64) NOT NULL UNIQUE, -- SHA256 de l'email pour index
                       password_hash VARCHAR(255) NOT NULL,
                       created_at TIMESTAMP NULL DEFAULT NULL,
                       updated_at TIMESTAMP NULL DEFAULT NULL,
                       PRIMARY KEY (id),
                       INDEX idx_email_hash (email_hash)
);

-- Profils des utilisateurs (plusieurs par utilisateur)
CREATE TABLE user_profiles (
                               id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                               user_id BIGINT UNSIGNED NOT NULL,
                               name_encrypted BLOB NOT NULL, -- Nom chiffré
                               email_encrypted BLOB NOT NULL, -- Email chiffré
                               created_at TIMESTAMP NULL DEFAULT NULL,
                               updated_at TIMESTAMP NULL DEFAULT NULL,
                               PRIMARY KEY (id),
                               FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                               INDEX idx_user_id (user_id)
);

-- Tirages au sort
CREATE TABLE draws (
                       id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                       user_id BIGINT UNSIGNED NULL, -- NULL si organisateur non inscrit
                       uuid VARCHAR(36) NOT NULL UNIQUE, -- UUID public du tirage
                       organizer_key_hash VARCHAR(64) NOT NULL, -- Hash de la clé organisateur
                       master_key_encrypted BLOB NOT NULL, -- Clé master chiffrée par clé organisateur

    -- Métadonnées chiffrées
                       title_encrypted BLOB NOT NULL,
                       description_encrypted BLOB NULL,
                       organizer_name_encrypted BLOB NOT NULL,
                       organizer_email_encrypted BLOB NOT NULL,

    -- Configuration du tirage
                       status ENUM('draft', 'open_registration', 'closed_registration', 'drawn', 'revealed', 'archived') DEFAULT 'draft',
                       registration_deadline TIMESTAMP NULL,
                       auto_accept_participants BOOLEAN DEFAULT FALSE,
                       allow_target_messages BOOLEAN DEFAULT TRUE, -- Cible peut répondre au secret santa

    -- Dates
                       created_at TIMESTAMP NULL DEFAULT NULL,
                       updated_at TIMESTAMP NULL DEFAULT NULL,
                       drawn_at TIMESTAMP NULL,
                       revealed_at TIMESTAMP NULL,

                       PRIMARY KEY (id),
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                       INDEX idx_uuid (uuid),
                       INDEX idx_user_id (user_id),
                       INDEX idx_status (status)
);

-- Participants aux tirages
CREATE TABLE participants (
                              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                              draw_id BIGINT UNSIGNED NOT NULL,
                              uuid VARCHAR(36) NOT NULL UNIQUE, -- UUID public du participant
                              individual_key_hash VARCHAR(64) NOT NULL, -- Hash de la clé individuelle
                              master_key_encrypted BLOB NOT NULL, -- Clé master chiffrée par clé individuelle

    -- Données chiffrées du participant
                              name_encrypted BLOB NOT NULL,
                              email_encrypted BLOB NOT NULL,

    -- Statut
                              status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
                              is_organizer BOOLEAN DEFAULT FALSE,

    -- Assignation (une fois le tirage effectué)
                              assigned_to_participant_id BIGINT UNSIGNED NULL,

    -- Dates
                              created_at TIMESTAMP NULL DEFAULT NULL,
                              updated_at TIMESTAMP NULL DEFAULT NULL,
                              accepted_at TIMESTAMP NULL,

                              PRIMARY KEY (id),
                              FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                              FOREIGN KEY (assigned_to_participant_id) REFERENCES participants(id) ON DELETE SET NULL,
                              UNIQUE KEY unique_participant_uuid (uuid),
                              UNIQUE KEY unique_name_per_draw (draw_id, name_encrypted(64)), -- Approximation pour unicité
                              INDEX idx_draw_id (draw_id),
                              INDEX idx_uuid (uuid),
                              INDEX idx_status (status)
);

-- Groupes d'exclusion
CREATE TABLE exclusion_groups (
                                  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                  draw_id BIGINT UNSIGNED NOT NULL,
                                  name_encrypted BLOB NOT NULL,
                                  created_at TIMESTAMP NULL DEFAULT NULL,
                                  updated_at TIMESTAMP NULL DEFAULT NULL,
                                  PRIMARY KEY (id),
                                  FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                                  INDEX idx_draw_id (draw_id)
);

-- Membres des groupes d'exclusion
CREATE TABLE exclusion_group_members (
                                         id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                         exclusion_group_id BIGINT UNSIGNED NOT NULL,
                                         participant_id BIGINT UNSIGNED NOT NULL,
                                         created_at TIMESTAMP NULL DEFAULT NULL,
                                         PRIMARY KEY (id),
                                         FOREIGN KEY (exclusion_group_id) REFERENCES exclusion_groups(id) ON DELETE CASCADE,
                                         FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                                         UNIQUE KEY unique_member_per_group (exclusion_group_id, participant_id),
                                         INDEX idx_exclusion_group_id (exclusion_group_id),
                                         INDEX idx_participant_id (participant_id)
);

-- Exclusions individuelles
CREATE TABLE exclusions (
                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                            draw_id BIGINT UNSIGNED NOT NULL,
                            participant_id BIGINT UNSIGNED NOT NULL, -- Qui ne peut pas piocher
                            excluded_participant_id BIGINT UNSIGNED NOT NULL, -- Qui ne peut pas être pioché
                            type ENUM('strong', 'weak') DEFAULT 'strong', -- Fort ou faible
                            source ENUM('manual', 'group', 'history') DEFAULT 'manual', -- Origine de l'exclusion
                            created_at TIMESTAMP NULL DEFAULT NULL,
                            PRIMARY KEY (id),
                            FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                            FOREIGN KEY (excluded_participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                            UNIQUE KEY unique_exclusion (participant_id, excluded_participant_id),
                            INDEX idx_draw_id (draw_id),
                            INDEX idx_participant_id (participant_id),
                            INDEX idx_type (type)
);

-- Messages entre participants
CREATE TABLE messages (
                          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          draw_id BIGINT UNSIGNED NOT NULL,
                          from_participant_id BIGINT UNSIGNED NOT NULL,
                          to_participant_id BIGINT UNSIGNED NOT NULL,

    -- Message chiffré
                          content_encrypted BLOB NOT NULL,

    -- Type de message
                          type ENUM('to_secret_santa', 'to_target') NOT NULL,

    -- Statut de modération
                          is_reported BOOLEAN DEFAULT FALSE,
                          is_reviewed BOOLEAN DEFAULT FALSE,
                          reviewer_notes TEXT NULL,

                          created_at TIMESTAMP NULL DEFAULT NULL,
                          updated_at TIMESTAMP NULL DEFAULT NULL,

                          PRIMARY KEY (id),
                          FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                          FOREIGN KEY (from_participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                          FOREIGN KEY (to_participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                          INDEX idx_draw_id (draw_id),
                          INDEX idx_from_participant (from_participant_id),
                          INDEX idx_to_participant (to_participant_id),
                          INDEX idx_type (type),
                          INDEX idx_reported (is_reported),
                          INDEX idx_created_at (created_at)
);

-- Réactions aux messages
CREATE TABLE message_reactions (
                                   id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                   message_id BIGINT UNSIGNED NOT NULL,
                                   participant_id BIGINT UNSIGNED NOT NULL,
                                   reaction VARCHAR(10) NOT NULL, -- Emoji ou code réaction
                                   created_at TIMESTAMP NULL DEFAULT NULL,
                                   PRIMARY KEY (id),
                                   FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
                                   FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                                   UNIQUE KEY unique_reaction_per_message (message_id, participant_id),
                                   INDEX idx_message_id (message_id)
);

-- Réponses prédéfinies par tirage (optionnel)
CREATE TABLE predefined_responses (
                                      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                      draw_id BIGINT UNSIGNED NOT NULL,
                                      response_encrypted BLOB NOT NULL, -- Réponse chiffrée
                                      created_at TIMESTAMP NULL DEFAULT NULL,
                                      PRIMARY KEY (id),
                                      FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                                      INDEX idx_draw_id (draw_id)
);

-- Historique des tirages précédents (pour éviter les répétitions)
CREATE TABLE draw_history (
                              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                              parent_draw_id BIGINT UNSIGNED NOT NULL, -- Tirage original
                              edition_number INT NOT NULL, -- Numéro d'édition
                              assignments_data JSON NOT NULL, -- Données des assignations précédentes
                              created_at TIMESTAMP NULL DEFAULT NULL,
                              PRIMARY KEY (id),
                              FOREIGN KEY (parent_draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                              INDEX idx_parent_draw_id (parent_draw_id),
                              INDEX idx_edition_number (edition_number)
);

-- Jobs de traitement (Laravel queues)
CREATE TABLE jobs (
                      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                      queue VARCHAR(255) NOT NULL,
                      payload LONGTEXT NOT NULL,
                      attempts TINYINT UNSIGNED NOT NULL,
                      reserved_at TIMESTAMP NULL DEFAULT NULL,
                      available_at TIMESTAMP NOT NULL,
                      created_at TIMESTAMP NOT NULL,
                      PRIMARY KEY (id),
                      INDEX idx_queue (queue),
                      INDEX idx_reserved_at (reserved_at),
                      INDEX idx_available_at (available_at)
);

-- Jobs échoués
CREATE TABLE failed_jobs (
                             id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                             uuid VARCHAR(255) NOT NULL,
                             connection TEXT NOT NULL,
                             queue TEXT NOT NULL,
                             payload LONGTEXT NOT NULL,
                             exception LONGTEXT NOT NULL,
                             failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             PRIMARY KEY (id),
                             UNIQUE KEY unique_uuid (uuid)
);

-- =====================================================
-- COMMENTAIRES SUR L'ARCHITECTURE
-- =====================================================

/*
POINTS IMPORTANTS :

1. CHIFFREMENT :
   - Toutes les données sensibles sont en BLOB chiffré
   - Hash des clés pour identification sans stockage des clés
   - Clé master chiffrée par chaque clé individuelle

2. UNICITÉ :
   - UUID pour identification publique (draws, participants)
   - Noms uniques par tirage (approximation avec BLOB tronqué)
   - Emails peuvent être dupliqués entre participants

3. EXCLUSIONS :
   - Type strong/weak pour gestion algorithme
   - Source pour traçabilité (manuel, groupe, historique)
   - Groupes avec membres automatiquement exclus

4. MESSAGES :
   - Type pour direction (vers secret santa ou vers cible)
   - Système de signalement intégré
   - Réactions anonymes sans timestamp

5. PERFORMANCE :
   - Index sur tous les champs de recherche fréquente
   - Queues pour traitement asynchrone
   - JSON pour données complexes (historique)

6. INTÉGRITÉ :
   - Clés étrangères avec CASCADE appropriées
   - Contraintes d'unicité là où nécessaire
   - Status ENUM pour workflow contrôlé
*/
