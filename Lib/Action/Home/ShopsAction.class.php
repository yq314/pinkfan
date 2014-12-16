<?php
class ShopsAction extends BaseAction{
	function _empty($id){
		$this->detail($id);
	}
	
	function index(){
		$this->load(C('PUBLIC').'css/index.css', 'css');
		$this->load(C('PUBLIC').'css/shops.css', 'css');
		$this->load(C('PUBLIC').'js/jquery.pagination.js', 'js', 'footer');
		
		$data = M('Shop')->order('credit DESC')->page(intval($_GET['p']).',30')->cache(7200)->select();
		$this->assign('data', $data);
		
		import('ORG.Util.Page');
		$count = M('Shop')->count();
		$Page = new Page($count, 30);
		$firstrow = $Page->firstRow + 1;
		$lastrow = min($Page->firstRow + $Page->listRows, $count);
		$this->assign('firstrow', $firstrow);
		$this->assign('lastrow', $lastrow);
		
		$pagenav = $Page->show();
		$this->assign('title', "粉红商铺 TOP $firstrow - $lastrow - ".C('DEFAULT_TITLE'));
		$this->assign('pagenav', $pagenav);
		$this->display();
	}
	
	protected function detail($id){
		if(!is_numeric($id)){
			$this->error('咦...好像木有找到哦...', '/');
		}
		
		$shop = M('Shop')->find($id);
		if(!$shop){
			$this->error('咦...好像木有找到哦...', '/');
		}
		$this->assign('shop', $shop);
		
		$map['shop_id'] = $shop['id'];
		$goods = M('Goods')->where($map)->order('timeline DESC')->page(intval($_GET['p']).',24')->cache(7200)->select();
		$this->assign('goods', $goods);
		$count = M('Goods')->where($map)->count();
		$this->assign('count', $count);

		import('ORG.Util.Page');
		$Page = new Page($count, 24);
		$this->assign('pagenav', $Page->show());
		
		$this->load(C('PUBLIC').'css/index.css', 'css');
		$this->load(C('PUBLIC').'css/shops.css', 'css');
        $this->load(C('PUBLIC').'js/jquery.masonry.min.js', 'js', 'footer');
        $this->load(C('PUBLIC').'js/shops_detail.js', 'js', 'footer');
		
		$this->assign('title', '在售宝贝 - '.$shop['name'].' - '.C('DEFAULT_TITLE'));
		$this->assign('description', $shop['name'].'在售宝贝');
		$this->display('detail');
	}
}