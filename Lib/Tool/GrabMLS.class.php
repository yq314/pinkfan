<?php

class GrabMLS {

    private $_ch = null;
    private $_url = 'http://www.somatched.com/api/editor??q=&s_od=1&q_sz=24&q_cat=1&q_clr=17&cmd=ThumbList&facet=1&facet_fields=category&q_pr_facet=0,2000,500&sid=2a1fda3cb17b6447fe465063ab8abe2c';
    static private $total_page = 1;

    public function __construct() {
        @unlink('mls_ids.txt');

        $this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($this->_ch, CURLOPT_REFERER, 'http://www.somatched.com/Rainbow.html');
        curl_setopt($this->_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7 360EE');
    }

    public function run($page=1){
        set_time_limit(0);

        curl_setopt($this->_ch, CURLOPT_URL, $this->_url . '&page=' . $page);
        $response = curl_exec($this->_ch);
        $data = json_decode($response, true);
        self::$total_page = $data['num_pages'];
        $ids = array();
        foreach($data['list'] as $item){
            $ids[] = substr($item['id'], 3);
        }

        file_put_contents('mls_ids.txt', implode("\n", $ids) . "\n", FILE_APPEND);

        if(self::$total_page > $page){
            $this->run($page + 1);
        }
    }

}

?>