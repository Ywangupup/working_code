<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/11 0011
 * Time: 上午 8:58
 */
namespace app\admin\controller;
use app\admin\model\User;
use app\admin\model\Role;

class UserController extends  CommonController {
    public function add(){
        if (request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'User.add',[],true);
            if ($result!==true){
                $this->error(implode(',',$result));
            }
            $userModel=new User();
            if($userModel->allowField(true)->save($postData)){
                $this->success("添加成功",url("admin/user/index"));
            }else{
                $this->error("添加失败");
            }
        }
        $roles=Role::where('auth_id_list','neq','*')->select();
        return $this->fetch('',['roles'=>$roles]);
    }

    public function index(){
        $userModel=new User();
        $lists=$userModel->alias('t1')
                         ->field('t1.*,t2.role_name')
                         ->join('sh_role t2','t1.role_id=t2.role_id','left')
                         ->where('t2.auth_id_list','neq','*')
                         ->paginate(2);
        return $this->fetch('',['lists'=>$lists]);
    }

    public function upd(){
        $userModel=new User();
        if(request()->isPost()){
            $postData=input('post.');

            $result=$this->validate($postData,'User.upd',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($userModel->allowField(true)->isUpdate(true)->save($postData)!==false){
                $this->success('修改成功',url('admin/user/index'));
            }else{
                $this->error('修改失败');
            }
        }
        $user_id=input('user_id');
        $data=$userModel->find($user_id);
        $roles=Role::select();
        return $this->fetch('',['data'=>$data,'roles'=>$roles]);
    }

    public function ajaxChangeActive(){
        if(request()->isAjax()){
            $user_id=input('user_id');
            $is_active=input('is_active');
            $updateActive=$is_active?0:1;
            $data=[
                'user_id'=>$user_id,
                'is_active'=>$updateActive
            ];
            $userModel=new User();
            if($userModel->update($data)!==false){
                return json(['status'=>200,'is_active'=>$updateActive]);
            }else{
                return json(['status'=>-1]);
            }
        }
    }

}