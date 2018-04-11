<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/15
 * Time : 14:23
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg  = '订单不存在,请检查ID';
    public $errorCode = 80000;
}