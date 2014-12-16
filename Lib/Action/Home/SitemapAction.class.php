<?php

class SitemapAction extends BaseAction{
    public function index(){
        $this->assign('title', '站点地图 - '.C('DEFAULT_TITLE'));
        $this->load(C('PUBLIC').'css/sitemap.css', 'css');

        $Goods = M('Goods');
        $list = $Goods->page(intval($_GET['p']).',300')->select();
        $this->assign('list', $list);

        import('ORG.Util.Page');
        $count = $Goods->count();
        $Page = new Page($count, 300);

        $pagenav = $Page->show();
        $this->assign('pagenav', $pagenav);

        $this->display('Sitemap:index');
    }

    public function google(){
        $cache = S('sitemap');
        if(!$cache){
            $s = new SiteMap();
            $s->addItem(SITE_URL);
            $s->addItem(SITE_URL.'/sitemap/');
			$s->addItem(SITE_URL.'/shops/');
            
            $urls = M('Goods')->order('timeline DESC')->getField('id,timeline');
            foreach($urls as $id=>$time){
                $s->addItem(SITE_URL.'/goods/'.$id, $time);
            }

            $cache = $s->getGoogle();
            S('sitemap', $cache, 86400);
        }

        header("Content-Type: application/xml");
        echo $cache;
    }
    
    public function shops(){
    	
    }
}
?>