<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14 0014
 * Time: 上午 1:13
 */
namespace app\admin\controller;
use app\admin\model\Attribute;
use app\admin\model\Type;

class TypeController extends CommonController{
    public function add(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Type.add',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $typeModel=new Type();
            if($typeModel->allowField(true)->save($postData)){
                $this->success("添加成功",url('admin/type/index'));
            }else{
                $this->error("添加失败");
            }
        }
        return $this->fetch();
    }

    public function index(){
        $lists=Type::select();
        return $this->fetch('',['lists'=>$lists]);
    }

    public function upd(){
        $typeModel=new Type();
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Type.upd',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($typeModel->isUpdate(true)->allowField(true)->save($postData)){
                $this->success("编辑成功",url('admin/type/index'));
            }else{
                $this->error("编辑失败");
            }
        }
        $type_id=input('type_id');
        $data=$typeModel->find($type_id);
        return $this->fetch('',['data'=>$data]);
    }

    public function ajaxDel(){
        if(request()->isAjax()){
            $type_id=input('type_id');
            if(Type::destroy($type_id)){
                return json(['status' =>200,'message'=>"删除成功"]);
            }else{
                return json(['status' =>-1,'message'=>"删除失败"]);
            }
        }
    }
    public function getAttr(){
        $type_id=input('type_id');
        $lists=Attribute::alias('t1')
                ->field('t1.*,t2.type_name')
                ->join('sh_type t2','t1.type_id=t2.type_id','left')
                ->where('t1.type_id',$type_id)
                ->select();
        return $this->fetch('',['lists'=>$lists]);
    }

}