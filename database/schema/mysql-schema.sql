/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `dear_santas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dear_santas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `mail_body` blob NOT NULL,
  `draw_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dear_santas_sender_id_foreign` (`sender_id`),
  KEY `dear_santas_draw_id_foreign` (`draw_id`),
  CONSTRAINT `dear_santas_draw_id_foreign` FOREIGN KEY (`draw_id`) REFERENCES `draws` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dear_santas_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dear_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dear_targets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `mail_type` varchar(255) NOT NULL,
  `draw_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dear_targets_sender_id_foreign` (`sender_id`),
  KEY `dear_targets_draw_id_foreign` (`draw_id`),
  CONSTRAINT `dear_targets_draw_id_foreign` FOREIGN KEY (`draw_id`) REFERENCES `draws` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dear_targets_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `draws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `draws` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ulid` char(26) NOT NULL,
  `organizer_id` bigint(20) unsigned DEFAULT NULL,
  `participant_organizer` tinyint(1) unsigned NOT NULL,
  `title` blob NOT NULL,
  `description` blob DEFAULT NULL,
  `budget` varchar(55) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `drawn_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `status` enum('created','ready','drawing','started','finished','error','canceled') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `draws_ulid_unique` (`ulid`),
  KEY `draws_organizer_id_foreign` (`organizer_id`),
  CONSTRAINT `draws_organizer_id_foreign` FOREIGN KEY (`organizer_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `exclusions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exclusions` (
  `participant_id` bigint(20) unsigned NOT NULL,
  `exclusion_id` bigint(20) unsigned NOT NULL,
  KEY `exclusions_participant_id_foreign` (`participant_id`),
  KEY `exclusions_exclusion_id_foreign` (`exclusion_id`),
  CONSTRAINT `exclusions_exclusion_id_foreign` FOREIGN KEY (`exclusion_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exclusions_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ulid` char(26) NOT NULL,
  `mailable_type` varchar(255) NOT NULL,
  `mailable_id` bigint(20) unsigned NOT NULL,
  `delivery_status` enum('created','sending','sent','error','received') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `draw_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mails_ulid_unique` (`ulid`),
  KEY `mails_mailable_type_mailable_id_index` (`mailable_type`,`mailable_id`),
  KEY `mails_draw_id_foreign` (`draw_id`),
  CONSTRAINT `mails_draw_id_foreign` FOREIGN KEY (`draw_id`) REFERENCES `draws` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ulid` char(26) NOT NULL,
  `draw_id` bigint(20) unsigned NOT NULL,
  `name` blob NOT NULL,
  `email` blob DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_ulid_unique` (`ulid`),
  KEY `participants_draw_id_foreign` (`draw_id`),
  KEY `participants_target_id_foreign` (`target_id`),
  CONSTRAINT `participants_draw_id_foreign` FOREIGN KEY (`draw_id`) REFERENCES `draws` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participants_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `websockets_statistics_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `websockets_statistics_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0000_00_00_000000_create_websockets_statistics_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2018_12_12_223012_create_draws_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2018_12_12_223015_create_mails_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2018_12_12_223023_create_participants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2019_12_06_003707_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2019_12_11_232036_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2020_02_19_140057_create_dear_santas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2020_11_15_235209_create_exclusions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2022_04_17_211703_create_dear_targets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2023_09_16_010213_create_notifications_table',1);
