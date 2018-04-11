<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/14
 * Time : 14:40
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code        = 403;
    public $msg         = '权限不够';
    public $errorCode   = 10001;
}