<?php

class IndexAction extends Action {

    protected function _initialize(){
        if(MODULE_NAME != 'Index' || ACTION_NAME != 'login'){
            if(!session('LOGGED_IN')){
                $this->redirect('index/login');
            }
        }
    }

    public function index() {
        if ($this->isPost()) {
            $this->_getGoods();
        }

        $this->display();
    }

    public function login(){
    	if ($this->isPost()){
            if($this->_post('username') == 'admin' && $this->_post('password') == '111111'){
                session('LOGGED_IN', true);
                $this->redirect('index/index');
            }
        }
        $this->display();
    }

    protected function _getGoods() {
        $cates = file_get_contents('pair2.txt');
        $cates = unserialize($cates);

        $url = $_POST['url'];
        $Getter = new GetGoods();
        $result = $Getter->fetch($url);
        if(!$result){
            return false;
        }
        dump($result);
        M('Shop')->add($result['shop'], null, true);

        $data = $result['item'];
        $data['cate_id'] = $cates[$data['tao_cate']];
        $data['shop_id'] = $result['shop']['id'];
        $data['timeline'] = time();
        $goods_result = M('Goods')->add($data);
        if(!$goods_result){
            Log::write('add goods fail: '.M('Goods')->getLastSql());
            $Stor->delete('pinkfan', getThumbPic($data['img'], 210));
            $Stor->delete('pinkfan', getThumbPic($data['img'], 470));
            echo '<span style="color:red;">' . $data['id'] . ' : failed.</span><br />';
            return false;
        }
        $Fts->addDoc($data['id'], $data['name']);
        echo $data['id'] . ' : done. <br />';

        return true;
    }

    public function grab_chss(){
        set_time_limit(0);

//         $grabber = new GrabCHSS();
//         $grabber->run();

//        $this->update_goods(SAE_TMP_PATH.'/chss_ids.txt');

        $old_ids = M('Goods')->getField('id,name');
        $new_ids = file('chss.txt');
        foreach($new_ids as $id){
            $id = trim($id);
            if(!isset($old_ids[$id])){
                echo $id.'<br />';
            }
        }
    }

    public function update_goods($id_file='ids.txt'){
        set_time_limit(0);

        $Getter = new GetGoods();
        $ids = file($id_file);

        $cates = file_get_contents('pair2.txt');
        $cates = unserialize($cates);

        $Stor = Think::instance('SaeStorage');
        $Fts = Think::instance('SaeFTS');

        ob_end_clean();
        ob_implicit_flush(true);

        header("Content-type:text/html;charset=utf-8");

        foreach($ids as $id){
            $id = trim($id);
            if(!$id){
                continue;
            }
        	echo 'processing '.$id;
        	
            $data = array();
            $data = $Getter->fetchById($id);
            if(!$data){
                echo ' ... <span style="color:red;">skipped</span>.<br />';
                continue;
            }

            $item = $data['item'];
            
            //get rates info from taobao
            echo ' ... getting rates ...';
            $rates = $Getter->getComments($id);
            if($rates){
            	$item['rates'] = $rates;
            	echo ' done.';
            }else{
            	$item['rates'] = json_encode(array());
            	echo ' <font color="red">fail</font>.';
            }
            
            //add to shop
            $shop_result = M('Shop')->add($data['shop'], null, true);
            if(!$shop_result){
            	Log::write('add shop fail: '.$data['shop']['id']);
            }
            
            //add to goods
            $item['cate_id'] = $cates[$item['tao_cate']];
            $item['shop_id'] = $data['shop']['id'];
            $item['like_count'] = rand(0, 10) + rand(0, 20) + rand(0, 30) + rand(0, 40) + rand(0, 50);
            $item['timeline'] = time();
            $goods_result = M('Goods')->add($item);
            if(!$goods_result){
            	Log::write('add goods fail: '.M('Goods')->getError().' Last SQL:'.M('Goods')->getLastSql());
            	$Stor->delete('pinkfan', getThumbPic($item['img'], 210));
            	$Stor->delete('pinkfan', getThumbPic($item['img'], 470));
                echo ' ... <span style="color:red;">failed</span>.<br />';
                continue;
            }
            $Fts->addDoc($id, $item['name']);
            echo ' ... done. <br />';
        }

        $kv = new SaeKV();
        $kv->init();
        $kv->set('update_date', date('Y-m-d'));

        $this->display('index');
    }

    public function clean_img(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $files_in_dir = array();
        $files_in_db = array();

        $Store = Think::instance('SaeStorage');

//
//        $path = 'E:/sae/SAE_Local_Environment-windows-1.1.0/storage/storage/pinkfan/public/';
//
        $files_in_db = M('Goods')->getField('img,id');
        foreach($files_in_db as $file=>$id){
            if(!$Store->fileExists('pinkfan', $file.'_210.jpg')){
                echo $file.'_210.jpg'.'<br/>';
            }
            if(!$Store->fileExists('pinkfan', $file.'_470.jpg')){
                echo $file.'_470.jpg'.'<br/>';
            }
        }

//        $domain = 'E:\sae\SAE_Local_Environment-windows-1.1.0\storage\storage\pinkfan\public\goods';
//        $dir = opendir($domain);
//        while(false !== ($dirs = readdir($dir))){
//            $dir1 = $domain.DIRECTORY_SEPARATOR.$dirs;
//            if (is_dir($dir1) && $dirs != '.' && $dirs != '..'){
//                $sub_dir = opendir($dir1);
//                while(false !== ($sub_dirs = readdir($sub_dir))){
//                    $dir2 = $dir1.DIRECTORY_SEPARATOR.$sub_dirs;
//                    if (is_dir($dir2) && $sub_dirs != '.' && $sub_dirs != '..'){
//                        $file_dir = opendir($dir2);
//                        while(false !== ($files = readdir($file_dir))){
//                            $file_name = $dir2.DIRECTORY_SEPARATOR.$files;
//                            if(is_file($file_name) && $files != '.' && $files != '..'){
//                                $file_path = substr('goods'.'/'.$dirs.'/'.$sub_dirs.'/'.$files, 0, -8);
//                                if(!isset($files_in_db[$file_path])){
//                                    $ex[] = $file_path;
//                                }
//                            }
//                        }
//                        closedir($file_dir);
//                    }
//                }
//                closedir($sub_dir);
//            }
//        }
//        closedir($dir);
//        dump($ex);
    }

