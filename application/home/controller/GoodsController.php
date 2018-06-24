<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/19 0019
 * Time: 下午 9:42
 */
namespace app\home\controller;
use app\home\model\Category;
use app\home\model\Goods;
use think\Controller;
use think\Db;

class GoodsController extends Controller{

    public function detail(){
        $goods_id=input('goods_id',0,'intval');
        $goodsModel=new Goods();
        $goods_data=$goodsModel->find($goods_id);
        $catModel=new Category();
        $familyData=$catModel->getFamilyCats($goods_data['cat_id']);
        $goods_data['goods_img']=json_decode($goods_data['goods_img']);
        $goods_data['goods_middle']=json_decode($goods_data['goods_middle']);
        $goods_data['goods_thumb']=json_decode($goods_data['goods_thumb']);
        $singleData=$goodsModel->getSingleData($goods_id);
        $onlyData=$goodsModel->getonlyData($goods_id);
        $history=$goodsModel->addGoodsToHistory($goods_id);
        //将历史记录的数组转成字符串;
        $history=implode(',',$history);
        //定义sql  field() 是mysql的函数;可以将查询的数据严格按照顺序排序;
        $sql="select * from sh_goods where goods_id in ($history) ORDER BY field(goods_id,$history)";
        $historyData=Db::query($sql);
        return $this->fetch('',[
            'goods_data'=>$goods_data,
            'familyData'=>$familyData,
            'singleData' =>$singleData,
            'onlyData'=>$onlyData,
            'historyData'=>$historyData
        ]);
    }

    public function addGoodsToCart(){
        if(request()->isAjax()){
            if(!session('uid')){
                return json(['code'=>-1,'message'=>'您还没有登录']);
            }
            $cart=new \cart\Cart();
            $cart->addCart(input('goods_id'),input('goods_attr_ids'),input('goods_number'));
            return json(['code'=>200,'message'=>'添加成功']);
        }
    }
    public function ajaxGetPrice(){
        $goods_id=input("goods_id");
        $goods_attr_ids=input("goods_attr_ids");
        $goods_price=Goods::field('goods_price')->find($goods_id);
        $condition=[
            'goods_id'=>$goods_id,
            'goods_attr_id'=>['in',$goods_attr_ids]
        ];
        $attr_price=Db::name('goods_attr')->field("sum(attr_price) as price")->where($condition)->find();
        return json(['code'=>200,'goods_price'=>$attr_price['price']+$goods_price['goods_price']]);

    }
}
