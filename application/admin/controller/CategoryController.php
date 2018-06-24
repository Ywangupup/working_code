<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14 0014
 * Time: 下午 8:51
 */
namespace app\admin\controller;
use app\admin\model\Category;

class CategoryController extends CommonController{

    public function add(){
        $catModel=new Category();
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Category.add',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($catModel->allowField(true)->save($postData)){
                $this->redirect('admin/category/index');
            }else{
                $this->error("添加失败");
            }
        }
        $cats=$catModel->getCatsSon();
        return $this->fetch('',['cats'=>$cats]);
    }

    public function index(){
        $catModel=new Category();
        $lists=$catModel->getCatsSon();
        return $this->fetch('',['lists'=>$lists]);
    }

    public function upd(){
        $catModel=new Category();
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Category.upd',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            if($postData['cat_id']==$postData['pid']){
                $this->error("不能选择该分类及该分类的子类作为父分类");
            }
            if($catModel->allowField(true)->isUpdate(true)->save($postData)){
                $this->redirect('admin/category/index');
            }else{
                $this->error("编辑失败");
            }
        }
        $cat_id=input('cat_id');
        $data=$catModel->find($cat_id);
        $cats=$catModel->getCatsSon();
        return $this->fetch('',['data'=>$data,'cats'=>$cats]);
    }
    public function ajaxDel(){
        if(request()->isAjax()){
            $cat_id=input('cat_id');
            if(Category::destroy($cat_id)){
                return json(['status'=>200,'message'=>'删除成功']);
            }else{
                return json(['status'=>-1,'message'=>'删除失败']);
            }
        }
    }
}