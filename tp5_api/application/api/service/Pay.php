<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/16
 * Time : 11:40
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderId;
    private $orderNo;

    public function __construct ($orderId)
    {
        if ($orderId) {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderId = $orderId;
    }

    public function pay()
    {
        //订单号可能不存在
        //订单号存在但是和当前用户不匹配
        //检测订单是否已经被检测
        $this->checkOrderValid();
        //进行库存检测
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderId);
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);

    }

    private function makeWxPreOrder($totalPrice)
    {
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        //实例化统一下单类
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('微信支付');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url('');//回调地址
        return $this->getPaySignture($wxOrderData);
    }

    private function getPaySignture($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        //prepay_id向用户推送消息模板时需要用到
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id','=',$this->orderId)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(rand(time().mt_rand(0,1000)));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();//把对象转换为数组
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);

        return $rawValues;
    }

    /**
     * 检测订单的有效性
     * @return mixed true
     * @throws OrderException
     * @throws TokenException
     */
    private function checkOrderValid()
    {
        $order = OrderModel::where('id','=',$this->orderId)->find();
        //检测订单是否存在
        if (!$order) {
            throw new OrderException();
        }
        //检测订单是否和当前用户匹配
        if (Token::isValidOperate($order->user_id)) {
            throw new TokenException([
                'msg'=>'订单与用户不匹配',
                'errorCode'=>10003
            ]);
        }
        //检测订单是否是未支付状态
        if ($order->status != OrderStatusEnum::UNPAY) {
            throw new OrderException([
                'msg'=>'订单已支付过了',
                'errorCode'=>80003,
                'code'=>404
            ]);
        }
        $this->orderNo = $order->order_no;
        return true;

    }

}