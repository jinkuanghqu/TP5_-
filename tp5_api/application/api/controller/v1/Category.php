<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/9
 * Time: 13:57
 */

namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;


class Category
{
    public function getAllCategories()
    {
        $categories = CategoryModel::getAllCategories();
        if ($categories->isEmpty()) {
            throw new CategoryException();
        }
        return json($categories);
    }
}