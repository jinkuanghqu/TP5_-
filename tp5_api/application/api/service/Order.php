<?php
/**
 * Created by jinkuang
 * Email: jinkuanghqu@gmail.com
 * Date : 2018/3/14
 * Time : 16:45
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product as ProductModel;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct as OrderProductModel;
use think\Exception;
use think\Db;

class Order
{
    //订单的商品列表，也就是用户传递过来的参数
    protected $oProducts;
    //从数据库查询出来的真实商品信息
    protected $products;
    protected $uid;

    /**
     * 用户提交的商品列表数据
     * $oProducts = [
     *      [
     *          'product_id'=>2,
     *          'count'     =>3
     *      ],[
     *          'product_id'=>4,
     *          'count'     =>3
     *      ],[
     *          'product_id'=>5,
     *          'count'     =>1
     *      ]
     * ];
     *  数据库查询出来的数据
     *  $products = [
     *      [
     *          'product_id'=>2,
     *          'count'     =>2
     *      ],[
     *          'product_id'=>4,
     *          'count'     =>6
     *      ],[
     *          'product_id'=>5,
     *          'count'     =>10
     *      ]
     * ];
     *
     *  思路：比如说确定$oProducts中商品product_id=1的这个商品在真实商品信息列表$products中的序列号
     *
     */
    /**
     * 下单
     * @param $uid 用户id
     * @param $oProducts 用户商品参数列表
     * @return array 处理后的订单信息
     */
    public function place($uid,$oProducts)
    {
       $this->oProducts = $oProducts;
       $this->products  = $this->getProductsByOrder();
       $this->uid       = $uid;
       $status = $this->getOrderStatus();
       if (!$status['pass']) {
           $status['order_id'] = -1;
           return $status;
       }
       //库存检查通过后，开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order     = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;

    }

    /**
     * 创建订单
     * @param $snap 订单快照
     * @return array 订单简要信息
     * @throws Exception
     */
    private function createOrder($snap)
    {
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order   = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();
            $orderId = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderId;
            }
            $orderProduct = new OrderProductModel();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no'    => $orderNo,
                'order_id'    => $orderId,
                'create_time' => $create_time
            ];
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

    }

    /**
     * 组装订单快照数据
     * @param $status
     * @return array 订单快照数据
     */
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' =>0,
            'totalCount' =>0,
            'pStatus'    =>[],
            'snapAddress'=>null,
            'snapName'   =>'',
            'snapImg'    =>''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['orderPrice'] = $status['totalCount'];
        $snap['pStatus']    = $status['pStatusArray'];
        $snap['snapAddress']= json_encode($this->getUserAddress());
        $snap['snapName']   = $this->products[0]['name'];
        $snap['snapImg']    = $this->products[0]['main_img_url'];
        if (count($this->products)>1){
            $snap['snapName'] .= '等';
        }
        return $snap;

    }

    /**
     * 获取用户地址
     * @return array
     * @throws UserException
     */
    private function getUserAddress()
    {
      $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
      if (!$userAddress) {
          throw new UserException([
              'msg'=>'用户地址不存在,下单失败',
              'errorCode'=>60001
          ]);
      }
      return $userAddress->toArray();
    }

    /**
     * 根据用户传递的商品数据从数据库中查询出真实的商品数据列表
     * @param $oProducts 用户传递的商品数据列表
     * @return mixed  从数据库中查询出真实的商品数据列表
     */
    private function getProductsByOrder($oProducts)
    {
        $oIds = [];
        foreach ($oProducts as $item){
           array_push($oIds,$item['product_id']);
        }
        $products = ProductModel::all($oIds)->visible(['id','price','stock','name','main_img_url'])->toArray();
        return $products;
    }

    /**
     * 组装订单数据
     * @return array 订单数据
     */
    private function getOrderStatus()
    {
        $status = [
            'pass'=>true,//判断订单是否有效
            'totalCount'=>0,//所有商品的总数量
            'orderPrice'=>0,//所有商品的总价格
            'pStatusArray'=>[]//所有商品的详细信息
        ];
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'],$oProduct['count'],$this->products);
            if (!$pStatus['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'],$pStatus);
        }
        return $status;
    }

    public function checkOrderStock($orderId)
    {
        $oProducts = OrderProduct::where('order_id','=',$orderId)->select();
        $this->oProducts = $oProducts;
        $this->products  = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();

        return $status;


    }

    /**
     * 查询组装商品信息:检测库存
     * @param $oPid 商品id
     * @param $oCount 商品数量
     * @param $products 从数据库中查询出来的真实商品列表数据
     * @return array 返回正确的商品数据
     * @throws OrderException 创建订单失败
     */
    private function getProductStatus($oPid,$oCount,$products)
    {
        //思路：$oProducts中某一个商品（product_id=1）在真实商品信息列表$products中的序列号
        $pIndex = -1;
        //存放单个商品的详细信息
        $pStatus = [
            'id'=>null,
            'haveStock'=>false,
            'count'=>0,
            'name'=>'',
            'totalPrice'=>0//该商品的总价格 = count * price
        ];
        $count = count($products);
        for ($i=0;$i<$count;$i++) {
            if ($oPid == $products[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            throw new OrderException([
                'msg'=>'id为'.$oPid.'的商品不存在,创建订单失败'
            ]);
        } else {
            $product = $products[$pIndex];
            $pStatus['id']         = $product['id'];
            $pStatus['count']      = $oCount;
            $pStatus['name']       = $product['name'];
            $pStatus['totalPrice'] = $product['price']*$oCount;
            //检测库存
            if ($product['stock']-$oCount>=0) {
                $pStatus['haveStock']  = true;
            }

            return $pStatus;
        }
    }

    /**
     * 生成订单号
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J','K','L','M','N','O','P','Q');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

}