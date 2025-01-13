<?php
namespace app\user\controller;
use library\Controller;
use think\Db;

/**
 * 会员等级管理
 * Class Level
 * @package app\store\controller
 */
class Level extends Controller
{

    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'UserLevel';

    /**
     * 等级管理
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
        $query = $this->_query($this->table)->order('id asc')->page();
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
     * 添加等级
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
        $this->title = '添加等级';
        $this->_form($this->table, 'form');
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
        if($this->request->isGet()) {
            $this->price = !empty($data['price']) ? json_decode($data['price'],true):[];
        }
        if($this->request->isPost())
        {
            // 会员价格设置
            $title_arr= input('post.title');
            $time_arr= input('post.time');
            $price_arr= input('post.price');
            $price_param = [];
            foreach ($title_arr as $k=>$t){
                $price_param[] = ['title'=>$t,'time'=>intval($time_arr[$k]),'price'=>bcadd($price_arr[$k],0,2),'level_key'=>$k];
            }
            $data['price'] = json_encode($price_param);
        }
    }

}