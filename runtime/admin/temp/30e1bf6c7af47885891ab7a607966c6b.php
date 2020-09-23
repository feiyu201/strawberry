<?php /*a:1:{s:55:"E:\phpstudy\WWW\okadmin\app\admin\view\index\index.html";i:1600852754;}*/ ?>
<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <title>草莓万能开发框架</title>
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="keywords" content="草莓万能开发框架 草莓万能快速开发框架">
   <meta name="description" content="草莓万能开发框架 草莓万能快速开发框架">
   <link rel="shortcut icon" href="/static/images/favicon.ico" type="image/x-icon"/>
   <link rel="stylesheet" href="/static/css/okadmin.css">
   <link rel="stylesheet" href="/static/lib/loading/okLoading.css"/>
   <script type="text/javascript" src="/static/js/okconfig.js"></script>
   <script type="text/javascript" src="/static/lib/loading/okLoading.js"></script>
</head>
<body class="layui-layout-body">
<!-- 更换主体 Eg:orange_theme|blue_theme -->
<div class="layui-layout layui-layout-admin okadmin blue_theme">
   <!--头部导航-->
   <div class="layui-header okadmin-header">
      <ul class="layui-nav layui-layout-left">
         <li class="layui-nav-item">
            <a class="ok-menu ok-show-menu" href="javascript:" title="菜单切换">
               <i class="layui-icon layui-icon-shrink-right"></i>
            </a>
         </li>
         <!--天气信息-->
         <!--
         <li class="ok-nav-item ok-hide-md">
             <div class="weather-ok">
                 <iframe frameborder="0" scrolling="no" class="iframe-style" src="pages/weather.html" frameborder="0"></iframe>
             </div>
         </li>
          -->
      </ul>
      <ul class="layui-nav layui-layout-right">
         <li class="layui-nav-item ok-input-search">
            <input type="text" placeholder="搜索..." class="layui-input layui-input-search"/>
         </li>
         <li class="layui-nav-item">
            <a class="ok-refresh" href="javascript:" title="刷新">
               <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
         </li>
         <li class="no-line layui-nav-item layui-hide-xs">
            <a id="notice" class="flex-vc pr10 pl10" href="javascript:">
               <i class="ok-icon ok-icon-notice icon-head-i" title="系统公告"></i>
               <span class="layui-badge-dot"></span>
               <cite></cite>
            </a>
         </li>

         <!-- <li class="no-line layui-nav-item layui-hide-xs">
            <a id="lock" class="flex-vc pr10 pl10" href="javascript:">
               <i class="ok-icon ok-icon-lock icon-head-i" title="锁屏"></i><cite></cite>
            </a>
         </li> -->

         <!-- 全屏 -->
         <li class="layui-nav-item layui-hide-xs">
            <a id="fullScreen" class=" pr10 pl10" href="javascript:;">
               <i class="layui-icon layui-icon-screen-full"></i>
            </a>
         </li>

         <li class="no-line layui-nav-item">
            <a href="javascript:">
               <img src="<?php echo htmlentities($admininfo['avatar']); ?>" class="layui-nav-img">
              	<?php echo htmlentities($admininfo['username']); ?>
            </a>
            <dl id="userInfo" class="layui-nav-child">
              <!-- <dd><a lay-id="u-1" href="javascript:" data-url="pages/member/user.html">个人中心<span
                   class="layui-badge-dot"></span></a></dd> -->
              <!-- <dd><a lay-id="u-2" href="javascript:" data-url="pages/member/user-info.html">基本资料</a></dd> -->
               <!-- <dd><a lay-id="u-3" href="javascript:" data-url="pages/member/user-pwd.html">安全设置</a></dd> -->
              <!-- <dd><a lay-id="u-4" href="javascript:" id="alertSkin">皮肤动画</a></dd>
               <dd>
                  <hr/>
               </dd> -->
               <dd><a href="javascript:void(0)" id="logout">退出登录</a></dd>
            </dl>
         </li>

         <!-- 菜单 -->
         <!-- <li class="layui-nav-item layui-hide-xs">
            <a id="okSetting" class="pr10 pl10" href="javascript:;">
               <i style="font-size: 18px" class="ok-icon ok-icon-moreandroid"></i>
            </a>
         </li> -->
      </ul>
   </div>
   <!--遮罩层-->
   <div class="ok-make"></div>
   <!--左侧导航区域-->
   <div class="layui-side layui-side-menu okadmin-bg-20222A ok-left">
      <div class="layui-side-scroll okadmin-side">
         <div class="okadmin-logo">草莓万能开发框架</div>
         <div class="user-photo">
            <a class="img" title="我的头像">
               <img src="<?php echo htmlentities($admininfo['avatar']); ?>" class="userAvatar">
            </a>
            <p>你好！<span class="userName"><?php echo htmlentities($admininfo['username']); ?></span>, 欢迎登录</p>
         </div>
         <!--左侧导航菜单-->
         <ul id="navBar" class="layui-nav okadmin-nav okadmin-bg-20222A layui-nav-tree">
            <li class="layui-nav-item layui-this">
               <a href="javascript:" lay-id="1" data-url="<?php echo url('welcome'); ?>">
                  <i is-close=false class="ok-icon">&#xe654;</i>
                  控制台
               </a>
            </li>
			
			<li class="layui-nav-item">
			    <a href="javascript:" lay-id="2" data-url="<?php echo url('admin/index'); ?>" is-close="true">
			   <i class="layui-icon">&#xe66f;</i><cite> 人员管理</cite>
			   </a>
			</li>
			<li class="layui-nav-item">
			   
			   <a href="javascript:" lay-id="3" data-url="<?php echo url('plugin/index'); ?>" is-close="true">
			   <i class="layui-icon">&#xe674;</i><cite>  插件管理</cite>
			   </a>
			</li>
         </ul>
      </div>
   </div>

   <!-- 内容主体区域 -->
   <div class="content-body">
      <div class="layui-tab ok-tab" lay-filter="ok-tab" lay-allowClose="true" lay-unauto>
         <div data-id="left" id="okLeftMove"
              class="ok-icon ok-icon-back okadmin-tabs-control move-left okNavMove"></div>
         <div data-id="right" id="okRightMove"
              class="ok-icon ok-icon-right okadmin-tabs-control move-right okNavMove"></div>
         <div class="layui-icon okadmin-tabs-control ok-right-nav-menu" style="right: 0;">
            <ul class="okadmin-tab">
               <li class="no-line okadmin-tab-item">
                  <div class="okadmin-link layui-icon-down" href="javascript:;"></div>
                  <dl id="tabAction" class="okadmin-tab-child layui-anim-upbit layui-anim">
                     <dd><a data-num="1" href="javascript:">关闭当前标签页</a></dd>
                     <dd><a data-num="2" href="javascript:">关闭其他标签页</a></dd>
                     <dd><a data-num="3" href="javascript:">关闭所有标签页</a></dd>
                  </dl>
               </li>
            </ul>
         </div>

         <ul id="tabTitle" class="layui-tab-title ok-tab-title not-scroll">
            <li class="layui-this" lay-id="1" tab="index">
               <i class="ok-icon">&#xe654;</i>
               <cite is-close=false>控制台</cite>
            </li>
         </ul>

         <div id="tabContent" class="layui-tab-content ok-tab-content">
            <div class="layui-tab-item layui-show">
               <iframe src='<?php echo url('welcome'); ?>' frameborder="0" scrolling="yes" width="100%"
                       height="100%"></iframe>
            </div>
         </div>
      </div>
   </div>

   <!--底部信息-->
   <div class="layui-footer okadmin-text-center">
      Copyright ©2018-©2020 草莓万能开发框架 All Rights Reserved
   </div>
