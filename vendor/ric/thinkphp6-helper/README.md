广州技客 Thinkphp6 工具类
=======================

### 安装
~~~
composer require ric/thinkphp6-helper
~~~
### api、rpc函数说明

> thinkphp6 取消了action 跨应用调用类，rpc函数即为该函数的替代品，可以实现，跨应用、跨目录调用
例：

rpc: 第一个参数为类名，填写完整的，方便ide调整，第二个参数为方法名，第三个参数为传值，api文件定义的需要一致，会根据api文件
                                               的注释定义自动做数据效验。
```$xslt
	$isexist= rpc('app\admin\logic\authadmin\UserLogic', 'exist_account', [$account]); 
```

api: 第一个参数为类名，第二个参数为方法名，第三个参数为传值，其中$data 数据是对应形式，键和api文件定义的需要一致，会根据api文件
的注释定义自动做数据效验。

```
    $data = [
        'website_id' => $website_id,
    ];
    $webinfo = api('app\website\api\config\Info'，'add',$data);
```

app\website\api\config\Info.php 文件例子

```$xslt
<?php
namespace app\website\api;


class Article
{
    /**
     * @title   获取搭建商列表
     * @desc    类的方法2 哦
     * @author  Ric
     * @version 1.0
     *
     * @param int $page  0 分页数，指定获取第几页的数据  require_分页数不能为空.number
     * @param int $size 10 分页大小，指定分页大小  require_分页大小不能为空.number
     *
     * @return int $id 0 索引
     * @return int $id 0 索引
     * @return int $id 0 索引
     */
    public function api($data)
    {
        return rpc('app\website\dao\Builder', 'getBuilderList', ['page'=>$data['page'], 'size'=>$data['size']]);
    }

}
```

> 在开发时，建议采用领域应用模式开发，即每个应用，通过api提供接口，对其它应用对接，然后api接口通过rpc方法处理
内部领域内容，一个应用即为一个领域。博主的目录结构如下，仅供参考：

```$xslt
app
  -- website    站点管理应用 （该应用提供站点管理服务，对平台内部，不设置路由，不直接对外提供接口，核心api、logic、dao三层）
     -- api     对平台提供api服务，通过rpc调用自身的logic和dao层，进行业务处理
        -- Config.php 站点配置信息
        -- Website.php 站点管理
     -- logic   业务处理
     -- dao     数据处理
     
  -- toadmin    后台平台api （该应用为后台平台提供api接口，设置路由，供前端调用，编写页面，核心controller，无其它）
     -- controller 控制器，根据需要通过api函数调用 website站点的api 接口，获取信息，返回数据
     
  -- topc       pc端平台api （该应用为pc端提供api接口，设置路由，供前端调用，编写页面，核心controller，无其它）
     -- controller 控制器，根据需要通过api函数调用 website站点的api 接口，获取信息，返回数据
     
```

## 输出统一的json格式
```$xslt
    /**
     * 返回操作成功json信息
     * @param array $object 当前返回对象
     * @param string $special 特殊返回对象处理 有类型：select
     */
    function totrue($object,$message=''){}
    
    /**
     * 返回json错误信息
     * @param string $status 当前错误状态
     * @param string $message 返回错误信息前追加内容,默认为空
     */
    function tofalse($status,$message=''){}
    
    返回的统一格式为 
    {
        "status":"状态码",
        "message":"操作描述",
        "data":'业务数据'
    }
```
     
## 该工具可以配合ric的其它系列工具使用，比如：
多端用户登录退出、权限验证工具：ric/thinkphp6-auth
根据注释自动生成api文档：ric/thinkphp6-apidoc
think6 json输出、数据验证辅助工具 ：ric/thinkphp6-helper
后继推出的更多工具...


## 联系方式：
https://www.jeekup.com/

## 版本
> 20190923 v1.0.3
* 规范输出使用toTrue、toFalse