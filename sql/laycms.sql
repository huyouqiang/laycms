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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_fields`
--

LOCK TABLES `cms_form_fields` WRITE;
/*!40000 ALTER TABLE `cms_form_fields` DISABLE KEYS */;
INSERT INTO `cms_form_fields` VALUES (9,3,'name','名称','input',NULL,NULL,NULL,1,1,1,'2026-03-01 13:56:53','2026-03-01 13:56:53'),(20,4,'name','名称','input',NULL,NULL,NULL,1,1,1,NULL,NULL),(21,4,'age','年龄','number',NULL,NULL,NULL,2,1,1,NULL,NULL),(22,4,'school_id','学校id','relation',NULL,NULL,NULL,3,0,1,NULL,NULL),(23,4,'rich_text_1','富文本1','editor',NULL,NULL,NULL,4,0,1,NULL,NULL),(24,4,'rich_text_2','富文本2','editor',NULL,NULL,NULL,5,0,1,NULL,NULL),(25,4,'img','图片','file',NULL,NULL,NULL,6,0,1,NULL,NULL),(26,4,'gender','性别','radio','{\"1\":\"男\",\"2\":\"女\"}',NULL,NULL,7,0,1,NULL,NULL),(27,4,'likes','爱好','checkbox','{\"1\":\"音乐\",\"2\":\"电影\"}',NULL,NULL,8,0,1,NULL,NULL),(28,4,'date','日期','date',NULL,NULL,NULL,9,0,1,NULL,NULL),(29,4,'datetime','日期时间','datetime',NULL,NULL,NULL,10,0,1,NULL,NULL),(30,4,'textarea','多行文本','textarea',NULL,NULL,NULL,11,0,1,NULL,NULL),(31,4,'school_id_1','学校id1','relation',NULL,NULL,NULL,12,0,1,NULL,NULL),(32,5,'name','部门名称','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(33,5,'code','部门编码','input',NULL,NULL,NULL,2,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(34,5,'parent_id','上级部门','relation',NULL,NULL,NULL,3,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(35,5,'sort_order','排序','number',NULL,NULL,NULL,4,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(36,5,'description','描述','textarea',NULL,NULL,NULL,5,0,0,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(37,6,'name','姓名','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(38,6,'emp_no','工号','input',NULL,NULL,NULL,2,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(39,6,'department_id','部门','relation',NULL,NULL,NULL,3,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(40,6,'mobile','手机','input',NULL,NULL,NULL,4,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(41,6,'email','邮箱','input',NULL,NULL,NULL,5,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(42,6,'position','职位','input',NULL,NULL,NULL,6,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(43,6,'status','状态','radio','{\"1\":\"在职\",\"2\":\"离职\"}',NULL,NULL,7,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(44,7,'title','标题','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(45,7,'content','内容','editor',NULL,NULL,NULL,2,0,0,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(46,7,'type','类型','select','{\"notice\":\"普通通知\",\"urgent\":\"紧急通知\"}',NULL,NULL,3,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(47,7,'publish_at','发布时间','datetime',NULL,NULL,NULL,4,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(48,8,'staff_id','申请人','relation',NULL,NULL,NULL,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(49,8,'leave_type','请假类型','select','{\"annual\":\"年假\",\"sick\":\"病假\",\"personal\":\"事假\"}',NULL,NULL,2,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(50,8,'start_date','开始日期','date',NULL,NULL,NULL,3,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(51,8,'end_date','结束日期','date',NULL,NULL,NULL,4,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(52,8,'days','天数','number',NULL,NULL,NULL,5,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(53,8,'reason','请假事由','textarea',NULL,NULL,NULL,6,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(54,8,'status','状态','select','{\"pending\":\"待审批\",\"approved\":\"已通过\",\"rejected\":\"已拒绝\"}',NULL,NULL,7,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(55,9,'staff_id','申请人','relation',NULL,NULL,NULL,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(56,9,'title','报销事由','input',NULL,NULL,NULL,2,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(57,9,'amount','金额','number',NULL,NULL,NULL,3,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(58,9,'category','类别','select','{\"travel\":\"差旅\",\"office\":\"办公\",\"other\":\"其他\"}',NULL,NULL,4,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(59,9,'remark','备注','textarea',NULL,NULL,NULL,5,0,0,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(60,9,'status','状态','select','{\"pending\":\"待审批\",\"approved\":\"已通过\",\"rejected\":\"已拒绝\"}',NULL,NULL,6,0,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(61,10,'name','分类名称','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(62,10,'sort_order','排序','number',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(63,10,'remark','备注','textarea',NULL,NULL,NULL,3,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(64,11,'name','单位名称','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(65,11,'remark','备注','textarea',NULL,NULL,NULL,2,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(66,12,'name','桌号','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(67,12,'seats','可坐人数','number',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(68,12,'status','状态','select','{\"free\":\"空闲\",\"available\":\"可用\",\"occupied\":\"占用\"}',NULL,NULL,3,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(69,12,'remark','备注','textarea',NULL,NULL,NULL,4,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(70,13,'name','菜品名称','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(71,13,'category_id','分类','relation',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(72,13,'unit_id','单位','relation',NULL,NULL,NULL,3,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(73,13,'price','售价','number',NULL,NULL,NULL,4,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(74,13,'cost','成本价','number',NULL,NULL,NULL,5,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(75,13,'status','状态','radio','{\"1\":\"上架\",\"0\":\"下架\"}',NULL,NULL,6,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(76,13,'sort_order','排序','number',NULL,NULL,NULL,7,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(77,13,'remark','备注','textarea',NULL,NULL,NULL,8,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(78,21,'name','姓名','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(79,21,'role','角色','select','{\"waiter\":\"服务员\",\"chef\":\"厨师\",\"cashier\":\"收银\",\"admin\":\"管理员\"}',NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(80,21,'mobile','手机','input',NULL,NULL,NULL,3,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(81,21,'status','状态','radio','{\"1\":\"在职\",\"0\":\"离职\"}',NULL,NULL,4,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(82,21,'remark','备注','textarea',NULL,NULL,NULL,5,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(83,19,'name','姓名','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(84,19,'mobile','手机','input',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(85,19,'level','等级','select','{\"normal\":\"普通\",\"silver\":\"银卡\",\"gold\":\"金卡\"}',NULL,NULL,3,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(86,19,'points','积分','number',NULL,NULL,NULL,4,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(87,19,'balance','储值余额','number',NULL,NULL,NULL,5,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(88,19,'remark','备注','textarea',NULL,NULL,NULL,6,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(89,14,'order_no','订单号','input',NULL,NULL,NULL,1,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(90,14,'table_id','桌位','relation',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(91,14,'member_id','会员','relation',NULL,NULL,NULL,3,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(92,14,'staff_id','服务员','relation',NULL,NULL,NULL,4,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(93,14,'total_amount','订单总额','number',NULL,NULL,NULL,5,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(94,14,'discount_amount','优惠金额','number',NULL,NULL,NULL,6,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(95,14,'pay_amount','实付金额','number',NULL,NULL,NULL,7,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(96,14,'status','状态','select','{\"draft\":\"草稿\",\"ordered\":\"已下单\",\"paid\":\"已结账\",\"cancelled\":\"已取消\"}',NULL,NULL,8,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(97,14,'pay_at','结账时间','datetime',NULL,NULL,NULL,9,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(98,14,'remark','备注','textarea',NULL,NULL,NULL,10,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(99,15,'order_id','订单','relation',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(100,15,'dish_id','菜品','relation',NULL,NULL,NULL,2,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(101,15,'qty','数量','number',NULL,NULL,NULL,3,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(102,15,'price','单价','number',NULL,NULL,NULL,4,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(103,15,'amount','小计','number',NULL,NULL,NULL,5,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(104,15,'remark','备注','textarea',NULL,NULL,NULL,6,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(105,16,'name','原材料名称','input',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(106,16,'unit_id','单位','relation',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(107,16,'min_stock','最低库存预警','number',NULL,NULL,NULL,3,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(108,16,'remark','备注','textarea',NULL,NULL,NULL,4,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(109,17,'ingredient_id','原材料','relation',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(110,17,'qty','当前数量','number',NULL,NULL,NULL,2,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(111,17,'remark','备注','textarea',NULL,NULL,NULL,3,0,0,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(112,18,'ingredient_id','原材料','relation',NULL,NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(113,18,'type','类型','select','{\"in\":\"入库\",\"out\":\"出库\",\"adjust\":\"调整\"}',NULL,NULL,2,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(114,18,'qty','数量','number',NULL,NULL,NULL,3,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(115,18,'qty_after','变动后库存','number',NULL,NULL,NULL,4,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(116,18,'reason','事由','textarea',NULL,NULL,NULL,5,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(117,20,'type','类型','select','{\"income\":\"收入\",\"expense\":\"支出\"}',NULL,NULL,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(118,20,'category','类别','input',NULL,NULL,NULL,2,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(119,20,'amount','金额','number',NULL,NULL,NULL,3,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(120,20,'remark','备注','textarea',NULL,NULL,NULL,4,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(121,20,'occurred_at','发生时间','datetime',NULL,NULL,NULL,5,0,1,'2026-03-02 10:37:03','2026-03-02 10:37:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_groups`
--

