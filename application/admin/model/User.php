<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/11 0011
 * Time: 下午 11:47
 */
namespace app\admin\model;
use think\Model;

class User extends Model{
    protected $pk='user_id';
    protected $autoWriteTimestamp=true;
    protected static function init(){
        User::event('before_insert',function($user){
            $user['password']=md5( $user['password'].config('password_salt'));
        });
        User::event('before_update',function($user){
            if(isset($user['password'])){
                if($user['password']==''){
                    unset($user['password']);
                }else{
                    $user['password']=md5( $user['password'].config('password_salt'));
                }
            }
        });
    }
    public function checkUser($username,$password){
        $password=md5($password.config('password_salt'));
        $condition=['username'=>$username,'password'=>$password];
        $userinfo=$this->where($condition)->find();
        if($userinfo){
            session('username',$userinfo['username']);
            //调用方法取得将当前用户权限写入session中
            $this->writeAuthToSession($userinfo['role_id']);
            return true;
        }else{
            return false;
        }
    }
    /*把当前用户角色的权限写入到session中
     *@param role_id   当前用户的角色id
     */
    public function writeAuthToSession($role_id){
        $row=Role::find($role_id);
        $auth_id_list=$row['auth_id_list'];
        if($auth_id_list=='*'){
            $auths=Auth::where('pid',0)->select()->toArray();
            foreach ($auths as $k=>$one_auth){
                //通过模型查询出来的数据本身就是一个数组,所以不用加  []
                $auths[$k]['sonAuth']=Auth::where('pid',$one_auth['auth_id'])->select()->toArray();
            }
        session('visitorAuth','*');
        }else{
            $visitor=[];
            $all_auths=Auth::where('auth_id','in',$auth_id_list)->select()->toArray();
            $auths=[];
            foreach ($all_auths as $auth){
                if($auth['pid']==0){
                    $auths[]=$auth;
                }
                $visitor[]=strtolower($auth['auth_c'].'/'.$auth['auth_a']);
            }

            foreach ($auths as $onekey=>$oneauth){
                foreach ($all_auths as $twoauth){
                    if($twoauth['pid']==$oneauth['auth_id']){
                        //循环出来的数组元素是一个具体元素,必须要加[] ,不然会被下一个满足条件的元素覆盖
                        $auths[$onekey]['sonAuth'][]=$twoauth;
                    }
                }
            }
            session('visitorAuth',$visitor);
        }
        session('menuAuth',$auths);
    }
}