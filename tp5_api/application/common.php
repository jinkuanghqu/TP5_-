<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @param $url 请求地址
 * @param int $httpCode http状态码
 * @return mixed
 */
function curl_get($url,&$httpCode=0)
{
    $ch = curl_init(); // 启动一个CURL会话
    curl_setopt($ch, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不做认证证书来源的检查，部署到正式环境改为true
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;

}

/**
 * @param $lenth 所要生成的字符串的长度
 */
function getRandChars($lenth)
{
    $str = '';
    $strAll = 'abcdefghijklmnopqruvwxyzABCDEFGHIJKLMNOPQRUVWXYZ1234567890';
    $maxLenth = strlen($strAll)-1;
    for ($i = 0; $i < $lenth; $i++) {
        $str .= $strAll[rand(0,$maxLenth)];
    }
    return $str;

}

/**
 * @param $url 请求地址
 * @param int $timeout 超时时间
 * @param array $post 请求参数
 * @return mixed
 */
function https_post($url, $timeout = 300, $post = array())
{
    $ch = curl_init(); // 启动一个CURL会话
    curl_setopt($ch, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/x-www-form-urlencoded; charset=UTF-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $data = curl_exec($ch); // 执行操作
    if (curl_errno($ch)) {
        echo 'Errno'.curl_error($ch);//捕抓异常
    }
    curl_close($ch); // 关闭CURL会话
    return $data; // 返回数据
}

/**
 * @param $url 请求地址
 * @param int $timeout 超时时间
 * @param array $post 请求参数
 * @return mixed
 */
function http_post($url, $timeout = 300, $post = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //支持跳转
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2); //可跳转的次数
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if (!empty($post)) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_scalar($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
    }
    $data = curl_exec($ch);
    if ($data == false) {
        curl_close($ch);
    }
    @curl_close($ch);
    return $data;
}
