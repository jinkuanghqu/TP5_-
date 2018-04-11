<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 14:03:12
 * @version 1.0.0
 */
namespace app\api\controller\v1;

use think\Controller;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use app\api\validate\IDMustBePositiveInt;
use think\Queue;

class Banner {
    
    public function getBanner($id)
    {
    	$validate = new IDMustBePositiveInt();
    	$validate->goCheck();
    	$bannerInfo = BannerModel::getBannerById($id);
    	if (!$bannerInfo) {
    		throw new BannerMissException();
    	}
    	return json($bannerInfo);
    }

}