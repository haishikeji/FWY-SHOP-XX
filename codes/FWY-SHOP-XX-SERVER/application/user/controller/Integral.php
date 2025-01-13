<?php


namespace app\user\controller;

use library\Controller;
use think\Db;

/**
 * 积分记录
 * Class Integral
 * @package app\user\controller
 */
class Integral extends Controller
{
    protected  $table ="UserIntegralLog";


    /**
     * 变更列表
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
        $this->title = '积分变更日志';
        $query = $this->_query($this->table);
        $where= [];
        if($this->request->request('user_name'))      $where[]= ['m.name','like','%'.$this->request->request('user_name').'%'];
        if($this->request->request('sign_time'))      $where[] = ['i.create_at','between time',[$this->request->request('sign_time').' 00:00:00',$this->request->request('sign_time').' 23:59:59']];
        $query->alias('i')
            ->field('i.* ,m.headimg,m.name,m.phone')
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