<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 6:47
 */
namespace app\admin\controller;
use app\admin\model\Auth;
use app\admin\model\Role;
use think\Db;

class RoleController extends CommonController{
    public function add(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Role.add',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $roleModel=new Role();
            if($roleModel->allowField(true)->save($postData)){
                $this->success("添加成功",url("admin/role/index"));
            }else{
                $this->error("添加失败");
            }
        }
        $authModel=new Auth();
        //取出所有权限,且以auth_id为下标
        $auths=$authModel->getAuthsSon();
        $children=[];
        foreach ($auths as $auth){
            $children[$auth['pid']][]=$auth['auth_id'];
        }
        return $this->fetch('',['auths'=>$auths,'children'=>$children]);
    }

    public function index(){
        $lists=Db::query("SELECT 
                          t1.*,GROUP_CONCAT(t2.auth_name SEPARATOR '|') all_auth 
                          FROM sh_role t1 
                          LEFT JOIN sh_auth t2 
                          on FIND_IN_SET(t2.auth_id,t1.auth_id_list) 
                          WHERE auth_id_list != '*' GROUP BY t1.role_id ");
        return $this->fetch('',['lists'=>$lists]);
    }
    public function upd(){
        $roleModel=new Role();
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Role.upd',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($roleModel->allowField(true)->isUpdate(true)->save($postData)){
                $this->success("分配成功",url("admin/role/index"));
            }else{
                $this->error("分配失败");
            }
        }
        $role_id=input('role_id');
        $data=$roleModel->find($role_id);
        $authModel=new Auth();
        //取出所有权限,且以auth_id为下标
        $auths=$authModel->getAuthsSon();
        $children=[];
        foreach ($auths as $auth){
            $children[$auth['pid']][]=$auth['auth_id'];
        }
        return $this->fetch('',['auths'=>$auths,'children'=>$children,'data'=>$data]);
    }

    public function ajaxDel(){
        if(request()->isAjax()){
            $role_id=input('role_id');
            if (Role::destroy($role_id)){
                return json(['status'=>200,'message'=>'删除成功']);
            }else{
                return json(['status'=>-1,'message'=>'删除失败']);
            }
        }
    }
}