    public function fix_cate(){
        $items = M('Goods')->field('id,tao_cate,cate_id')->where(array('cate_id'=>0))->select();
        $cates = file_get_contents('pair2.txt');
        $cates = unserialize($cates);
        foreach($items as $item){
            $item['cate_id'] = $cates[$item['tao_cate']];
            M('Goods')->save($item);
        }
    }
    
    public function fix_shop_credit(){
    	$items = M('Shop')->field('id,seller')->where(array('credit'=>1))->select();
    	
    	vendor('Top.TopClient');
    	vendor('Top.RequestCheckUtil');
    	vendor('Top.request.UserGetRequest');
    	
    	$client = new TopClient();
    	$client->appkey = C('TB_KEY');
    	$client->secretKey = C('TB_SECRET');
    	
    	foreach($items as $item){
    		$req = new UserGetRequest;
    		$req->setFields('seller_credit');
    		$req->setNick($items['seller']);
    		$resp = $client->execute($req);
    		if(isset($resp->user)){
    			dump($resp->user->seller_credit->level);
    		}
    	}
    	
    }

    public function add_index(){
        set_time_limit(0);
        ob_end_clean();
        ob_implicit_flush(true);

        header("Content-type:text/html;charset=utf-8");

        $fts = new SaeFTS();

        $map['id'] = array('in', array('6017319470', '14862752117', '13235714647', '7780189242', '15546944146'));
        $docs = M('Goods')->where($map)->getField('id,name');
        foreach($docs as $id=>$name){
            if($fts->addDoc($id, $name)){
                echo "{$id}:{$name} <span style=\"color:green\">success</span><br />";
            }else{
                echo "{$id}:{$name} <span style=\"color:green\">{$fts->errmsg()}</span><br />";
            }
        }
    }

    public function delete(){
        $ids = array('16872408877', '13954557245', '14056825473', '14188190398', '13559545695', '13955043421');
        $fts = new SaeFTS();
        foreach($ids as $id){
            $fts->deleteDoc($id);
        }
    }

    public function fix_pic(){
        $src = 'C:/Users/Chin/Desktop/1.jpg';
        import('ORG.Util.Image');
        $Image = new Image();
        $Image->thumb($src, $src.'_210.jpg', '', 210, 1000);
        $Image->thumb($src, $src.'_470.jpg', '', 470, 1000);
    }
    
    public function fetch_rates(){
    	$items = M('Goods')->field('id')->where(array('timeline'=>array('gt', '1354943364')))->select();
    	ob_end_clean();
    	ob_implicit_flush(true);
    	header("Content-type:text/html;charset=utf-8");
    	ini_set('default_socket_timeout', 300);
    	$i = 1;
    	foreach ($items as $item){
    		echo $i.'. updating '.$item['id'];
    		$page = file_get_contents('http://item.taobao.com/item.htm?id='.$item['id']);
    		preg_match('/(seller_num_id|sellerid|userid|sid|userNumId|user_id|seller_id_num)=(\d+)/i', $page, $m);
    		if(!empty($m) && !empty($m[2])){
    			$seller_id = $m[2];
    			$url = 'http://rate.taobao.com/feedRateList.htm?userNumId='.$seller_id.'&auctionNumId='.$item['id'].'&currentPageNum=1';
    			$rates = file_get_contents($url);
    			$rates = substr(trim($rates), 1, -1);
    			$rates = iconv('gbk', 'utf8', $rates);
    			$rates = json_decode($rates, true);
    			$comments = array();
    			if(!empty($rates['comments'])){
    				foreach ($rates['comments'] as $c){
    					$comments[] = array(
    							'content'	=>	$c['content'],
    							'date'		=>	$c['date'],
    							'nick'		=>	$c['user']['nick']
    					);
    				}
    				$item['rates'] = json_encode($comments);
    				M('Goods')->save($item);
    				echo ' ... done<br />';
    			}else{
    				echo ' ... <font color="red">fail</font><br />';
    			}
    		}
    		$i++;
    	}
    }
    
    public function fetchwords(){
    	$tags = array();
    	$list = M('Goods')->getField('id,name');
    	if(!empty($list) && IS_SAE){
    		$Seg = new SaeSegment();
    		foreach ($list as $id=>$name){
    			$ret = $Seg->segment($name, 1);
    			if($ret !== false){
    				foreach ($ret as $word){
    					if (($word['word_tag'] == '95' || $word['word_tag'] == '171') &&
    					!eregi("[^\x80-\xff]", "$word[word]") && strlen($word[word]) > 3){
    						$tags[$word['word']] = isset($tags[$word['word']]) ?
    						($tags[$word['word']] + 1) : 0;
    					}
    				}
    			}
    		}
    	}
    	dump($tags);
    	$this->display();
    }
}

?>
