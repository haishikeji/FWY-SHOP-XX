<?php
namespace app\mall\controller;
use library\Controller;
use think\Db;

/**
 * 商品评价
 * Class Comment
 * @package app\mall\controller
 */
class Comment extends Controller
{

    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'OrderComment';

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
        $this->title = '列表';
        $where = [];
        $where[] = ['c.is_deleted','=',0];
        if($good_name = input('goods_name')) $where[] = ['g.name','like','%'.$good_name.'%'];
        if($user_name = input('user_name')) $where[] = ['m.name','like','%'.$user_name.'%'];
        $query = $this->_query($this->table)
            ->field('c.*,m.name user_name,m.headimg,g.cover,g.name goods_name')
            ->alias('c')
            ->leftJoin('StoreMember m','c.user_id = m.id')
            ->leftJoin('StoreGoods g','c.goods_id = g.id')
            ->where($where);
        $query->order('c.id desc')->page();
    }

    /**
     * 数据列表处理
     * @auth true
     * @menu true
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _index_page_filter(&$data)
    {

    }




    /**
     * 删除
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $this->_save($this->table, ['is_deleted' => 1]);
    }


    /**
     * 表单数据处理
     * @auth true
     * @menu true
     * @param array $data
     */
    protected function _form_filter(&$data)
    {
        if($this->request->isGet()){

        }
    }

}