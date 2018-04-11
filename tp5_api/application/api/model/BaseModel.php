<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    //读取器，get+字段+Attr,字段必须是驼峰命名法命名
    //$value是要读取的内容由框架自动读取
    protected function prefixImgUrl($value,$data)
    {
    	$finalUrl = $value;
    	if ($data['from'] == 1) {
    		$finalUrl = config('setting.img_prefix').$value;
    	}
    	return $finalUrl;
    }
}
