DROP TABLE IF EXISTS `cm_platform_manage`;
CREATE TABLE `cm_platform_manage`  (
   `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
   `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
   `third_id` int(11) NULL DEFAULT NULL COMMENT '平台第三方的主键id',
   `type` enum('wx','miniprogram') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型',
   `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
   `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信公众号' ROW_FORMAT = Dynamic;