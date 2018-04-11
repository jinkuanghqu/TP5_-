<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/9
 * Time: 11:08
 */

namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent ($count = 20)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if ($products->isEmpty()) {
            throw new ProductException();
        }
//        $products = collection($products)->hidden(['summary']);
        return json($products);
    }

    /**
     * 获取该分类下所有的产品
     * @param $id
     * @return \think\response\Json
     * @throws ProductException
     */
    public function getAllInCategories($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryId($id);
        if ($products->isEmpty()) {
            throw new ProductException();
        }
        return json($products);
    }

    /**
     * 获取一个产品的详细数据
     * @param $id
     */
    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();

        $product = ProductModel::getPorductDetail($id);
        if (!$product) {
            throw new ProductException();
        }
        return $product;

    }

}