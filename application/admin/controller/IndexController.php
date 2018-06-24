<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/11 0011
 * Time: 上午 8:48
 */
namespace app\admin\controller;

class IndexController extends CommonController {
    public function index(){
        return $this->fetch();
    }
    public function top(){
        return $this->fetch();
    }
    public function left(){
        return $this->fetch();
    }
    public function main(){
        return $this->fetch();
    }
}