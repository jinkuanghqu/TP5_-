<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 15:47:49
 * @version 1.0.0
 */
namespace app\lib\exception;

class ParamterException extends BaseException 
{
    
    public $code = 400;
    public $msg  = 'paramters error';
    public $errorCode = 10000;

    public function __construct($params = [])
    {
    	if (!is_array($params)) {
    		return ;
    	}
    	if (array_key_exists('code', $params)) {
    		$this->code = $params['code'];
    	}
    	if (array_key_exists('msg',$params)) {
    		$this->msg = $params['msg'];
    	}
    	if (array_key_exists('errorCode',$params)) {
    		$this->errorCode = $params['errorCode'];
    	}
    }

}