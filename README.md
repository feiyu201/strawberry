# 草莓快速开发框架

#### 介绍
基于ThinkPHP6+Layui+vue+easywechat开发的开源开发框架

小程序支付 小程序授权 小程序获取手机号都有了

自动生成API接口和接口文档

一键自动生成CRUD

UI组件和JS组件封装

插件市场

多个小程序管理

多应用管理(开发中)

#### 交流QQ群
QQ群：578270353

#### 软件架构
thinkphp6 php>=7.1

#### 更新日志

迁移到了开发文档里 这里写太长了 写不下了 开发文档地址如下：

http://doc.caomei.zone/

#### 演示地址
http://caomei.shiliucrm.com/

用户名：admin 密码 112233

自动生成的文档地址：http://caomei.shiliucrm.com/doc/ 开发者可以换成自己的url

请不要删数据
持续更新中...

#### 安装说明：

1、cd到自己想安装的目录 git clone https://gitee.com/qzxc_admin/strawberry.git

2、然后cd strawberry 然后在该目录下执行：composer install 如果太慢的话可以执行composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
切换到淘宝镜像。

3、修改runtime config public addons文件包权限777（如果是windows本地安装的话可以先忽略）

4、宝塔别忘了配置网站的伪静态为thinkphp的规则.宝塔要安装fileinfo扩展，否则图片上传会报错。

5、执行在线安装。www.xxx.com/install.php 注意前面是你自己的在线网址或者本地网址。

6、安装成功后就可以访问了，登陆密码是安装的时候输入的密码。遇到问题请到QQ群：578270353

![输入图片说明](https://images.gitee.com/uploads/images/2020/0929/111351_0cbc35c8_1405153.png "屏幕截图.png")

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

1、首先Fork仓库代码到自己的仓库

2、然后git clone 自己的仓库到你本地，注意是clone你fork后的自己的仓库地址。

3、编写代码并提交

4、Push到你自己的仓库

5、到gitee上自己的仓库里创建 Pull Request 并描述你完成的功能或者做出的修改。