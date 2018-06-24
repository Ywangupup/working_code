<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/23 0023
 * Time: 下午 10:20
 */
namespace app\admin\validate;
use think\Validate;

class Order extends  Validate{
    protected $rule=[
        'company'    =>'require',
        'number'=>'require'
    ];
    protected $message=[
        'company.require'  =>'请选择物流公司',
        'number.require'=>'请填写运单号'
    ];
    protected $scene=[
        'upd'=>['company','number']
    ];
}