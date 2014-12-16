<?php

function isSpider(){
    $bots = array(
                    'Google'    => 'googlebot',
                    'Baidu'        => 'baiduspider',
                    'Yahoo'        => 'yahoo slurp',
                    'Soso'        => 'sosospider',
                    'Msn'        => 'msnbot',
                    'Altavista'    => 'scooter ',
                    'Sogou'        => 'sogou spider',
                    'Yodao'        => 'yodaobot',
    				'Bing'			=>	'bingbot',
    				'360'		=>	'360spider'
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