<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 2:45
 */
namespace app\admin\controller;
use app\admin\model\Auth;

class AuthController extends CommonController{
    public function add(){
        $authModel=new Auth();
        if(request()->isPost()){
            $postData=input('post.');
            if($postData['pid']==0){
                $result=$this->validate($postData,'Auth.ding',[],true);
            }else{
                $result=$this->validate($postData,'Auth.add',[],true);
            }
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($authModel->save($postData)){
                $this->success("添加成功",url('admin/auth/index'));
            }else{
                $this->error("添加失败");
            }
        }
        $auths=$authModel->getAuthsSon();
        return $this->fetch('',['auths'=>$auths]);
    }

    public function index(){
        $authModel=new Auth();
        $lists=$authModel->getAuthsSon();
        return $this->fetch('',['lists'=>$lists]);
    }

    public function upd(){
        $authModel=new Auth();
        if(request()->isPost()){
            $postData=input('post.');
            if($postData['pid']==0){
                $result=$this->validate($postData,'Auth.ding',[],true);
            }else{
                $result=$this->validate($postData,'Auth.upd',[],true);
            }
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($authModel->isUpdate(true)->save($postData)){
                $this->success("修改成功",url('admin/auth/index'));
            }else{
                $this->error("修改失败");
            }
        }
        $auth_id=input('auth_id');
        $auths=$authModel->getAuthsSon();
        $data=$authModel->find($auth_id);
        return $this->fetch('',['data'=>$data,'auths'=>$auths]);
    }

    public function ajaxDel(){
        if(request()->isAjax()){
            $auth_id=input('auth_id');
            if (Auth::destroy($auth_id)){
                return json(['status'=>200,'message'=>'删除成功']);
            }else{
                return json(['status'=>-1,'message'=>'删除失败']);
            }
        }
    }
}