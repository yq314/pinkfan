<?php

class LikeAction extends Action{
    public function add(){
        if(!is_numeric($_GET['id'])){
            $ajax['status'] = 0;
            $ajax['info'] = '请求错误';
            echo $_GET['callback'].'('.  json_encode($ajax).')';
            return;
        }
        $map['id'] = $_GET['id'];
        $result = M('goods')->where($map)->setInc('like_count');
        if($result){
            $ajax['status'] = 1;
            $ajax['info'] = $result;
        }else{
            $ajax['status'] = 0;
            $ajax['info'] = '操作失败';
        }
        echo $_GET['callback'].'('.  json_encode($ajax).')';
    }
}
?>