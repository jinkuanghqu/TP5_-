<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 14:08:59
 * @version 1.0.0
 */
namespace app\lib\exception;

class BannerMissException extends BaseException 
{
    
    public $code = 404;
    public $msg  = 'request the banner is not found';
    public $errorCode = 40000;
}