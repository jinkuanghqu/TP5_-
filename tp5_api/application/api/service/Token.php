<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/13
 * Time : 16:51
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken()
    {
        $randChars = getRandChars(32);
        $timeStamp = $_SERVER['REQUEST_TIME'];
        $salt      = config('secure.token_salt');
        return md5($randChars.$timeStamp.$salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars  = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars,true);
            }

            if (array_key_exists($key,$vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取Token变量并不存在');
            }
        }
    }

    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**
     * 用户和管理员都可以访问
     * @return bool
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * 只有用户可以访问
     * @return bool
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needExclusive()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function isValidOperate($checkedUid)
    {
        if ($checkedUid) {
            throw new Exception('检测UID时必须传递一个被检测的UID');
        }
        $currentUid = self::getCurrentUid();
        if ($currentUid == $checkedUid)
        {
            return true;
        } else {
            return false;
        }
    }
}