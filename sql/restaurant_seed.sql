-- 餐饮系统数据结构及 Demo 数据（单店）
-- 基于 LayCMS 动态表单，需在已有 laycms 数据库上执行
-- 七大模块：基础信息、商品、订单、库存、会员、财务、员工
-- 使用: mysql -u root -p laycms < sql/restaurant_seed.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ========== 1. 表单分组 ==========
INSERT INTO `cms_form_groups` (`name`, `sort_order`, `created_at`, `updated_at`)
VALUES ('餐饮管理', 20, NOW(), NOW());
SET @rest_group_id = LAST_INSERT_ID();

-- ========== 2. 表单定义 ==========
INSERT INTO `cms_forms` (`form_group_id`, `name`, `table_name`, `description`, `sort_order`, `created_at`, `updated_at`)
VALUES
-- 基础信息
(@rest_group_id, '菜品分类', 'rest_category', '基础-菜品分类', 1, NOW(), NOW()),
(@rest_group_id, '计量单位', 'rest_unit', '基础-单位', 2, NOW(), NOW()),
(@rest_group_id, '桌位', 'rest_table', '基础-餐桌', 3, NOW(), NOW()),
-- 商品
(@rest_group_id, '菜品', 'rest_dish', '商品-菜品', 4, NOW(), NOW()),
-- 订单
(@rest_group_id, '订单', 'rest_order', '订单-主表', 5, NOW(), NOW()),
(@rest_group_id, '订单明细', 'rest_order_item', '订单-明细', 6, NOW(), NOW()),
-- 库存
(@rest_group_id, '原材料', 'rest_ingredient', '库存-原材料', 7, NOW(), NOW()),
(@rest_group_id, '库存', 'rest_inventory', '库存-当前库存', 8, NOW(), NOW()),
(@rest_group_id, '库存流水', 'rest_inventory_log', '库存-入库出库', 9, NOW(), NOW()),
-- 会员
(@rest_group_id, '会员', 'rest_member', '会员-信息', 10, NOW(), NOW()),
-- 财务
(@rest_group_id, '收支记录', 'rest_finance', '财务-流水', 11, NOW(), NOW()),
-- 员工
(@rest_group_id, '员工', 'rest_staff', '员工-信息', 12, NOW(), NOW());

SET @fid_cat = (SELECT id FROM cms_forms WHERE table_name = 'rest_category' LIMIT 1);
SET @fid_unit = (SELECT id FROM cms_forms WHERE table_name = 'rest_unit' LIMIT 1);
SET @fid_table = (SELECT id FROM cms_forms WHERE table_name = 'rest_table' LIMIT 1);
SET @fid_dish = (SELECT id FROM cms_forms WHERE table_name = 'rest_dish' LIMIT 1);
SET @fid_order = (SELECT id FROM cms_forms WHERE table_name = 'rest_order' LIMIT 1);
SET @fid_order_item = (SELECT id FROM cms_forms WHERE table_name = 'rest_order_item' LIMIT 1);
SET @fid_ing = (SELECT id FROM cms_forms WHERE table_name = 'rest_ingredient' LIMIT 1);
SET @fid_inv = (SELECT id FROM cms_forms WHERE table_name = 'rest_inventory' LIMIT 1);
SET @fid_inv_log = (SELECT id FROM cms_forms WHERE table_name = 'rest_inventory_log' LIMIT 1);
SET @fid_member = (SELECT id FROM cms_forms WHERE table_name = 'rest_member' LIMIT 1);
SET @fid_finance = (SELECT id FROM cms_forms WHERE table_name = 'rest_finance' LIMIT 1);
SET @fid_staff = (SELECT id FROM cms_forms WHERE table_name = 'rest_staff' LIMIT 1);

-- ========== 3. 业务表结构（按依赖顺序 DROP/CREATE） ==========
DROP TABLE IF EXISTS `rest_order_item`;
DROP TABLE IF EXISTS `rest_order`;
DROP TABLE IF EXISTS `rest_inventory_log`;
DROP TABLE IF EXISTS `rest_inventory`;
DROP TABLE IF EXISTS `rest_dish`;
DROP TABLE IF EXISTS `rest_category`;
DROP TABLE IF EXISTS `rest_unit`;
DROP TABLE IF EXISTS `rest_table`;
DROP TABLE IF EXISTS `rest_ingredient`;
DROP TABLE IF EXISTS `rest_member`;
DROP TABLE IF EXISTS `rest_finance`;
DROP TABLE IF EXISTS `rest_staff`;

