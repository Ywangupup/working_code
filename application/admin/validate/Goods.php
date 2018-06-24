<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 3:34
 */
namespace app\admin\validate;
use think\Validate;

class Goods extends Validate{
    protected $rule=[
        'goods_name' =>'require|unique:Goods',
        'goods_price'    =>'require|gt:0',
        'goods_number'=>'require|gt:0',
        'cat_id' => 'require'
    ];
    protected $message=[
        'goods_name.require'=>'商品名称必填',
        'goods_name.unique'=>'商品名称已存在',
        'goods_price.require'  =>'商品价格必填',
        'goods_price.gt'  =>'商品价格必须大于0',
        'cat_id.require'  =>'请选择商品分类',
        'goods_number.gt'  =>'商品库存必须大于0',
        'goods_number.require'=>'库存数量必填'
    ];
    protected $scene=[
        'add' =>['goods_name','cat_id','goods_price','goods_number']
    ];
}