<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/9
 * Time: 14:15
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code        = 404;
    public $msg         = 'your request category is not found';
    public $errorCode  = 50000;
}