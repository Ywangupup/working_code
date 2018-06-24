<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14 0014
 * Time: 下午 9:25
 */
namespace app\admin\model;
use think\Model;

class Category extends Model{
    protected $pk='cat_id';
    protected $autoWriteTimestamp=true;
    public function getCatsSon(){
        $data=$this->select();
        return $this->_getCatsSon($data);
    }
    private function _getCatsSon($data,$pid=0,$deep=0){
        static $cats=[];
        foreach ($data as $cat){
            if($cat['pid']==$pid){
                $cat['deep']=$deep;
                $cats[$cat['cat_id']]=$cat;
                $this->_getCatsSon($data,$cat['cat_id'],$deep+1);
            }
        }
        return $cats;
    }
}