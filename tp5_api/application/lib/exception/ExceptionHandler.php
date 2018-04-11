<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 13:46:39
 * @version 1.0.0
 */
namespace app\lib\exception;

use think\exception\Handle;
use think\Request;
use think\Log;

/*
 * 重写Handle的render方法，实现自定义异常消息
 */
class ExceptionHandler extends Handle 
{    
    private $code;
    private $msg;
    private $errorCode;
    //rewrite render method
    public function render(\Exception $e)
    {
    	// return json('rewrite render method in ExceptionHandler.php');
    	if ($e instanceof BaseException) {
    		$this->code      = $e->code;
    		$this->msg       = $e->msg;
    		$this->errorCode = $e->errorCode;
    	} else {
    		if (config('app_debug')) {
    			return parent::render($e);
    		}
    		$this->code      = 500;
    		$this->msg       = 'Internal Server Error';
    		$this->errorCode = 99999;
    		$this->recordErrorLog($e);

    	}
    	$request = Request::instance();
    	$result  = [
    		'errorCode' => $this->errorCode,
    		'msg'       => $this->msg,
    		'requestUrl'=> $request->url()
    	];

    	return json($result);
    }

    private function recordErrorLog(\Exception $e)
    {
    	Log::init([
    			'type' => 'File',
    			'path' => LOG_PATH,
    			'level'=> ['error'] 
    		]);
    	Log::record($e->getMessage(),'error');
    }
}