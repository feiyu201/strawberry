CREATE TABLE IF NOT EXISTS `__PREFIX__wx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公众号名称',
  `appid` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'APPID',
  `appsecret` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'appsecret',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'token',
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `encodingaeskey` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'encodingaeskey',
  `type` enum('2','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2' COMMENT '公众号类型:1=服务号,2=订阅号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信公众号';
