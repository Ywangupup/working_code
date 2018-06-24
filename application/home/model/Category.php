<?php
namespace app\home\model;
use think\Model;

class Category extends Model{
    protected $pk='cat_id';
    protected $autoWriteTimestamp=true;

    //递归查找当前分类cat_id的祖先（父）分类
    public function getFamilyCats($cat_id){
        $data=$this->select()->toArray();
        return array_reverse($this->_getFamilyCats($data,$cat_id));
    }

    private function _getFamilyCats($data,$cat_id){
        static $cats=[];
        foreach($data as $k=>$cat){
            if($cat_id==$cat['cat_id']){
                $cats[]=$cat;
                unset($data[$k]);
                $this->_getFamilyCats($data,$cat['pid']);
            }
        }
        return $cats;

    }

    public function getSonsCatId($cat_id){
        $data=$this->select();
        return $this->_getSonsCatId($data,$cat_id);
    }

    private function _getSonsCatId($data,$cat_id){
        static $catIdS=[];
        foreach ($data as $k=>$cat){
            if($cat['pid']==$cat_id){
                $catIdS[]=$cat['cat_id'];
                unset($data[$k]);
                $this->_getSonsCatId($data,$cat['cat_id']);
            }
        }
        return $catIdS;
    }

}