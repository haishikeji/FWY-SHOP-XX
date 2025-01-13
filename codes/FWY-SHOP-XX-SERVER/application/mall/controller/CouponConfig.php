<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019  [ http://www.pinxun.cn ]
// +----------------------------------------------------------------------
// | 官方网站: http://demo.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\mall\controller;
use library\Controller;
use library\tools\Data;
use think\Db;

/**
 * 优惠券配置
 * Class CouponConfig
 * @package app\mall\controller
 */
class  CouponConfig extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'CouponConfig';

    /**
     * 优惠券管理
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
        $this->title = '优惠券配置';
        $query = $this->_query($this->table)->like('title')->equal('status');
        $query->where(['is_deleted' => '0'])->order('id desc')->page();
    }

    /**
     * 数据列表处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _index_page_filter(&$data)
    {
        foreach ($data as $k=>&$v) {
            if ($v['coupon_type'] == 1) {
                if ($v['goods_id']) {
                    $v['goods_name']=Db::name('store_goods')->where('id',$v['goods_id'])->value('name');
                }

            }
            $v['sheng']=Db::name('user_coupon_list')->where('config_id',$v['id'])->count();

        }

    }


    /**
     * 添加优惠券
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        $this->title = '添加优惠券';
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑优惠券
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        $this->title = '编辑优惠券';
        $this->_form($this->table, 'form');
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    protected function _form_filter(&$data)
    {
        if($this->request->isPost()){
            if($data['time_type']==1){
                if(empty($data['time'])){
                    $this->error('请选择优惠券限定日期');
                }
                $time=explode('~',$data['time']);
                $data['start_tm']=$time[0];
                $data['end_tm']=$time[1];
            }

            if($data['coupon_type']==1){
                if(empty($data['goods_id'])){
                    $this->error('请选择优惠券商品');
                }
            }

            if($data['low_amount']<=0){
                $data['low_amount']=$data['amount']+0.01;
            }

            if($data['amount']>$data['low_amount']){
                $this->error('对不起，优惠金额不能大于限制金额');
            }

        }else{
            $this->goods=Db::name('store_goods')->where('is_deleted',0)->where('status',1)->field('id,name')->select();
        }
    }




    /**
     * 禁用优惠券
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        $this->_save($this->table, ['status' => '0']);
    }

    /**
     * 启用优惠券
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        $this->_save($this->table, ['status' => '1']);
    }

    /**
     * 删除优惠券
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        $this->_delete($this->table);
    }
}
