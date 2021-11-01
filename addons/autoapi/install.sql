DROP TABLE IF EXISTS `__PREFIX__autoapi`;
CREATE TABLE `__PREFIX__autoapi`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '表名',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '生成接口时间',
  `admin_id` int(10) NULL DEFAULT NULL COMMENT '操作人ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Api自动生成高级版' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;