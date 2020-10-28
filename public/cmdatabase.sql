-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-09-27 12:38:23
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `strawberrydb`
--

-- --------------------------------------------------------

--
-- 表的结构 `cm_admin`
--

CREATE TABLE `cm_admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IP',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

--
-- 转存表中的数据 `cm_admin`
--

INSERT INTO `cm_admin` (`id`, `username`, `nickname`, `password`, `salt`, `avatar`, `email`, `loginfailure`, `logintime`, `loginip`, `createtime`, `updatetime`, `token`, `status`) VALUES
(1, 'admin', 'Admin', 'f691da52b2e9fa8e4dddf29f1ccabef6', '9ee7ns', '/static/images/avatar.png', 'admin@admin.com', 0, 1600298414, '123.196.11.216', 1492186163, 1601174630, '04e8afb9-646c-4a41-8452-cd6330c3232b', 'normal');

-- --------------------------------------------------------

--
-- 表的结构 `cm_attachment`
--

CREATE TABLE `cm_attachment` (
  `id` int(20) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(10) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_auth_group`
--

CREATE TABLE `cm_auth_group` (
  `id` int(8) UNSIGNED NOT NULL,
  `createtime` int(11) NOT NULL,
  `updatetime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` varchar(30) DEFAULT 'normal' COMMENT '状态',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '角色组',
  `rules` text COMMENT '权限',
  `remark` text COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色组管理';

--
-- 转存表中的数据 `cm_auth_group`
--

INSERT INTO `cm_auth_group` (`id`, `createtime`, `updatetime`, `status`, `title`, `rules`, `remark`) VALUES
(1, 1601170339, 1601172768, 'normal', '超级管理员', '536,556,553,554,555,537,557,538,541,547,548,549,550,542,', '超级管理员'),
(4, 1601170436, 1601181256, 'normal', '插件管理员', '0,536,538,541,547,548,549,550,542,', '这里是备注');

-- --------------------------------------------------------

--
-- 表的结构 `cm_auth_group_access`
--

CREATE TABLE `cm_auth_group_access` (
  `uid` mediumint(8) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL,
  `createtime` int(11) DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(11) DEFAULT '0' COMMENT '修改时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cm_auth_group_access`
--

INSERT INTO `cm_auth_group_access` (`uid`, `group_id`, `createtime`, `updatetime`) VALUES
(1, 1, 0, 0),
(2, 4, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cm_auth_rule`
--

CREATE TABLE `cm_auth_rule` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `auth_open` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节点表';

--
-- 转存表中的数据 `cm_auth_rule`
--

INSERT INTO `cm_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weigh`, `status`, `auth_open`) VALUES
(536, 1, 0, 'admin/index/welcome', '控制台', 'fa-dashboard', '', '', 1, NULL, NULL, 0, 'normal', 1),
(537, 1, 556, 'admin/admin/index', '人员管理', 'fa-group', '', '', 1, NULL, 1601167961, 2, 'normal', 1),
(538, 1, 0, 'admin/Attachment/index', '附件管理', 'fa-file-image-o', '', '', 1, NULL, NULL, 5, 'normal', 1),
(541, 1, 0, 'admin/plugin/index', '插件管理', 'fa-rocket', '', '', 1, NULL, NULL, 10, 'normal', 1),
(542, 1, 0, 'admin/applets/index', '小程序管理', 'fa-wechat', '', '', 1, NULL, NULL, 15, 'normal', 1),
(553, 1, 556, 'admin/AuthRule/index', '菜单管理', 'fa-bars', '', '', 1, NULL, 1601167951, 1, 'normal', 1),
(554, 1, 553, 'admin/AuthRule/add', '菜单添加', 'fa-add', '', '这里是备注', 0, 1601122750, NULL, 100, 'normal', 1),
(555, 1, 553, 'admin/AuthRule/edit', '菜单编辑', 'fa-edit', '', '', 0, 1601122878, 1601123339, 100, 'normal', 1),
(556, 1, 0, 'Auth', '权限管理', 'fa-group', '', '', 1, 1601167936, NULL, 1, 'normal', 1),
(557, 1, 556, 'admin/AuthGroup/index', '角色管理', 'fa-group', '', '', 1, 1601168043, NULL, 100, 'normal', 1);

-- --------------------------------------------------------

--
-- 表的结构 `cm_market`
--

CREATE TABLE `cm_market` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `androidurl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '安卓地址',
  `author` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '作者',
  `bought` int(10) DEFAULT '0',
  `button` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(10) NOT NULL COMMENT '分类id',
  `demourl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'demo地址',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述',
  `diffextended` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '新版描述',
  `diffregular` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '新版',
  `donateimage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `downloads` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extendedfile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extendedprice` decimal(10,2) NOT NULL COMMENT '价格',
  `flag` set('hot','index','recommend') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标志(多选):hot=热门,index=首页,recommend=推荐',
  `homepage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '开发者主页地址',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片地址',
  `intro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '插件名称',
  `iosurl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '喜欢数量',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `originalextendedprice` decimal(10,2) NOT NULL,
  `originalprice` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `qq` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'qq',
  `releasetime` int(10) DEFAULT NULL COMMENT '版本时间',
  `releaselist` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本列表',
  `require` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '引用版本',
  `sales` int(10) NOT NULL DEFAULT '0' COMMENT '销量',
  `score` float(2,1) NOT NULL DEFAULT '5.0' COMMENT '积分',
  `screenshots` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片',
  `star` float(2,1) NOT NULL DEFAULT '5.0' COMMENT '星',
  `thanks` int(10) NOT NULL DEFAULT '0' COMMENT '感谢数量',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '外部链接地址',
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本',
  `views` int(10) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `refreshtime` int(10) NOT NULL COMMENT '刷新时间(int)',
  `createtime` int(10) NOT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `deletetime` int(10) DEFAULT NULL COMMENT '删除时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='市场分类' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_text_addondownload`
--

CREATE TABLE `cm_text_addondownload` (
  `id` int(10) NOT NULL,
  `content` longtext NOT NULL,
  `os` set('windows','linux','mac','ubuntu') DEFAULT '' COMMENT '操作系统',
  `version` varchar(255) DEFAULT '' COMMENT '最新版本',
  `filesize` varchar(255) DEFAULT '' COMMENT '文件大小',
  `language` set('zh-cn','en') DEFAULT '' COMMENT '语言',
  `downloadurl` varchar(1500) DEFAULT '' COMMENT '下载地址',
  `screenshots` varchar(1500) DEFAULT '' COMMENT '预览截图',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `downloads` varchar(10) DEFAULT '0' COMMENT '下载次数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='下载';

-- --------------------------------------------------------

--
-- 表的结构 `cm_wxapp`
--

CREATE TABLE `cm_wxapp` (
  `id` int(10) NOT NULL,
  `stid` int(10) unsigned NOT NULL,
  `appid` varchar(50) NOT NULL,
  `token` varchar(32) CHARACTER SET utf8 NOT NULL,
  `encodingaeskey` varchar(43) CHARACTER SET utf8 NOT NULL,
  `level` tinyint(4) NOT NULL,
  `account` varchar(30) CHARACTER SET utf8 NOT NULL,
  `original` varchar(50) CHARACTER SET utf8 NOT NULL,
  `key` varchar(50) CHARACTER SET utf8 NOT NULL,
  `secret` varchar(50) CHARACTER SET utf8 NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `status` varchar(30) CHARACTER SET utf8 NOT NULL,
  `addons` varchar(50) NOT NULL DEFAULT '',
  `createtime` int(10) NOT NULL DEFAULT '0',
  `updatetime` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;


--
-- 转储表的索引
--

--
-- 表的索引 `cm_admin`
--
ALTER TABLE `cm_admin`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_attachment`
--
ALTER TABLE `cm_attachment`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_auth_group`
--
ALTER TABLE `cm_auth_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_auth_group_access`
--
ALTER TABLE `cm_auth_group_access`
  ADD UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `group_id` (`group_id`);

--
-- 表的索引 `cm_auth_rule`
--
ALTER TABLE `cm_auth_rule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING BTREE,
  ADD KEY `pid` (`pid`),
  ADD KEY `weigh` (`weigh`);

--
-- 表的索引 `cm_market`
--
ALTER TABLE `cm_market`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `cm_text_addondownload`
--
ALTER TABLE `cm_text_addondownload`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_wxapp`
--
ALTER TABLE `cm_wxapp`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cm_admin`
--
ALTER TABLE `cm_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `cm_attachment`
--
ALTER TABLE `cm_attachment`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `cm_auth_group`
--
ALTER TABLE `cm_auth_group`
  MODIFY `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `cm_auth_rule`
--
ALTER TABLE `cm_auth_rule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=558;

--
-- 使用表AUTO_INCREMENT `cm_market`
--
ALTER TABLE `cm_market`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `cm_wxapp`
--
ALTER TABLE `cm_wxapp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;