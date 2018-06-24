<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14 0014
 * Time: 上午 8:48
 */
namespace app\admin\controller;
use app\admin\model\Attribute;
use app\admin\model\Type;

class AttributeController extends CommonController{
    public function add(){
        if(request()->isPost()){
            $postData=input('post.');
            if($postData['attr_input_type']==1){
                $result=$this->validate($postData,'Attribute.liebiaoselect',[],true);
            }else{
                $result=$this->validate($postData,'Attribute.add',[],true);
            }
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $attrModel=new Attribute();
            if($attrModel->allowField(true)->save($postData)){
                $this->success("添加成功",url("admin/attribute/index"));
            }else{
                $this->error("添加失败");
            }
        }
        $types=Type::select();
        return $this->fetch('',['types'=>$types]);
    }

    public function index(){
        $attrModel=new Attribute();
        $lists=$attrModel->alias('t1')
            ->field('t1.*,t2.type_name')
            ->join('type t2','t1.type_id=t2.type_id','left')
            ->select();
        return $this->fetch('',['lists'=>$lists]);
    }

    public function upd(){
        if(request()->isPost()){
            $postData=input('post.');
            if($postData['attr_input_type']==1){
                $result=$this->validate($postData,'Attribute.liebiaoselect',[],true);
            }else{
                $result=$this->validate($postData,'Attribute.upd',[],true);
            }
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $attrModel=new Attribute();
            if($attrModel->allowField(true)->isUpdate(true)->save($postData)){
                $this->success("添加成功",url("admin/attribute/index"));
            }else{
                $this->error("添加失败");
            }
        }
        $attr_id=input('attr_id');
        $attrModel=new Attribute();
        $data=$attrModel->find($attr_id);
        $types=Type::select();
        return $this->fetch('',['types'=>$types,'data'=>$data]);
    }
    public function ajaxDel(){
        if (request()->isAjax()){
            $attr_id=input('attr_id');
            if(Attribute::destroy($attr_id)){
                return json(['status'=>200,'message'=>'删除成功']);
            }else{
                return json(['status'=>-1,'message'=>'删除失败']);
            }
        }
    }
}