-- 基础信息
CREATE TABLE `rest_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `sort_order` smallint DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rest_unit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(20) NOT NULL COMMENT '单位名称',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rest_table` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) NOT NULL COMMENT '桌号',
  `seats` smallint DEFAULT 4 COMMENT '可坐人数',
  `status` varchar(20) DEFAULT 'free' COMMENT 'free空 available可用 occupied占用',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 商品
CREATE TABLE `rest_dish` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) NOT NULL COMMENT '菜品名称',
  `category_id` bigint unsigned DEFAULT NULL COMMENT '分类',
  `unit_id` bigint unsigned DEFAULT NULL COMMENT '单位',
  `price` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '售价',
  `cost` decimal(10,2) DEFAULT NULL COMMENT '成本价',
  `status` varchar(20) DEFAULT '1' COMMENT '1上架 0下架',
  `sort_order` smallint DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_dish_category` (`category_id`),
  KEY `idx_rest_dish_unit` (`unit_id`),
  CONSTRAINT `fk_rest_dish_category` FOREIGN KEY (`category_id`) REFERENCES `rest_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_dish_unit` FOREIGN KEY (`unit_id`) REFERENCES `rest_unit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 员工（无依赖）
CREATE TABLE `rest_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `role` varchar(30) DEFAULT NULL COMMENT '角色:waiter服务员 chef厨师 cashier收银 admin管理员',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机',
  `status` varchar(20) DEFAULT '1' COMMENT '1在职 0离职',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 会员
CREATE TABLE `rest_member` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机',
  `level` varchar(20) DEFAULT 'normal' COMMENT '等级:normal普通 silver银 gold金',
  `points` int DEFAULT 0 COMMENT '积分',
  `balance` decimal(10,2) DEFAULT 0 COMMENT '储值余额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 订单
CREATE TABLE `rest_order` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_no` varchar(32) DEFAULT NULL COMMENT '订单号',
  `table_id` bigint unsigned DEFAULT NULL COMMENT '桌位',
  `member_id` bigint unsigned DEFAULT NULL COMMENT '会员',
  `staff_id` bigint unsigned DEFAULT NULL COMMENT '服务员',
  `total_amount` decimal(12,2) DEFAULT 0 COMMENT '订单总额',
  `discount_amount` decimal(10,2) DEFAULT 0 COMMENT '优惠金额',
  `pay_amount` decimal(12,2) DEFAULT 0 COMMENT '实付金额',
  `status` varchar(30) DEFAULT 'draft' COMMENT 'draft草稿 ordered已下单 paid已结账 cancelled已取消',
  `pay_at` datetime DEFAULT NULL COMMENT '结账时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_order_table` (`table_id`),
  KEY `idx_rest_order_member` (`member_id`),
  KEY `idx_rest_order_staff` (`staff_id`),
  KEY `idx_rest_order_status` (`status`),
  CONSTRAINT `fk_rest_order_table` FOREIGN KEY (`table_id`) REFERENCES `rest_table` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_order_member` FOREIGN KEY (`member_id`) REFERENCES `rest_member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_order_staff` FOREIGN KEY (`staff_id`) REFERENCES `rest_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rest_order_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL COMMENT '订单',
  `dish_id` bigint unsigned NOT NULL COMMENT '菜品',
  `qty` decimal(10,2) NOT NULL DEFAULT 1 COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  `amount` decimal(12,2) NOT NULL COMMENT '小计',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_order_item_order` (`order_id`),
  KEY `idx_rest_order_item_dish` (`dish_id`),
  CONSTRAINT `fk_rest_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `rest_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_order_item_dish` FOREIGN KEY (`dish_id`) REFERENCES `rest_dish` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 库存
