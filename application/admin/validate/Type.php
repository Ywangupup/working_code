<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14 0014
 * Time: 上午 1:24
 */
namespace app\admin\validate;
use think\Validate;

class Type extends Validate{
    protected $rule=[
        'type_name' =>'require|unique:Type',
    ];
    protected $message=[
        'type_name.require'=>'类型名称必填',
        'type_name.unique'  =>'类型名称已存在'
    ];
    protected $scene=[
        'add' =>['type_name'],
        'upd' =>['type_name']
    ];
}