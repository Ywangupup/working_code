<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +---------------------------------------------------------------------
// 应用公共文件
error_reporting(E_ERROR|E_WARNING|E_PARSE);
//短信验证接口
function sendSms($to,$datas,$tempId){
    include_once("../extend/sendSms/CCPRestSmsSDK.php");
    //引入接口文件
    $accountSid= '8a216da863f8e6c20163fa598ce6009b';
    //主账号，登陆云通讯网站后，可在控制台首页看到开发者主账号ACCOUNT SID
    $accountToken= '063419c66c3b4cbdbd929f37a9ed6b86';
    //主账号Token，登陆云通讯网站后，可在控制台首页看到开发者主账号AUTH TOKEN
    $appId='8a216da863f8e6c20163fa598d4d00a2';
    //登陆云通讯网站后,使用管理控制台中已创建应用的APPID
    $serverIP='app.cloopen.com';
    //生产环境请求地址:app.cloopen.com   生产环境和沙盒环境一致
    $serverPort='8883';
    //请求端口 ，无论生产环境还是沙盒环境都为8883
    $softVersion='2013-12-26';
    //REST API版本号保持不变  参考官网

    // 初始化REST SDK
//    global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
    $rest = new REST($serverIP,$serverPort,$softVersion);
    $rest->setAccount($accountSid,$accountToken);
    $rest->setAppId($appId);
    //发送模版短信
    //$to  短信接收手机号
    //$datas 是一个数组,[发送的验证吗,几分钟有效]
    //$tempId  是模版  固定是1
    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
    //返回结果json数据,是接口返回的json
    return json($result);
}

function sendEmail($to,$title,$content){
    // 实例化
    include "../extend/sendEmail/class.phpmailer.php";
    $pm = new PHPMailer();
    // 服务器相关信息
    $pm->Host = 'smtp.163.com'; // SMTP服务器
    $pm->IsSMTP(); // 设置使用SMTP服务器发送邮件
    $pm->SMTPAuth = true; // 需要SMTP身份认证
    $pm->Username = 'Ywangupup'; // 登录SMTP服务器的用户名
    $pm->Password = 'ywang2233'; //授权码 登录SMTP服务器的密码
    // 发件人信息
    $pm->From = 'Ywangupup@163.com';
    $pm->FromName = '测试使用';
    // 收件人信息
    $pm->AddAddress($to); // 添加一个收件人
    //$pm->AddAddress('wangwei2@itcast.cn', 'wangwei2'); // 添加另一个收件人
    // 邮件内容
    $pm->CharSet = 'utf-8'; // 内容编码
    $pm->Subject = $title; // 邮件标题
    $pm->MsgHTML($content);
    // 发送邮件
    if($pm->Send()){
        return true;
    }else {
        return false;
    }
}