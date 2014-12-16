<?php

class UAction extends BaseAction{
    public function g(){
        $this->handle('Goods');
    }

    public function s(){
        $this->handle('Shop');
    }
    
    public function d(){
    	$ref = empty($_GET['ref']) ? 'goods' : $_GET['ref'];
    	$this->_redirect(C('TAOKE_'.strtoupper($ref)));
    }

    private function handle($type){
        if(is_numeric($_GET['id'])){
            $to = M($type)->field('url,taoke_url')->where("id='{$_GET['id']}'")->find();
            if($to['taoke_url']){
                $this->_redirect($to['taoke_url']);
                exit;
            }elseif($to['url']){
                $this->_redirect($to['url']);
                exit;
            }
        }
        $this->_redirect(C('TAOKE_' . strtoupper($type)));
    }
    
    private function _redirect($url){
    	$spider = isSpider();
    	if(!$spider){
    		if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], SITE_URL)!==false){
    			redirect($url);
    		}
    	}
    	$this->assign('tourl', $url);
    	$this->display('redirect');
    }
}
?>