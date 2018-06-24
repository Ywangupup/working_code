<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21 0021
 * Time: 下午 8:47
 */
namespace app\home\controller;
use app\home\model\Order;
use app\home\model\OrderGoods;
use think\Controller;
use app\home\model\Goods;
use think\Db;

class OrderController extends Controller{
    //展示当前用户的购物车数据,并预览到订单预处理页面,
    public function orderInfo(){
        if(!session('uid')){
            $this->error("请您先行登录再查看订单");
        }
        if(request()->isPost()){
            //调用写入订单信息方法
            $this->_writeOrder();die;
        }
        $goodsModel=new Goods();
        $cartData=$goodsModel->getCartGoodsData();
        if(empty($cartData)){
            $this->error("您还没有选择需要购买的商品","home/index/index");
        }
        return $this->fetch('',['cartData'=>$cartData]);
    }
    //将用户提交的信息入库到商品订单表,订单信息表
    private function _writeOrder(){
        //接受订单的收货信息
        $orderData=input('post.');
        //验证信息
        $result=$this->validate($orderData,'Order',[],true);
        if($result!==true){
            $this->error(implode(',',$result));
        }
        //获取购物车商品,求出需付款的总价格
        $goodsModel=new Goods();
        $cartData=$goodsModel->getCartGoodsData();
        $total_price=0;
        foreach($cartData as $cart){
            $total_price+=($cart['goodsInfo']['goods_price']+$cart['attrInfo']['attrs_price'])*$cart['goods_number'];
        }
        //生成订单入库所需的基本数据
        $orderData['order_id']=date('ymd').time().uniqid();
        $orderData['total_price']=$total_price;
        $orderData['uid']=session('uid');
        Db::startTrans();//开启mysql事务
        try{
            //入库订单基本数据
            $order=Order::create($orderData);
            if(!$order){
                //抛出异常;                throw new \Exception("订单入库失败");
            }
            foreach ($cartData as $cart){
                //入库商品订单表
                $orderGoods=OrderGoods::create([
                    'order_id' =>$order['order_id'],
                    'goods_id'=>$cart['goods_id'],
                    'goods_attr_ids'=>$cart['goods_attr_ids'],
                    'goods_number'=>$cart['goods_number'],
                    'goods_price' =>($cart['goodsInfo']['goods_price']+$cart['attrInfo']['attrs_price'])*$cart['goods_number'],
                ]);
                //减库存
                $condition=[
                    'goods_id'=>$cart['goods_id'],
                    'goods_number'=>['egt',$cart['goods_number']]
                ];
                $goodsNumber=Goods::where($condition)->setDec('goods_number',$cart['goods_number']);
                if(!$orderGoods||!$goodsNumber){
                    //抛出异常
                    throw new \Exception("订单商品入库失败或者商品被买完了");
                }
            }
            //没有异常提交数据
            Db::commit();
            //没有异常且订单生成成功.清空购物车;
            $cart=new \cart\Cart();
            $cart->clearCart();
        }catch (\Exception $exception){
            //有异常.回滚事务,返回错误信息;
            Db::rollback();
            $this->error($exception->getMessage());
        }
        //调用支付私有方法,唤醒支付宝接口
        $this->_payMoney($order['order_id'],$total_price);
    }
    //私有方法,唤醒支付宝支付接口
    private function _payMoney($order_id,$total_price,$title="shop商城支付",$content="商品付款"){
        $payData=[
            'WIDout_trade_no'=>$order_id,
            'WIDsubject' =>$title,
            'WIDtotal_amount'=>$total_price,
            'WIDbody'=>$content
        ];
        include "../extend/alipay/pagepay/pagepay.php";
    }
    //支付宝回调同步方法
    public function returnurl(){
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
        $arr=input('get.');
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        if($result) {//验证成功
            //请在这里加上商户的业务逻辑程序代码
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            //商户订单号
            $out_trade_no = htmlspecialchars($arr['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($arr['trade_no']);
            $data=[
                'pay_status'=>1,
                'ali_order_id'=>$trade_no
            ];
            if(Order::where('order_id',$out_trade_no)->Update($data)){
                $this->success("支付成功","home/order/selfOrder");
            }else{
                $this->error("支付失败","home/Cart/cartList");
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        }
        else {
            //验证失败
            //如果验证失败重新配置公钥和私钥
            $this->error("验证失败");
        }
    }
    //支付宝支付回调的异步方法
    public function notifyurl(){
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
        $arr=input('post.');
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        if($result) {//验证成功
            //请在这里加上商户的业务逻辑程序代码
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            //商户订单号
            $out_trade_no = htmlspecialchars($arr['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($arr['trade_no']);
            $data=[
                'pay_status'=>1,
                'ali_order_id'=>$trade_no
            ];
            if(Order::where('order_id',$out_trade_no)->Update($data)){
                echo "success";
                $this->success("支付成功","home/index/index");
            }else{
                $this->error("支付失败","home/Cart/cartList");
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        }
        else {
            //验证失败
            //如果验证失败重新配置公钥和私钥
            $this->error("验证失败");
        }
    }
    //订单列表
    public function selfOrder(){
        if(!session('uid')){
            $this->error("请先登录,再查看订单");
        }
        $lists=Order::where('uid',session('uid'))->select()->toArray();
        $goods_ids=[];
        foreach ($lists as $k=>$list){
            $goods_ids=OrderGoods::where('order_id','in',$list['order_id'])->field("group_concat(goods_id Separator ',') as goods_id")->find()->toArray();
            $goods_ids=implode(',',$goods_ids);
            $lists[$k]['goods_data']=Goods::where('goods_id','in',$goods_ids)->field("goods_thumb,group_concat(goods_name Separator '<br>') as goods_name")->find()->toArray();
//              可以把数组构造成二维数组,少一次取值
//            $lists[$k]['goods_thumb']=$lists[$k]['goods_data']['goods_thumb'];
//            $lists[$k]['goods_name']=$lists[$k]['goods_data']['goods_name'];
//            unset($lists[$k]['goods_data']);
        }
        return $this->fetch('',['lists'=>$lists]);
    }
    //订单支付方法
    public function goPayMoney(){
        $orderData=Order::find(input('id'));
        if($orderData){
            $this->_payMoney($orderData['order_id'],$orderData['total_price']);
        }else{
            $this->error('支付异常,请稍后再试');
        }
    }
    //获取物流信息
    public function getLogistics(){
        if(request()->isAjax()){
            //获取物流接口的key
            $key=config('key');
            //获取物流公司
            $company=input('company');
            //获取运单号
            $number=input('number');
            //发送跨域请求,请求快递接口
            echo file_get_contents("http://www.kuaidi100.com/applyurl?key={$key}&com={$company}&nu={$number}&show=0");
        }
    }
}