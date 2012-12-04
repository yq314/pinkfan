<?php

vendor('Top.TopClient');
vendor('Top.RequestCheckUtil');
vendor('Top.request.ItemGetRequest');
vendor('Top.request.ShopGetRequest');
vendor('Top.request.UserGetRequest');
vendor('Top.request.TaobaokeItemsDetailGetRequest');

class GetGoods{

    public function getID($url) {
        $id = 0;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            if (isset($params['id']))
                $id = $params['id'];
            elseif (isset($params['item_id']))
                $id = $params['item_id'];
            elseif (isset($params['default_item_id']))
                $id = $params['default_item_id'];
        }
        return $id;
    }

    public function fetch($url){
        $id = $this->getID($url);
        if ($id == 0) {
            return FALSE;
        }

        return $this->fetchById($id);
    }

    public function fetchById($id) {
        $client = new TopClient;
        $client->appkey = C('TB_KEY');
        $client->secretKey = C('TB_SECRET');

        $req = new ItemGetRequest;
        $req->setFields("detail_url,title,nick,pic_url,price,cid");
        $req->setNumIid($id);
        $resp = $client->execute($req);

        if($resp->code == 7){
            //请求限制
            $log = $id . ' | ' . $resp->msg . ' | ' . $resp->sub_code . ' | ' . $resp->sub_msg;
            echo '<span style="color:yellow;">' . $log . '</span><br />';
            Log::write($log);
            if($resp->sub_msg){
                preg_match('/\d+/', $resp->sub_msg, $m);
                is_numeric($m[0]) && sleep(intval($m[0]) + 1);
                return $this->fetchById($id);
            }
            return false;
        }elseif($resp->code){
            //请求错误
            $log = $id . ' | ' . $resp->msg;
            echo '<span style="color:red;">' . $log . '</span><br />';
            Log::write($log);
            return false;
        }

        if (!isset($resp->item)){
            return false;
        }

        $result = array();
        $goods = (array) $resp->item;

        if (empty($goods['detail_url']) || empty($goods['pic_url']))
            return false;

        $result['item']['id'] = $id;
        $result['item']['name'] = $goods['title'];
        $result['item']['price'] = $goods['price'];
        $result['item']['url'] = $goods['detail_url'];
        $result['item']['tao_cate'] = $goods['cid'];

        $image = $this->_copyImg($goods['pic_url'], 'goods');
        if(empty($image)){
            return false;
        }
        $result['item']['img'] = $image['url'];
        $result['item']['img_width'] = $image['width'];
        $result['item']['img_height'] = $image['height'];

        $tao_ke_pid = C('TAOKE_ID');
        $shop_click_url = '';
        if (!empty($tao_ke_pid)) {
            $req = new TaobaokeItemsDetailGetRequest;
            $req->setFields("click_url,shop_click_url");
            $req->setNumIids($id);
            $req->setPid($tao_ke_pid);
            $resp = $client->execute($req);

            if (isset($resp->taobaoke_item_details)) {
                $taoke = (array) $resp->taobaoke_item_details->taobaoke_item_detail;
                if (!empty($taoke['click_url']))
                    $result['item']['taoke_url'] = $taoke['click_url'];

                if (!empty($taoke['shop_click_url']))
                    $shop_click_url = $taoke['shop_click_url'];
            }
        }

        if (!empty($goods['nick'])) {
            $req = new ShopGetRequest;
            $req->setFields("sid,title");
            $req->setNick($goods['nick']);
            $resp = $client->execute($req);

            if (isset($resp->shop)) {
                $shop = (array) $resp->shop;
                $result['shop']['seller'] = $goods['nick'];
                $result['shop']['name'] = $shop['title'];
                $result['shop']['id'] = $shop['sid'];
                $result['shop']['url'] = 'http://shop' . $shop['sid'] . '.taobao.com';
                if(!empty($shop_click_url)){
                    $result['shop']['taoke_url'] = $shop_click_url;
                }

                $req = new UserGetRequest;
                $req->setFields('seller_credit');
                $req->setNick($goods['nick']);
                $resp = $client->execute($req);
                if(isset($resp->user)){
                    $result['shop']['credit'] = (int)$resp->user->seller_credit->level;
                }
            }
        }

        return $result;
    }

    private function _copyImg($from, $to) {
        $ext = pathinfo($from, PATHINFO_EXTENSION);
        if(empty($ext) || !in_array($ext, array('jpg','jpeg','png','gif'))) $ext = 'jpg';
        $file_name = md5(microtime(true)) . rand_string(3) . '.' . $ext;
        $dir = $to . '/' . substr($file_name, 0, 1) . '/' . substr($file_name, 1, 1);
        $file_path = $dir . '/' . $file_name;
        $tmp_path = SAE_TMP_PATH . '/' . $file_name;
        $tmp_path_210 = getThumbPic($tmp_path, 210);
        $file_path_210 = getThumbPic($file_path, 210);
        $tmp_path_470 = getThumbPic($tmp_path, 470);
        $file_path_470 = getThumbPic($file_path, 470);

        $data = file_get_contents($from);
        if(empty($data)){
            return false;
        }
        file_put_contents($tmp_path, $data);

        import('ORG.Util.Image');
        $Image = new Image();
        $Image->thumb($tmp_path, $tmp_path_210, '', 210, 1000);
        $info = $Image->getImageInfo($tmp_path_210);
        $Image->thumb($tmp_path, $tmp_path_470, '', 470, 1000);

        $Stor = Think::instance('SaeStorage');
        $up_status = $Stor->upload('pinkfan', $file_path_210, $tmp_path_210);
        if($up_status == false){
            echo 'upload '. $file_path_210 .' failed.';
        }
        $up_status = $Stor->upload('pinkfan', $file_path_470, $tmp_path_470);
        if($up_status == false){
            echo 'upload '. $file_path_470 .' failed.';
        }

        return array(
            'url' => $file_path,
            'width' => $info['width'],
            'height' => $info['height'],
        );
    }

}

?>