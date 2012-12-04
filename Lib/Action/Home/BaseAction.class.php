<?php

class BaseAction extends Action{

    protected function _initialize(){
        $meta['title'] = C('DEFAULT_TITLE');
        $meta['description'] = C('DEFAULT_DESC');
        $meta['keywords'] = C('DEFAULT_KEYWORDS');

        $cates = M('Category')->cache('cat_list', 3600)->getField('id,name');
        $this->assign('cates', $cates);

        $this->assign($meta);
    }

    protected function load($file, $type='js', $pos='header'){
        static $load = array();
        $load[$pos][] = array('file'=>$file, 'type'=>$type);
        $this->assign($load);
    }
}
