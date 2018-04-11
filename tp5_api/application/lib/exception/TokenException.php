<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/13
 * Time : 17:15
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 400;
    public $msg  = 'token无效或已过期';
    public $errorCode = 10001;
}