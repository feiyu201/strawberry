--------------------

--
-- 表的结构 `super_admin`
--

CREATE TABLE `super_admin` (
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
-- 转存表中的数据 `super_admin`
--

INSERT INTO `super_admin` (`id`, `username`, `nickname`, `password`, `salt`, `avatar`, `email`, `loginfailure`, `logintime`, `loginip`, `createtime`, `updatetime`, `token`, `status`) VALUES
(1, 'admin', 'Admin', '91e44c26476adcc406a2346cc1876330', 'qi0c1o', '/storage/topic/20200919\\f8c822af5bd1c3c1fe37b5f89b764871.png', 'admin@admin.com', 0, 1600298414, '123.196.11.216', 1492186163, 1600511971, '04e8afb9-646c-4a41-8452-cd6330c3232b', 'normal'),
(2, 'user68', 'user68', 'e6e68fc34b3078a0a0e3e672949286c9', 'tTJQcG', '/storage/topic/20200919\\697fc10c02160b1f2e7cdec35330708b.jpg', 'user68@china.com', 0, 1596548037, '115.171.166.186', 1595566848, 1600512019, '10ffe001-7a7d-4115-9320-159a9529b95a', 'normal'),
(3, 'bafang1', '123123', '882c568d209513246747dc8793ec6757', 'vlkeo3', '/storage/topic/20200919\\9cdece8ea5bdd015f4fc48fc4998eda9.jpg', '123123@qq.com', 0, 1600417566, '125.80.131.21', 1596598139, 1600512007, '44e997ce-19bc-443f-a19e-8f0373eae5e1', 'normal');

-- --------------------------------------------------------

--
-- 表的结构 `super_auth_rule`
--

CREATE TABLE `super_auth_rule` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
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
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节点表';

--
-- 转存表中的数据 `super_auth_rule`
--

INSERT INTO `super_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weigh`, `status`) VALUES
(1, 'menu', 1231, '12', '123123', '', '', '', 0, NULL, NULL, 0, ''),
(513, 'file', 0, 'test', '测试菜单', '&#xe66f;', '', '', 1, 1600862240, 1600862240, 0, 'normal'),
(514, 'file', 513, 'addons/test/index/index', '查看', '&#xe62e;', '', '', 0, 1600862240, 1600862240, 0, 'normal'),
(515, 'file', 513, 'addons/test/index/add', '添加', '&#xe62e;', '', '', 0, 1600862240, 1600862240, 0, 'normal'),
(516, 'file', 513, 'addons/test/index/detail', '详情', '&#xe62e;', '', '', 0, 1600862240, 1600862240, 0, 'normal');

-- --------------------------------------------------------

--
-- 表的结构 `super_market`
--

CREATE TABLE `super_market` (
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
-- 表的结构 `super_text_addondownload`
--

CREATE TABLE `super_text_addondownload` (
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



--
-- 表的结构 `super_attachment`
--

CREATE TABLE `super_attachment` (
  `id` int(20) UNSIGNED NOT NULL COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(10) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';


--
-- 转储表的索引
--

--
-- 表的索引 `super_attachment`
--
ALTER TABLE `super_attachment`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `super_attachment`
--
ALTER TABLE `super_attachment`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;



--
-- 转储表的索引
--

--
-- 表的索引 `super_admin`
--
ALTER TABLE `super_admin`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `super_auth_rule`
--
ALTER TABLE `super_auth_rule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING BTREE,
  ADD KEY `pid` (`pid`),
  ADD KEY `weigh` (`weigh`);

--
-- 表的索引 `super_market`
--
ALTER TABLE `super_market`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `super_text_addondownload`
--
ALTER TABLE `super_text_addondownload`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `super_admin`
--
ALTER TABLE `super_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `super_auth_rule`
--
ALTER TABLE `super_auth_rule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=517;

--
-- 使用表AUTO_INCREMENT `super_market`
--
ALTER TABLE `super_market`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
