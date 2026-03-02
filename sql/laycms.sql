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
  `name` varchar(255) DEFAULT NULL,
  `age` bigint NOT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `rich_text_1` text,
  `rich_text_2` text,
  `img` varchar(500) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `likes` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `textarea` text,
  `school_id_1` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_class_age` (`age`),
  KEY `fk_class_school_id` (`school_id`),
  KEY `fk_class_school_id_1` (`school_id_1`),
  CONSTRAINT `fk_class_school_id` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_class_school_id_1` FOREIGN KEY (`school_id_1`) REFERENCES `school` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,'2026-03-02 08:17:36','2026-03-02 09:37:07','班级1',55,83,NULL,NULL,NULL,'2','[\"2\"]','2026-03-02','2026-03-02 17:37:04',NULL,NULL),(2,'2026-03-02 08:45:38','2026-03-02 09:39:04','班级33',88,61,'<p>123</p>\r\n','<p>456</p>\r\n','upload/20260302173150_da0898.webp','1','[\"1\", \"2\"]','2026-03-02','2026-03-02 17:36:54','123',61);
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_fields`
--

LOCK TABLES `cms_form_fields` WRITE;
/*!40000 ALTER TABLE `cms_form_fields` DISABLE KEYS */;
INSERT INTO `cms_form_fields` VALUES (9,3,'name','名称','input',NULL,NULL,NULL,1,1,1,'2026-03-01 13:56:53','2026-03-01 13:56:53'),(20,4,'name','名称','input',NULL,NULL,NULL,1,1,1,NULL,NULL),(21,4,'age','年龄','number',NULL,NULL,NULL,2,1,1,NULL,NULL),(22,4,'school_id','学校id','relation',NULL,NULL,NULL,3,0,1,NULL,NULL),(23,4,'rich_text_1','富文本1','editor',NULL,NULL,NULL,4,0,1,NULL,NULL),(24,4,'rich_text_2','富文本2','editor',NULL,NULL,NULL,5,0,1,NULL,NULL),(25,4,'img','图片','file',NULL,NULL,NULL,6,0,1,NULL,NULL),(26,4,'gender','性别','radio','{\"1\":\"男\",\"2\":\"女\"}',NULL,NULL,7,0,1,NULL,NULL),(27,4,'likes','爱好','checkbox','{\"1\":\"音乐\",\"2\":\"电影\"}',NULL,NULL,8,0,1,NULL,NULL),(28,4,'date','日期','date',NULL,NULL,NULL,9,0,1,NULL,NULL),(29,4,'datetime','日期时间','datetime',NULL,NULL,NULL,10,0,1,NULL,NULL),(30,4,'textarea','多行文本','textarea',NULL,NULL,NULL,11,0,1,NULL,NULL),(31,4,'school_id_1','学校id1','relation',NULL,NULL,NULL,12,0,1,NULL,NULL);
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
INSERT INTO `cms_form_groups` VALUES (1,'学校分组',0,'2026-03-01 13:38:42','2026-03-01 13:40:27'),(2,'班级分组',0,'2026-03-01 13:40:37','2026-03-01 14:07:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_relations`
--

