<?php

class UAction extends BaseAction{
    public function g(){
        $this->handle('Goods');
    }

    public function s(){
        $this->handle('Shop');
    }

    private function handle($type){
        if(is_numeric($_GET['id'])){
            $to = M($type)->field('url,taoke_url')->where("id='{$_GET['id']}'")->find();
            if($to['taoke_url']){
                redirect($to['taoke_url']);
            }elseif($to['url']){
                redirect($to['url']);
            }
        }
        redirect(C('TAOKE_' . strtoupper($type)));
    }
}
?>