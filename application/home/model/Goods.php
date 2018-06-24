<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/16 0016
 * Time: 下午 7:58
 */
namespace app\home\model;
use think\Db;
use think\Model;

class Goods extends Model{

    public function getTypeGoods($type,$limit=5){
        $condition=[
            'is_delete' =>0,
            'is_sale'   =>1
        ];
        switch ($type){
            case 'is_crazy':
                $goodsData=$this->where($condition)->limit($limit)->order('goods_price asc')->select();
                break;
            case 'is_guess':
                $goodsData=$this->where($condition)->order('rand()')->limit($limit)->select();
                break;
            default:
                $condition[$type]=1;
                $goodsData=$this->where($condition)->limit($limit)->select();
                break;
        }
        return $goodsData;
    }
  //****************取出商品的单选属性attr_type=1*******
    public function getSingleData($goods_id){
        $singleData=Db::name('goods_attr')
            ->alias('t1')
            ->field('t1.*,t2.attr_name')
            ->join('sh_attribute t2','t1.attr_id=t2.attr_id','left')
            ->where('t2.attr_type=1 and t1.goods_id='.$goods_id)
            ->select();
        $single_data=[];
        foreach ($singleData as $data){
            $single_data[$data['attr_id']][]=$data;
        }
        return $single_data;
    }
    //取出商品的唯一属性attr_type
    public function getonlyData($goods_id){
        $onlyData=Db::name('goods_attr')
            ->alias('t1')
            ->field('t1.*,t2.attr_name')
            ->join('sh_attribute t2','t1.attr_id=t2.attr_id','left')
            ->where('t2.attr_type=0 and t1.goods_id='.$goods_id)
            ->select();
        return $onlyData;
    }
   //将当前浏览的商品写入cookie中
    public function addGoodsToHistory($goods_id){
        //获取cookie中的数据
        $history=cookie('history')?:[];
        //判断是否有数据
        if($history){
            //有数据(有历史记录)将goods_id存入数组最前面
            array_unshift($history,$goods_id);
            //将数组元素去重
            $history=array_unique($history);
            //判断数组长度有没有超出5
            if(count($history)>5){
                array_pop($history);
            }
        }else{
            //没有数据直接将good_id写入到cookie中
            $history[] = $goods_id;
        }
        cookie('history',$history,3600*24*7);
        return $history;
    }
    //获取当前用户购物车数据
    public function getCartGoodsData(){
        $cart=new \cart\Cart();
        $carts=$cart->getCart();
        $cartData=[];
        foreach ($carts as $k=>$data){
            $arr=explode('-',$k);
            $goodsInfo=$this->find($arr[0])->toArray();
            $attrInfo=Db::name('goods_attr')
                ->alias('t1')
                ->field("sum(t1.attr_price) as  attrs_price, group_concat(t2.attr_name,':',t1.attr_value separator'<br/>') as singleAttr")
                ->join("sh_attribute t2","t1.attr_id=t2.attr_id",'left')
                ->where('goods_attr_id','in' ,($arr[1]))
                ->find();
            $cartData[]=[
                'goods_id'=>$arr[0],
                'goods_attr_ids'=>$arr[1],
                'goods_number'=>$data,
                'goodsInfo'=>$goodsInfo,
                //select sum(attr_price) attrs_price,
                //GROUP_CONCAT(attr_name ,':', attr_value  separator '<br/>')
                // from sh_goods_attr t1 LEFT JOIN sh_attribute t2
                // ON t1.attr_id=t2.attr_id where goods_attr_id in (59,64)
                'attrInfo'=>$attrInfo
            ];
        }
        return $cartData;
    }
}