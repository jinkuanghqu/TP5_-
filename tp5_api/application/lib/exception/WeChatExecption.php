<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/9
 * Time : 16:46
 */

namespace app\lib\exception;


class WeChatExecption extends BaseException
{
    public $code        = 404;
    public $msg         = '微信服务器接口调用失败';
    public $errorCode   = 99999;
}