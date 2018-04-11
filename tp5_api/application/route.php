<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//banner
Route::get('hello','index/index/index');
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');
//主题
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');



//Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategories');
//Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//Route::get('api/:version/product/recent','api/:version.Product/getRecent');
//产品
Route::group('api/:version/product',function(){
    Route::get('/by_category','api/:version.Product/getAllInCategories');
    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
    Route::get('/recent','api/:version.Product/getRecent');
});
//分类
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');

//token
Route::post('api/:version/token/user','api/:version.Token/getToken');
//地址
Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');

//订单
Route::post('api/:version/order','api/:version.Order/placeOrder');

//支付
Route::post('api/:version/pre_order','api/:version.Pay/getPreOrder');
Route::post('api/:version/notify','api/:version.Pay/receiveNotify');
