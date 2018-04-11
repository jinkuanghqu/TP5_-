<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/14
 * Time : 15:40
 */

namespace app\api\validate;


use app\lib\exception\ParamterException;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'products' => 'require|checkProducts',
    ];

    protected $singRule = [
        'product_id' => 'require|isPositiveInteger',
        'count'      => 'require|isPositiveInteger'
    ];

    protected function checkProducts($values)
    {
        if (!is_array($values)) {
            throw new ParamterException([
                'msg' => '商品参数不正确'
            ]);
        }

        if (empty($values)) {
            throw new ParamterException([
                'msg' => '商品列表不能为空'
            ]);
        }
        foreach ($values as $key=>$value)
        {
            $this->checkProduct($value);
        }
        return true;

    }

    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singRule);
        $result   = $validate->check($value);
        if (!$result) {
            throw new ParamterException([
                'msg' => '商品列表参数错误'
            ]);
        }
    }
}