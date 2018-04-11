<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/13
 * Time : 17:51
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['img_id','delete_time','product_id'];

    public function imgUrl()
    {
        return $this->belongsTo('Image','img_id','id');
    }

}