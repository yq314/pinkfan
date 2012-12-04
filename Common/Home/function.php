<?php

//获取蜘蛛爬虫名或防采集
function isSpider(){
    $bots = array(
                    'Google'    => 'googlebot',
                    'Baidu'        => 'baiduspider',
                    'Yahoo'        => 'yahoo slurp',
                    'Soso'        => 'sosospider',
                    'Msn'        => 'msnbot',
                    'Altavista'    => 'scooter ',
                    'Sogou'        => 'sogou spider',
                    'Yodao'        => 'yodaobot'
            );
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($bots as $k => $v){
        if (strstr($v,$userAgent) !== false){
            return $k;
            break;
        }
    }
    return false;
}

?>