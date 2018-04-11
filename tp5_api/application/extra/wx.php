<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/9
 * Time : 15:41
 */

return [
    //微信小程序的appId
    'app_id'      => '',
    //微信小程序的secret
    'app_secret'  => '',
    //code 换取 session_key的url
    'login_url'   => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
];