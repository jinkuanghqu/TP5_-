<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/14
 * Time : 11:48
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code = 201;//201表示资源状态发生改变成功
    public $msg  = 'ok';
    public $errorCode = 0;
}