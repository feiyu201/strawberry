<<<<<<< HEAD
ThinkPHP 6.0
===============

> 运行环境要求PHP7.1+。

[官方应用服务市场](https://market.topthink.com) | [`ThinkPHP`开发者扶持计划](https://sites.thinkphp.cn/1782366)

ThinkPHPV6.0版本由[亿速云](https://www.yisu.com/)独家赞助发布。

## 主要新特性

* 采用`PHP7`强类型（严格模式）
* 支持更多的`PSR`规范
* 原生多应用支持
* 更强大和易用的查询
* 全新的事件系统
* 模型事件和数据库事件统一纳入事件系统
* 模板引擎分离出核心
* 内部功能中间件化
* SESSION/Cookie机制改进
* 对Swoole以及协程支持改进
* 对IDE更加友好
* 统一和精简大量用法

## 安装

~~~
composer create-project topthink/think tp 6.0.*
~~~

如果需要更新框架使用
~~~
composer update topthink/framework
~~~

## 文档

[完全开发手册](https://www.kancloud.cn/manual/thinkphp6_0/content)

## 参与开发

请参阅 [ThinkPHP 核心框架包](https://github.com/top-think/framework)。

## 版权信息

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2006-2020 by ThinkPHP (http://thinkphp.cn)

All rights reserved。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
=======
# 草莓万能开发框架

#### 介绍
thinkphp6版本的

#### 交流QQ群
QQ群：578270353

#### 软件架构
thinkphp6 php7.1

#### 更新日志
2020.9.19 完成了基本的登陆和管理员管理功能。万里长征的第一步。

#### 安装说明：

1、cd到自己想安装的目录 git clone https://gitee.com/qzxc_admin/strawberry.git

2、cd strawberry

3、执行
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

composer install

注意：要把putenv()函数和proc_open()函数打开。也就是去掉这两个函数的禁用。如果你用宝塔安装了多个php，每个php的最好都去掉。
还要注意默认的php版本最好是php7的。如果安装了多个php版本，尤其是php5.6。有可能会报php版本过低。可以通过软链接将php的版本默认为php7。

4、修改runtime权限777

5、别忘了配置网站的伪静态为thinkphp.

6、导入sql文件。stdatabase.sql是数据库文件。

7、修改config/database.php里面的数据库账户和密码以及数据库名。


然后就可以访问了。默认用户名admin 密码 112233

![输入图片说明](https://images.gitee.com/uploads/images/2020/0919/214104_d0ae3f6b_1405153.png "屏幕截图.png")

####  特别感谢

以下项目排名不分先后

ThinkPHP：https://github.com/top-think/framework

Annotations：https://github.com/doctrine/annotations

Layui：https://github.com/sentsin/layui

ok-admin：http://ok-admin.xlbweb.cn/

Jquery：https://github.com/jquery/jquery

RequireJs：https://github.com/requirejs/requirejs

CKEditor：https://github.com/ckeditor/ckeditor4

Echarts：https://github.com/apache/incubator-echarts

石榴CRM：https://www.shiliucrm.com （广告：-））

热猫商城：https://gitee.com/qzxc_admin/hotmall （广告：-））


#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request
>>>>>>> 3bbba236d9e4c67c4da9fa9941b6c9128618e8cd