LOCK TABLES `cms_form_groups` WRITE;
/*!40000 ALTER TABLE `cms_form_groups` DISABLE KEYS */;
INSERT INTO `cms_form_groups` VALUES (1,'学校分组',0,'2026-03-01 13:38:42','2026-03-01 13:40:27'),(2,'班级分组',0,'2026-03-01 13:40:37','2026-03-01 14:07:57'),(3,'OA办公',10,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(4,'餐饮管理',20,'2026-03-02 10:37:03','2026-03-02 10:37:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_form_relations`
--

LOCK TABLES `cms_form_relations` WRITE;
/*!40000 ALTER TABLE `cms_form_relations` DISABLE KEYS */;
INSERT INTO `cms_form_relations` VALUES (6,4,22,3,'id',NULL,NULL),(7,4,31,3,'id',NULL,NULL),(8,5,34,5,'id','2026-03-02 10:19:34','2026-03-02 10:19:34'),(9,6,39,5,'id','2026-03-02 10:19:34','2026-03-02 10:19:34'),(10,8,48,6,'id','2026-03-02 10:19:34','2026-03-02 10:19:34'),(11,9,55,6,'id','2026-03-02 10:19:34','2026-03-02 10:19:34'),(12,13,71,10,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(13,13,72,11,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(14,14,90,12,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(15,14,91,19,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(16,14,92,21,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(17,15,99,14,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(18,15,100,13,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(19,16,106,11,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(20,17,109,16,'id','2026-03-02 10:37:03','2026-03-02 10:37:03'),(21,18,112,16,'id','2026-03-02 10:37:03','2026-03-02 10:37:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_forms`
--

LOCK TABLES `cms_forms` WRITE;
/*!40000 ALTER TABLE `cms_forms` DISABLE KEYS */;
INSERT INTO `cms_forms` VALUES (3,1,'学校列表','school','学校列表',0,'2026-03-01 13:55:37','2026-03-01 15:34:49'),(4,2,'班级列表','class',NULL,0,NULL,NULL),(5,3,'部门','oa_department','组织架构-部门',1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(6,3,'员工','oa_staff','员工信息',2,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(7,3,'公告','oa_announcement','公司公告',3,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(8,3,'请假单','oa_leave','员工请假',4,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(9,3,'费用报销','oa_expense','费用报销单',5,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(10,4,'菜品分类','rest_category','基础-菜品分类',1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(11,4,'计量单位','rest_unit','基础-单位',2,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(12,4,'桌位','rest_table','基础-餐桌',3,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(13,4,'菜品','rest_dish','商品-菜品',4,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(14,4,'订单','rest_order','订单-主表',5,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(15,4,'订单明细','rest_order_item','订单-明细',6,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(16,4,'原材料','rest_ingredient','库存-原材料',7,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(17,4,'库存','rest_inventory','库存-当前库存',8,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(18,4,'库存流水','rest_inventory_log','库存-入库出库',9,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(19,4,'会员','rest_member','会员-信息',10,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(20,4,'收支记录','rest_finance','财务-流水',11,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(21,4,'员工','rest_staff','员工-信息',12,'2026-03-02 10:37:03','2026-03-02 10:37:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_group_permissions`
--

LOCK TABLES `cms_group_permissions` WRITE;
/*!40000 ALTER TABLE `cms_group_permissions` DISABLE KEYS */;
INSERT INTO `cms_group_permissions` VALUES (1,1,'_forms',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(2,1,'_users',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(3,1,'sample_articles',1,1,1,1,'2026-03-01 12:32:26','2026-03-01 12:32:26'),(4,1,'class',0,0,0,0,'2026-03-01 12:57:04','2026-03-01 12:57:04'),(10,1,'school',0,0,0,0,NULL,NULL),(15,4,'_forms',0,0,0,0,NULL,NULL),(16,4,'_users',0,0,0,0,NULL,NULL),(17,4,'school',0,1,0,0,NULL,NULL),(18,4,'class',0,1,0,0,NULL,NULL),(19,1,'oa_department',1,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(20,1,'oa_staff',1,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(21,1,'oa_announcement',1,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(22,1,'oa_leave',1,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(23,1,'oa_expense',1,1,1,1,'2026-03-02 10:19:34','2026-03-02 10:19:34'),(24,1,'rest_category',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(25,1,'rest_unit',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(26,1,'rest_table',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(27,1,'rest_dish',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(28,1,'rest_order',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(29,1,'rest_order_item',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(30,1,'rest_ingredient',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(31,1,'rest_inventory',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(32,1,'rest_inventory_log',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(33,1,'rest_member',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(34,1,'rest_finance',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03'),(35,1,'rest_staff',1,1,1,1,'2026-03-02 10:37:03','2026-03-02 10:37:03');
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
-- Table structure for table `oa_announcement`
--

DROP TABLE IF EXISTS `oa_announcement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oa_announcement` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'notice' COMMENT '类型:notice通知 urgent紧急',
  `publish_at` datetime DEFAULT NULL COMMENT '发布时间',
  `author_id` bigint unsigned DEFAULT NULL COMMENT '发布人',
  PRIMARY KEY (`id`),
  KEY `idx_oa_announcement_publish` (`publish_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oa_announcement`
--

LOCK TABLES `oa_announcement` WRITE;
/*!40000 ALTER TABLE `oa_announcement` DISABLE KEYS */;
INSERT INTO `oa_announcement` VALUES (1,'2026-03-02 10:19:34','2026-03-02 10:19:34','春节放假通知','<p>经公司研究决定，春节放假时间为2月8日至2月17日，共10天。请各部门做好节前工作安排，祝大家新春快乐！</p>','notice','2026-03-02 18:19:34',NULL),(2,'2026-03-02 10:19:34','2026-03-02 10:19:34','重要：系统升级维护通知','<p><strong>紧急通知</strong></p><p>本周六凌晨2:00-6:00 进行系统升级维护，届时OA系统将暂时无法访问，请提前安排好工作。</p>','urgent','2026-03-02 18:19:34',NULL),(3,'2026-03-02 10:19:34','2026-03-02 10:19:34','2026年度体检安排','<p>公司定于3月中旬组织年度体检，具体时间地点另行通知，请各位同事关注。</p>','notice','2026-03-02 18:19:34',NULL);
/*!40000 ALTER TABLE `oa_announcement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oa_department`
--

DROP TABLE IF EXISTS `oa_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oa_department` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '部门名称',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '部门编码',
  `parent_id` bigint unsigned DEFAULT NULL COMMENT '上级部门',
  `sort_order` smallint DEFAULT '0' COMMENT '排序',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `idx_oa_department_parent` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oa_department`
--

LOCK TABLES `oa_department` WRITE;
/*!40000 ALTER TABLE `oa_department` DISABLE KEYS */;
INSERT INTO `oa_department` VALUES (1,'2026-03-02 10:19:34','2026-03-02 10:19:34','总经理室','GM',NULL,1,'公司最高决策层'),(2,'2026-03-02 10:19:34','2026-03-02 10:19:34','技术部','TECH',1,2,'技术研发'),(3,'2026-03-02 10:19:34','2026-03-02 10:19:34','产品部','PM',1,3,'产品设计与管理'),(4,'2026-03-02 10:19:34','2026-03-02 10:19:34','行政部','ADMIN',1,4,'行政人事'),(5,'2026-03-02 10:19:34','2026-03-02 10:19:34','财务部','FIN',1,5,'财务管理');
/*!40000 ALTER TABLE `oa_department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oa_expense`
--

DROP TABLE IF EXISTS `oa_expense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oa_expense` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `staff_id` bigint unsigned NOT NULL COMMENT '申请人',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '报销事由',
  `amount` decimal(12,2) NOT NULL COMMENT '金额',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类别:travel差旅 office办公 other其他',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'pending' COMMENT '状态:pending待审批 approved已通过 rejected已拒绝',
  PRIMARY KEY (`id`),
  KEY `idx_oa_expense_staff` (`staff_id`),
  CONSTRAINT `fk_oa_expense_staff` FOREIGN KEY (`staff_id`) REFERENCES `oa_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oa_expense`
--

LOCK TABLES `oa_expense` WRITE;
/*!40000 ALTER TABLE `oa_expense` DISABLE KEYS */;
INSERT INTO `oa_expense` VALUES (1,'2026-03-02 10:19:34','2026-03-02 10:19:34',2,'北京出差差旅费',3500.00,'travel','3月出差北京参加技术大会','approved'),(2,'2026-03-02 10:19:34','2026-03-02 10:19:34',6,'办公用品采购',320.50,'office','键盘、鼠标等','approved'),(3,'2026-03-02 10:19:34','2026-03-02 10:19:34',3,'客户招待费',1200.00,'other','项目洽谈招待','pending');
/*!40000 ALTER TABLE `oa_expense` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oa_leave`
--

DROP TABLE IF EXISTS `oa_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oa_leave` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `staff_id` bigint unsigned NOT NULL COMMENT '申请人',
  `leave_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请假类型:annual年假 sick病假 personal事假',
  `start_date` date NOT NULL COMMENT '开始日期',
  `end_date` date NOT NULL COMMENT '结束日期',
  `days` decimal(5,2) DEFAULT NULL COMMENT '天数',
  `reason` text COLLATE utf8mb4_unicode_ci COMMENT '请假事由',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'pending' COMMENT '状态:pending待审批 approved已通过 rejected已拒绝',
  PRIMARY KEY (`id`),
  KEY `idx_oa_leave_staff` (`staff_id`),
  CONSTRAINT `fk_oa_leave_staff` FOREIGN KEY (`staff_id`) REFERENCES `oa_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oa_leave`
--

LOCK TABLES `oa_leave` WRITE;
/*!40000 ALTER TABLE `oa_leave` DISABLE KEYS */;
INSERT INTO `oa_leave` VALUES (1,'2026-03-02 10:19:34','2026-03-02 10:19:34',2,'annual','2026-03-10','2026-03-12',3.00,'家庭事务','approved'),(2,'2026-03-02 10:19:34','2026-03-02 10:19:34',6,'sick','2026-03-05','2026-03-06',2.00,'身体不适需休息','approved'),(3,'2026-03-02 10:19:34','2026-03-02 10:19:34',7,'personal','2026-03-20','2026-03-21',2.00,'办理证件','pending');
/*!40000 ALTER TABLE `oa_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oa_staff`
--

DROP TABLE IF EXISTS `oa_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oa_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `emp_no` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '工号',
  `department_id` bigint unsigned DEFAULT NULL COMMENT '部门',
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '邮箱',
  `position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职位',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '状态:1在职 2离职',
  PRIMARY KEY (`id`),
  KEY `idx_oa_staff_dept` (`department_id`),
  CONSTRAINT `fk_oa_staff_department` FOREIGN KEY (`department_id`) REFERENCES `oa_department` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oa_staff`
--

LOCK TABLES `oa_staff` WRITE;
/*!40000 ALTER TABLE `oa_staff` DISABLE KEYS */;
INSERT INTO `oa_staff` VALUES (1,'2026-03-02 10:19:34','2026-03-02 10:19:34','张总','E001',1,'13800001001','zhang@company.com','总经理','1'),(2,'2026-03-02 10:19:34','2026-03-02 10:19:34','李技术','E002',2,'13800001002','li@company.com','技术总监','1'),(3,'2026-03-02 10:19:34','2026-03-02 10:19:34','王产品','E003',3,'13800001003','wang@company.com','产品经理','1'),(4,'2026-03-02 10:19:34','2026-03-02 10:19:34','赵行政','E004',4,'13800001004','zhao@company.com','行政主管','1'),(5,'2026-03-02 10:19:34','2026-03-02 10:19:34','钱会计','E005',5,'13800001005','qian@company.com','会计','1'),(6,'2026-03-02 10:19:34','2026-03-02 10:19:34','孙开发','E006',2,'13800001006','sun@company.com','开发工程师','1'),(7,'2026-03-02 10:19:34','2026-03-02 10:32:16','周设计','E007',3,'13800001007','zhou@company.com','UI设计师','1');
/*!40000 ALTER TABLE `oa_staff` ENABLE KEYS */;
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
-- Table structure for table `rest_category`
--

DROP TABLE IF EXISTS `rest_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `sort_order` smallint DEFAULT '0' COMMENT '排序',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_category`
--

LOCK TABLES `rest_category` WRITE;
/*!40000 ALTER TABLE `rest_category` DISABLE KEYS */;
INSERT INTO `rest_category` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','热菜',1,NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','凉菜',2,NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','汤羹',3,NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03','主食',4,NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03','饮料',5,NULL);
/*!40000 ALTER TABLE `rest_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_dish`
--

DROP TABLE IF EXISTS `rest_dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_dish` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '菜品名称',
  `category_id` bigint unsigned DEFAULT NULL COMMENT '分类',
  `unit_id` bigint unsigned DEFAULT NULL COMMENT '单位',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '售价',
  `cost` decimal(10,2) DEFAULT NULL COMMENT '成本价',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '1上架 0下架',
  `sort_order` smallint DEFAULT '0' COMMENT '排序',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_dish_category` (`category_id`),
  KEY `idx_rest_dish_unit` (`unit_id`),
  CONSTRAINT `fk_rest_dish_category` FOREIGN KEY (`category_id`) REFERENCES `rest_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_dish_unit` FOREIGN KEY (`unit_id`) REFERENCES `rest_unit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_dish`
--

LOCK TABLES `rest_dish` WRITE;
/*!40000 ALTER TABLE `rest_dish` DISABLE KEYS */;
INSERT INTO `rest_dish` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','宫保鸡丁',1,1,38.00,12.00,'1',1,NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','鱼香肉丝',1,1,36.00,10.00,'1',2,NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','红烧肉',1,1,48.00,18.00,'1',3,NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03','凉拌黄瓜',2,1,12.00,3.00,'1',1,NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03','皮蛋豆腐',2,1,15.00,4.00,'1',2,NULL),(6,'2026-03-02 10:37:03','2026-03-02 10:37:03','番茄蛋汤',3,1,18.00,5.00,'1',1,NULL),(7,'2026-03-02 10:37:03','2026-03-02 10:37:03','米饭',4,1,2.00,0.50,'1',1,NULL),(8,'2026-03-02 10:37:03','2026-03-02 10:37:03','可乐',5,2,5.00,1.50,'1',1,NULL),(9,'2026-03-02 10:37:03','2026-03-02 10:37:03','雪碧',5,2,5.00,1.50,'1',2,NULL),(10,'2026-03-02 10:37:03','2026-03-02 10:37:03','鲜榨橙汁',5,3,15.00,6.00,'1',3,NULL);
/*!40000 ALTER TABLE `rest_dish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_finance`
--

DROP TABLE IF EXISTS `rest_finance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_finance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'income收入 expense支出',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类别',
  `amount` decimal(12,2) NOT NULL COMMENT '金额',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `occurred_at` datetime DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`),
  KEY `idx_rest_finance_type` (`type`),
  KEY `idx_rest_finance_at` (`occurred_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_finance`
--

LOCK TABLES `rest_finance` WRITE;
/*!40000 ALTER TABLE `rest_finance` DISABLE KEYS */;
INSERT INTO `rest_finance` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','income','堂食',236.00,'当日营业款','2026-03-02 18:37:03'),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','expense','采购',850.00,'食材采购','2026-03-02 18:37:03'),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','expense','工资',8000.00,'本月工资','2026-03-02 18:37:03');
/*!40000 ALTER TABLE `rest_finance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_ingredient`
--

DROP TABLE IF EXISTS `rest_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_ingredient` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '原材料名称',
  `unit_id` bigint unsigned DEFAULT NULL COMMENT '单位',
  `min_stock` decimal(10,2) DEFAULT '0.00' COMMENT '最低库存预警',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_ingredient_unit` (`unit_id`),
  CONSTRAINT `fk_rest_ingredient_unit` FOREIGN KEY (`unit_id`) REFERENCES `rest_unit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_ingredient`
--

LOCK TABLES `rest_ingredient` WRITE;
/*!40000 ALTER TABLE `rest_ingredient` DISABLE KEYS */;
INSERT INTO `rest_ingredient` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','鸡肉',4,5.00,NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','猪肉',4,10.00,NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','鸡蛋',4,5.00,NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03','大米',4,50.00,NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03','食用油',4,10.00,NULL),(6,'2026-03-02 10:37:03','2026-03-02 10:37:03','酱油',5,1000.00,NULL);
/*!40000 ALTER TABLE `rest_ingredient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_inventory`
--

DROP TABLE IF EXISTS `rest_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ingredient_id` bigint unsigned NOT NULL COMMENT '原材料',
  `qty` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '当前数量',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rest_inventory_ingredient` (`ingredient_id`),
  CONSTRAINT `fk_rest_inventory_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `rest_ingredient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_inventory`
--

LOCK TABLES `rest_inventory` WRITE;
/*!40000 ALTER TABLE `rest_inventory` DISABLE KEYS */;
INSERT INTO `rest_inventory` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,25.50,NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,35.00,NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03',3,15.00,NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03',4,100.00,NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03',5,20.00,NULL),(6,'2026-03-02 10:37:03','2026-03-02 10:37:03',6,2000.00,NULL);
/*!40000 ALTER TABLE `rest_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_inventory_log`
--

DROP TABLE IF EXISTS `rest_inventory_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_inventory_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ingredient_id` bigint unsigned NOT NULL COMMENT '原材料',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'in入库 out出库 adjust调整',
  `qty` decimal(12,2) NOT NULL COMMENT '数量正负',
  `qty_after` decimal(12,2) DEFAULT NULL COMMENT '变动后库存',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '事由',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人',
  PRIMARY KEY (`id`),
  KEY `idx_rest_inv_log_ingredient` (`ingredient_id`),
  KEY `idx_rest_inv_log_type` (`type`),
  CONSTRAINT `fk_rest_inv_log_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `rest_ingredient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_inventory_log`
--

LOCK TABLES `rest_inventory_log` WRITE;
/*!40000 ALTER TABLE `rest_inventory_log` DISABLE KEYS */;
INSERT INTO `rest_inventory_log` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,'in',20.00,25.50,'采购入库',NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,'in',30.00,35.00,'采购入库',NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03',4,'out',-10.00,90.00,'厨房领用',NULL);
/*!40000 ALTER TABLE `rest_inventory_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_member`
--

DROP TABLE IF EXISTS `rest_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_member` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机',
  `level` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'normal' COMMENT '等级:normal普通 silver银 gold金',
  `points` int DEFAULT '0' COMMENT '积分',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '储值余额',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_member`
--

LOCK TABLES `rest_member` WRITE;
/*!40000 ALTER TABLE `rest_member` DISABLE KEYS */;
INSERT INTO `rest_member` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','刘会员','13800002001','normal',120,0.00,NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','陈会员','13800002002','silver',580,200.00,NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','杨会员','13800002003','gold',1500,500.00,NULL);
/*!40000 ALTER TABLE `rest_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_order`
--

DROP TABLE IF EXISTS `rest_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_order` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_no` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '订单号',
  `table_id` bigint unsigned DEFAULT NULL COMMENT '桌位',
  `member_id` bigint unsigned DEFAULT NULL COMMENT '会员',
  `staff_id` bigint unsigned DEFAULT NULL COMMENT '服务员',
  `total_amount` decimal(12,2) DEFAULT '0.00' COMMENT '订单总额',
  `discount_amount` decimal(10,2) DEFAULT '0.00' COMMENT '优惠金额',
  `pay_amount` decimal(12,2) DEFAULT '0.00' COMMENT '实付金额',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'draft' COMMENT 'draft草稿 ordered已下单 paid已结账 cancelled已取消',
  `pay_at` datetime DEFAULT NULL COMMENT '结账时间',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_order_table` (`table_id`),
  KEY `idx_rest_order_member` (`member_id`),
  KEY `idx_rest_order_staff` (`staff_id`),
  KEY `idx_rest_order_status` (`status`),
  CONSTRAINT `fk_rest_order_member` FOREIGN KEY (`member_id`) REFERENCES `rest_member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_order_staff` FOREIGN KEY (`staff_id`) REFERENCES `rest_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_order_table` FOREIGN KEY (`table_id`) REFERENCES `rest_table` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_order`
--

LOCK TABLES `rest_order` WRITE;
/*!40000 ALTER TABLE `rest_order` DISABLE KEYS */;
INSERT INTO `rest_order` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','ORD202603010001',1,1,1,85.00,5.00,80.00,'paid','2026-03-02 18:37:03',NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','ORD202603010002',3,2,1,156.00,0.00,156.00,'paid','2026-03-02 18:37:03',NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','ORD202603010003',2,NULL,2,45.00,0.00,45.00,'ordered',NULL,'加辣');
/*!40000 ALTER TABLE `rest_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_order_item`
--

DROP TABLE IF EXISTS `rest_order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_order_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL COMMENT '订单',
  `dish_id` bigint unsigned NOT NULL COMMENT '菜品',
  `qty` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  `amount` decimal(12,2) NOT NULL COMMENT '小计',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_order_item_order` (`order_id`),
  KEY `idx_rest_order_item_dish` (`dish_id`),
  CONSTRAINT `fk_rest_order_item_dish` FOREIGN KEY (`dish_id`) REFERENCES `rest_dish` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `rest_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_order_item`
--

LOCK TABLES `rest_order_item` WRITE;
/*!40000 ALTER TABLE `rest_order_item` DISABLE KEYS */;
INSERT INTO `rest_order_item` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,1,1.00,38.00,38.00,NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,4,1.00,12.00,12.00,NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,7,2.00,2.00,4.00,NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,8,2.00,5.00,10.00,NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03',1,6,1.00,18.00,18.00,NULL),(6,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,2,2.00,36.00,72.00,NULL),(7,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,3,1.00,48.00,48.00,NULL),(8,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,5,1.00,15.00,15.00,NULL),(9,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,7,3.00,2.00,6.00,NULL),(10,'2026-03-02 10:37:03','2026-03-02 10:37:03',2,10,1.00,15.00,15.00,NULL),(11,'2026-03-02 10:37:03','2026-03-02 10:37:03',3,1,1.00,38.00,38.00,'加辣'),(12,'2026-03-02 10:37:03','2026-03-02 10:37:03',3,8,1.00,5.00,5.00,NULL),(13,'2026-03-02 10:37:03','2026-03-02 10:37:03',3,7,1.00,2.00,2.00,NULL);
/*!40000 ALTER TABLE `rest_order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_staff`
--

DROP TABLE IF EXISTS `rest_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `role` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '角色:waiter服务员 chef厨师 cashier收银 admin管理员',
  `mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '1在职 0离职',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_staff`
--

LOCK TABLES `rest_staff` WRITE;
/*!40000 ALTER TABLE `rest_staff` DISABLE KEYS */;
INSERT INTO `rest_staff` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','张三','waiter','13900001001','1',NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','李四','waiter','13900001002','1',NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','王五','chef','13900001003','1','主厨'),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03','赵六','cashier','13900001004','1',NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03','孙七','admin','13900001005','1','店长');
/*!40000 ALTER TABLE `rest_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_table`
--

DROP TABLE IF EXISTS `rest_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_table` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '桌号',
  `seats` smallint DEFAULT '4' COMMENT '可坐人数',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'free' COMMENT 'free空 available可用 occupied占用',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_table`
--

LOCK TABLES `rest_table` WRITE;
/*!40000 ALTER TABLE `rest_table` DISABLE KEYS */;
INSERT INTO `rest_table` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','1号桌',2,'free',NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','2号桌',2,'free',NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','3号桌',4,'free',NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03','4号桌',4,'free',NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03','5号桌',6,'free',NULL),(6,'2026-03-02 10:37:03','2026-03-02 10:37:03','6号桌',8,'free','包间');
/*!40000 ALTER TABLE `rest_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rest_unit`
--

DROP TABLE IF EXISTS `rest_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rest_unit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '单位名称',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rest_unit`
--

LOCK TABLES `rest_unit` WRITE;
/*!40000 ALTER TABLE `rest_unit` DISABLE KEYS */;
INSERT INTO `rest_unit` VALUES (1,'2026-03-02 10:37:03','2026-03-02 10:37:03','份',NULL),(2,'2026-03-02 10:37:03','2026-03-02 10:37:03','瓶',NULL),(3,'2026-03-02 10:37:03','2026-03-02 10:37:03','杯',NULL),(4,'2026-03-02 10:37:03','2026-03-02 10:37:03','斤',NULL),(5,'2026-03-02 10:37:03','2026-03-02 10:37:03','克',NULL);
/*!40000 ALTER TABLE `rest_unit` ENABLE KEYS */;
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

-- Dump completed on 2026-03-02 18:39:35
