<?php
namespace app\mall\controller;
use library\Controller;

/**
 * 分类管理
 * Class WashCate
 * @package app\mall\controller
 */
class WashCate extends Controller
{

    /**
     * 当前操作数据库
     * @var string
     */
    protected $table = 'WashCate';

    /**
     * 分类管理
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
        $this->title = '分类管理';
        $query = $this->_query($this->table)->where('is_deleted',0)->page(false);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(&$data)
    {
    }

    /**
     * 添加分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        $this->_form($this->table, 'form');
    }

    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _form_filter(&$data)
    {
        if($this->request->isGet()) {
            $this->ladder_set = !empty($data['ladder_set']) ? json_decode($data['ladder_set'],true):[];
        }
        if($this->request->isPost()) {
            // 价格设置
            $title_arr= input('post.ladder_title');
            $price_arr= input('post.price');
            $price_param = [];
            if(empty($price_arr) || empty($title_arr)) $this->error('请设置价格');
            foreach ($title_arr as $k=>$t){
                $price_param[] = ['title'=>$t,'price'=>bcadd($price_arr[$k],0,2),'ladder_key'=>$k];
            }
            $data['ladder_set'] = json_encode($price_param);
        }
    }


    /**
     * 启用分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function enable()
    {
        $this->_save($this->table, ['status' => '1']);
    }

    /**
     * 禁用分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbidden()
    {
        $this->_save($this->table, ['status' => '0']);
    }

    /**
     * 删除分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $this->_save($this->table, ['is_deleted' => 1]);
    }

}
