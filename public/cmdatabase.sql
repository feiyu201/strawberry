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
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'fa-circle-o' COMMENT '图标',
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
(536, 1, 0, 'index/welcome', '控制台', 'fa-dashboard', '', '', 1, NULL, NULL, 0, 'normal', 1),
(537, 1, 556, 'admin/index', '人员管理', 'fa-group', '', '', 1, NULL, 1601167961, 2, 'normal', 1),
(538, 1, 0, 'Attachment/index', '附件管理', 'fa-file-image-o', '', '', 1, NULL, NULL, 5, 'normal', 1),
(541, 1, 0, 'plugin/index', '插件管理', 'fa-rocket', '', '', 1, NULL, NULL, 10, 'normal', 1),
(542, 1, 541, 'Plugin/getAllplug', '获取远程插件', 'fa-rocket', '', '', 0, NULL, NULL, 10, 'normal', 1),
(543, 1, 0, 'applets/index', '小程序管理', 'fa-wechat', '', '', 1, NULL, NULL, 15, 'normal', 1),
(553, 1, 556, 'AuthRule/index', '菜单管理', 'fa-bars', '', '', 1, NULL, 1601167951, 1, 'normal', 1),
(554, 1, 553, 'AuthRule/add', '菜单添加', 'fa-add', '', '这里是备注', 0, 1601122750, NULL, 100, 'normal', 1),
(555, 1, 553, 'AuthRule/edit', '菜单编辑', 'fa-edit', '', '', 0, 1601122878, 1601123339, 100, 'normal', 1),
(556, 1, 0, 'admin/Auth', '权限管理', 'fa-group', '', '', 1, 1601167936, NULL, 1, 'normal', 1),
(557, 1, 556, 'AuthGroup/index', '角色管理', 'fa-group', '', '', 1, 1601168043, NULL, 100, 'normal', 1),
(558, 1, 0, 'user/index', '会员管理', 'fa-group', '', '', 1, 1601168043, NULL, 100, 'normal', 1);
(559, 1, 558, 'user/add', '添加', 'fa-add', '', '', 0, 1601122878, 1601123339, 100, 'normal', 1),
(559, 1, 558, 'user/edit', '编辑', 'fa-edit', '', '', 0, 1601122878, 1601123339, 100, 'normal', 1),
(559, 1, 558, 'user/del', '删除', 'fa-add', '', '', 0, 1601122878, 1601123339, 100, 'normal', 1);

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

-- --------------------------------------------------------

--
-- 表的结构 `cm_plugin`
--

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

-- --------------------------------------------------------

--
-- 表的结构 `cm_user`
--

CREATE TABLE `cm_user` (
  `id` int UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `openid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '微信信息',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `level` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '格言',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int UNSIGNED NOT NULL DEFAULT '0' COMMENT '积分',
  `successions` int UNSIGNED NOT NULL DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int UNSIGNED NOT NULL DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` int DEFAULT NULL COMMENT '上次登录时间',
  `logintime` int DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '加入IP',
  `jointime` int DEFAULT NULL COMMENT '加入时间',
  `past_time` int UNSIGNED DEFAULT NULL COMMENT '过期时间',
  `begin_time` int UNSIGNED DEFAULT NULL COMMENT '开始时间',
  `createtime` int DEFAULT NULL COMMENT '创建时间',
  `updatetime` int DEFAULT NULL COMMENT '更新时间',
  `token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Token',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '状态',
  `verification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '验证',
  `inviter_mem_info_id` int DEFAULT NULL COMMENT '上级用户ID',
  `inviter_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户的邀请码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员表' ROW_FORMAT=DYNAMIC;

--
-- 转储表的索引
--

--
-- 表的索引 `cm_user`
--
ALTER TABLE `cm_user`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cm_user`
--
ALTER TABLE `cm_user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=108;
COMMIT;



-- --------------------------------------------------------

--
-- 表的结构 `cm_test_name`
--
DROP TABLE IF EXISTS `cm_test_name`;
CREATE TABLE `cm_test_name` (
  `id` int NOT NULL  COMMENT 'ID' AUTO_INCREMENT,
  `select_test` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '下拉:10=选项一,20=选项二',
  `set_test` set('music','reading','swimming') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '爱好(多选):music=音乐,reading=读书,swimming=游泳',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '编辑器',
  `test_content` text  COMMENT 'test_content',
  `time123` datetime NOT NULL COMMENT '时间',
  `switch` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '开关',
  `state` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '单选:10=选项一,20=选项二',
  `create_time` int NOT NULL COMMENT 'create_time',
  `create1time` int COMMENT 'create1time',
  `update_time` int  COMMENT 'update_time',
  `create_at` int  COMMENT 'create_at',
  `test_city` varchar(255)  COMMENT 'test_city',
  `img` varchar(255)  COMMENT 'img',
  `image` varchar(255)  COMMENT 'image',
  `images` text COMMENT 'images',
  `imgs` text  COMMENT 'imgs',
  `file` text  COMMENT 'file',
  `a_fieldlist` text  COMMENT 'fieldlist',
  `test1_name_id` int NOT NULL COMMENT '关联id',
  `test1_name_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '关联ids',
	 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='自动CRUD测试表' ROW_FORMAT=DYNAMIC;
--
-- 转存表中的数据 `cm_test_name`
--

-- --------------------------------------------------------

--
-- 表的结构 `cm_test1_name`
--

CREATE TABLE `cm_test1_name` (
  `id` int NOT NULL COMMENT 'ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `cm_test1_name`
--

INSERT INTO `cm_test1_name` (`id`, `name`) VALUES
(1, 'name89'),
(2, 'name2'),
(3, 'name3'),
(4, 'name4');

-- --------------------------------------------------------

-- 表cm_config 结构
CREATE TABLE IF NOT EXISTS `cm_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量名',
  `group` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分组',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '变量值',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '变量字典数据',
  `rule` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '配置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';


/*!40000 ALTER TABLE `cm_config` DISABLE KEYS */;
INSERT INTO `cm_config` (`id`, `name`, `group`, `title`, `tip`, `type`, `value`, `content`, `rule`, `extend`, `setting`) VALUES
	(1, 'name', 'basic', '站点名称', '请填写站点名称', 'string', '草莓万能开发框架是最好的框架', '', 'required', 'class="layui-input"', NULL),
	(2, 'beian', 'basic', '备案号', '粤ICP备15000000号-1', 'string', '', '', '', 'class="layui-input"', NULL),
	(18, 'baidumap', 'map', '百度API', '请配置百度地图API', 'string', 'api', NULL, '', 'class="layui-input"', NULL),
	(20, 'baidumapscrect', 'map', '百度SECRET', '请配置百度地图', 'string', 'secret', NULL, '', '', NULL);

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

--
-- 转储表的索引
--

--
-- 转储表的索引
--

--
-- 表的索引 `cm_test1_name`
--
ALTER TABLE `cm_test1_name`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;