LOCK TABLES `cms_form_relations` WRITE;
/*!40000 ALTER TABLE `cms_form_relations` DISABLE KEYS */;
INSERT INTO `cms_form_relations` VALUES (6,4,22,3,'id',NULL,NULL),(7,4,31,3,'id',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_forms`
--

LOCK TABLES `cms_forms` WRITE;
/*!40000 ALTER TABLE `cms_forms` DISABLE KEYS */;
INSERT INTO `cms_forms` VALUES (3,1,'学校列表','school','学校列表',0,'2026-03-01 13:55:37','2026-03-01 15:34:49'),(4,2,'班级列表','class',NULL,0,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_group_permissions`
--

LOCK TABLES `cms_group_permissions` WRITE;
/*!40000 ALTER TABLE `cms_group_permissions` DISABLE KEYS */;
INSERT INTO `cms_group_permissions` VALUES (1,1,'_forms',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(2,1,'_users',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(3,1,'sample_articles',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(4,1,'class',0,0,0,0,'2026-03-01 12:57:04','2026-03-01 12:57:04'),(10,1,'school',0,0,0,0,NULL,NULL),(15,4,'_forms',0,0,0,0,NULL,NULL),(16,4,'_users',0,0,0,0,NULL,NULL),(17,4,'school',0,1,0,0,NULL,NULL),(18,4,'class',0,1,0,0,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_users`
--

LOCK TABLES `cms_users` WRITE;
/*!40000 ALTER TABLE `cms_users` DISABLE KEYS */;
INSERT INTO `cms_users` VALUES (1,'admin','$2y$12$qP1XZ3Vd7QAAzP3ZIQq6IOj/8VGJTaGT2P3sMWqOjwcH0HUfHuW7q','系统管理员',1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(5,'hu','$2b$12$mmGrlOBDt.dnz7A7b6oh0eRzJA5RaaJ8v90sn2kWfSB4myHrLsHyy','hu',4,0,1,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school`
--

LOCK TABLES `school` WRITE;
/*!40000 ALTER TABLE `school` DISABLE KEYS */;
INSERT INTO `school` VALUES (1,'2026-03-01 13:57:10','2026-03-01 13:57:10','上海一中'),(2,'2026-03-01 13:58:56','2026-03-01 13:58:56','上海二中'),(3,NULL,NULL,'上海8中'),(4,NULL,NULL,'上海9中'),(5,NULL,NULL,'上海10中'),(6,NULL,NULL,'上海11中'),(7,NULL,NULL,'上海12中'),(8,NULL,NULL,'上海13中'),(9,NULL,NULL,'上海14中'),(10,NULL,NULL,'上海15中'),(11,NULL,NULL,'上海16中'),(12,NULL,NULL,'上海17中'),(13,NULL,NULL,'上海18中'),(14,NULL,NULL,'上海19中'),(15,NULL,NULL,'上海20中'),(16,NULL,NULL,'上海21中'),(17,NULL,NULL,'上海22中'),(18,NULL,NULL,'上海23中'),(19,NULL,NULL,'上海24中'),(20,NULL,NULL,'上海25中'),(21,NULL,NULL,'上海26中'),(22,NULL,NULL,'上海27中'),(23,NULL,NULL,'上海28中'),(24,NULL,NULL,'上海29中'),(25,NULL,NULL,'上海30中'),(26,NULL,NULL,'上海31中'),(27,NULL,NULL,'上海32中'),(28,NULL,NULL,'上海33中'),(29,NULL,NULL,'上海34中'),(30,NULL,NULL,'上海35中'),(31,NULL,NULL,'上海36中'),(32,NULL,NULL,'上海37中'),(33,NULL,NULL,'上海38中'),(34,NULL,NULL,'上海39中'),(35,NULL,NULL,'上海40中'),(36,NULL,NULL,'上海41中'),(37,NULL,NULL,'上海42中'),(38,NULL,NULL,'上海43中'),(39,NULL,NULL,'上海44中'),(40,NULL,NULL,'上海45中'),(41,NULL,NULL,'上海46中'),(42,NULL,NULL,'上海47中'),(43,NULL,NULL,'上海48中'),(44,NULL,NULL,'上海49中'),(45,NULL,NULL,'上海50中'),(46,NULL,NULL,'上海51中'),(47,NULL,NULL,'上海52中'),(48,NULL,NULL,'上海53中'),(49,NULL,NULL,'上海54中'),(50,NULL,NULL,'上海55中'),(51,NULL,NULL,'上海56中'),(52,NULL,NULL,'上海57中'),(53,NULL,NULL,'上海58中'),(54,NULL,NULL,'上海59中'),(55,NULL,NULL,'上海60中'),(56,NULL,NULL,'上海61中'),(57,NULL,NULL,'上海62中'),(58,NULL,NULL,'上海63中'),(59,NULL,NULL,'上海64中'),(60,NULL,NULL,'上海65中'),(61,NULL,NULL,'上海66中'),(62,NULL,NULL,'上海67中'),(63,NULL,NULL,'上海68中'),(64,NULL,NULL,'上海69中'),(65,NULL,NULL,'上海70中'),(66,NULL,NULL,'上海71中'),(67,NULL,NULL,'上海72中'),(68,NULL,NULL,'上海73中'),(69,NULL,NULL,'上海74中'),(70,NULL,NULL,'上海75中'),(71,NULL,NULL,'上海76中'),(72,NULL,NULL,'上海77中'),(73,NULL,NULL,'上海78中'),(74,NULL,NULL,'上海79中'),(75,NULL,NULL,'上海80中'),(76,NULL,NULL,'上海81中'),(77,NULL,NULL,'上海82中'),(78,NULL,NULL,'上海83中'),(79,NULL,NULL,'上海84中'),(80,NULL,NULL,'上海85中'),(81,NULL,NULL,'上海86中'),(82,NULL,NULL,'上海87中'),(83,NULL,NULL,'上海88中'),(84,NULL,NULL,'上海89中'),(85,NULL,NULL,'上海90中'),(86,NULL,NULL,'上海91中'),(87,NULL,NULL,'上海92中'),(88,NULL,NULL,'上海93中'),(89,NULL,NULL,'上海94中'),(90,NULL,NULL,'上海95中'),(91,NULL,NULL,'上海96中'),(92,NULL,NULL,'上海97中'),(93,NULL,NULL,'上海98中'),(94,NULL,NULL,'上海99中'),(95,NULL,NULL,'上海100中'),(96,NULL,NULL,'上海100中'),(97,NULL,NULL,'上海101中'),(98,NULL,NULL,'上海102中'),(99,NULL,NULL,'上海103中'),(100,NULL,NULL,'上海104中'),(101,NULL,NULL,'上海105中'),(102,NULL,NULL,'上海106中'),(103,NULL,NULL,'上海107中'),(104,NULL,NULL,'上海108中'),(105,NULL,NULL,'上海109中'),(106,NULL,NULL,'上海110中'),(107,NULL,NULL,'上海111中'),(108,NULL,NULL,'上海112中'),(109,NULL,NULL,'上海113中'),(110,NULL,NULL,'上海114中'),(111,NULL,NULL,'上海115中'),(112,NULL,NULL,'上海116中'),(113,NULL,NULL,'上海117中'),(114,NULL,NULL,'上海118中'),(115,NULL,NULL,'上海119中'),(116,NULL,NULL,'上海120中'),(117,NULL,NULL,'上海121中'),(118,NULL,NULL,'上海122中'),(119,NULL,NULL,'上海123中'),(120,NULL,NULL,'上海124中'),(121,NULL,NULL,'上海125中'),(122,NULL,NULL,'上海126中'),(123,NULL,NULL,'上海127中'),(124,NULL,NULL,'上海128中'),(125,NULL,NULL,'上海129中'),(126,NULL,NULL,'上海130中'),(127,NULL,NULL,'上海131中'),(128,NULL,NULL,'上海132中'),(129,NULL,NULL,'上海133中'),(130,NULL,NULL,'上海134中'),(131,NULL,NULL,'上海135中'),(132,NULL,NULL,'上海136中'),(133,NULL,NULL,'上海137中'),(134,NULL,NULL,'上海138中'),(135,NULL,NULL,'上海139中'),(136,NULL,NULL,'上海140中'),(137,NULL,NULL,'上海141中'),(138,NULL,NULL,'上海142中'),(139,NULL,NULL,'上海143中'),(140,NULL,NULL,'上海144中'),(141,NULL,NULL,'上海145中'),(142,NULL,NULL,'上海146中'),(143,NULL,NULL,'上海147中'),(144,NULL,NULL,'上海148中'),(145,NULL,NULL,'上海149中'),(146,NULL,NULL,'上海150中'),(147,NULL,NULL,'上海151中'),(148,NULL,NULL,'上海152中'),(149,NULL,NULL,'上海153中'),(150,NULL,NULL,'上海154中'),(151,NULL,NULL,'上海155中'),(152,NULL,NULL,'上海156中'),(153,NULL,NULL,'上海157中'),(154,NULL,NULL,'上海158中'),(155,NULL,NULL,'上海159中'),(156,NULL,NULL,'上海160中'),(157,NULL,NULL,'上海161中'),(158,NULL,NULL,'上海162中'),(159,NULL,NULL,'上海163中'),(160,NULL,NULL,'上海164中'),(161,NULL,NULL,'上海165中'),(162,NULL,NULL,'上海166中'),(163,NULL,NULL,'上海167中'),(164,NULL,NULL,'上海168中'),(165,NULL,NULL,'上海169中'),(166,NULL,NULL,'上海170中'),(167,NULL,NULL,'上海171中'),(168,NULL,NULL,'上海172中'),(169,NULL,NULL,'上海173中'),(170,NULL,NULL,'上海174中'),(171,NULL,NULL,'上海175中'),(172,NULL,NULL,'上海176中'),(173,NULL,NULL,'上海177中'),(174,NULL,NULL,'上海178中'),(175,NULL,NULL,'上海179中'),(176,NULL,NULL,'上海180中'),(177,NULL,NULL,'上海181中'),(178,NULL,NULL,'上海182中'),(179,NULL,NULL,'上海183中'),(180,NULL,NULL,'上海184中'),(181,NULL,NULL,'上海185中'),(182,NULL,NULL,'上海186中'),(183,NULL,NULL,'上海187中'),(184,NULL,NULL,'上海188中'),(185,NULL,NULL,'上海189中'),(186,NULL,NULL,'上海190中'),(187,NULL,NULL,'上海191中'),(188,NULL,NULL,'上海192中'),(189,NULL,NULL,'上海193中'),(190,NULL,NULL,'上海194中'),(191,NULL,NULL,'上海195中'),(192,NULL,NULL,'上海196中'),(193,NULL,NULL,'上海197中'),(194,NULL,NULL,'上海198中'),(195,NULL,NULL,'上海199中'),(196,NULL,NULL,'上海200中'),(197,NULL,NULL,'上海200中'),(198,NULL,NULL,'上海201中'),(199,NULL,NULL,'上海202中'),(200,NULL,NULL,'上海203中'),(201,NULL,NULL,'上海204中'),(202,NULL,NULL,'上海205中'),(203,NULL,NULL,'上海206中'),(204,NULL,NULL,'上海207中'),(205,NULL,NULL,'上海208中'),(206,NULL,NULL,'上海209中'),(207,NULL,NULL,'上海210中'),(208,NULL,NULL,'上海211中'),(209,NULL,NULL,'上海212中'),(210,NULL,NULL,'上海213中'),(211,NULL,NULL,'上海214中'),(212,NULL,NULL,'上海215中'),(213,NULL,NULL,'上海216中'),(214,NULL,NULL,'上海217中'),(215,NULL,NULL,'上海218中'),(216,NULL,NULL,'上海219中'),(217,NULL,NULL,'上海220中'),(218,NULL,NULL,'上海221中'),(219,NULL,NULL,'上海222中'),(220,NULL,NULL,'上海223中'),(221,NULL,NULL,'上海224中'),(222,NULL,NULL,'上海225中'),(223,NULL,NULL,'上海226中'),(224,NULL,NULL,'上海227中'),(225,NULL,NULL,'上海228中'),(226,NULL,NULL,'上海229中'),(227,NULL,NULL,'上海230中'),(228,NULL,NULL,'上海231中'),(229,NULL,NULL,'上海232中'),(230,NULL,NULL,'上海233中'),(231,NULL,NULL,'上海234中'),(232,NULL,NULL,'上海235中'),(233,NULL,NULL,'上海236中'),(234,NULL,NULL,'上海237中'),(235,NULL,NULL,'上海238中'),(236,NULL,NULL,'上海239中'),(237,NULL,NULL,'上海240中'),(238,NULL,NULL,'上海241中'),(239,NULL,NULL,'上海242中'),(240,NULL,NULL,'上海243中'),(241,NULL,NULL,'上海244中'),(242,NULL,NULL,'上海245中'),(243,NULL,NULL,'上海246中'),(244,NULL,NULL,'上海247中'),(245,NULL,NULL,'上海248中'),(246,NULL,NULL,'上海249中'),(247,NULL,NULL,'上海250中'),(248,NULL,NULL,'上海251中'),(249,NULL,NULL,'上海252中'),(250,NULL,NULL,'上海253中'),(251,NULL,NULL,'上海254中'),(252,NULL,NULL,'上海255中'),(253,NULL,NULL,'上海256中'),(254,NULL,NULL,'上海257中'),(255,NULL,NULL,'上海258中'),(256,NULL,NULL,'上海259中'),(257,NULL,NULL,'上海260中'),(258,NULL,NULL,'上海261中'),(259,NULL,NULL,'上海262中'),(260,NULL,NULL,'上海263中'),(261,NULL,NULL,'上海264中'),(262,NULL,NULL,'上海265中'),(263,NULL,NULL,'上海266中'),(264,NULL,NULL,'上海267中'),(265,NULL,NULL,'上海268中'),(266,NULL,NULL,'上海269中'),(267,NULL,NULL,'上海270中'),(268,NULL,NULL,'上海271中'),(269,NULL,NULL,'上海272中'),(270,NULL,NULL,'上海273中'),(271,NULL,NULL,'上海274中'),(272,NULL,NULL,'上海275中'),(273,NULL,NULL,'上海276中'),(274,NULL,NULL,'上海277中'),(275,NULL,NULL,'上海278中'),(276,NULL,NULL,'上海279中'),(277,NULL,NULL,'上海280中'),(278,NULL,NULL,'上海281中'),(279,NULL,NULL,'上海282中'),(280,NULL,NULL,'上海283中'),(281,NULL,NULL,'上海284中'),(282,NULL,NULL,'上海285中'),(283,NULL,NULL,'上海286中'),(284,NULL,NULL,'上海287中'),(285,NULL,NULL,'上海288中'),(286,NULL,NULL,'上海289中'),(287,NULL,NULL,'上海290中'),(288,NULL,NULL,'上海291中'),(289,NULL,NULL,'上海292中'),(290,NULL,NULL,'上海293中'),(291,NULL,NULL,'上海294中'),(292,NULL,NULL,'上海295中'),(293,NULL,NULL,'上海296中'),(294,NULL,NULL,'上海297中'),(295,NULL,NULL,'上海298中'),(296,NULL,'2026-03-01 18:31:38','上海299中'),(298,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(299,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(300,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(301,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(302,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(303,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(304,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中'),(305,'2026-03-01 18:31:38','2026-03-01 18:31:38','上海299中');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (1,'超级管理员组','拥有所有权限','2026-03-01 12:32:26','2026-03-01 12:32:26'),(4,'访客',NULL,NULL,NULL);
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

-- Dump completed on 2026-03-02 17:39:55
