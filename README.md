# 草莓万能开发框架

#### 介绍
thinkphp6版本的

#### 交流QQ群
QQ群：578270353

#### 软件架构
thinkphp6 php7.1

#### 更新日志
2020.9.24 完成了基本的附件管理。

2020.9.23 完成了基本的小程序管理。

2020.9.22 完成了基本的插件管理。

2020.9.19 完成了基本的登陆和管理员管理功能。万里长征的第一步。

#### 演示地址
http://stadmin.shiliucrm.com/

用户名：admin 密码 112233 或者 user68 密码 112233

请不要删数据
持续更新中...

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

7、复制一份config/database.php.example
，改名database.php，修改config/database.php里面的数据库账户和密码以及数据库名。


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


#### 如何参与一起开发？

1、首先Fork仓库代码

2、然后git clone 到你本地，注意是clone你fork后的自己的地址。git clone https://gitee.com/<yourname>/strawberry.git

3、编写代码并提交

4、Push 到你的分支

5、创建 Pull Request 并描述你完成的功能或者做出的修改。