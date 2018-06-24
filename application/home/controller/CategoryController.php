<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/19 0019
 * Time: 下午 8:05
 */
namespace app\home\controller;
use app\home\model\Category;
use app\home\model\Goods;
use think\Controller;

class CategoryController extends Controller{
    public function index(){
        $cat_id=input('cat_id');
        $catModel=new Category();
        $familyCats=$catModel->getFamilyCats($cat_id);
        $cats=[];
        $catData=$catModel->select();
        foreach ($catData as $cat){
            $cats[$cat['cat_id']]=$cat;
        }
        $children=[];
        foreach($catData as $cat){
            $children[$cat['pid']][]=$cat['cat_id'];
        }
        $sonsCatIds=$catModel->getSonsCatId($cat_id);
        $sonsCatIds[]=$cat_id;
        $condition=[
            'is_delete'=>0,
            'is_sale' =>1,
            'cat_id' =>['in',$sonsCatIds]
        ];
        $catGoods=Goods::where($condition)->select()->toArray();
//        halt($catGoods);
        return $this->fetch('',[
            'familyCats'=> $familyCats,
            'cats' =>$cats,
            'children'=>$children,
            'catGoods'=>$catGoods
        ]);
    }
}