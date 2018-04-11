<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/9
 * Time: 11:10
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,30',
    ];
    protected $message = [
        'count.bentween'=>'数量必须是1到30之间的正整数',
    ];
}