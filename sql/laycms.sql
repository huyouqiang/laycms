/*
 Navicat Premium Dump SQL

 Source Server         : 本地-127
 Source Server Type    : MySQL
 Source Server Version : 80042 (8.0.42)
 Source Host           : localhost:3306
 Source Schema         : laycms

 Target Server Type    : MySQL
 Target Server Version : 80042 (8.0.42)
 File Encoding         : 65001

 Date: 01/08/2025 02:44:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sys_admins
-- ----------------------------
DROP TABLE IF EXISTS `sys_admins`;
CREATE TABLE `sys_admins` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `salt` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `role` smallint unsigned DEFAULT NULL,
  `status` tinyint unsigned DEFAULT '1' COMMENT '1=正常，2=冻结',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `group` (`role`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sys_admins
-- ----------------------------
BEGIN;
INSERT INTO `sys_admins` (`uid`, `username`, `password`, `salt`, `email`, `role`, `status`) VALUES (1, 'adminer', '42506950578906df0b70e49dfdefa38c7337e4d1', '767e274ef5', 'hello@dilicms.com', 1, 1);
INSERT INTO `sys_admins` (`uid`, `username`, `password`, `salt`, `email`, `role`, `status`) VALUES (3, 'y-1', 'f429f6e2a5049a90c185f662a7a10ed92ec75ff4', '7cba99aa84', '123456@qq.com', 2, 1);
COMMIT;

-- ----------------------------
-- Table structure for sys_roles
-- ----------------------------
DROP TABLE IF EXISTS `sys_roles`;
CREATE TABLE `sys_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `rights` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `models` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `category_models` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `plugins` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sys_roles
-- ----------------------------
BEGIN;
INSERT INTO `sys_roles` (`id`, `name`, `rights`, `models`, `category_models`, `plugins`) VALUES (1, 'root', '', '', '', '');
INSERT INTO `sys_roles` (`id`, `name`, `rights`, `models`, `category_models`, `plugins`) VALUES (2, '运营人员', '30,31,32', 's_1', '0', '0');
INSERT INTO `sys_roles` (`id`, `name`, `rights`, `models`, `category_models`, `plugins`) VALUES (3, '测试人员', '30,32,34', '0', '0', '0');
INSERT INTO `sys_roles` (`id`, `name`, `rights`, `models`, `category_models`, `plugins`) VALUES (5, 'g_1', '50', 'notice_read_record', '0', '0');
COMMIT;

-- ----------------------------
-- Table structure for sys_sessions
-- ----------------------------
DROP TABLE IF EXISTS `sys_sessions`;
CREATE TABLE `sys_sessions` (
  `session_id` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `last_activity` int unsigned NOT NULL DEFAULT '0',
  `user_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sys_sessions
-- ----------------------------
BEGIN;
INSERT INTO `sys_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES ('664b40fb7ff59a133510b2798ce40f39', '116.235.56.155', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1753785160, 'a:3:{s:3:\"uid\";s:1:\"1\";s:10:\"model_type\";s:5:\"model\";s:5:\"model\";s:10:\"headmaster\";}');
INSERT INTO `sys_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES ('f408233edd94b629b19ef7f750bc7de0', '101.90.129.104', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1753795901, '');
COMMIT;

-- ----------------------------
-- Table structure for sys_settings
-- ----------------------------
DROP TABLE IF EXISTS `sys_settings`;
CREATE TABLE `sys_settings` (
  `backend_theme` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `backend_lang` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `backend_root_access` tinyint unsigned DEFAULT '1',
  `backend_access_point` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'admin',
  `backend_title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'DiliCMS后台管理',
  `backend_logo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'images/logo.gif',
  `plugin_dev_mode` tinyint unsigned NOT NULL DEFAULT '0',
  `backend_http_auth_on` tinyint DEFAULT '0',
  `backend_http_auth_user` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `backend_http_auth_password` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sys_settings
-- ----------------------------
BEGIN;
INSERT INTO `sys_settings` (`backend_theme`, `backend_lang`, `backend_root_access`, `backend_access_point`, `backend_title`, `backend_logo`, `plugin_dev_mode`, `backend_http_auth_on`, `backend_http_auth_user`, `backend_http_auth_password`) VALUES ('default', 'zh-cn', 1, '', '通用数据库管理系统', 'images/logo.jpeg', 0, 0, '', '');
COMMIT;

-- ----------------------------
-- Table structure for sys_site_settings
-- ----------------------------
DROP TABLE IF EXISTS `sys_site_settings`;
CREATE TABLE `sys_site_settings` (
  `site_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_domain` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_logo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_icp` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_terms` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `site_stats` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_footer` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_status` tinyint DEFAULT '1',
  `site_close_reason` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_keyword` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_description` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_theme` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `attachment_url` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `attachment_dir` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `attachment_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `attachment_maxupload` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `thumbs_preferences` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sys_site_settings
-- ----------------------------
BEGIN;
INSERT INTO `sys_site_settings` (`site_name`, `site_domain`, `site_logo`, `site_icp`, `site_terms`, `site_stats`, `site_footer`, `site_status`, `site_close_reason`, `site_keyword`, `site_description`, `site_theme`, `attachment_url`, `attachment_dir`, `attachment_type`, `attachment_maxupload`, `thumbs_preferences`) VALUES ('通用数据库管理系统', 'https://eick12nm.xyz', 'images/logo.jpeg', '©2024 皖ICP备XXXXXX号-X', '', '', '', 1, '网站维护升级中......', '通用数据库管理系统', '通用数据库管理系统', 'default', '/attachments', 'attachments', '*.jpg;*.jpeg;*.gif;*.png;*.doc;', '2097152', '[]');
COMMIT;

-- ----------------------------
-- Table structure for usr_school
-- ----------------------------
DROP TABLE IF EXISTS `usr_school`;
CREATE TABLE `usr_school` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id[input]',
  `create_time` int unsigned NOT NULL DEFAULT '0' COMMENT '时间[date|Y-m-d H:i:s]',
  `student_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '姓名[input]',
  `student_gender` tinyint NOT NULL COMMENT '性别[radio|1=男&2=女]',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of usr_school
-- ----------------------------
BEGIN;
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (1, 1753947373, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (2, 1753947373, 'huhu9', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (3, 1753947374, 'huhu5', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (4, 1753947374, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (5, 1753947374, 'huhu5', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (6, 1753947374, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (7, 1753947374, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (8, 1753947374, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (9, 1753947374, 'huhu5', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (10, 1753947374, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (11, 1753947375, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (12, 1753947375, 'huhu9', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (13, 1753947375, 'huhu5', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (14, 1753947375, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (15, 1753947375, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (16, 1753947375, 'huhu7', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (17, 1753947375, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (18, 1753947375, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (19, 1753947375, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (20, 1753947376, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (21, 1753947376, 'huhu3', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (22, 1753947376, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (23, 1753947376, 'huhu3', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (24, 1753947376, 'huhu8', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (25, 1753947376, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (26, 1753947376, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (27, 1753947376, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (28, 1753947376, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (29, 1753947376, 'huhu1', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (30, 1753947376, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (31, 1753947376, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (32, 1753947376, 'huhu8', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (33, 1753947384, 'huhu2', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (34, 1753947384, 'huhu4', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (35, 1753947385, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (36, 1753947385, 'huhu7', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (37, 1753947385, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (38, 1753947385, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (39, 1753947385, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (40, 1753947385, 'huhu7', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (41, 1753947385, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (42, 1753947385, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (43, 1753947385, 'huhu3', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (44, 1753947385, 'huhu2', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (45, 1753947385, 'huhu9', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (46, 1753947385, 'huhu2', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (47, 1753947386, 'huhu7', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (48, 1753947386, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (49, 1753947386, 'huhu2', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (50, 1753947386, 'huhu2', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (51, 1753947386, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (52, 1753947386, 'huhu3', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (53, 1753947386, 'huhu3', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (54, 1753947386, 'huhu2', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (55, 1753947386, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (56, 1753947387, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (57, 1753947387, 'huhu2', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (58, 1753947387, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (59, 1753947387, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (60, 1753947387, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (61, 1753947387, 'huhu4', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (62, 1753947387, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (63, 1753947387, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (64, 1753947387, 'huhu2', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (65, 1753947387, 'huhu8', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (66, 1753947387, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (67, 1753947387, 'huhu8', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (68, 1753947387, 'huhu3', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (69, 1753947387, 'huhu3', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (70, 1753947387, 'huhu3', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (71, 1753947387, 'huhu7', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (72, 1753947387, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (73, 1753947387, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (74, 1753947387, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (75, 1753947387, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (76, 1753947387, 'huhu9', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (77, 1753947387, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (78, 1753947387, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (79, 1753947387, 'huhu1', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (80, 1753947387, 'huhu3', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (81, 1753947388, 'huhu1', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (82, 1753947388, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (83, 1753947388, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (84, 1753947388, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (85, 1753947388, 'huhu2', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (86, 1753947388, 'huhu3', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (87, 1753947388, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (88, 1753947388, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (89, 1753947388, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (90, 1753947388, 'huhu1', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (91, 1753947388, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (92, 1753947388, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (93, 1753947388, 'huhu5', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (94, 1753947388, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (95, 1753947388, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (96, 1753947388, 'huhu9', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (97, 1753947388, 'huhu2', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (98, 1753947388, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (99, 1753947388, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (100, 1753947388, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (101, 1753947388, 'huhu5', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (102, 1753947388, 'huhu6', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (103, 1753947388, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (104, 1753947389, 'huhu3', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (105, 1753947389, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (106, 1753947389, 'huhu8', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (107, 1753947389, 'huhu9', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (108, 1753947389, 'huhu0', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (109, 1753947389, 'huhu8', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (110, 1753947389, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (111, 1753947389, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (112, 1753947389, 'huhu1', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (113, 1753947389, 'huhu8', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (114, 1753947389, 'huhu4', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (115, 1753947389, 'huhu0', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (116, 1753947389, 'huhu6', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (117, 1753947389, 'huhu4', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (118, 1753947389, 'huhu7', 1);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (119, 1753947389, 'huhu3', 0);
INSERT INTO `usr_school` (`id`, `create_time`, `student_name`, `student_gender`) VALUES (120, 1753947389, 'huhu6', 0);
COMMIT;

-- ----------------------------
-- Table structure for usr_student
-- ----------------------------
DROP TABLE IF EXISTS `usr_student`;
CREATE TABLE `usr_student` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id[input]',
  `create_time` int unsigned NOT NULL DEFAULT '0' COMMENT '时间[date|Y-m-d H:i:s]',
  `student_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '姓名[input]',
  `student_gender` tinyint NOT NULL COMMENT '性别[radio|1=男&2=女&3=保密]',
  `student_like` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '爱好[checkbox|1=看书&2=旅游&3=音乐]',
  `student_img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '头像[file]',
  `student_json` json NOT NULL COMMENT '配置[json]',
  `student_html` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '富文本[editor]',
  `student_float` float NOT NULL COMMENT '浮点型[float]',
  `student_select` tinyint NOT NULL COMMENT '下拉框[select|1=男&2=女]',
  `student_notice` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '通知[checkbox|1=全校&2=班级&3=个人]',
  `student_file` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '文件[file]',
  `student_fee` tinyint NOT NULL COMMENT '学费[radio|1=已交&2=未交&3=未知]',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of usr_student
-- ----------------------------
BEGIN;
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (12, 1753910031, 'huhu', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (13, 1753941593, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (14, 1753941593, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (15, 1753941594, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (16, 1753941594, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (17, 1753941594, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (18, 1753941594, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (19, 1753941594, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (20, 1753941594, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (21, 1753941598, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (22, 1753941598, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (23, 1753941598, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (24, 1753941598, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (25, 1753941598, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (26, 1753941598, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (27, 1753941599, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (28, 1753941599, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (29, 1753941599, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (30, 1753941599, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (31, 1753941599, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (32, 1753941599, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (33, 1753941600, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (34, 1753941600, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (35, 1753941600, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (36, 1753941600, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (37, 1753941601, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (38, 1753941601, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (39, 1753941601, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (40, 1753941601, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (41, 1753941602, 'huhu4', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (42, 1753941602, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (43, 1753941602, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (44, 1753941602, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (45, 1753941602, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (57, 1753941604, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (58, 1753941604, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (59, 1753941604, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (60, 1753941604, 'huhu5', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (61, 1753941604, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (62, 1753941604, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (63, 1753941604, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (64, 1753941604, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (65, 1753941604, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (66, 1753941604, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (67, 1753941604, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (68, 1753941604, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (69, 1753941605, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (70, 1753941605, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (71, 1753941605, 'huhu4', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (72, 1753941605, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (73, 1753941605, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (74, 1753941605, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (75, 1753941605, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (76, 1753941605, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (77, 1753941605, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (78, 1753941605, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (79, 1753941605, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (80, 1753941605, 'huhu3', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (81, 1753941605, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (82, 1753941605, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (83, 1753941605, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (84, 1753941605, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (85, 1753941605, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (86, 1753941605, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (87, 1753941605, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (88, 1753941605, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (89, 1753941605, 'huhu5', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (90, 1753941606, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (91, 1753941606, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (92, 1753941606, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (93, 1753941606, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (94, 1753941606, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (95, 1753941606, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (96, 1753941606, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (97, 1753941606, 'huhu3', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (98, 1753941606, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (99, 1753941606, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (100, 1753941606, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (101, 1753941606, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (102, 1753941606, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (103, 1753941606, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (104, 1753941606, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (105, 1753941606, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (106, 1753941606, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (107, 1753941606, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (108, 1753941606, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (109, 1753941606, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (110, 1753941606, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (111, 1753941606, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (112, 1753941606, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (113, 1753941607, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (114, 1753941607, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (115, 1753941607, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (116, 1753941607, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (117, 1753941607, 'huhu4', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (118, 1753941607, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (119, 1753941607, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (120, 1753941607, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (121, 1753941607, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (122, 1753941607, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (123, 1753941607, 'huhu5', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (124, 1753941607, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (125, 1753941607, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (126, 1753941607, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (127, 1753941608, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (128, 1753941608, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (129, 1753941608, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (130, 1753941608, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (131, 1753941609, 'huhu5', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (132, 1753941609, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (133, 1753941609, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (134, 1753941609, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (135, 1753941609, 'huhu3', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (136, 1753941609, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (137, 1753941609, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (138, 1753941609, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (139, 1753941609, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (140, 1753941609, 'huhu3', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (141, 1753941610, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (142, 1753941610, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (143, 1753941610, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (144, 1753941610, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (145, 1753941611, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (146, 1753941611, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (147, 1753941611, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (148, 1753941611, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (149, 1753941611, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (150, 1753941611, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (151, 1753941632, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (152, 1753941632, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (153, 1753941632, 'huhu2', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (154, 1753941632, 'huhu8', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (155, 1753941632, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (156, 1753941632, 'huhu3', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (157, 1753941632, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (158, 1753941632, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (159, 1753941632, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (160, 1753941632, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (161, 1753941632, 'huhu6', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (162, 1753941632, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (163, 1753941632, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (164, 1753941633, 'huhu4', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (165, 1753941633, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (166, 1753941633, 'huhu0', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (167, 1753941633, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (168, 1753941633, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (169, 1753941633, 'huhu6', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (170, 1753941633, 'huhu4', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (171, 1753941633, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (172, 1753941633, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (173, 1753941646, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (174, 1753941647, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (175, 1753941648, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (176, 1753941649, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (177, 1753941650, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (178, 1753941650, 'huhu5', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (179, 1753941659, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (180, 1753941659, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (181, 1753941670, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (182, 1753941670, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (183, 1753941695, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (184, 1753941696, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (185, 1753941696, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (186, 1753941696, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (187, 1753941696, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (188, 1753941696, 'huhu1', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (189, 1753941696, 'huhu1', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (190, 1753941696, 'huhu4', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (191, 1753941697, 'huhu7', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (192, 1753941697, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (193, 1753941697, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (194, 1753941697, 'huhu9', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (199, 1753941757, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (200, 1753941758, 'huhu3', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (201, 1753941761, 'huhu8', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (202, 1753941768, 'huhu5', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (203, 1753941768, 'huhu4', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (204, 1753941769, 'huhu2', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (205, 1753941769, 'huhu9', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (206, 1753941770, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (207, 1753941770, 'huhu7', 0, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (208, 1753941806, 'huhu0', 1, '', '', 'null', '', 0, 0, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (209, 1753941806, 'huhu8', 1, '1', '', 'null', '1', 0, 1, '', '', 0);
INSERT INTO `usr_student` (`id`, `create_time`, `student_name`, `student_gender`, `student_like`, `student_img`, `student_json`, `student_html`, `student_float`, `student_select`, `student_notice`, `student_file`, `student_fee`) VALUES (210, 1753941808, 'huhu3', 3, '2,3', '/uploads/20250731/04cc3ee916570139.png', '{}', '<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Textarea Value Example</title>\n</head>\n<body>', 0.15, 1, '2,3', '/uploads/20250731/04cc3ee916570139.jpg', 3);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
