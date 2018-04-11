<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/9
 * Time: 11:28
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code        = 404;
    public $msg         = 'your request product is not found';
    public $errorCode  = 20000;
}