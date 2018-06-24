<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 3:34
 */
namespace app\admin\validate;
use think\Validate;

class Category extends Validate{
    protected $rule=[
        'cat_name' =>'require|unique:Category',
        'pid'    =>'require'
    ];
    protected $message=[
        'cat_name.require'=>'商品分类名称必填',
        'pid.require'  =>'商品父级名称必填',
        'cat_name.unique'=>'商品分类名称已存在'
    ];
    protected $scene=[
        'add' =>['cat_name','pid'],
        'upd' =>['cat_name','pid']
    ];
}