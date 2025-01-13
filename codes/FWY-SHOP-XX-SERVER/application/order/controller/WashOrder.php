<?php
namespace app\order\controller;
use app\common\model\GoodsOrder;
use app\common\model\UserMessage;
use app\common\model\WashOrderItem;
use library\Controller;
use library\tools\Data;
use think\Db;
use app\common\model\GoodsOrderItem;
/**
 * 洗鞋订单
 * Class WashOrder
 * @package app\order\controller
 */
class WashOrder extends Controller
{
    protected $table = 'WashOrder';
    /**
     * 订单列表
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
        $this->title = '订单管理';
        $this->order_status = [0=>'待支付',1=>'已支付',2=>'已完成',9=>'已取消'];
        $this->pay_state = ['待支付','已支付'];
        $this->all_pay_type = all_pay_type();
        $query = $this->_query($this->table);
        $where = [];
        if($this->request->request('phone'))$where[]= ['o.phone','like','%'.$this->request->request('tel').'%'];
        if($this->request->request('user_name'))$where[]= ['u.name','like','%'.$this->request->request('user_name').'%'];
        if($this->request->request('order_no')) $where[]= ['o.order_no','like','%'.$this->request->request('order_no').'%'];
        if($this->request->request('pay_state',-1) > -1) $where[]= ['o.pay_state','=',$this->request->request('pay_state')];
        if($this->request->request('order_status',-1) > -1) $where[]= ['o.status','=',$this->request->request('order_status')];
        $query->alias('o')
            ->field('o.* , u.name ,u.phone as user_phone,u.headimg')
            ->join('store_member u',' o.user_id = u.id ','LEFT');
        if(!empty($where)) $query->where($where);
        $query ->order('o.id desc')->page();
    }

    /**
     * 订单列表处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _index_page_filter(array &$data)
    {
        foreach ($data as &$vo) {
           $vo['item_list'] = WashOrderItem::field('i.*,g.ladder_set,g.title')
               ->alias('i')
               ->where(['i.order_id'=>$vo['id']])
               ->leftJoin('wash_cate g','g.id = i.cate_id')
               ->select()->toArray();
        }
    }

    /**
     * 修改快递
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function express()
    {
        if ($this->request->isGet()) {
            $where = ['is_deleted' => '0', 'status' => '1'];
            $this->expressList = Db::name('express_company')->where($where)->order('sort desc,id desc')->select();
        }
        $this->_form($this->table);
    }

    /**
     * 快递表单处理
     * @param array $vo
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _express_form_filter(&$vo)
    {
        if ($this->request->isPost()) {
            $order = Db::name($this->table)->where(['id' => $vo['id']])->find();
            if (empty($order)) $this->error('订单查询异常，请稍候再试！');
            $express = Db::name('express_company')->where(['express_code' => $vo['express_company_code']])->find();
            if (empty($express)) $this->error('发货快递公司异常，请重新选择快递公司！');
            $vo['express_company_title'] = $express['express_title'];
            $vo['express_send_at'] = empty($order['express_send_at']) ? date('Y-m-d H:i:s') : $order['express_send_at'];
            $vo['express_state'] = '1';
            $vo['status'] = '2';
        }
    }


    /**
     * 订单详情
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function detail()
    {
        $this->title = '订单详情';
        $this->_form($this->table);
    }

    /**
     * 订单发货
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */

    public function deliver()
    {
        $this->title = '发货';
        $this->express_company = Db::name('express_company')->field('id,express_title')->select();
        $this->_form($this->table,'deliver');
    }

    /**
     * 订单完成
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function complete()
    {
        if($this->request->isPost())
        {
            $check_status = \app\common\model\WashOrder::where('id',input('post.id'))->value('status');
            if($check_status != 1) $this->error('订单状态有误');
        }
        $this->_save($this->table, ['status' => 2]);
    }

    /**
     * 表单数据处理
     * @auth true
     * @menu true
     * @param array $data
     */
    protected function _form_filter(&$data)
    {
        if ($this->request->isGet() && $this->request->action() == 'detail'){
            $order_item = WashOrderItem::where(['o.order_id'=>$data['id']])
                ->alias('o')
                ->field('o.*,g.ladder_set,g.title')
                ->leftJoin('wash_cate g','g.id=o.cate_id')
                ->select()->toArray();
            array_walk($order_item,function (&$v){
                $v['images_arr'] = $v['images'] ? explode('|',$v['images']) : [];
            });
            $this->order_item = $order_item;
            $this->data = $data;
        }

        if ($this->request->isPost() && $this->request->action() == 'deliver') {
            $express_company =  Db::name('express_company')->field('id,express_title')->find($data['express_company_id']);
            $data['express_company_title'] = $express_company['express_title'] ? $express_company['express_title'] : '';
            $data['express_send_at'] = date("Y-m-d H:i:s");
            $data['express_state'] = 1;
        }
    }



}