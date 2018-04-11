<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/14
 * Time : 11:32
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code        = 404;
    public $msg         = 'this user is not found';
    public $errorCode   = 60000;
}