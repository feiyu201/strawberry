/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : okadmin

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-11-12 17:05:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cm_plugin
-- ----------------------------
DROP TABLE IF EXISTS `cm_plugin`;
CREATE TABLE `cm_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '插件标志',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '插件名称',
  `description` text NOT NULL COMMENT '插件描述',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '作者',
  `version` varchar(16) NOT NULL COMMENT '版本号',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `install` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否安装',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='插件表';
