<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22 0022
 * Time: 下午 8:21
 */
namespace app\home\model;
use think\Model;

class Order extends  Model{
    protected $pk="id";
    protected $autoWriteTimestamp=true;
}