<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/15 0015
 * Time: 上午 9:58
 */
namespace app\admin\controller;
use app\admin\model\Attribute;
use app\admin\model\Category;
use app\admin\model\Goods;
use app\admin\model\Type;

class GoodsController extends CommonController{
    public function add(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Goods.add',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $goods_img=$this->uploadImg();  //调用方法,获取上传图片的保存地址
            if($goods_img){
                $postData['goods_img']=json_encode($goods_img);
                $thumb_img=$this->thumbImg($goods_img);  //获取上传图片缩略图的保存地址
                $postData['goods_middle']=json_encode($thumb_img['middle']);
                $postData['goods_thumb']=json_encode($thumb_img['min']);
            }
            $goodsModel=new Goods();
            if($goodsModel->allowField(true)->save($postData)){
                $this->redirect("admin/goods/index");
            }else{
                $this->error("添加失败");
            }
        }
        $types=Type::select();
        $catModel=new Category();
        $cats=$catModel->getCatsSon();
        return $this->fetch('',['cats'=>$cats,'types'=>$types]);
    }
    //获取用户上传的图片组,保存图片,并返回其保存路径
    public function uploadImg(){
        $goods_img=[];
        $files=request()->file('img');
        if($files){
            $uploadpath='./upload/';
            $condition=['size'=>1024*1024*3,'ext'=>'jpeg,jpg,gif,png'];
            foreach ($files as $file){
                $info=$file->validate($condition)->move($uploadpath);
                if($info){
                    $goods_img[]=str_replace('\\','/',$info->getSaveName());
                }
            }
        }
        return $goods_img;
    }
    //传入参数获取到图片组保存的路径,并生成两个不同大小缩略图保存,返回保存路径,
    public function thumbImg($goods_img){
        $upload='./upload/';
        $middle=[];
        $min=[];
        foreach ($goods_img as $path){
            $path_arr=explode('/',$path);
            $middle_img_path=$path_arr[0].'/middle_'.$path_arr[1];
            $raw_img=\think\Image::open($upload.$path);
            $raw_img->thumb(350,350,2)->save($upload.$middle_img_path);
            $middle[]=$middle_img_path;
        }
        foreach ($goods_img as $path){
            $path_arr=explode('/',$path);
            $min_img_path=$path_arr[0].'/min_'.$path_arr[1];
            $raw_img=\think\Image::open($upload.$path);
            $raw_img->thumb(50,50,2)->save($upload.$min_img_path);
            $min[]=$min_img_path;
        }
        return ['middle'=>$middle,'min'=>$min];
    }
    //获取ajax传递的参数,查询该商品类型的属性数据并返回json;
    public function ajaxSetIs(){
        if(request()->isAjax()){
            $type_id=input('type_id');
            $attrModel=new Attribute();
            $attrs=$attrModel->where('type_id',$type_id)->select();
            return json($attrs);
        }
    }

    public function index(){
        $lists=Goods::select();
        return $this->fetch('',['lists'=>$lists]);
    }

}