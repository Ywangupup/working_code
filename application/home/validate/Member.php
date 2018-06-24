<?php
namespace app\home\validate;
use think\Validate;

class Member extends Validate{
    protected $rule=[
        'username' =>'require|unique:Member',
        'password'    =>'require|regex:\w{6,}',
        'repassword'=>'require|confirm:password',
        'email'   =>'require|regex:\w+@([a-zA-Z0-9]+\.){1,3}[a-zA-Z]{2,6}',
        'phone'  =>'require|unique:Member',
        'captcha' =>'require|captcha:2'

    ];
    protected $message=[
        'username.require'=>'用户名必填',
        'password.require'  =>'密码必填',
        'password.regex'  =>'密码最少6位',
        'username.unique'=>'用户名已存在',
        'repassword.require'=>'请输入确认密码',
        'repassword.confirm'=>'两次输入的密码不一致',
        'email.require' =>'邮箱必填',
        'email.regex' =>'邮箱不合法',
        'phone.require' =>'填写手机号',
        'phone.unique' =>'手机号已被使用请更换其他',
        'captcha.require'   =>'请填写验证码',
        'captcha.captcha'   =>'验证码错误',

    ];
    protected $scene=[
        'register' =>['username','password','repassword','email'],
        'sendSms'  =>['phone'],
        'login'   =>['username'=>'require','password','captcha'],
        'email'  =>['email','username'=>'require'],
        'upd'  =>['password','repassword']
    ];
}