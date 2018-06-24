<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14 0014
 * Time: 下午 7:16
 */
namespace app\admin\model;
use think\Model;

class Attribute extends Model{
    protected $pk="attr_id";
    protected $autoWriteTimestamp=true;
    protected static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        Attribute::event('before_insert',function ($attribute){
            if(isset($attribute['attr_values'])){
                $attribute['attr_values']=trim($attribute['attr_values']);
            }
        });
        Attribute::event('before_update',function ($attribute){
            if (isset($attribute['attr_values'])){
                $attribute['attr_values']=trim($attribute['attr_values']);
            }
        });
    }
}