<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 3:34
 */
namespace app\admin\validate;
use think\Validate;

class Attribute extends Validate{
    protected $rule=[
        'attr_name' =>'require|unique:Attribute',
        'type_id'    =>'require',
        'attr_values'=>'require'
    ];
    protected $message=[
        'attr_name.require'=>'属性名称必填',
        'type_id.require'  =>'商品类型必填',
        'attr_name.unique'=>'属性名称已存在',
        'attr_values.unique'=>'属性值必填'
    ];
    protected $scene=[
        'add' =>['attr_name','type_id'],
        'ding'=>['attr_name'],
        'upd' =>['attr_name','type_id'],
        'liebiaoselect' =>['attr_name','type_id','attr_values']
    ];
}