<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/16
 * Time : 14:26
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    //待支付
    const UNPAY = 1;
    //已支付
    const PAY   = 2;
    //已发货
    const DELIVERED =3;
    //已支付但库存不足
    const PAY_BUT_OUT_OF = 4;
}