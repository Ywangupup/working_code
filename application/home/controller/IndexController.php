<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/16 0016
 * Time: 下午 7:07
 */
namespace app\home\controller;
use app\home\model\Category;
use app\home\model\Goods;
use think\Controller;

class IndexController extends Controller{
    public function index(){
        $catModel=new Category();
        //查询所有分类,并拼接数组方便取值
        $cats=$catModel->select();
        $catData=[];
        foreach ($cats as $cat){
            $catData[$cat['cat_id']]=$cat;
        }
        $children=[];
        foreach ($cats as $cat){
            $children[$cat['pid']][]=$cat['cat_id'];
        }
        $goodsModel=new Goods();
        $crazyData=$goodsModel->getTypeGoods('is_crazy');
        $guessData=$goodsModel->getTypeGoods('is_guess');
        $hotData=$goodsModel->getTypeGoods('is_hot');
        $newData=$goodsModel->getTypeGoods('is_new');
        $bestData=$goodsModel->getTypeGoods('is_best');
        $navCats=$catModel->where(['is_show'=>1,'pid'=>0])->select();
        return $this->fetch('', [
            'navCats'=>$navCats,
            'catData' =>$catData,
            'children' => $children,
            'crazyData' => $crazyData,
            'guessData' => $guessData,
            'hotData' => $hotData,
            'newData' => $newData,
            'bestData' => $bestData,
        ]);
    }
}