<?php
namespace app\user\controller;
use app\common\model\LabelMessage;
use library\Controller;
use think\Db;

/**
 * 消息推送管理
 * Class Message
 * @package app\user\controller
 */
class Message extends Controller
{

    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'LabelMessage';

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
        $query = $this->_query($this->table)->where('type',2)->where('is_deleted',0)->order('id desc')->page();
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
     * 添加分组
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        $this->title = '添加';
        $this->_form($this->table, 'form');
    }


    /**
     * 编辑分组
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
        $this->title = '编辑';
        $this->_form($this->table, 'form');
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


    public function send()
    {
        $this->_form($this->table, 'send');
    }

    /**
     * 表单数据处理
     * @auth true
     * @menu true
     * @param array $data
     */
    protected function _form_filter(&$data)
    {
        $data['type'] = 2;
    }

}