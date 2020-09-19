# 草莓万能开发框架

#### 介绍
thinkphp6版本的

#### 软件架构
thinkphp6 php7.1


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




#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request