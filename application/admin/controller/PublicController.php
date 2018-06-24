<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 1:54
 */
namespace app\admin\controller;
use app\admin\model\User;
use think\Controller;

class PublicController extends Controller{
    public function login(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'User.login',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $userModel=new User();
            if($userModel->checkUser($postData['username'],$postData['password'])){
                $this->redirect('admin/index/index');
            }else{
                $this->error("用户名或密码错误");
            }
        }
        return $this->fetch();
    }

    public function logout(){
        session(null);
        $this->redirect("admin/public/login");
    }
}