<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 13:50:33
 * @version 1.0.0
 */
namespace app\lib\exception;

use think\Exception;


class BaseException extends Exception
{
    //HTTP stauts code
    public $code = 400;
    //details error info
    public $msg  = 'params error';
    //user defined error code 
    public $errorCode = 99999;

}