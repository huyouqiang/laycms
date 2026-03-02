-- OA 办公系统数据结构及 Demo 数据
-- 基于 LayCMS 动态表单，需在已有 laycms 数据库上执行
-- 使用: mysql -u root -p laycms < sql/oa_seed.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ========== 1. 表单分组 ==========
INSERT INTO `cms_form_groups` (`name`, `sort_order`, `created_at`, `updated_at`)
VALUES ('OA办公', 10, NOW(), NOW());
SET @oa_group_id = LAST_INSERT_ID();

-- ========== 2. 表单定义 ==========
INSERT INTO `cms_forms` (`form_group_id`, `name`, `table_name`, `description`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@oa_group_id, '部门', 'oa_department', '组织架构-部门', 1, NOW(), NOW()),
  (@oa_group_id, '员工', 'oa_staff', '员工信息', 2, NOW(), NOW()),
  (@oa_group_id, '公告', 'oa_announcement', '公司公告', 3, NOW(), NOW()),
  (@oa_group_id, '请假单', 'oa_leave', '员工请假', 4, NOW(), NOW()),
  (@oa_group_id, '费用报销', 'oa_expense', '费用报销单', 5, NOW(), NOW());

SET @fid_dept = (SELECT id FROM cms_forms WHERE table_name = 'oa_department' LIMIT 1);
SET @fid_staff = (SELECT id FROM cms_forms WHERE table_name = 'oa_staff' LIMIT 1);
SET @fid_ann = (SELECT id FROM cms_forms WHERE table_name = 'oa_announcement' LIMIT 1);
SET @fid_leave = (SELECT id FROM cms_forms WHERE table_name = 'oa_leave' LIMIT 1);
SET @fid_expense = (SELECT id FROM cms_forms WHERE table_name = 'oa_expense' LIMIT 1);

-- ========== 3. 业务表结构 ==========
DROP TABLE IF EXISTS `oa_expense`;
DROP TABLE IF EXISTS `oa_leave`;
DROP TABLE IF EXISTS `oa_announcement`;
DROP TABLE IF EXISTS `oa_staff`;
DROP TABLE IF EXISTS `oa_department`;