</div>

<!-- 锁屏 -->
<div class="lock-screen">
   <div class="lock-bg">
     
      <img class="lock-gradual" src="/static/images/wallpaper/9f28afe0e71b3ba8778e307bea2f006d.jpg" alt="" />
      <img class="lock-gradual" src="/static/images/wallpaper/29bce2d5cf30fc96866dcb5e287661ea.jpg" alt="" />
      <img class="lock-gradual" src="/static/images/wallpaper/b4b55f8ec6b2763a737a2d6e1c50b71e.jpg" alt="" />
      <img class="lock-gradual" src="/static/images/wallpaper/b8df65c6452dcf8b0302b8bfce9e7ec9.jpg" alt="" />
      <img class="lock-gradual" src="/static/images/wallpaper/b390e4c33b7d656f09dc7fd155759a4f.jpg" alt="" />
      <img class="lock-gradual" src="/static/images/wallpaper/3fded2e777723f145a4773dfdb68a9e3.jpg" alt="" />
   </div>
   <div class="lock-content">
      <!--雪花-->
      <div class="snowflake">
         <canvas id="snowflake"></canvas>
      </div>
      <!--雪花 END-->
      <div class="time">
         <div>
            <div class="hhmmss"></div>
            <div class="yyyymmdd"></div>
         </div>
      </div>
      <div class="quit" id="lockQuit">
         <i class="layui-icon layui-icon-logout" title="退出登录"></i>
      </div>
      <table class="unlock">
         <tr>
            <td>
               <div class="layui-form lock-form">
                  <div class="lock-head">
                     <img src="<?php echo htmlentities($admininfo['avatar']); ?>" alt="avatar.png" />
                  </div>
                  <div class="layui-form-item">
                     <div class="layui-col-xs8 layui-col-sm8 layui-col-md8">
                        <input type="password"
                               required
                               lay-verify="required"
                               id="lockPassword"
                               name="lock_password"
                               style="border-radius: 0;border:0;height: 44px"
                               placeholder="默认密码123456"
                               autocomplete="off"
                               class="layui-input" />
                     </div>
                     <div class="layui-col-xs4 layui-col-sm4 layui-col-md4">
                        <button style="width: 100%;box-sizing:border-box;border-radius: 0;"
                                type="button"
                                lay-submit
                                lay-filter="lockSubmit"
                                class="layui-btn lock-btn layui-btn-lg layui-btn-normal">确定</button>
                     </div>
                  </div>
               </div>
            </td>
         </tr>
      </table>
   </div>
</div>
<!--js逻辑-->
<script src="/static/lib/layui/layui.js"></script>
<script src="/static/js/snowflake.js?snowflake=雪花"></script>
<script src="/static/js/okadmin.js"></script>
</body>
</html>
