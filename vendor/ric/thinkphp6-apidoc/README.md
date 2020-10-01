广州技客 Thinkphp6 接口文档自动生成工具
=======================
### 可以自动根据对外路由生成 对外API文档
### 可以自动根据领域应用api生成 领域API文档（配合https://gitee.com/tojeekup/thinkphp6-auth 使用）
### ThinkPHP6 API自动生成 layui美化，模版基版来源于互联网

### 使用方法
####1、安装扩展
```
composer require ric/thinkphp6-apidoc
```

由于不想写路由和样式移动，所以安装后，请自己手动把：
./vendor/ric/thinkphp6-apidoc/src/route.php 复制到  ./route/doc/route.php
./vendor/ric/thinkphp6-apidoc/src/assets 目录 复制为 ./public/static/doc 目录


####2、配置参数
- 安装好扩展后在 config\apidoc.php 配置文件

####3、书写规范

- 请参考Demo.php文件


####4、访问方法
- http://你的域名/doc 或者 http://你的域名/index.php/doc 

     
## 该工具可以配合ric的其它系列工具使用，比如：
多端用户登录退出、权限验证工具：ric/thinkphp6-auth
根据注释自动生成api文档：ric/thinkphp6-apidoc
think6 json输出、数据验证辅助工具 ：ric/thinkphp6-helper
后继推出的更多工具...


## 联系方式：
https://www.jeekup.com/

## 更新日志

>> 20190912 v1.0.3

* 修复了最近两天6.0视图模块更新导致报错问题