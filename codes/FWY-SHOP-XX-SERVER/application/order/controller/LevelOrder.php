<?php
namespace app\order\controller;
use library\Controller;
use think\Db;
/**
 * 会员订单管理
 * Class LevelOrder
 * @package app\order\controller
 */
class LevelOrder extends Controller
{
    protected $table = 'LevelOrder';
    /**
     * 订单列表
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOExceptio
     */
    public function index()
    {
        $this->title = '订单管理';
        $query = $this->_query($this->table);
        $where = [];
        $where[] = ['o.is_deleted','=',0];
        $where[] = ['o.cancel_state','=',0];
        if($this->request->request('tel'))$where[]= ['u.phone','like','%'.$this->request->request('tel').'%'];
        if($this->request->request('user_name'))$where[]= ['u.name','like','%'.$this->request->request('user_name').'%'];
        if($this->request->request('order_no')) $where[]= ['o.order_no','like','%'.$this->request->request('order_no').'%'];
        $query->alias('o')
            ->field('o.* , u.name u_name ,u.phone u_phone,l.name level_name')
            ->join('store_member u',' o.user_id = u.id ','LEFT')
            ->leftJoin('user_level l',' o.level_id = l.id ');
        if(!empty($where)) $query->where($where);
        $query ->order('o.id desc')->page();
    }

    /**
     * 表单数据处理
     * @auth true
     * @menu true
     * @param array $data
     */
    protected function _form_filter(&$data)
    {

    }


}