CREATE TABLE `oa_department` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) NOT NULL COMMENT '部门名称',
  `code` varchar(50) DEFAULT NULL COMMENT '部门编码',
  `parent_id` bigint unsigned DEFAULT NULL COMMENT '上级部门',
  `sort_order` smallint DEFAULT 0 COMMENT '排序',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `idx_oa_department_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oa_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `emp_no` varchar(30) DEFAULT NULL COMMENT '工号',
  `department_id` bigint unsigned DEFAULT NULL COMMENT '部门',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `position` varchar(50) DEFAULT NULL COMMENT '职位',
  `status` varchar(20) DEFAULT '1' COMMENT '状态:1在职 2离职',
  PRIMARY KEY (`id`),
  KEY `idx_oa_staff_dept` (`department_id`),
  CONSTRAINT `fk_oa_staff_department` FOREIGN KEY (`department_id`) REFERENCES `oa_department` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oa_announcement` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(200) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `type` varchar(20) DEFAULT 'notice' COMMENT '类型:notice通知 urgent紧急',
  `publish_at` datetime DEFAULT NULL COMMENT '发布时间',
  `author_id` bigint unsigned DEFAULT NULL COMMENT '发布人',
  PRIMARY KEY (`id`),
  KEY `idx_oa_announcement_publish` (`publish_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oa_leave` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `staff_id` bigint unsigned NOT NULL COMMENT '申请人',
  `leave_type` varchar(30) NOT NULL COMMENT '请假类型:annual年假 sick病假 personal事假',
  `start_date` date NOT NULL COMMENT '开始日期',
  `end_date` date NOT NULL COMMENT '结束日期',
  `days` decimal(5,2) DEFAULT NULL COMMENT '天数',
  `reason` text COMMENT '请假事由',
  `status` varchar(20) DEFAULT 'pending' COMMENT '状态:pending待审批 approved已通过 rejected已拒绝',
  PRIMARY KEY (`id`),
  KEY `idx_oa_leave_staff` (`staff_id`),
  CONSTRAINT `fk_oa_leave_staff` FOREIGN KEY (`staff_id`) REFERENCES `oa_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `oa_expense` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `staff_id` bigint unsigned NOT NULL COMMENT '申请人',
  `title` varchar(200) NOT NULL COMMENT '报销事由',
  `amount` decimal(12,2) NOT NULL COMMENT '金额',
  `category` varchar(50) DEFAULT NULL COMMENT '类别:travel差旅 office办公 other其他',
  `remark` text COMMENT '备注',
  `status` varchar(20) DEFAULT 'pending' COMMENT '状态:pending待审批 approved已通过 rejected已拒绝',
  PRIMARY KEY (`id`),
  KEY `idx_oa_expense_staff` (`staff_id`),
  CONSTRAINT `fk_oa_expense_staff` FOREIGN KEY (`staff_id`) REFERENCES `oa_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========== 4. 表单字段 ==========
INSERT INTO `cms_form_fields` (`form_id`, `field_name`, `label`, `form_control`, `options`, `sort_order`, `is_required`, `is_list_visible`, `created_at`, `updated_at`)
VALUES
-- 部门
(@fid_dept, 'name', '部门名称', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_dept, 'code', '部门编码', 'input', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_dept, 'parent_id', '上级部门', 'relation', NULL, 3, 0, 1, NOW(), NOW()),
(@fid_dept, 'sort_order', '排序', 'number', NULL, 4, 0, 1, NOW(), NOW()),
(@fid_dept, 'description', '描述', 'textarea', NULL, 5, 0, 0, NOW(), NOW()),
-- 员工
(@fid_staff, 'name', '姓名', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_staff, 'emp_no', '工号', 'input', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_staff, 'department_id', '部门', 'relation', NULL, 3, 0, 1, NOW(), NOW()),
(@fid_staff, 'mobile', '手机', 'input', NULL, 4, 0, 1, NOW(), NOW()),
(@fid_staff, 'email', '邮箱', 'input', NULL, 5, 0, 1, NOW(), NOW()),
(@fid_staff, 'position', '职位', 'input', NULL, 6, 0, 1, NOW(), NOW()),
(@fid_staff, 'status', '状态', 'radio', '{"1":"在职","2":"离职"}', 7, 0, 1, NOW(), NOW()),
-- 公告
(@fid_ann, 'title', '标题', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_ann, 'content', '内容', 'editor', NULL, 2, 0, 0, NOW(), NOW()),
(@fid_ann, 'type', '类型', 'select', '{"notice":"普通通知","urgent":"紧急通知"}', 3, 0, 1, NOW(), NOW()),
(@fid_ann, 'publish_at', '发布时间', 'datetime', NULL, 4, 0, 1, NOW(), NOW()),
-- 请假
(@fid_leave, 'staff_id', '申请人', 'relation', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_leave, 'leave_type', '请假类型', 'select', '{"annual":"年假","sick":"病假","personal":"事假"}', 2, 1, 1, NOW(), NOW()),
(@fid_leave, 'start_date', '开始日期', 'date', NULL, 3, 1, 1, NOW(), NOW()),
(@fid_leave, 'end_date', '结束日期', 'date', NULL, 4, 1, 1, NOW(), NOW()),
(@fid_leave, 'days', '天数', 'number', NULL, 5, 0, 1, NOW(), NOW()),
(@fid_leave, 'reason', '请假事由', 'textarea', NULL, 6, 0, 1, NOW(), NOW()),
(@fid_leave, 'status', '状态', 'select', '{"pending":"待审批","approved":"已通过","rejected":"已拒绝"}', 7, 0, 1, NOW(), NOW()),
-- 报销
(@fid_expense, 'staff_id', '申请人', 'relation', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_expense, 'title', '报销事由', 'input', NULL, 2, 1, 1, NOW(), NOW()),
(@fid_expense, 'amount', '金额', 'number', NULL, 3, 1, 1, NOW(), NOW()),
(@fid_expense, 'category', '类别', 'select', '{"travel":"差旅","office":"办公","other":"其他"}', 4, 0, 1, NOW(), NOW()),
(@fid_expense, 'remark', '备注', 'textarea', NULL, 5, 0, 0, NOW(), NOW()),
(@fid_expense, 'status', '状态', 'select', '{"pending":"待审批","approved":"已通过","rejected":"已拒绝"}', 6, 0, 1, NOW(), NOW());

-- ========== 5. 表单关联（relation 字段） ==========
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_dept, f.id, @fid_dept, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_dept AND f.field_name = 'parent_id' LIMIT 1;

INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_staff, f.id, @fid_dept, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_staff AND f.field_name = 'department_id' LIMIT 1;

INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_leave, f.id, @fid_staff, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_leave AND f.field_name = 'staff_id' LIMIT 1;

INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_expense, f.id, @fid_staff, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_expense AND f.field_name = 'staff_id' LIMIT 1;

-- 需将 relation 字段的 form_control 设为 relation（LayCMS 通过 form_relations 表识别）
UPDATE cms_form_fields SET form_control = 'relation'
WHERE field_name IN ('parent_id', 'department_id', 'staff_id')
  AND form_id IN (@fid_dept, @fid_staff, @fid_leave, @fid_expense);

-- ========== 6. Demo 数据 ==========
INSERT INTO `oa_department` (`name`, `code`, `parent_id`, `sort_order`, `description`, `created_at`, `updated_at`) VALUES
('总经理室', 'GM', NULL, 1, '公司最高决策层', NOW(), NOW()),
('技术部', 'TECH', 1, 2, '技术研发', NOW(), NOW()),
('产品部', 'PM', 1, 3, '产品设计与管理', NOW(), NOW()),
('行政部', 'ADMIN', 1, 4, '行政人事', NOW(), NOW()),
('财务部', 'FIN', 1, 5, '财务管理', NOW(), NOW());

INSERT INTO `oa_staff` (`name`, `emp_no`, `department_id`, `mobile`, `email`, `position`, `status`, `created_at`, `updated_at`) VALUES
('张总', 'E001', 1, '13800001001', 'zhang@company.com', '总经理', '1', NOW(), NOW()),
('李技术', 'E002', 2, '13800001002', 'li@company.com', '技术总监', '1', NOW(), NOW()),
('王产品', 'E003', 3, '13800001003', 'wang@company.com', '产品经理', '1', NOW(), NOW()),
('赵行政', 'E004', 4, '13800001004', 'zhao@company.com', '行政主管', '1', NOW(), NOW()),
('钱会计', 'E005', 5, '13800001005', 'qian@company.com', '会计', '1', NOW(), NOW()),
('孙开发', 'E006', 2, '13800001006', 'sun@company.com', '开发工程师', '1', NOW(), NOW()),
('周设计', 'E007', 3, '13800001007', 'zhou@company.com', 'UI设计师', '1', NOW(), NOW());

INSERT INTO `oa_announcement` (`title`, `content`, `type`, `publish_at`, `created_at`, `updated_at`) VALUES
('春节放假通知', '<p>经公司研究决定，春节放假时间为2月8日至2月17日，共10天。请各部门做好节前工作安排，祝大家新春快乐！</p>', 'notice', NOW(), NOW(), NOW()),
('重要：系统升级维护通知', '<p><strong>紧急通知</strong></p><p>本周六凌晨2:00-6:00 进行系统升级维护，届时OA系统将暂时无法访问，请提前安排好工作。</p>', 'urgent', NOW(), NOW(), NOW()),
('2026年度体检安排', '<p>公司定于3月中旬组织年度体检，具体时间地点另行通知，请各位同事关注。</p>', 'notice', NOW(), NOW(), NOW());

INSERT INTO `oa_leave` (`staff_id`, `leave_type`, `start_date`, `end_date`, `days`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(2, 'annual', '2026-03-10', '2026-03-12', 3, '家庭事务', 'approved', NOW(), NOW()),
(6, 'sick', '2026-03-05', '2026-03-06', 2, '身体不适需休息', 'approved', NOW(), NOW()),
(7, 'personal', '2026-03-20', '2026-03-21', 2, '办理证件', 'pending', NOW(), NOW());

INSERT INTO `oa_expense` (`staff_id`, `title`, `amount`, `category`, `remark`, `status`, `created_at`, `updated_at`) VALUES
(2, '北京出差差旅费', 3500.00, 'travel', '3月出差北京参加技术大会', 'approved', NOW(), NOW()),
(6, '办公用品采购', 320.50, 'office', '键盘、鼠标等', 'approved', NOW(), NOW()),
(3, '客户招待费', 1200.00, 'other', '项目洽谈招待', 'pending', NOW(), NOW());

-- ========== 7. 权限配置（为超级管理员组开放 OA 表权限） ==========
INSERT IGNORE INTO `cms_group_permissions` (`user_group_id`, `table_name`, `can_create`, `can_read`, `can_update`, `can_delete`, `created_at`, `updated_at`)
VALUES
  (1, 'oa_department', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'oa_staff', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'oa_announcement', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'oa_leave', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'oa_expense', 1, 1, 1, 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- 完成
SELECT 'OA 系统数据初始化完成！' AS message;
