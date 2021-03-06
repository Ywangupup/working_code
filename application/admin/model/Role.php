<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 下午 9:14
 */
namespace app\admin\model;
use think\Model;

class Role extends Model{
    protected $pk="role_id";
    protected $autoWriteTimestamp=true;
    protected static function init()
    {
        Role::event('before_insert',function ($role){
            $role['auth_id_list']=implode(',',$role['auth_id_list']);
        });
        Role::event('before_update',function ($role){
            $role['auth_id_list']=implode(',',$role['auth_id_list']);
        });
        parent::init(); // TODO: Change the autogenerated stub
    }
}