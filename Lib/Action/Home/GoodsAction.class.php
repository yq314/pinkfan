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
        $this->load(C('PUBLIC').'js/detail.js', 'js', 'footer');

        $filter['cat'] = $data['cate_id'];
        $this->assign('filter', $filter);
        if($data['rates'] != NULL){
        	$data['rates'] = json_decode($data['rates'],true);
        }
        
        $this->_parseTag($data['name']);
        
        $this->assign('data', $data);
        $this->assign('title', $data['name'].' - '.C('DEFAULT_TITLE'));
        $this->assign('description', '宝贝详情：'.$data['name'].' - '.C('DEFAULT_TITLE'));
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
    
    private function _parseTag($name){
    	if(IS_SAE){
    		$Seg = new SaeSegment();
    		$ret = $Seg->segment($name, 1);
    		if($ret !== false){
    			$tags = array();
    			foreach ($ret as $word){
    				if (($word['word_tag'] == '95' || $word['word_tag'] == '171') &&
    				 !eregi("[^\x80-\xff]", "$word[word]") && strlen($word[word]) > 3 &&
    				!in_array($word['word'], $tags)){
    					$tags[] = $word['word'];
    				}
    			}
    			$this->assign('tags', $tags);
    		}
    	}
    }
}
?>