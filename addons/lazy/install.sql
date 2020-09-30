/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : test

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 31/07/2020 15:32:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for __PREFIX__lazy
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__lazy`;
CREATE TABLE `__PREFIX__lazy`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '表名',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '生成接口时间',
  `member_id` int(10) NULL DEFAULT NULL COMMENT '操作人ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Api自动生成' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;