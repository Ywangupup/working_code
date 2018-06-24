<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22 0022
 * Time: 下午 11:20
 */
namespace app\home\validate;
use think\Validate;

class Order extends Validate{
    protected $rule=[
        'receiver' =>'require',
        'address' =>'require',
        'tel' =>'require',
        'zcode'=>'require'
    ];
    protected $message=[
        'receiver.require'=>'请填写收货人',
        'address.require'=>'请填写收货地址',
        'tel.require'=>'请填写收货人电话号码',
        'zcode.require'=>'请填写邮编',

    ];
}