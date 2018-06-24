<?php
namespace app\home\controller;
use app\home\model\Member;
use think\Controller;

class PublicController extends  Controller{

    public function register(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Member.register',[],true);
            if($result!==true){
                $this->error(implode('|',$result));
            }
            if(md5($postData['phonecaptcha'].config('sms_salt'))!==cookie('sms')){
                $this->error("短信验证码输入错误");
            }
            $memberModel=new Member();
            $info=$memberModel->allowField(true)->save($postData);
            if($info){
                $this->success("注册成功可以登录了",url('home/public/login'));
            }else{
                $this->error('注册失败');
            }
        }
        return $this->fetch();
    }

    public function sendSms(){
        if(request()->isAjax()){
            $phone=input('phone');
            $result=$this->validate(['phone'=>$phone],'Member.sendSms',[],true);
            if($result!==true){
                return json(['code'=>-1,'message'=>$result]);
            }
            $rand=mt_rand(1000,9999);
            $sms=md5($rand.config('sms_salt'));
            cookie('sms',$sms);
            return sendSms($phone,array($rand,5),'1');
        }
    }

    public function login(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Member.login',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $postData['password']=md5($postData['password'].config("password_salt"));
            $memberModel=new Member();
            if($memberModel->checkUser($postData['username'],$postData['password'])){
                if(input('return_url')){
                    $this->redirect('home/goods/detail',['goods_id'=>input('return_url')]);
                }
                $this->redirect('home/index/index');
            }else{
                $this->error("用户名或密码错误");
            }
        }
        return $this->fetch();
    }

    public function logout(){
        session('home_username',null);
        session('uid',null);
        $this->redirect('home/index/index');
    }
    //更改密码验证信息逻辑
    public function findPassword(){
        if (request()->isPost()){
            $emailCaptcha=input('emailCaptcha');
            $emailCaptcha=md5($emailCaptcha.config('email_salt'));
            if($emailCaptcha==cookie('emailCaptcha')){
                $condition=['email'=>input('email'),'username'=>input('username')];
                $userInfo=Member::where($condition)->find();
                if($userInfo){
                    $this->redirect("home/public/changePass",
                        [
                            'uid'=>$userInfo['uid'],
                        'username'=>$userInfo['username']
                    ]);
                }else{
                    $this->error("您没有绑定过该邮箱");
                }
            }else{
                $this->error("验证码错误");
            }
        }
        return $this->fetch();
    }
    //邮箱发送验证码,进行更改密码程序
    public function sendEmail(){
        if(request()->isAjax()){
            $username=input('username');
            $email=input('email');
            $result=$this->validate(['email'=>$email,'username'=>$username],'Member.email',[],true);
            if($result!==true){
                return json(['status'=>-1,'message'=>$result]);
            }
            $condition=['email'=>$email,'username'=>$username];
            if($userInfo=Member::where($condition)->find()){
                //有用户使用该邮箱
                $rand=mt_rand(100000,999999);
                $emailCaptcha=md5($rand.config('email_salt'));
                cookie('emailCaptcha',$emailCaptcha,3000);
                $title="shop商城密码找回";
                $content="请在密码找回页面输入验证码:<h3 color='blue'>".$rand.",进行密码找回</h3>";
                if(sendEmail($userInfo['email'],$title,$content)){
                    return json(['status'=>200,'message'=>'发送成功,请前往邮箱查看验证码']);
                }else{
                    return json(['status'=>-3,'message'=>'发送失败,请检查邮箱是否正确']);
                }
            }else{
                //没有用户使用该邮箱,
                return json(['status'=>-2,'message'=>"您的邮箱还没有绑定过账号!请前往注册页面"]);
            }
        }
    }
    //更改密码
    public function changePass(){
        if(request()->isAjax()){
            $uid=input('uid');
            $password=input('password');
            $repassword=input('repassword');
            $conditon=[
                'password'=>$password,
                'repassword'=>$repassword
            ];
            $result=$this->validate($conditon,'Member.upd',[],true);
            if($result!==true){
                return json(["statusCode"=>-1,'message'=>$result]);
            }
            $password=md5($password.config('password_salt'));
            $data=['uid'=>$uid,'password'=>$password];
            $memberModel=new Member();
            if($memberModel->isUpdate(true)->save($data)){
                return json(['statusCode'=>200,'message'=>'修改成功,请前往登录']);
            }else{
                return json(['statusCode'=>-2,'message'=>'修改失败,请检查填写信息是否正确']);
            }
        }
        $data=['uid'=>input('uid'),'username'=>input('username')];
        return $this->fetch('',['data'=>$data]);
    }

}