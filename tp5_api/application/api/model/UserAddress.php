<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/14
 * Time : 14:13
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = ['id','user_id','delete_time'];
}