<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21 0021
 * Time: 上午 11:59
 */
namespace app\home\controller;
use app\home\model\Goods;
use think\Controller;

class CartController extends Controller{

    public function cartList(){
        if(!session('uid')){
            $this->error("您还没有登录","home/public/login");
        }
        $goodsModel=new Goods();
        $cartData=$goodsModel->getCartGoodsData();
        return $this->fetch('',['cartData'=>$cartData]);
    }

    public function delCartGoods(){
        if(request()->isAjax()){
            $cart=new \cart\Cart();
            $status=$cart->delCart(input('goods_id'),input('goods_attr_ids'));
            if($status){
                return json(['code'=>200,'message'=>'删除成功']);
            }else{
                return json(['code'=>-1,'message'=>'删除失败']);
            }
        }
    }

    public function clearCart(){
        if(request()->isAjax()){
           $cart=new \cart\Cart();
           $status=$cart->clearCart();
           if($status){
               return json(['code'=>200,'message'=>'清空完成!']);
           }else{
               return json(['code'=>-1,'message'=>'清空失败!']);
           }
        }
    }

    public function cahngeNum(){
        $goods_id=input('goods_id');
        $goods_attr_ids=input('goods_attr_ids');
        $goods_number=input('goods_number');
        $cart=new \cart\Cart();
        $result=$cart->changeCartNum($goods_id,$goods_attr_ids,$goods_number);
        if($result){
            return json(['code'=>200]);
        }else{
            return json(['code'=>-1]);
        }

    }
}