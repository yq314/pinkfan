<?php
class IndexAction extends BaseAction {
    public function index(){
        $this->load(C('PUBLIC').'css/index.css', 'css');
        $this->load(C('PUBLIC').'js/jquery.masonry.min.js', 'js', 'footer');
        $this->load(C('PUBLIC').'js/jquery.pagination.js', 'js', 'footer');
        $this->load(C('PUBLIC').'js/index.js?201205071836.js', 'js', 'footer');

        $filter['cat'] = $this->_get('cat', 'intval', 0);
        $filter['sort'] = $this->_get('sort', 'intval', 0);
        $filter['price'] = $this->_get('price', 'intval', 0);
        $filter['q'] = $this->_get('q', 'htmlspecialchars', '');
        if(!empty($filter['q'])){
            $this->assign('q', $filter['q']);
            $this->assign('title', '搜索结果：'.$filter['q'].' - 粉红控');
        }else{
            if(empty($filter['cat'])){
                $cat_name = '宝贝';
            }else{
                $cat_name = M('Category')->where('id='.$filter['cat'])->getField('name');
            }
            $this->assign('cat_name', '粉红'.$cat_name);
            $this->assign('title', '粉红'.$cat_name.' - '.C('DEFAULT_TITLE'));
        }

        $this->assign('filter', $filter);

        $condition = $this->_formCondition($filter['cat'], $filter['price']);
        $count = M('Goods')->where($condition)->count();

        $this->assign('goods_count', $count);

        $cur_page = $this->_get('p', 'intval', 1);
        $this->assign('cur_page', $cur_page);

        $kv = new SaeKV();
        $kv->init();
        $update_date = $kv->get('update_date');
        if(!$update_date){
            $update_date = date('Y-m-d');
        }
        $this->assign('update_date', $update_date);

        $order = $filter['sort'] ? 'like_count DESC' : 'timeline DESC';

        $p = ($cur_page - 1) * 5 + 1;
        $data = D('GoodsView')->where($condition)->order($order)->
        	limit(isSpider() ? 80 : 16)->page(isSpider() ? $cur_page : $p)->select();
        $this->assign('data', $data);
        $this->assign('init_data', $this->fetch('waterfall'));
        
        if(isSpider()){
        	import('ORG.Util.Page');
        	$Page = new Page($count, 80);
        	$this->assign('pagenav', $Page->show());
        }

        $this->display();
    }

    private function _formCondition($cat, $price){
        $condition = array();
        if($cat){
            $condition['cate_id'] = $cat;
        }
        if($price){
            switch ($price){
                case 1:
                    $condition['price'] = array('elt', 100);
                    break;
                case 2:
                    $condition['price'] = array('elt', 200);
                    break;
                case 3:
                    $condition['price'] = array('elt', 500);
                    break;
                case 4:
                    $condition['price'] = array('elt', 1000);
                    break;
                case 5:
                    $condition['price'] = array('elt', 2000);
                    break;
                default:
                    $condition['price'] = array('gt', 2000);
            }
        }
        if(($ids = $this->_getIdsByQuery($this->_get('q'))) !== false){
            $condition['id'] = array('in', $ids);
        }
        return $condition;
    }

    public function getData(){
        $cat = $this->_get('cat', 'intval', 0);
        $sort = $this->_get('sort', 'intval', 0);
        $price = $this->_get('price', 'intval', 0);
        $cur_page = $this->_get('cur_page', 'intval', 1);
        $sub_page = $this->_get('sub_page', 'intval', 1);

        $condition = $this->_formCondition($cat, $price);
        $order = $sort ? 'like_count DESC' : 'timeline DESC';

        $p = ($cur_page - 1) * 5 + $sub_page;
        $data = D('GoodsView')->where($condition)->order($order)->limit(16)->page($p)->select();
        $this->assign('data', $data);

        header("Content-type: application/x-javascript; charset=utf-8");

        echo $_GET['callback'].'('.json_encode($this->fetch('waterfall')).')';
    }

    private function _getIdsByQuery($q){
        if(empty($q)){
           return false;
        }

        $key = md5($q);
        $ids = S($key);
        if($ids === false){
           $Fts = new SaeFTS();
           $ret = $Fts->search($q);
           $ids = array();
           if($ret['count'] > 0){
               foreach($ret['result'] as $r){
                   $ids[] = $r['docid'];
               }
           }
           S($key, $ids, 3600);
        }
        return $ids;
    }
}