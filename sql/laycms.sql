-- MySQL dump 10.13  Distrib 8.0.45, for macos14.8 (arm64)
--
-- Host: 127.0.0.1    Database: laycms
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(500) DEFAULT NULL,
  `rich_text` text,
  `school_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_class_school_id` (`school_id`),
  CONSTRAINT `fk_class_school_id` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,'2026-03-01 12:55:32','2026-03-01 13:12:16','班级3',NULL,NULL,NULL),(2,'2026-03-01 12:55:36','2026-03-01 13:12:11','班级2',NULL,NULL,NULL),(3,'2026-03-01 12:55:38','2026-03-01 13:59:03','班级1',NULL,NULL,2);
/*!40000 ALTER TABLE `class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_form_fields`
--

DROP TABLE IF EXISTS `cms_form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cms_form_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `field_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_control` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'input',
  `options` text COLLATE utf8mb4_unicode_ci,
  `attributes` text COLLATE utf8mb4_unicode_ci,
  `validation_rules` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_list_visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_form_fields_form_id_field_name_unique` (`form_id`,`field_name`),
  CONSTRAINT `cms_form_fields_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `cms_forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_fields`
--

LOCK TABLES `cms_form_fields` WRITE;
/*!40000 ALTER TABLE `cms_form_fields` DISABLE KEYS */;
INSERT INTO `cms_form_fields` VALUES (6,2,'name','名称','input',NULL,NULL,NULL,1,1,1,'2026-03-01 13:11:51','2026-03-01 13:11:51'),(7,2,'img','图片','file',NULL,NULL,NULL,2,0,1,'2026-03-01 13:42:51','2026-03-01 13:42:51'),(8,2,'rich_text','富文本','editor',NULL,NULL,NULL,3,0,1,'2026-03-01 13:45:54','2026-03-01 13:45:54'),(9,3,'name','名称','input',NULL,NULL,NULL,1,1,1,'2026-03-01 13:56:53','2026-03-01 13:56:53'),(10,2,'school_id','学校ID','relation',NULL,NULL,NULL,4,0,1,'2026-03-01 13:57:54','2026-03-01 13:57:54');
/*!40000 ALTER TABLE `cms_form_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_form_groups`
--

DROP TABLE IF EXISTS `cms_form_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cms_form_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_groups`
--

LOCK TABLES `cms_form_groups` WRITE;
/*!40000 ALTER TABLE `cms_form_groups` DISABLE KEYS */;
INSERT INTO `cms_form_groups` VALUES (1,'学校分组',0,'2026-03-01 13:38:42','2026-03-01 13:40:27'),(2,'文章分组',0,'2026-03-01 13:40:37','2026-03-01 13:40:37');
/*!40000 ALTER TABLE `cms_form_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_form_relations`
--

DROP TABLE IF EXISTS `cms_form_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cms_form_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `form_field_id` bigint unsigned NOT NULL,
  `related_form_id` bigint unsigned NOT NULL,
  `related_field_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_form_relations_form_field_id_unique` (`form_field_id`),
  KEY `cms_form_relations_form_id_foreign` (`form_id`),
  KEY `cms_form_relations_related_form_id_foreign` (`related_form_id`),
  CONSTRAINT `cms_form_relations_form_field_id_foreign` FOREIGN KEY (`form_field_id`) REFERENCES `cms_form_fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cms_form_relations_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `cms_forms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cms_form_relations_related_form_id_foreign` FOREIGN KEY (`related_form_id`) REFERENCES `cms_forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_relations`
--

LOCK TABLES `cms_form_relations` WRITE;
/*!40000 ALTER TABLE `cms_form_relations` DISABLE KEYS */;
INSERT INTO `cms_form_relations` VALUES (1,2,10,3,'id','2026-03-01 13:57:54','2026-03-01 13:57:54');
/*!40000 ALTER TABLE `cms_form_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_forms`
--

DROP TABLE IF EXISTS `cms_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cms_forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_group_id` bigint unsigned NOT NULL DEFAULT '1',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_forms_table_name_unique` (`table_name`),
  KEY `cms_forms_form_group_id_foreign` (`form_group_id`),
  CONSTRAINT `cms_forms_form_group_id_foreign` FOREIGN KEY (`form_group_id`) REFERENCES `cms_form_groups` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_forms`
--

LOCK TABLES `cms_forms` WRITE;
/*!40000 ALTER TABLE `cms_forms` DISABLE KEYS */;
INSERT INTO `cms_forms` VALUES (2,2,'班级','class',NULL,0,'2026-03-01 12:35:49','2026-03-01 13:41:03'),(3,1,'学校','school',NULL,0,'2026-03-01 13:55:37','2026-03-01 13:55:37');
/*!40000 ALTER TABLE `cms_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_group_permissions`
--

DROP TABLE IF EXISTS `cms_group_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cms_group_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_group_id` bigint unsigned NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_create` tinyint(1) NOT NULL DEFAULT '0',
  `can_read` tinyint(1) NOT NULL DEFAULT '0',
  `can_update` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_group_permissions_user_group_id_table_name_unique` (`user_group_id`,`table_name`),
  CONSTRAINT `cms_group_permissions_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_group_permissions`
--

LOCK TABLES `cms_group_permissions` WRITE;
/*!40000 ALTER TABLE `cms_group_permissions` DISABLE KEYS */;
INSERT INTO `cms_group_permissions` VALUES (1,1,'_forms',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(2,1,'_users',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(3,1,'sample_articles',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(4,1,'class',0,0,0,0,'2026-03-01 12:57:04','2026-03-01 12:57:04'),(5,2,'_forms',0,0,0,0,'2026-03-01 13:27:47','2026-03-01 13:27:47'),(6,2,'_users',0,0,0,0,'2026-03-01 13:27:47','2026-03-01 13:27:47'),(7,2,'class',0,0,0,0,'2026-03-01 13:27:47','2026-03-01 13:27:47'),(8,2,'sample_articles',0,1,0,0,'2026-03-01 13:27:47','2026-03-01 13:27:47');
/*!40000 ALTER TABLE `cms_group_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_users`
--

DROP TABLE IF EXISTS `cms_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cms_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_group_id` bigint unsigned DEFAULT NULL,
  `is_root` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_users_username_unique` (`username`),
  KEY `cms_users_user_group_id_foreign` (`user_group_id`),
  CONSTRAINT `cms_users_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_users`
--

LOCK TABLES `cms_users` WRITE;
/*!40000 ALTER TABLE `cms_users` DISABLE KEYS */;
INSERT INTO `cms_users` VALUES (1,'admin','$2y$12$qP1XZ3Vd7QAAzP3ZIQq6IOj/8VGJTaGT2P3sMWqOjwcH0HUfHuW7q','系统管理员',1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(2,'hu','$2y$12$H3MBwfwmpgEFBMlQC.25XewWMpQ9SKN6wggvU8axmOOgGdZlNguTO','hu',2,0,1,'2026-03-01 13:28:38','2026-03-01 13:28:38');
/*!40000 ALTER TABLE `cms_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2024_01_01_000001_create_user_groups_table',1),(3,'2024_01_01_000002_create_users_table',1),(4,'2024_01_01_000003_create_forms_table',1),(5,'2024_01_01_000004_create_form_fields_table',1),(6,'2024_01_01_000005_create_group_permissions_table',1),(7,'2024_01_01_000006_create_sample_table',1),(8,'2024_01_01_000007_create_form_groups_table',2),(9,'2024_01_01_000008_create_form_relations_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school`
--

LOCK TABLES `school` WRITE;
/*!40000 ALTER TABLE `school` DISABLE KEYS */;
INSERT INTO `school` VALUES (1,'2026-03-01 13:57:10','2026-03-01 13:57:10','上海一中'),(2,'2026-03-01 13:58:56','2026-03-01 13:58:56','上海二中');
/*!40000 ALTER TABLE `school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (1,'超级管理员组','拥有所有权限','2026-03-01 12:32:26','2026-03-01 12:32:26'),(2,'访客',NULL,'2026-03-01 13:26:30','2026-03-01 13:26:30');
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-01 22:03:16
