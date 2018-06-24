<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 3:34
 */
namespace app\admin\validate;
use think\Validate;

class Auth extends Validate{
    protected $rule=[
        'auth_name' =>'require|unique:Auth',
        'auth_c'    =>'require',
        'auth_a'    =>'require'
    ];
    protected $message=[
        'auth_name.require'=>'权限名称必填',
        'auth_c.require'  =>'控制器名称必填',
        'auth_a.require'  =>'控制器方法名称必填',
        'auth_name.unique'=>'权限名称已存在'
    ];
    protected $scene=[
        'add' =>['auth_name','auth_c','auth_a'],
        'ding'=>['auth_name'],
        'upd' =>['auth_name','auth_c','auth_a']
    ];
}