CREATE TABLE `rest_ingredient` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(100) NOT NULL COMMENT '原材料名称',
  `unit_id` bigint unsigned DEFAULT NULL COMMENT '单位',
  `min_stock` decimal(10,2) DEFAULT 0 COMMENT '最低库存预警',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_rest_ingredient_unit` (`unit_id`),
  CONSTRAINT `fk_rest_ingredient_unit` FOREIGN KEY (`unit_id`) REFERENCES `rest_unit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rest_inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ingredient_id` bigint unsigned NOT NULL COMMENT '原材料',
  `qty` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '当前数量',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rest_inventory_ingredient` (`ingredient_id`),
  CONSTRAINT `fk_rest_inventory_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `rest_ingredient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rest_inventory_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ingredient_id` bigint unsigned NOT NULL COMMENT '原材料',
  `type` varchar(20) NOT NULL COMMENT 'in入库 out出库 adjust调整',
  `qty` decimal(12,2) NOT NULL COMMENT '数量正负',
  `qty_after` decimal(12,2) DEFAULT NULL COMMENT '变动后库存',
  `reason` varchar(255) DEFAULT NULL COMMENT '事由',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人',
  PRIMARY KEY (`id`),
  KEY `idx_rest_inv_log_ingredient` (`ingredient_id`),
  KEY `idx_rest_inv_log_type` (`type`),
  CONSTRAINT `fk_rest_inv_log_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `rest_ingredient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 财务
CREATE TABLE `rest_finance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(20) NOT NULL COMMENT 'income收入 expense支出',
  `category` varchar(50) DEFAULT NULL COMMENT '类别',
  `amount` decimal(12,2) NOT NULL COMMENT '金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `occurred_at` datetime DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`),
  KEY `idx_rest_finance_type` (`type`),
  KEY `idx_rest_finance_at` (`occurred_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========== 4. 表单字段 ==========
INSERT INTO `cms_form_fields` (`form_id`, `field_name`, `label`, `form_control`, `options`, `sort_order`, `is_required`, `is_list_visible`, `created_at`, `updated_at`)
VALUES
-- 分类
(@fid_cat, 'name', '分类名称', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_cat, 'sort_order', '排序', 'number', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_cat, 'remark', '备注', 'textarea', NULL, 3, 0, 0, NOW(), NOW()),
-- 单位
(@fid_unit, 'name', '单位名称', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_unit, 'remark', '备注', 'textarea', NULL, 2, 0, 0, NOW(), NOW()),
-- 桌位
(@fid_table, 'name', '桌号', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_table, 'seats', '可坐人数', 'number', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_table, 'status', '状态', 'select', '{"free":"空闲","available":"可用","occupied":"占用"}', 3, 0, 1, NOW(), NOW()),
(@fid_table, 'remark', '备注', 'textarea', NULL, 4, 0, 0, NOW(), NOW()),
-- 菜品
(@fid_dish, 'name', '菜品名称', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_dish, 'category_id', '分类', 'relation', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_dish, 'unit_id', '单位', 'relation', NULL, 3, 0, 1, NOW(), NOW()),
(@fid_dish, 'price', '售价', 'number', NULL, 4, 1, 1, NOW(), NOW()),
(@fid_dish, 'cost', '成本价', 'number', NULL, 5, 0, 0, NOW(), NOW()),
(@fid_dish, 'status', '状态', 'radio', '{"1":"上架","0":"下架"}', 6, 0, 1, NOW(), NOW()),
(@fid_dish, 'sort_order', '排序', 'number', NULL, 7, 0, 1, NOW(), NOW()),
(@fid_dish, 'remark', '备注', 'textarea', NULL, 8, 0, 0, NOW(), NOW()),
-- 员工
(@fid_staff, 'name', '姓名', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_staff, 'role', '角色', 'select', '{"waiter":"服务员","chef":"厨师","cashier":"收银","admin":"管理员"}', 2, 0, 1, NOW(), NOW()),
(@fid_staff, 'mobile', '手机', 'input', NULL, 3, 0, 1, NOW(), NOW()),
(@fid_staff, 'status', '状态', 'radio', '{"1":"在职","0":"离职"}', 4, 0, 1, NOW(), NOW()),
(@fid_staff, 'remark', '备注', 'textarea', NULL, 5, 0, 0, NOW(), NOW()),
-- 会员
(@fid_member, 'name', '姓名', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_member, 'mobile', '手机', 'input', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_member, 'level', '等级', 'select', '{"normal":"普通","silver":"银卡","gold":"金卡"}', 3, 0, 1, NOW(), NOW()),
(@fid_member, 'points', '积分', 'number', NULL, 4, 0, 1, NOW(), NOW()),
(@fid_member, 'balance', '储值余额', 'number', NULL, 5, 0, 1, NOW(), NOW()),
(@fid_member, 'remark', '备注', 'textarea', NULL, 6, 0, 0, NOW(), NOW()),
-- 订单
(@fid_order, 'order_no', '订单号', 'input', NULL, 1, 0, 1, NOW(), NOW()),
(@fid_order, 'table_id', '桌位', 'relation', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_order, 'member_id', '会员', 'relation', NULL, 3, 0, 1, NOW(), NOW()),
(@fid_order, 'staff_id', '服务员', 'relation', NULL, 4, 0, 1, NOW(), NOW()),
(@fid_order, 'total_amount', '订单总额', 'number', NULL, 5, 0, 1, NOW(), NOW()),
(@fid_order, 'discount_amount', '优惠金额', 'number', NULL, 6, 0, 0, NOW(), NOW()),
(@fid_order, 'pay_amount', '实付金额', 'number', NULL, 7, 0, 1, NOW(), NOW()),
(@fid_order, 'status', '状态', 'select', '{"draft":"草稿","ordered":"已下单","paid":"已结账","cancelled":"已取消"}', 8, 0, 1, NOW(), NOW()),
(@fid_order, 'pay_at', '结账时间', 'datetime', NULL, 9, 0, 1, NOW(), NOW()),
(@fid_order, 'remark', '备注', 'textarea', NULL, 10, 0, 0, NOW(), NOW()),
-- 订单明细
(@fid_order_item, 'order_id', '订单', 'relation', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_order_item, 'dish_id', '菜品', 'relation', NULL, 2, 1, 1, NOW(), NOW()),
(@fid_order_item, 'qty', '数量', 'number', NULL, 3, 1, 1, NOW(), NOW()),
(@fid_order_item, 'price', '单价', 'number', NULL, 4, 1, 1, NOW(), NOW()),
(@fid_order_item, 'amount', '小计', 'number', NULL, 5, 0, 1, NOW(), NOW()),
(@fid_order_item, 'remark', '备注', 'textarea', NULL, 6, 0, 0, NOW(), NOW()),
-- 原材料
(@fid_ing, 'name', '原材料名称', 'input', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_ing, 'unit_id', '单位', 'relation', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_ing, 'min_stock', '最低库存预警', 'number', NULL, 3, 0, 1, NOW(), NOW()),
(@fid_ing, 'remark', '备注', 'textarea', NULL, 4, 0, 0, NOW(), NOW()),
-- 库存
(@fid_inv, 'ingredient_id', '原材料', 'relation', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_inv, 'qty', '当前数量', 'number', NULL, 2, 1, 1, NOW(), NOW()),
(@fid_inv, 'remark', '备注', 'textarea', NULL, 3, 0, 0, NOW(), NOW()),
-- 库存流水
(@fid_inv_log, 'ingredient_id', '原材料', 'relation', NULL, 1, 1, 1, NOW(), NOW()),
(@fid_inv_log, 'type', '类型', 'select', '{"in":"入库","out":"出库","adjust":"调整"}', 2, 1, 1, NOW(), NOW()),
(@fid_inv_log, 'qty', '数量', 'number', NULL, 3, 1, 1, NOW(), NOW()),
(@fid_inv_log, 'qty_after', '变动后库存', 'number', NULL, 4, 0, 1, NOW(), NOW()),
(@fid_inv_log, 'reason', '事由', 'textarea', NULL, 5, 0, 1, NOW(), NOW()),
-- 收支
(@fid_finance, 'type', '类型', 'select', '{"income":"收入","expense":"支出"}', 1, 1, 1, NOW(), NOW()),
(@fid_finance, 'category', '类别', 'input', NULL, 2, 0, 1, NOW(), NOW()),
(@fid_finance, 'amount', '金额', 'number', NULL, 3, 1, 1, NOW(), NOW()),
(@fid_finance, 'remark', '备注', 'textarea', NULL, 4, 0, 1, NOW(), NOW()),
(@fid_finance, 'occurred_at', '发生时间', 'datetime', NULL, 5, 0, 1, NOW(), NOW());

-- ========== 5. 表单关联 ==========
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_dish, f.id, @fid_cat, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_dish AND f.field_name = 'category_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_dish, f.id, @fid_unit, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_dish AND f.field_name = 'unit_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_order, f.id, @fid_table, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_order AND f.field_name = 'table_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_order, f.id, @fid_member, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_order AND f.field_name = 'member_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_order, f.id, @fid_staff, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_order AND f.field_name = 'staff_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_order_item, f.id, @fid_order, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_order_item AND f.field_name = 'order_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_order_item, f.id, @fid_dish, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_order_item AND f.field_name = 'dish_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_ing, f.id, @fid_unit, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_ing AND f.field_name = 'unit_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_inv, f.id, @fid_ing, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_inv AND f.field_name = 'ingredient_id' LIMIT 1;
INSERT INTO `cms_form_relations` (`form_id`, `form_field_id`, `related_form_id`, `related_field_name`, `created_at`, `updated_at`)
SELECT @fid_inv_log, f.id, @fid_ing, 'id', NOW(), NOW() FROM cms_form_fields f WHERE f.form_id = @fid_inv_log AND f.field_name = 'ingredient_id' LIMIT 1;

UPDATE cms_form_fields SET form_control = 'relation'
WHERE field_name IN ('category_id', 'unit_id', 'table_id', 'member_id', 'staff_id', 'order_id', 'dish_id', 'ingredient_id')
  AND form_id IN (@fid_dish, @fid_order, @fid_order_item, @fid_ing, @fid_inv, @fid_inv_log);

-- ========== 6. Demo 数据 ==========
INSERT INTO `rest_category` (`name`, `sort_order`, `remark`, `created_at`, `updated_at`) VALUES
('热菜', 1, NULL, NOW(), NOW()),
('凉菜', 2, NULL, NOW(), NOW()),
('汤羹', 3, NULL, NOW(), NOW()),
('主食', 4, NULL, NOW(), NOW()),
('饮料', 5, NULL, NOW(), NOW());

INSERT INTO `rest_unit` (`name`, `remark`, `created_at`, `updated_at`) VALUES
('份', NULL, NOW(), NOW()),
('瓶', NULL, NOW(), NOW()),
('杯', NULL, NOW(), NOW()),
('斤', NULL, NOW(), NOW()),
('克', NULL, NOW(), NOW());

INSERT INTO `rest_table` (`name`, `seats`, `status`, `remark`, `created_at`, `updated_at`) VALUES
('1号桌', 2, 'free', NULL, NOW(), NOW()),
('2号桌', 2, 'free', NULL, NOW(), NOW()),
('3号桌', 4, 'free', NULL, NOW(), NOW()),
('4号桌', 4, 'free', NULL, NOW(), NOW()),
('5号桌', 6, 'free', NULL, NOW(), NOW()),
('6号桌', 8, 'free', '包间', NOW(), NOW());

INSERT INTO `rest_staff` (`name`, `role`, `mobile`, `status`, `remark`, `created_at`, `updated_at`) VALUES
('张三', 'waiter', '13900001001', '1', NULL, NOW(), NOW()),
('李四', 'waiter', '13900001002', '1', NULL, NOW(), NOW()),
('王五', 'chef', '13900001003', '1', '主厨', NOW(), NOW()),
('赵六', 'cashier', '13900001004', '1', NULL, NOW(), NOW()),
('孙七', 'admin', '13900001005', '1', '店长', NOW(), NOW());

INSERT INTO `rest_member` (`name`, `mobile`, `level`, `points`, `balance`, `remark`, `created_at`, `updated_at`) VALUES
('刘会员', '13800002001', 'normal', 120, 0, NULL, NOW(), NOW()),
('陈会员', '13800002002', 'silver', 580, 200.00, NULL, NOW(), NOW()),
('杨会员', '13800002003', 'gold', 1500, 500.00, NULL, NOW(), NOW());

INSERT INTO `rest_dish` (`name`, `category_id`, `unit_id`, `price`, `cost`, `status`, `sort_order`, `remark`, `created_at`, `updated_at`) VALUES
('宫保鸡丁', 1, 1, 38.00, 12.00, '1', 1, NULL, NOW(), NOW()),
('鱼香肉丝', 1, 1, 36.00, 10.00, '1', 2, NULL, NOW(), NOW()),
('红烧肉', 1, 1, 48.00, 18.00, '1', 3, NULL, NOW(), NOW()),
('凉拌黄瓜', 2, 1, 12.00, 3.00, '1', 1, NULL, NOW(), NOW()),
('皮蛋豆腐', 2, 1, 15.00, 4.00, '1', 2, NULL, NOW(), NOW()),
('番茄蛋汤', 3, 1, 18.00, 5.00, '1', 1, NULL, NOW(), NOW()),
('米饭', 4, 1, 2.00, 0.50, '1', 1, NULL, NOW(), NOW()),
('可乐', 5, 2, 5.00, 1.50, '1', 1, NULL, NOW(), NOW()),
('雪碧', 5, 2, 5.00, 1.50, '1', 2, NULL, NOW(), NOW()),
('鲜榨橙汁', 5, 3, 15.00, 6.00, '1', 3, NULL, NOW(), NOW());

INSERT INTO `rest_ingredient` (`name`, `unit_id`, `min_stock`, `remark`, `created_at`, `updated_at`) VALUES
('鸡肉', 4, 5.00, NULL, NOW(), NOW()),
('猪肉', 4, 10.00, NULL, NOW(), NOW()),
('鸡蛋', 4, 5.00, NULL, NOW(), NOW()),
('大米', 4, 50.00, NULL, NOW(), NOW()),
('食用油', 4, 10.00, NULL, NOW(), NOW()),
('酱油', 5, 1000.00, NULL, NOW(), NOW());

INSERT INTO `rest_inventory` (`ingredient_id`, `qty`, `remark`, `created_at`, `updated_at`) VALUES
(1, 25.50, NULL, NOW(), NOW()),
(2, 35.00, NULL, NOW(), NOW()),
(3, 15.00, NULL, NOW(), NOW()),
(4, 100.00, NULL, NOW(), NOW()),
(5, 20.00, NULL, NOW(), NOW()),
(6, 2000.00, NULL, NOW(), NOW());

INSERT INTO `rest_inventory_log` (`ingredient_id`, `type`, `qty`, `qty_after`, `reason`, `created_at`, `updated_at`) VALUES
(1, 'in', 20.00, 25.50, '采购入库', NOW(), NOW()),
(2, 'in', 30.00, 35.00, '采购入库', NOW(), NOW()),
(4, 'out', -10.00, 90.00, '厨房领用', NOW(), NOW());

INSERT INTO `rest_order` (`order_no`, `table_id`, `member_id`, `staff_id`, `total_amount`, `discount_amount`, `pay_amount`, `status`, `pay_at`, `remark`, `created_at`, `updated_at`) VALUES
('ORD202603010001', 1, 1, 1, 85.00, 5.00, 80.00, 'paid', NOW(), NULL, NOW(), NOW()),
('ORD202603010002', 3, 2, 1, 156.00, 0, 156.00, 'paid', NOW(), NULL, NOW(), NOW()),
('ORD202603010003', 2, NULL, 2, 45.00, 0, 45.00, 'ordered', NULL, '加辣', NOW(), NOW());

INSERT INTO `rest_order_item` (`order_id`, `dish_id`, `qty`, `price`, `amount`, `remark`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 38.00, 38.00, NULL, NOW(), NOW()),
(1, 4, 1, 12.00, 12.00, NULL, NOW(), NOW()),
(1, 7, 2, 2.00, 4.00, NULL, NOW(), NOW()),
(1, 8, 2, 5.00, 10.00, NULL, NOW(), NOW()),
(1, 6, 1, 18.00, 18.00, NULL, NOW(), NOW()),
(2, 2, 2, 36.00, 72.00, NULL, NOW(), NOW()),
(2, 3, 1, 48.00, 48.00, NULL, NOW(), NOW()),
(2, 5, 1, 15.00, 15.00, NULL, NOW(), NOW()),
(2, 7, 3, 2.00, 6.00, NULL, NOW(), NOW()),
(2, 10, 1, 15.00, 15.00, NULL, NOW(), NOW()),
(3, 1, 1, 38.00, 38.00, '加辣', NOW(), NOW()),
(3, 8, 1, 5.00, 5.00, NULL, NOW(), NOW()),
(3, 7, 1, 2.00, 2.00, NULL, NOW(), NOW());

INSERT INTO `rest_finance` (`type`, `category`, `amount`, `remark`, `occurred_at`, `created_at`, `updated_at`) VALUES
('income', '堂食', 236.00, '当日营业款', NOW(), NOW(), NOW()),
('expense', '采购', 850.00, '食材采购', NOW(), NOW(), NOW()),
('expense', '工资', 8000.00, '本月工资', NOW(), NOW(), NOW());

-- ========== 7. 权限配置 ==========
INSERT IGNORE INTO `cms_group_permissions` (`user_group_id`, `table_name`, `can_create`, `can_read`, `can_update`, `can_delete`, `created_at`, `updated_at`)
VALUES
  (1, 'rest_category', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_unit', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_table', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_dish', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_order', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_order_item', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_ingredient', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_inventory', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_inventory_log', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_member', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_finance', 1, 1, 1, 1, NOW(), NOW()),
  (1, 'rest_staff', 1, 1, 1, 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

SELECT '餐饮系统数据初始化完成！' AS message;
