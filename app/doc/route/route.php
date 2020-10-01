<?php
use think\facade\Route;

Route::miss(function () {
    return json(['status'=>'-1','message'=>'未找到路由信息！']);
});

Route::any( '/', function () {
    return redirect( '/doc/document?name=explain' );
} );
Route::get( 'assets', "\\ric\\apidoc\\Controller@assets", [ 'deny_ext' => 'php|.htacess' ] );
Route::get( 'module', "\\ric\\apidoc\\Controller@module" );
Route::get( 'action', "\\ric\\apidoc\\Controller@action" );
Route::get( 'document', "\\ric\\apidoc\\Controller@document" );
Route::any( 'login$', "\\ric\\apidoc\\Controller@login" );
Route::any( 'format_params', "\\ric\\apidoc\\Controller@format_params" );