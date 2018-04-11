<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/9
 * Time : 15:18
 */

namespace app\api\service;

use app\api\model\User;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatExecption;
use think\Exception;
use app\api\model\User as UserModel;


class UserToken extends Token
{
    protected $code;
    protected $wxAppId;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    public function __construct ($code)
    {
       $this->code        = $code;
       $this->wxAppId     = config('wx.app_id');
       $this->wxAppSecret = config('wx.app_secret');
       $this->wxLoginUrl  = sprintf(config('wx.login_url'),$this->wxAppId,$this->wxAppSecret,$this->code);
    }

    /**
     * 获取token
     * @throws Exception
     */
    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result,true);
        if (empty($wxResult)) {
           throw new Exception('获取session_key及openID异常,微信服务器内部错误');
        } else {
            $loginFail = array_key_exists('errcode',$wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            } else {

                $this->grantToken($wxResult);
            }
        }

    }

    /**
     * 把token存储到缓存中并返回
     * @param $wxResult
     * @return string
     */
    private function grantToken($wxResult)
    {
        $openId = $wxResult['openid'];
        $user = UserModel::getByOpenId($openId);
        if ($user) {
            $uid = $user->id;
        } else {
            $uid = $this->newUser($openId);
        }

        $cachedValue = $this->prepareCachedValue($wxResult,$uid);
        $token = $this->saceToCache($cachedValue);
        return $token;
    }

    /**
     * 新增一个用户到数据库中
     * @param $openid 微信用户openid
     * @return mixed  返回uid
     */
    private function newUser($openid)
    {
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    /**
     * 微信服务器接口调用失败处理
     * @param $wxResult
     * @throws WeChatExecption
     */
    private function processLoginError($wxResult)
    {
        throw new WeChatExecption([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }

    /**
     * 组装要缓存的数据
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCachedValue($wxResult,$uid)
    {
        $cachedValue          = $wxResult;
        $cachedValue['uid']   = $uid;
        $cachedValue['scope'] = ScopeEnum::User;

        return $cachedValue;

    }

    /**
     * 将数据保存到缓存中
     * @param $cachedValue
     */
    private function saceToCache($cachedValue)
    {
        $key = self::generateToken();//生成令牌
        $value = json_encode($cachedValue);
        $expireIn = config('setting.token_expire_in');
        $request = cache($key,$value,$expireIn);
        if (!$request) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        //返回令牌
        return $key;

    }
}