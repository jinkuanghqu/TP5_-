<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 15:55:43
 * @version 1.0.0
 */

namespace app\api\validate;

class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];

    protected $message = [
    	'id' => 'id必须是正整数',
    ];
}