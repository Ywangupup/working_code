<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/11 0011
 * Time: 下午 11:36
 */
namespace app\admin\validate;
use think\Validate;

class User extends  Validate{
    protected $rule=[
        'username'  =>'require|unique:user',
        'password'  =>'require|confirm:repassword',
        'captcha'  =>'require|captcha',
        'role_id'  =>'require'
    ];
    protected $message=[
        'username.require'=>'用户名必填',
        'username.unique'=>'用户名已存在',
        'password.require' =>'密码必填',
        'password.confirm' =>'两次输入的密码不一致',
        'captcha.require' =>'验证码必填',
        'captcha.captcha' =>'验证码错误',
        'role.require'  =>'必须分配角色'

    ];
    protected $scene=[
        'add' =>['username','password','repassword','role_id'],
        'upd' =>['username','role_id ','password'],
        'login'=>['username'=>'require' , 'password'=>'require','captcha']
    ];
}