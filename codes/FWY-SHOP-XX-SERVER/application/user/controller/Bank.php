<?php
namespace app\user\controller;
use library\Controller;
use think\Db;

/**
 * 银行账号管理
 * Class Bank
 * @package app\store\controller
 */
class Bank extends Controller
{

    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'UserBank';

    /**
     * 账号管理
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
        $this->title = '等级管理';
        $where = [] ;
        $where[] = ['b.is_deleted','=',0];
        $query = $this->_query($this->table)
            ->field('b.*,m.headimg')
            ->alias('b')
            ->join('store_member m','m.id = b.user_id','LEFT')
            ->order('b.id asc')->page();
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
     * 编辑等级
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        $this->title = '编辑等级';
        $this->_form($this->table, 'form');
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