<?php
class TagsAction extends BaseAction{
	const TAGS_PER_PAGE = 300;
	
	public function index(){
		$this->assign('title', '热门检索 - '.C('DEFAULT_TITLE'));
        $this->load(C('PUBLIC').'css/tags.css', 'css');
		
		$tags = include './Data/tags.php';
		
		$count = count($tags);
		import('ORG.Util.Page');
		$Page = new Page($count, self::TAGS_PER_PAGE);
		$pagenav = $Page->show();
		
		$p = intval($_GET['p']);
		if(empty($p)){
			$p = 1;
		}
		$tags = array_slice($tags, ($p-1)*self::TAGS_PER_PAGE, self::TAGS_PER_PAGE);
		
		$this->assign('pagenav', $pagenav);
		$this->assign('tags', $tags);
		$this->display();
	}
	
}