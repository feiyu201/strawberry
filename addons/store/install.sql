DROP TABLE IF EXISTS `__PREFIX__brand`;
CREATE TABLE `__PREFIX__brand` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `logo` char(255) NOT NULL DEFAULT '' COMMENT 'logo图标',
  `name` char(30) NOT NULL COMMENT '名称',
  `website_url` char(255) NOT NULL DEFAULT '' COMMENT '官网地址',
  `is_enable` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `sort`  int(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '顺序',
  `seo_title` char(100) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` char(130) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  `seo_desc` char(230) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `upd_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='品牌表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__brand_category_join`;
CREATE TABLE `__PREFIX__brand_category_join` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `brand_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '品牌id',
  `brand_category_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类id',
  `add_time` int(11) UNSIGNED DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='品牌分类关联表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods`;
CREATE TABLE `__PREFIX__goods` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `brand_id` int(11) UNSIGNED DEFAULT '0' COMMENT '品牌id',
  `site_type` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '商品类型（跟随站点类型一致[0销售, 1展示, 2自提, 3虚拟销售, 4销售+自提]）',
  `title` char(60) NOT NULL DEFAULT '' COMMENT '标题',
  `title_color` char(7) NOT NULL DEFAULT '' COMMENT '标题颜色',
  `simple_desc` char(160) NOT NULL DEFAULT '' COMMENT '简述',
  `model` char(30) NOT NULL DEFAULT '' COMMENT '型号',
  `place_origin` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产地（地区省id）',
  `inventory` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存（所有规格库存总和）',
  `inventory_unit` char(15) NOT NULL DEFAULT '' COMMENT '库存单位',
  `images` text COMMENT '图片ID用英文逗号链接，第一张为默认封面',
  `original_price` char(60) NOT NULL DEFAULT '' COMMENT '原价（单值:10, 区间:10.00-20.00）一般用于展示使用',
  `min_original_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最低原价',
  `max_original_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最大原价',
  `price` char(60) NOT NULL DEFAULT '' COMMENT '销售价格（单值:10, 区间:10.00-20.00）一般用于展示使用',
  `min_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最低价格',
  `max_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最高价格',
  `give_integral` int(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '购买赠送积分比例',
  `buy_min_number` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '最低起购数量 （默认1）',
  `buy_max_number` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最大购买数量（最大数值 100000000, 小于等于0或空则不限）',
  `is_deduction_inventory` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否扣减库存（0否, 1是）',
  `is_shelves` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否上架（下架后用户不可见, 0否, 1是）',
  `is_home_recommended` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否首页推荐（0否, 1是）',
  `content_web` mediumtext COMMENT '电脑端详情内容',
  `photo_count` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '相册图片数量',
  `sales_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `access_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '访问次数',
  `video` char(255) NOT NULL DEFAULT '' COMMENT '短视频',
  `is_exist_many_spec` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否存在多个规格（0否, 1是）',
  `spec_base` text COMMENT '规格ID用英文逗号链接',
  `fictitious_goods_value` text COMMENT '虚拟商品展示数据',
  `seo_title` char(100) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` char(130) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  `seo_desc` char(230) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `is_delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否已删除（0 未删除, 大于0则是删除时间）',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `upd_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods_browse`;
CREATE TABLE `__PREFIX__goods_browse` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `upd_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户商品浏览记录表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods_category`;
CREATE TABLE `__PREFIX__goods_category` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `icon` char(255) NOT NULL DEFAULT '' COMMENT 'icon图标',
  `name` char(60) NOT NULL DEFAULT '' COMMENT '名称',
  `vice_name` char(80) NOT NULL DEFAULT '' COMMENT '副标题',
  `describe` char(255) NOT NULL DEFAULT '' COMMENT '描述',
  `bg_color` char(30) NOT NULL DEFAULT '' COMMENT 'css背景色值',
  `big_images` char(255) NOT NULL DEFAULT '' COMMENT '大图片',
  `is_home_recommended` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否首页推荐（0否, 1是）',
  `sort`  int(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '顺序',
  `is_enable` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否启用（0否，1是）',
  `seo_title` char(100) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` char(130) NOT NULL DEFAULT '' COMMENT 'SEO关键字',
  `seo_desc` char(230) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `upd_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品分类表' ROW_FORMAT=DYNAMIC;

ALTER TABLE `__PREFIX__goods_category` ADD PRIMARY KEY(`id`);
ALTER TABLE `__PREFIX__goods_category` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id';

DROP TABLE IF EXISTS `__PREFIX__goods_category_join`;
CREATE TABLE `__PREFIX__goods_category_join` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `category_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类id',
  `add_time` int(11) UNSIGNED DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品分类关联表' ROW_FORMAT=DYNAMIC;

ALTER TABLE `__PREFIX__goods_category_join` ADD PRIMARY KEY(`id`);
ALTER TABLE `__PREFIX__goods_category_join` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id';

DROP TABLE IF EXISTS `__PREFIX__goods_comments`;
CREATE TABLE `__PREFIX__goods_comments` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '业务订单id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `business_type` char(30) NOT NULL DEFAULT '' COMMENT '业务类型名称（如订单 order）',
  `content` char(255) NOT NULL DEFAULT '' COMMENT '评价内容',
  `images` text COMMENT '图片数据（一维数组json）',
  `reply` char(255) NOT NULL DEFAULT '' COMMENT '回复内容',
  `rating` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评价级别（默认0 1~5）',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否显示（0否, 1是）',
  `is_anonymous` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否匿名（0否，1是）',
  `is_reply` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否回复（0否，1是）',
  `reply_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '回复时间',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `upd_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品评论表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods_favor`;
CREATE TABLE `__PREFIX__goods_favor` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户商品收藏表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods_photo`;
CREATE TABLE `__PREFIX__goods_photo` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `md5` varchar(60) DEFAULT NULL COMMENT '图片MD5',
  `images` varchar(700) DEFAULT NULL COMMENT '图片路径',
  `is_show` tinyint(3) UNSIGNED DEFAULT '1' COMMENT '是否显示（0否, 1是）',
  `sort`  int(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '顺序',
  `add_time` int(11) UNSIGNED DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品相册图片表' ROW_FORMAT=DYNAMIC;

ALTER TABLE `__PREFIX__goods_photo` ADD PRIMARY KEY(`id`);
ALTER TABLE `__PREFIX__goods_photo` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id';

DROP TABLE IF EXISTS `__PREFIX__goods_spec_base`;
CREATE TABLE `__PREFIX__goods_spec_base` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `inventory` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存',
  `weight` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '重量（kg） ',
  `coding` char(80) NOT NULL DEFAULT '' COMMENT '编码',
  `barcode` char(80) NOT NULL DEFAULT '' COMMENT '条形码',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `extends` longtext COMMENT '扩展数据(json格式存储)',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品规格基础表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods_spec_type`;
CREATE TABLE `__PREFIX__goods_spec_type` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `value` text NOT NULL COMMENT '类型值（json字符串存储）',
  `name` char(230) NOT NULL DEFAULT '' COMMENT '类型名称',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品规格类型表' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `__PREFIX__goods_spec_value`;
CREATE TABLE `__PREFIX__goods_spec_value` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '自增id',
  `goods_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品id',
  `goods_spec_base_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品规格基础id',
  `value` char(230) NOT NULL DEFAULT '' COMMENT '规格值',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品规格值表' ROW_FORMAT=DYNAMIC;

