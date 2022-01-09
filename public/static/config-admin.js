var BASE_URL = document.scripts[document.scripts.length - 1].src.substring(0, document.scripts[document.scripts.length - 1].src.lastIndexOf("/") + 1);
window.BASE_URL = BASE_URL;
require.config({
    urlArgs: "v=" + CONFIG.VERSION,
    baseUrl: BASE_URL,
    paths: {
        "easy-admin": ["plugs/strawberry/easy-admin"],
        "jquery": ["js/jquery.min"],
        "layuiall": ["plugs/layui-v2.5.6/layui.all"],
        "layui": ["plugs/layui-v2.5.6/layui"],
        "ckeditor": ["lib/ckeditor4/ckeditor"],
        "tableSelect": ["plugs/lay-module/tableSelect/tableSelect"],
    }
});


// 初始化控制器对应的JS自动加载
if ("undefined" != typeof CONFIG.AUTOLOAD_JS && CONFIG.AUTOLOAD_JS) {
    require([BASE_URL + CONFIG.CONTROLLER_JS_PATH], function (Controller) {
        if (eval('Controller.' + CONFIG.ACTION)) {
            eval('Controller.' + CONFIG.ACTION + '()');
        }
    });
}