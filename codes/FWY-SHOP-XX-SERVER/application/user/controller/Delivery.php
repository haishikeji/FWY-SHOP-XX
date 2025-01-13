<?php


namespace app\user\controller;

use library\Controller;
use think\Db;

/**
 * 收货地址
 * Class Delivery
 * @package app\user\controller
 */
class Delivery extends Controller
{
    protected  $table ="DeliveryAddress";


    /**
     * 列表
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $this->title = '收货地址列表';
        $query = $this->_query($this->table);
        $where= [];
        $were[] = ['i.is_deleted','=',0];
        if($this->request->request('user_name'))    $where[]= ['m.name','like','%'.$this->request->request('user_name').'%'];
        if($this->request->request('name'))         $where[]= ['i.name','like','%'.$this->request->request('name').'%'];
        if($this->request->request('sel_pro'))      $where[]= ['i.pro_name','like','%'.$this->request->request('sel_pro').'%'];
        if($this->request->request('sel_city'))     $where[]= ['i.city_name','like','%'.$this->request->request('sel_city').'%'];
        if($this->request->request('name'))         $where[]= ['i.name','like','%'.$this->request->request('name').'%'];
        if($this->request->request('add_name'))     $where[]= ['i.pro_name|i.city_name|i.county_name|i.street_name|i.detail','like','%'.$this->request->request('add_name').'%'];
        $query->alias('i')->field('i.* ,m.headimg,m.name as user_name,m.phone')
            ->join('store_member m',' m.id = i.user_id ','LEFT');
        if(!empty($where)) $query->where($where);
        $query ->order('i.id desc')->page();
    }

    /**
     * 删除
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $this->_save($this->table, ['is_deleted' => 1]);
    }



}