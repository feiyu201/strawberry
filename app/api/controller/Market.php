<?php
//  *+-----------------------------------------------------------------------
//  *                      .::::.
//  *                    .::::::::.            | Author: 丶长情
//  *                    :::::::::::           | Email: zeng1144318071@gmail.com
//  *                 ..:::::::::::'           | Datetime: 2020/08/15
//  *             '::::::::::::'               | Remarks:
//  *                .::::::::::
//  *           '::::::::::::::..
//  *                ..::::::::::::.
//  *              ``::::::::::::::::
//  *               ::::``:::::::::'        .:::.
//  *              ::::'   ':::::'       .::::::::.
//  *            .::::'      ::::     .:::::::'::::.
//  *           .:::'       :::::  .:::::::::' ':::::.
//  *          .::'        :::::.:::::::::'      ':::::.
//  *         .::'         ::::::::::::::'         ``::::.
//  *     ...:::           ::::::::::::'              ``::.
//  *   ```` ':.          ':::::::::'                  ::::..
//  *                      '.:::::'                    ':'````..
//  * +-----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;
use think\facade\Db;

/**
 * @title 市场分类接口
 */

class Market extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ["*"];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ["*"];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @title    添加
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Market/add)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   varchar androidurl &nbsp; 安卓地址 YES
     * @param   varchar author &nbsp; 作者 NO
     * @param   int bought &nbsp;  YES
     * @param   varchar button &nbsp;  YES
     * @param   int category_id &nbsp; 分类id NO
     * @param   varchar demourl &nbsp; demo地址 YES
     * @param   varchar description &nbsp; 描述 NO
     * @param   varchar diffextended &nbsp; 新版描述 YES
     * @param   varchar diffregular &nbsp; 新版 YES
     * @param   varchar donateimage &nbsp;  YES
     * @param   varchar downloads &nbsp;  YES
     * @param   varchar extendedfile &nbsp;  YES
     * @param   decimal extendedprice &nbsp; 价格 NO
     * @param   set flag &nbsp; 标志(多选):hot=热门,index=首页,recommend=推荐 NO
     * @param   varchar homepage &nbsp; 开发者主页地址 NO
     * @param   varchar image &nbsp; 图片地址 NO
     * @param   varchar intro &nbsp; 插件名称 NO
     * @param   varchar iosurl &nbsp;  YES
     * @param   int likes &nbsp; 喜欢数量 NO
     * @param   varchar name &nbsp; 名称 NO
     * @param   decimal originalextendedprice &nbsp;  NO
     * @param   decimal originalprice &nbsp;  NO
     * @param   decimal price &nbsp; 价格 NO
     * @param   varchar qq &nbsp; qq NO
     * @param   int releasetime &nbsp; 版本时间 YES
     * @param   varchar releaselist &nbsp; 版本列表 NO
     * @param   varchar require &nbsp; 引用版本 NO
     * @param   int sales &nbsp; 销量 NO
     * @param   float score &nbsp; 积分 NO
     * @param   varchar screenshots &nbsp; 图片 NO
     * @param   float star &nbsp; 星 NO
     * @param   int thanks &nbsp; 感谢数量 NO
     * @param   varchar title &nbsp; 标题 NO
     * @param   varchar url &nbsp; 外部链接地址 NO
     * @param   varchar version &nbsp; 版本 NO
     * @param   int views &nbsp; 浏览量 NO
     * @param   int refreshtime &nbsp; 刷新时间(int) NO
     * @param   int createtime &nbsp; 创建时间 NO
     * @param   int updatetime &nbsp; 更新时间 YES
     * @param   int deletetime &nbsp; 删除时间 YES
     * @param   int weigh &nbsp; 权重 NO
     * @param   enum status &nbsp; 状态 NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function add()
    {
        $param = request()->param();
        $model = new \app\common\model\Market();
        $result = $model->save($param);
        if ($result)
            $this->success();
        else
            $this->error('添加失败');

    }

    /**
     * @title    编辑
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Market/edit)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @param   varchar androidurl &nbsp; 安卓地址 YES
     * @param   varchar author &nbsp; 作者 NO
     * @param   int bought &nbsp;  YES
     * @param   varchar button &nbsp;  YES
     * @param   int category_id &nbsp; 分类id NO
     * @param   varchar demourl &nbsp; demo地址 YES
     * @param   varchar description &nbsp; 描述 NO
     * @param   varchar diffextended &nbsp; 新版描述 YES
     * @param   varchar diffregular &nbsp; 新版 YES
     * @param   varchar donateimage &nbsp;  YES
     * @param   varchar downloads &nbsp;  YES
     * @param   varchar extendedfile &nbsp;  YES
     * @param   decimal extendedprice &nbsp; 价格 NO
     * @param   set flag &nbsp; 标志(多选):hot=热门,index=首页,recommend=推荐 NO
     * @param   varchar homepage &nbsp; 开发者主页地址 NO
     * @param   varchar image &nbsp; 图片地址 NO
     * @param   varchar intro &nbsp; 插件名称 NO
     * @param   varchar iosurl &nbsp;  YES
     * @param   int likes &nbsp; 喜欢数量 NO
     * @param   varchar name &nbsp; 名称 NO
     * @param   decimal originalextendedprice &nbsp;  NO
     * @param   decimal originalprice &nbsp;  NO
     * @param   decimal price &nbsp; 价格 NO
     * @param   varchar qq &nbsp; qq NO
     * @param   int releasetime &nbsp; 版本时间 YES
     * @param   varchar releaselist &nbsp; 版本列表 NO
     * @param   varchar require &nbsp; 引用版本 NO
     * @param   int sales &nbsp; 销量 NO
     * @param   float score &nbsp; 积分 NO
     * @param   varchar screenshots &nbsp; 图片 NO
     * @param   float star &nbsp; 星 NO
     * @param   int thanks &nbsp; 感谢数量 NO
     * @param   varchar title &nbsp; 标题 NO
     * @param   varchar url &nbsp; 外部链接地址 NO
     * @param   varchar version &nbsp; 版本 NO
     * @param   int views &nbsp; 浏览量 NO
     * @param   int refreshtime &nbsp; 刷新时间(int) NO
     * @param   int createtime &nbsp; 创建时间 NO
     * @param   int updatetime &nbsp; 更新时间 YES
     * @param   int deletetime &nbsp; 删除时间 YES
     * @param   int weigh &nbsp; 权重 NO
     * @param   enum status &nbsp; 状态 NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function edit()
    {
        // $id = request()->param('id');
        $param = request()->param();
        $model = new \app\common\model\Market();
        $result = $model->update($param);
        if ($result)
            $this->success();
        else
            $this->error('编辑失败');

    }

    /**
     * @title    查询单条
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Market/info/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @return   int id &nbsp; 主键id
     * @return   varchar androidurl &nbsp; 安卓地址 YES
     * @return   varchar author &nbsp; 作者 NO
     * @return   int bought &nbsp;  YES
     * @return   varchar button &nbsp;  YES
     * @return   int category_id &nbsp; 分类id NO
     * @return   varchar demourl &nbsp; demo地址 YES
     * @return   varchar description &nbsp; 描述 NO
     * @return   varchar diffextended &nbsp; 新版描述 YES
     * @return   varchar diffregular &nbsp; 新版 YES
     * @return   varchar donateimage &nbsp;  YES
     * @return   varchar downloads &nbsp;  YES
     * @return   varchar extendedfile &nbsp;  YES
     * @return   decimal extendedprice &nbsp; 价格 NO
     * @return   set flag &nbsp; 标志(多选):hot=热门,index=首页,recommend=推荐 NO
     * @return   varchar homepage &nbsp; 开发者主页地址 NO
     * @return   varchar image &nbsp; 图片地址 NO
     * @return   varchar intro &nbsp; 插件名称 NO
     * @return   varchar iosurl &nbsp;  YES
     * @return   int likes &nbsp; 喜欢数量 NO
     * @return   varchar name &nbsp; 名称 NO
     * @return   decimal originalextendedprice &nbsp;  NO
     * @return   decimal originalprice &nbsp;  NO
     * @return   decimal price &nbsp; 价格 NO
     * @return   varchar qq &nbsp; qq NO
     * @return   int releasetime &nbsp; 版本时间 YES
     * @return   varchar releaselist &nbsp; 版本列表 NO
     * @return   varchar require &nbsp; 引用版本 NO
     * @return   int sales &nbsp; 销量 NO
     * @return   float score &nbsp; 积分 NO
     * @return   varchar screenshots &nbsp; 图片 NO
     * @return   float star &nbsp; 星 NO
     * @return   int thanks &nbsp; 感谢数量 NO
     * @return   varchar title &nbsp; 标题 NO
     * @return   varchar url &nbsp; 外部链接地址 NO
     * @return   varchar version &nbsp; 版本 NO
     * @return   int views &nbsp; 浏览量 NO
     * @return   int refreshtime &nbsp; 刷新时间(int) NO
     * @return   int createtime &nbsp; 创建时间 NO
     * @return   int updatetime &nbsp; 更新时间 YES
     * @return   int deletetime &nbsp; 删除时间 YES
     * @return   int weigh &nbsp; 权重 NO
     * @return   enum status &nbsp; 状态 NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function info()
    {
        $id = request()->param('id');
        $result = Db::name('market')->where('id', $id)->find();
        $result["category_name"] = Db::name("category")->where("id",$result["category_id"])->field('username')->find()['username'];
        if ($result)
            $this->success('查询成功', $result);
        else
            $this->error('信息不存在');

    }

    /**
     * @title    查询列表
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Market/_list)
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int page &nbsp; 第几页 Yes
     * @param   int page &nbsp; 显示条数 Yes
     * @param   int id &nbsp; 主键id Yes
     * @param   varchar androidurl &nbsp; 安卓地址 YES
     * @param   varchar author &nbsp; 作者 NO
     * @param   int bought &nbsp;  YES
     * @param   varchar button &nbsp;  YES
     * @param   int category_id &nbsp; 分类id NO
     * @param   varchar demourl &nbsp; demo地址 YES
     * @param   varchar description &nbsp; 描述 NO
     * @param   varchar diffextended &nbsp; 新版描述 YES
     * @param   varchar diffregular &nbsp; 新版 YES
     * @param   varchar donateimage &nbsp;  YES
     * @param   varchar downloads &nbsp;  YES
     * @param   varchar extendedfile &nbsp;  YES
     * @param   decimal extendedprice &nbsp; 价格 NO
     * @param   set flag &nbsp; 标志(多选):hot=热门,index=首页,recommend=推荐 NO
     * @param   varchar homepage &nbsp; 开发者主页地址 NO
     * @param   varchar image &nbsp; 图片地址 NO
     * @param   varchar intro &nbsp; 插件名称 NO
     * @param   varchar iosurl &nbsp;  YES
     * @param   int likes &nbsp; 喜欢数量 NO
     * @param   varchar name &nbsp; 名称 NO
     * @param   decimal originalextendedprice &nbsp;  NO
     * @param   decimal originalprice &nbsp;  NO
     * @param   decimal price &nbsp; 价格 NO
     * @param   varchar qq &nbsp; qq NO
     * @param   int releasetime &nbsp; 版本时间 YES
     * @param   varchar releaselist &nbsp; 版本列表 NO
     * @param   varchar require &nbsp; 引用版本 NO
     * @param   int sales &nbsp; 销量 NO
     * @param   float score &nbsp; 积分 NO
     * @param   varchar screenshots &nbsp; 图片 NO
     * @param   float star &nbsp; 星 NO
     * @param   int thanks &nbsp; 感谢数量 NO
     * @param   varchar title &nbsp; 标题 NO
     * @param   varchar url &nbsp; 外部链接地址 NO
     * @param   varchar version &nbsp; 版本 NO
     * @param   int views &nbsp; 浏览量 NO
     * @param   int refreshtime &nbsp; 刷新时间(int) NO
     * @param   int createtime &nbsp; 创建时间 NO
     * @param   int updatetime &nbsp; 更新时间 YES
     * @param   int deletetime &nbsp; 删除时间 YES
     * @param   int weigh &nbsp; 权重 NO
     * @param   enum status &nbsp; 状态 NO
     * @return   int id &nbsp; 主键id
     * @return   varchar androidurl &nbsp; 安卓地址 YES
     * @return   varchar author &nbsp; 作者 NO
     * @return   int bought &nbsp;  YES
     * @return   varchar button &nbsp;  YES
     * @return   int category_id &nbsp; 分类id NO
     * @return   varchar demourl &nbsp; demo地址 YES
     * @return   varchar description &nbsp; 描述 NO
     * @return   varchar diffextended &nbsp; 新版描述 YES
     * @return   varchar diffregular &nbsp; 新版 YES
     * @return   varchar donateimage &nbsp;  YES
     * @return   varchar downloads &nbsp;  YES
     * @return   varchar extendedfile &nbsp;  YES
     * @return   decimal extendedprice &nbsp; 价格 NO
     * @return   set flag &nbsp; 标志(多选):hot=热门,index=首页,recommend=推荐 NO
     * @return   varchar homepage &nbsp; 开发者主页地址 NO
     * @return   varchar image &nbsp; 图片地址 NO
     * @return   varchar intro &nbsp; 插件名称 NO
     * @return   varchar iosurl &nbsp;  YES
     * @return   int likes &nbsp; 喜欢数量 NO
     * @return   varchar name &nbsp; 名称 NO
     * @return   decimal originalextendedprice &nbsp;  NO
     * @return   decimal originalprice &nbsp;  NO
     * @return   decimal price &nbsp; 价格 NO
     * @return   varchar qq &nbsp; qq NO
     * @return   int releasetime &nbsp; 版本时间 YES
     * @return   varchar releaselist &nbsp; 版本列表 NO
     * @return   varchar require &nbsp; 引用版本 NO
     * @return   int sales &nbsp; 销量 NO
     * @return   float score &nbsp; 积分 NO
     * @return   varchar screenshots &nbsp; 图片 NO
     * @return   float star &nbsp; 星 NO
     * @return   int thanks &nbsp; 感谢数量 NO
     * @return   varchar title &nbsp; 标题 NO
     * @return   varchar url &nbsp; 外部链接地址 NO
     * @return   varchar version &nbsp; 版本 NO
     * @return   int views &nbsp; 浏览量 NO
     * @return   int refreshtime &nbsp; 刷新时间(int) NO
     * @return   int createtime &nbsp; 创建时间 NO
     * @return   int updatetime &nbsp; 更新时间 YES
     * @return   int deletetime &nbsp; 删除时间 YES
     * @return   int weigh &nbsp; 权重 NO
     * @return   enum status &nbsp; 状态 NO
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function _list()
    {
        $page = $this->request->param('page',1,'intval');
        $limit = $this->request->param('limit',10,'intval');
        $where = [];
        $androidurl=request()->param("androidurl");
        $author=request()->param("author");
        $bought=request()->param("bought");
        $button=request()->param("button");
        $category_id=request()->param("category_id");
        $demourl=request()->param("demourl");
        $description=request()->param("description");
        $diffextended=request()->param("diffextended");
        $diffregular=request()->param("diffregular");
        $donateimage=request()->param("donateimage");
        $downloads=request()->param("downloads");
        $extendedfile=request()->param("extendedfile");
        $extendedprice=request()->param("extendedprice");
        $flag=request()->param("flag");
        $homepage=request()->param("homepage");
        $image=request()->param("image");
        $intro=request()->param("intro");
        $iosurl=request()->param("iosurl");
        $likes=request()->param("likes");
        $name=request()->param("name");
        $originalextendedprice=request()->param("originalextendedprice");
        $originalprice=request()->param("originalprice");
        $price=request()->param("price");
        $qq=request()->param("qq");
        $releasetime=request()->param("releasetime");
        $releaselist=request()->param("releaselist");
        $require=request()->param("require");
        $sales=request()->param("sales");
        $score=request()->param("score");
        $screenshots=request()->param("screenshots");
        $star=request()->param("star");
        $thanks=request()->param("thanks");
        $title=request()->param("title");
        $url=request()->param("url");
        $version=request()->param("version");
        $views=request()->param("views");
        $refreshtime=request()->param("refreshtime");
        $createtime=request()->param("createtime");
        $updatetime=request()->param("updatetime");
        $deletetime=request()->param("deletetime");
        $weigh=request()->param("weigh");
        $status=request()->param("status");
        if ($androidurl)$where["androidurl"] = ['like', '%' .$androidurl. '%'];
        if ($author)$where["author"] = ['like', '%' .$author. '%'];
        if ($bought)$where["bought"] = ['like', '%' .$bought. '%'];
        if ($button)$where["button"] = ['like', '%' .$button. '%'];
        if ($category_id)$where["category_id"] = ['like', '%' .$category_id. '%'];
        if ($demourl)$where["demourl"] = ['like', '%' .$demourl. '%'];
        if ($description)$where["description"] = ['like', '%' .$description. '%'];
        if ($diffextended)$where["diffextended"] = ['like', '%' .$diffextended. '%'];
        if ($diffregular)$where["diffregular"] = ['like', '%' .$diffregular. '%'];
        if ($donateimage)$where["donateimage"] = ['like', '%' .$donateimage. '%'];
        if ($downloads)$where["downloads"] = ['like', '%' .$downloads. '%'];
        if ($extendedfile)$where["extendedfile"] = ['like', '%' .$extendedfile. '%'];
        if ($extendedprice)$where["extendedprice"] = ['like', '%' .$extendedprice. '%'];
        if ($flag)$where["flag"] = ['like', '%' .$flag. '%'];
        if ($homepage)$where["homepage"] = ['like', '%' .$homepage. '%'];
        if ($image)$where["image"] = ['like', '%' .$image. '%'];
        if ($intro)$where["intro"] = ['like', '%' .$intro. '%'];
        if ($iosurl)$where["iosurl"] = ['like', '%' .$iosurl. '%'];
        if ($likes)$where["likes"] = ['like', '%' .$likes. '%'];
        if ($name)$where["name"] = ['like', '%' .$name. '%'];
        if ($originalextendedprice)$where["originalextendedprice"] = ['like', '%' .$originalextendedprice. '%'];
        if ($originalprice)$where["originalprice"] = ['like', '%' .$originalprice. '%'];
        if ($price)$where["price"] = ['like', '%' .$price. '%'];
        if ($qq)$where["qq"] = ['like', '%' .$qq. '%'];
        if (request()->param("startreleasetime") && request()->param("endreleasetime"))$where["releasetime"] = [['>=', request()->param("startreleasetime")], ['<=', request()->param("endreleasetime")], 'and'];
        if ($releaselist)$where["releaselist"] = ['like', '%' .$releaselist. '%'];
        if ($require)$where["require"] = ['like', '%' .$require. '%'];
        if ($sales)$where["sales"] = ['like', '%' .$sales. '%'];
        if ($score)$where["score"] = ['like', '%' .$score. '%'];
        if ($screenshots)$where["screenshots"] = ['like', '%' .$screenshots. '%'];
        if ($star)$where["star"] = ['like', '%' .$star. '%'];
        if ($thanks)$where["thanks"] = ['like', '%' .$thanks. '%'];
        if ($title)$where["title"] = ['like', '%' .$title. '%'];
        if ($url)$where["url"] = ['like', '%' .$url. '%'];
        if ($version)$where["version"] = ['like', '%' .$version. '%'];
        if ($views)$where["views"] = ['like', '%' .$views. '%'];
        if (request()->param("startrefreshtime") && request()->param("endrefreshtime"))$where["refreshtime"] = [['>=', request()->param("startrefreshtime")], ['<=', request()->param("endrefreshtime")], 'and'];
        if (request()->param("startcreatetime") && request()->param("endcreatetime"))$where["createtime"] = [['>=', request()->param("startcreatetime")], ['<=', request()->param("endcreatetime")], 'and'];
        if (request()->param("startupdatetime") && request()->param("endupdatetime"))$where["updatetime"] = [['>=', request()->param("startupdatetime")], ['<=', request()->param("endupdatetime")], 'and'];
        if (request()->param("startdeletetime") && request()->param("enddeletetime"))$where["deletetime"] = [['>=', request()->param("startdeletetime")], ['<=', request()->param("enddeletetime")], 'and'];
        if ($weigh)$where["weigh"] = ['like', '%' .$weigh. '%'];
        if ($status)$where["status"] = ['like', '%' .$status. '%'];

        $result = Db::name('market')->where($where)->page($page,$limit)->select()->toArray();
        foreach($result as $elt => $item){

            $result[$elt]["category_name"] = Db::name("category")->where("id",$item["category_id"])->field('username')->find()['username'];
        }
        if ($result)
            $this->success('查询成功', $result);
        else
            $this->error('信息不存在');
    }

    /**
     * @title    删除
     * @author 一笑奈何
     * @desc  (描述信息)
     * @method   (POST/GET)
     * @ApiRoute    (/api/Market/del/id/{id})
     * @ApiHeaders  (name="token", type="string", required=true, description="请求的Token")
     * @param   int id &nbsp; 主键id Yes
     * @return   int code &nbsp; 返回参数 200
     * @return   string message &nbsp; 返回信息 successful
     * @return   array data &nbsp; 返回数据 successful
     * */
    public function del()
    {
        $id = request()->param('id');
        $result = Db::name('market')->where('id', $id)->delete();
        if ($result)
            $this->success('删除成功');
        else
            $this->error('删除失败');
    }

}