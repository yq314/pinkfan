<?php

class GoodsAction extends BaseAction{
    public function _empty($id){
        $this->detail($id);
    }

    protected function detail($id){
        if(!is_numeric($id)){
            $this->error('咦...好像木有找到哦...', '/');
        }

        $data = D('GoodsView')->find($id);
        if(!$data){
            $this->error('咦...好像木有找到哦...', '/');
        }

        $this->_getGoodsFromSameShop($data['shop_id'], $id);
        $this->_getGoodsFromSameCate($data['cate_id'], $id);

        $this->load(C('PUBLIC').'css/detail.css', 'css');
        $this->load('http://static.pinglun.la/md/pinglun.la.js', 'js', 'footer');
        $this->load(C('PUBLIC').'js/detail.js', 'js', 'footer');

        $filter['cat'] = $data['cate_id'];
        $this->assign('filter', $filter);
        $this->assign('data', $data);
        $this->assign('title', $data['name'].' - 粉红控');
        $this->display('detail');
    }

    private function _getGoodsFromSameShop($shop_id, $self_id, $limit=2){
        $where['shop_id'] = $shop_id;
        $where['id'] = array('neq', $self_id);
        $goodsFromSameShop = M('Goods')->field('id,name,img')->where($where)->limit($limit)->order('timeline DESC')->select();
        $this->assign('goodsFromSameShop', $goodsFromSameShop);
    }

    private function _getGoodsFromSameCate($cate_id, $self_id, $limit=6){
        $where['cate_id'] = $cate_id;
        $where['id'] = array('neq', $self_id);
        $goodsFromSameCate = M('Goods')->field('id,name,img')->where($where)->limit($limit)->order('timeline DESC')->select();
        $this->assign('goodsFromSameCate', $goodsFromSameCate);
    }
}
?>