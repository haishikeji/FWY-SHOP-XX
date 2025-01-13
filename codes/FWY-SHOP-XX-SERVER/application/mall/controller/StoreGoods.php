<?php
namespace app\mall\controller;
use app\common\model\GoodsSeason;
use app\common\model\GoodsTerritory;
use app\common\model\User;
use library\Controller;
use think\Db;
use app\common\model\GoodsCate;
use app\common\model\StoreGoodsItem;
use library\tools\Data;

/**
 * 商品管理
 * Class StoreGoods
 * @package app\mall\controller
 */
class StoreGoods extends Controller
{

    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'StoreGoods';

    /**
     * 商品列表
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
        $this->title = '商品管理';
        $where = [];
        $where['is_deleted'] = 0;
        $query = $this->_query($this->table)->where($where)->like('name');
        $query->dateBetween('create_at')->order('sort desc , id desc')->page();
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
        $this->clist = GoodsCate::getCateTree();
        $list = Db::name('StoreGoodsItem')->where('status', '1')->whereIn('goods_id', array_unique(array_column($data, 'id')))->select();
        foreach ($data as &$vo) {
            list($vo['list'], $vo['cate']) = [[], []];
            foreach ($list as $goods){
                if ($goods['goods_id'] === $vo['id']) array_push($vo['list'], $goods);
            }
        }
    }


    /**
     * 添加商品
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
        $this->title = '添加商品';
        $this->isAddMode = '1';
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑商品
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    function edit()
    {
        $this->title = '编辑商品';
        $this->isAddMode = '0';
        $this->_form($this->table, 'form');
    }





    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(&$data)
    {
        if($this->request->isGet()){
            $fields = 'goods_spec,goods_id,status,original_price,sell_price,virtual,cover pic,goods_no sku,weight';
            $defaultValues = [];
            if(isset_full($data,'id')) $defaultValues = Db::name('StoreGoodsItem')->where(['goods_id' => $data['id']])->column($fields);
            $this->defaultValues = json_encode($defaultValues, JSON_UNESCAPED_UNICODE);
            $this->goods_cate = GoodsCate::getCateTree();
            $this->express= Db::name('freight_template')->select();
        }
        // 添加或编辑商品
        if($this->request->isPost() && in_array($this->request->action(),['add','edit'])){
            list($data) = [$this->request->post()];
            $check_price = true;
            foreach (json_decode($data['lists'], true) as $vo){
                if( $vo[0]['sell_price'] <=0 )$check_price = false;
            }
            if(!$check_price) $this->error('价格设置有误');
        }

    }




    /**
     * 商品上架
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function up()
    {
        $this->_save($this->table, ['status' => '1','is_edit'=>0]);
    }

    /**
     * 商品下架
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function down()
    {
        $this->_save($this->table, ['status' => '0']);
    }


    /**
     * 商品删除
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        $this->_save($this->table, ['is_deleted' => '1']);
    }


    /**
     * 商品库存入库
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function stock()
    {
        if ($this->request->isGet()) {
            $goods_id = $this->request->get('id');
            $goods_info = Db::name('StoreGoods')->where(['id' => $goods_id])->find();
            empty($goods_info) && $this->error('无效的商品信息，请稍候再试！');
            $goods_info['list'] = Db::name('StoreGoodsItem')->where(['goods_id' => $goods_id])->select();
            $this->fetch('', ['vo' => $goods_info]);
        } else {
            list($post, $data) = [$this->request->post(), []];
            if (isset($post['id']) && isset($post['goods_id']) && is_array($post['goods_id'])) {
                foreach (array_keys($post['goods_id']) as $key) {
                    if ($post['goods_number'][$key] > 0) array_push($data, [
                        'goods_id'     => $post['goods_id'][$key],
                        'goods_spec'   => $post['goods_spec'][$key],
                        'number_stock' => $post['goods_number'][$key],
                    ]);
                }
                if (!empty($data)) {
                    Db::name('GoodsStock')->insertAll($data);
                    foreach ($data as $dv) {
                        Db::name('StoreGoodsItem')->where(['goods_id'=>$dv['goods_id'],'goods_spec'=>$dv['goods_spec']])->setInc('stock',$dv['number_stock']);
                        Db::name('StoreGoodsItem')->where(['goods_id'=>$dv['goods_id'],'goods_spec'=>$dv['goods_spec']])->setInc('base_stock',$dv['number_stock']);
                    }
                    Db::name('StoreGoods')->where('id',$post['id'])->setInc('stock',array_sum(array_column($data,'number_stock')));
                    Db::name('StoreGoods')->where('id',$post['id'])->setInc('base_stock',array_sum(array_column($data,'number_stock')));
                    $this->success('商品信息入库成功！');
                }
            }
            $this->error('没有需要商品入库的数据！');
        }
    }

    /**
     * 商品参数设置
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function param()
    {
        if ($this->request->isGet()) {
            $goods_id = $this->request->get('id');
            $goods_info = Db::name('StoreGoods')->where(['id' => $goods_id])->find();
            empty($goods_info) && $this->error('无效的商品信息，请稍候再试！');
            $param =  Db::name('goods_param')->where('goods_id',$goods_id)->find();
            $param_set = !$param ? [['title'=>'','value'=>'']]:json_decode($param['goods_param'],true);
            $this->fetch('', ['goods_info' => $goods_info,'param_set'=>$param_set]);
        }else{
            $goods_id = input('post.goods_id');
            $title_arr= input('post.title');
            $value_arr= input('post.value');
            if(empty($title_arr)) $this->error('请设置商品参数');
            $param_post = [];
            foreach ($title_arr as $k=>$t){
                $param_post[] = ['title'=>$t,'value'=>$value_arr[$k]];
            }
            Data::save('GoodsParam',['goods_id'=>$goods_id,'goods_param'=>json_encode($param_post)],'goods_id',['goods_id'=>$goods_id]);
            $this->success('保存成功!');
        }
    }



    /**
     * 添加完成商品之后逻辑处理
     * @param $result
     */
    protected function _form_result($result)
    {
        if ($result && $this->request->isPost() && in_array($this->request->action(),['add','edit'])) {
            list($data) = [$this->request->post()];
            $data['id'] = $result;
            $low_price = 0;
            foreach (json_decode($data['lists'], true) as $vo){
                if($low_price == 0 || $vo[0]['sell_price'] < $low_price )$low_price =  $vo[0]['sell_price'];
                Data::save('StoreGoodsItem', [
                    'goods_id'          => $data['id'],
                    'goods_spec'        => $vo[0]['key'],
                    'goods_no'          => $vo[0]['sku'],
                    'original_price'    => $vo[0]['original_price'],
                    'sell_price'        => $vo[0]['sell_price'],
                    'virtual'           => $vo[0]['virtual'],
                    'status'            => $vo[0]['status'] ? 1 : 0,
                    'weight'            => $vo[0]['weight'] ? $vo[0]['weight'] : 0,
                ], 'goods_spec', ['goods_id' => $data['id']]);
            }
            Db::name('StoreGoods')->where(['id'=>$data['id']])->update(['low_price'=>$low_price]);
            $this->success('商品编辑成功！', 'javascript:history.back()');
        }
    }








}