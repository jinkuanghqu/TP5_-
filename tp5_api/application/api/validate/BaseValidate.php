<?php
/**
 * 
 * @authors jinkuang
 * @email   jinkuanghqu@gmail.com
 * @date    2018-03-07 15:42:09
 * @version 1.0.0
 */
namespace app\api\validate;

use think\Validate;
use think\Request;
use app\lib\exception\ParamterException;

class BaseValidate extends Validate {
    
    public function goCheck()
    {
    	$request = Request::instance();
    	$params  = $request->param();
    	$result = $this->batch()->check($params);
    	if ($result !== true) {
    		$e = new ParamterException(
                [
                    //'msg' => is_array($this->error) ? implode(
                    //    ';', $this->error) : $this->error,
                    'msg' => $this->error
                ]);
            throw $e;
    	} else {
    		return true;
    	}
    }

    protected function isPositiveInteger($value, $rule='', $data='', $field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
        
    }

    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }

    }

    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}';
        $result = preg_match($rule,$value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 过滤掉请求的非法参数
     * @param $arrays 用户提交过来的参数
     * @return array
     * @throws ParamterException
     */
    public function getDataByRule($arrays)
    {
        if (array_key_exists('user_id',$arrays) | array_key_exists('uid',$arrays)) {
            throw new ParamterException([
                'msg' => '请求参数含有非法参数名',
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key=>$value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }
}