-- =====================================================
-- SECRETSANTA.FR - SCHÉMA DE BASE DE DONNÉES OPTIMISÉ
-- =====================================================

-- Optimisations principales:
-- 1. Partitionnement des tables volumineuses (messages)
-- 2. Index composites optimisés pour les requêtes fréquentes
-- 3. Colonnes JSON pour métadonnées flexibles
-- 4. Tables de cache pour performances
-- 5. Audit trail complet

-- =====================================================
-- TABLES PRINCIPALES
-- =====================================================

-- Table des utilisateurs inscrits (optimisée)
CREATE TABLE users (
                       id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                       email_hash VARCHAR(64) NOT NULL,
                       password_hash VARCHAR(255) NOT NULL,

    -- Métadonnées en JSON pour flexibilité
                       metadata JSON DEFAULT NULL COMMENT 'recovery_codes, 2fa_secret, preferences',

    -- Statistiques pour optimisation
                       login_count INT UNSIGNED DEFAULT 0,
                       last_login_at TIMESTAMP NULL DEFAULT NULL,
                       last_login_ip VARCHAR(45) NULL,

                       created_at TIMESTAMP NULL DEFAULT NULL,
                       updated_at TIMESTAMP NULL DEFAULT NULL,

                       PRIMARY KEY (id),
                       UNIQUE INDEX idx_email_hash (email_hash),
                       INDEX idx_last_login (last_login_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Profils des utilisateurs (optimisée)
CREATE TABLE user_profiles (
                               id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                               user_id BIGINT UNSIGNED NOT NULL,

    -- Données chiffrées avec versioning
                               encryption_version TINYINT UNSIGNED DEFAULT 2,
                               name_encrypted BLOB NOT NULL,
                               email_encrypted BLOB NOT NULL,

    -- Hash pour recherche rapide sans déchiffrement
                               name_hash VARCHAR(64) GENERATED ALWAYS AS (SHA2(name_encrypted, 256)) STORED,
                               email_hash VARCHAR(64) GENERATED ALWAYS AS (SHA2(email_encrypted, 256)) STORED,

                               is_default BOOLEAN DEFAULT FALSE,

                               created_at TIMESTAMP NULL DEFAULT NULL,
                               updated_at TIMESTAMP NULL DEFAULT NULL,

                               PRIMARY KEY (id),
                               FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                               INDEX idx_user_profiles (user_id, is_default),
                               INDEX idx_name_hash (name_hash),
                               INDEX idx_email_hash (email_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tirages au sort (optimisée avec cache)
CREATE TABLE draws (
                       id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                       user_id BIGINT UNSIGNED NULL,
                       uuid VARCHAR(36) NOT NULL,

    -- Clés et chiffrement avec versioning
                       encryption_version TINYINT UNSIGNED DEFAULT 2,
                       organizer_key_hash VARCHAR(64) NOT NULL,
                       master_key_encrypted BLOB NOT NULL,
                       key_rotation_data JSON DEFAULT NULL COMMENT 'key_id, rotation_schedule',

    -- Métadonnées chiffrées
                       title_encrypted BLOB NOT NULL,
                       description_encrypted BLOB NULL,
                       organizer_name_encrypted BLOB NOT NULL,
                       organizer_email_encrypted BLOB NOT NULL,

    -- Configuration avec valeurs par défaut optimisées
                       status ENUM('draft', 'open_registration', 'closed_registration', 'processing', 'drawn', 'revealed', 'archived') DEFAULT 'draft',
                       config JSON DEFAULT NULL COMMENT 'auto_accept, allow_messages, limits, etc.',

    -- Cache des statistiques
                       stats_cache JSON DEFAULT NULL COMMENT 'participant_count, message_count, etc.',
                       stats_updated_at TIMESTAMP NULL DEFAULT NULL,

    -- Dates importantes
                       registration_deadline TIMESTAMP NULL,
                       created_at TIMESTAMP NULL DEFAULT NULL,
                       updated_at TIMESTAMP NULL DEFAULT NULL,
                       drawn_at TIMESTAMP NULL,
                       revealed_at TIMESTAMP NULL,
                       archived_at TIMESTAMP NULL,

                       PRIMARY KEY (id),
                       UNIQUE INDEX idx_uuid (uuid),
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                       INDEX idx_status_dates (status, created_at),
                       INDEX idx_user_draws (user_id, status, created_at DESC),
                       INDEX idx_archival (archived_at, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Participants aux tirages (optimisée)
CREATE TABLE participants (
                              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                              draw_id BIGINT UNSIGNED NOT NULL,
                              uuid VARCHAR(36) NOT NULL,

    -- Clés et chiffrement
                              encryption_version TINYINT UNSIGNED DEFAULT 2,
                              individual_key_hash VARCHAR(64) NOT NULL,
                              master_key_encrypted BLOB NOT NULL,

    -- Données chiffrées avec hash pour recherche
                              name_encrypted BLOB NOT NULL,
                              email_encrypted BLOB NOT NULL,
                              name_hash VARCHAR(64) GENERATED ALWAYS AS (SHA2(name_encrypted, 256)) STORED,

    -- Statut et rôle
                              status ENUM('pending', 'accepted', 'rejected', 'removed') DEFAULT 'pending',
                              is_organizer BOOLEAN DEFAULT FALSE,

    -- Assignation
                              assigned_to_participant_id BIGINT UNSIGNED NULL,
                              assignment_metadata JSON DEFAULT NULL COMMENT 'assignment_time, retry_count',

    -- Dates et métadonnées
                              created_at TIMESTAMP NULL DEFAULT NULL,
                              updated_at TIMESTAMP NULL DEFAULT NULL,
                              accepted_at TIMESTAMP NULL,
                              last_access_at TIMESTAMP NULL,
                              access_count INT UNSIGNED DEFAULT 0,

                              PRIMARY KEY (id),
                              UNIQUE INDEX idx_uuid (uuid),
                              UNIQUE INDEX idx_draw_name_hash (draw_id, name_hash),
                              FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                              FOREIGN KEY (assigned_to_participant_id) REFERENCES participants(id) ON DELETE SET NULL,
                              INDEX idx_draw_status (draw_id, status, is_organizer),
                              INDEX idx_assignment (draw_id, assigned_to_participant_id),
                              INDEX idx_last_access (last_access_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLES D'EXCLUSIONS OPTIMISÉES
-- =====================================================

-- Exclusions avec index optimisés
CREATE TABLE exclusions (
                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                            draw_id BIGINT UNSIGNED NOT NULL,
                            participant_id BIGINT UNSIGNED NOT NULL,
                            excluded_participant_id BIGINT UNSIGNED NOT NULL,

                            type ENUM('strong', 'weak') DEFAULT 'strong',
                            source ENUM('manual', 'group', 'history', 'auto') DEFAULT 'manual',

    -- Métadonnées pour traçabilité
                            metadata JSON DEFAULT NULL COMMENT 'reason, created_by, priority',

                            created_at TIMESTAMP NULL DEFAULT NULL,

                            PRIMARY KEY (id),
                            UNIQUE INDEX idx_unique_exclusion (participant_id, excluded_participant_id),
                            FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                            FOREIGN KEY (excluded_participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                            INDEX idx_draw_exclusions (draw_id, type),
                            INDEX idx_participant_exclusions (participant_id, type, source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLES DE MESSAGES PARTITIONNÉES
-- =====================================================

-- Messages avec partitionnement par date
CREATE TABLE messages (
                          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                          draw_id BIGINT UNSIGNED NOT NULL,
                          from_participant_id BIGINT UNSIGNED NOT NULL,
                          to_participant_id BIGINT UNSIGNED NOT NULL,

    -- Contenu chiffré avec versioning
                          encryption_version TINYINT UNSIGNED DEFAULT 2,
                          content_encrypted BLOB NOT NULL,
                          content_hash VARCHAR(64) GENERATED ALWAYS AS (SHA2(content_encrypted, 256)) STORED,

    -- Type et statut
                          type ENUM('to_secret_santa', 'to_target', 'system') NOT NULL,
                          status ENUM('sent', 'read', 'deleted') DEFAULT 'sent',

    -- Modération
                          moderation_status ENUM('ok', 'reported', 'reviewing', 'removed') DEFAULT 'ok',
                          moderation_data JSON DEFAULT NULL COMMENT 'reporter_id, reason, reviewer_notes',

    -- Métadonnées
                          read_at TIMESTAMP NULL DEFAULT NULL,
                          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP NULL DEFAULT NULL,

                          PRIMARY KEY (id, created_at),
                          FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                          FOREIGN KEY (from_participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                          FOREIGN KEY (to_participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                          INDEX idx_draw_messages (draw_id, created_at DESC),
                          INDEX idx_participant_inbox (to_participant_id, status, created_at DESC),
                          INDEX idx_participant_sent (from_participant_id, created_at DESC),
                          INDEX idx_moderation (moderation_status, created_at),
                          INDEX idx_content_hash (content_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

-- =====================================================
-- TABLES DE CACHE ET PERFORMANCE
-- =====================================================

-- Cache des résultats de tirage pour accès rapide
CREATE TABLE draw_results_cache (
                                    draw_id BIGINT UNSIGNED NOT NULL,
                                    participant_id BIGINT UNSIGNED NOT NULL,

    -- Résultat chiffré avec la clé du participant
                                    encrypted_result BLOB NOT NULL,

    -- Métadonnées
                                    accessed_count INT UNSIGNED DEFAULT 0,
                                    last_accessed_at TIMESTAMP NULL DEFAULT NULL,
                                    expires_at TIMESTAMP NOT NULL,

                                    PRIMARY KEY (draw_id, participant_id),
                                    FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                                    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
                                    INDEX idx_expiration (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de métriques pour monitoring
CREATE TABLE draw_metrics (
                              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                              draw_id BIGINT UNSIGNED NOT NULL,

                              metric_type ENUM('performance', 'usage', 'error') NOT NULL,
                              metric_name VARCHAR(100) NOT NULL,
                              metric_value DECIMAL(10, 4) NOT NULL,
                              metadata JSON DEFAULT NULL,

                              recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

                              PRIMARY KEY (id),
                              FOREIGN KEY (draw_id) REFERENCES draws(id) ON DELETE CASCADE,
                              INDEX idx_draw_metrics (draw_id, metric_type, recorded_at),
                              INDEX idx_metric_analysis (metric_name, recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLES D'AUDIT
-- =====================================================

-- Audit trail complet
CREATE TABLE audit_logs (
                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- Contexte
                            user_id BIGINT UNSIGNED NULL,
                            participant_id BIGINT UNSIGNED NULL,
                            draw_id BIGINT UNSIGNED NULL,

    -- Action
                            action VARCHAR(100) NOT NULL,
                            entity_type VARCHAR(50) NOT NULL,
                            entity_id BIGINT UNSIGNED NOT NULL,

    -- Données
                            old_values JSON DEFAULT NULL,
                            new_values JSON DEFAULT NULL,

    -- Métadonnées
                            ip_address VARCHAR(45) NULL,
                            user_agent VARCHAR(255) NULL,
                            session_id VARCHAR(100) NULL,

                            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

                            PRIMARY KEY (id),
                            INDEX idx_user_audit (user_id, created_at DESC),
                            INDEX idx_entity_audit (entity_type, entity_id, created_at DESC),
                            INDEX idx_draw_audit (draw_id, created_at DESC),
                            INDEX idx_action_audit (action, created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

-- =====================================================
-- VUES MATÉRIALISÉES (TABLES DE REPORTING)
-- =====================================================

-- Vue matérialisée des statistiques de tirage
CREATE TABLE draw_statistics AS
SELECT
    d.id as draw_id,
    d.status,
    COUNT(DISTINCT p.id) as participant_count,
    COUNT(DISTINCT CASE WHEN p.status = 'accepted' THEN p.id END) as accepted_count,
    COUNT(DISTINCT m.id) as message_count,
    COUNT(DISTINCT e.id) as exclusion_count,
    MAX(p.last_access_at) as last_activity,
    d.created_at,
    d.drawn_at
FROM draws d
         LEFT JOIN participants p ON d.id = p.draw_id
         LEFT JOIN messages m ON d.id = m.draw_id
         LEFT JOIN exclusions e ON d.id = e.draw_id
GROUP BY d.id;

CREATE INDEX idx_draw_stats ON draw_statistics(draw_id);

-- =====================================================
-- PROCÉDURES STOCKÉES POUR PERFORMANCE
-- =====================================================

DELIMITER $$

-- Procédure pour nettoyer les données expirées
CREATE PROCEDURE cleanup_expired_data()
BEGIN
    -- Supprimer les caches expirés
DELETE FROM draw_results_cache WHERE expires_at < NOW();

-- Archiver les vieux tirages
UPDATE draws
SET status = 'archived', archived_at = NOW()
WHERE status = 'revealed'
  AND revealed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Nettoyer les vieux logs d'audit
DELETE FROM audit_logs
WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)
  AND action NOT IN ('draw_created', 'draw_revealed', 'user_deleted');
END$$

-- Procédure pour calculer les statistiques
CREATE PROCEDURE update_draw_statistics(IN p_draw_id BIGINT)
BEGIN
    DECLARE v_stats JSON;

SELECT JSON_OBJECT(
           'participant_count', COUNT(DISTINCT p.id),
           'accepted_count', COUNT(DISTINCT CASE WHEN p.status = 'accepted' THEN p.id END),
           'message_count', COUNT(DISTINCT m.id),
           'exclusion_count', COUNT(DISTINCT e.id),
           'last_activity', MAX(GREATEST(
        COALESCE(p.last_access_at, '1970-01-01'),
        COALESCE(m.created_at, '1970-01-01')
                                ))
       ) INTO v_stats
FROM draws d
         LEFT JOIN participants p ON d.id = p.draw_id
         LEFT JOIN messages m ON d.id = m.draw_id
         LEFT JOIN exclusions e ON d.id = e.draw_id
WHERE d.id = p_draw_id;

UPDATE draws
SET stats_cache = v_stats, stats_updated_at = NOW()
WHERE id = p_draw_id;
END$$

DELIMITER ;

-- =====================================================
-- TRIGGERS POUR INTÉGRITÉ ET AUDIT
-- =====================================================

DELIMITER $$

-- Trigger pour audit automatique
CREATE TRIGGER audit_participant_changes
    AFTER UPDATE ON participants
    FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO audit_logs (
            participant_id, draw_id, action, entity_type, entity_id,
            old_values, new_values, created_at
        ) VALUES (
            NEW.id, NEW.draw_id, 'status_changed', 'participant', NEW.id,
            JSON_OBJECT('status', OLD.status),
            JSON_OBJECT('status', NEW.status),
            NOW()
        );
END IF;
END$$

-- Trigger pour mise à jour des statistiques
CREATE TRIGGER update_stats_on_participant_change
    AFTER INSERT ON participants
    FOR EACH ROW
BEGIN
    CALL update_draw_statistics(NEW.draw_id);
    END$$

    DELIMITER ;
