<?php

class BaseAction extends Action{

    protected function _initialize(){
        $meta['title'] = C('DEFAULT_TITLE');
        $meta['description'] = C('DEFAULT_DESC');
        $meta['keywords'] = C('DEFAULT_KEYWORDS');

        $cates = M('Category')->cache('cat_list', 36000)->getField('id,name');
        $this->assign('cates', $cates);

        $this->assign($meta);
        
        $hot_keywords = C('HOT_KEYWORDS');
        $this->assign('hot_keywords', $hot_keywords);
        
        //set cookies for taobao jssdk
        $app_key = C('TB_KEY');
        $secret = C('TB_SECRET');
        $timestamp = time() . '000';
        $message = $secret . 'app_key' . $app_key . 'timestamp' . $timestamp . $secret;
        $mysign = strtoupper(hash_hmac('md5', $message, $secret));
        setcookie('timestamp', $timestamp);
        setcookie('sign', $mysign);
    }

    protected function load($file, $type='js', $pos='header'){
        static $load = array();
        $load[$pos][] = array('file'=>$file, 'type'=>$type);
        $this->assign($load);
    }
}
