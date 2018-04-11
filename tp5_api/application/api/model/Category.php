<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/9
 * Time: 13:57
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['delete_time','update_time','topic_img_id'];
    public function img()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }

    public static function getAllCategories()
    {
        $categgories = self::with('img')->select();
        return $categgories;
    }
}