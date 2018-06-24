<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 10:13
 */
namespace app\admin\validate;
use think\Validate;

class Role extends Validate{
    protected $rule=[
        'role_name' =>'require|unique:Role'
    ];
    protected $message=[
        'role_name.require'=>'角色名称必填',
        'role_name.unique' =>'角色名称已存在'
    ];
    protected $scene=[
        'add'=>['role_name'],
        'add'=>['role_name'],

    ];
}