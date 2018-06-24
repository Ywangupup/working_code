<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/23 0023
 * Time: 下午 8:25
 */
namespace app\admin\controller;
use app\home\model\Order;

class OrderController extends CommonController{
    //展示订单列表;
    public function index(){
        //获取搜索内容
        $keywrod=trim(input('keyword'));
        $where='';
        //有搜索,拼接Sql where条件
        if($keywrod){
            $where="order_id like '%$keywrod%' or receiver like '%$keywrod%' or address like '%$keywrod%' or tel like '%$keywrod%'";
        }
        $lists=Order::alias('t1')
            ->field("t1.*,t2.username")
            ->where($where)
            ->join("sh_member t2","t1.uid=t2.uid",'left')
            ->paginate(2);
        //判断是否是ajax获取分页
        if(request()->isAjax()){
            //分配模版内容
            $data=[
              'page'=>$lists->render(),
              'tbody'=>$this->fetch('order/tbody',['lists'=>$lists])
            ];
            //返回json数据,
            return json($data);
        }
        return $this->fetch('',['lists'=>$lists]);
    }
    //分配物流信息更新发货状态
    public function updLogistics(){
        if(request()->isPost()){
            $postData=input('post.');
            $result=$this->validate($postData,'Order.upd',[],true);
            if($result!==true){
                $this->error(implode(',',$result));
            }
            $postData['send_status']=1;
            $orderModel=new Order();
            if($orderModel->isUpdate(true)->save($postData)){
                $this->success("分配物流信息成功","admin/order/index");
            }else{
                $this->error("分配信息失败.请检查物流信息和订单号");
            }
        }
        $data=Order::find(input('id'));
        return $this->fetch('',['data'=>$data]);
    }
    //获取物流信息
    public function getLogistics(){
        if(request()->isAjax()){
            //获取物流接口的key
            $key=config('key');
            //获取物流公司
            $company=input('company');
            //获取运单号
            $number=input('number');
            //发送跨域请求,请求快递接口
            echo file_get_contents("http://www.kuaidi100.com/applyurl?key={$key}&com={$company}&nu={$number}&show=0");
        }
    }
}