<?php

$s = Think::instance('SaeStorage');
$url = $s->getUrl('pinkfan', '');
$public = IS_SAE ? $url : '/Public/';

return array(
    'APP_GROUP_LIST'        => 'Home,Admin',
    'DEFAULT_GROUP'         => 'Home',
    'URL_MODEL'             => 2,
    'URL_CASE_INSENSITIVE'  =>  true,
    'APP_AUTOLOAD_PATH'     =>  '@.Tool',


    'DB_TYPE'				=>	'mysql',		// 数据库类型
	'DB_HOST'				=>	'localhost',	// 数据库服务器地址
	'DB_NAME'				=>	'app_pinkfan',		// 数据库名称
	'DB_USER'				=>	'root',			// 数据库用户名
	'DB_PWD'				=>	'111111',				// 数据库密码
	'DB_PORT'				=>	'3306',			// 数据库端口
    'DB_PREFIX'             =>  'pf_',

    'DB_SQL_BUILD_CACHE'    =>  true,
    'DB_SQL_BUILD_LENGTH'   =>  20,


    'TB_KEY'                => 12570560,
    'TB_SECRET'             => 'ed28050cff6c9bda89ad24a6b63486ae',
    'TAOKE_ID'              => 13639730,

    'SAE_DOMAIN'            =>  $url,
    'PUBLIC'            =>  $public,

    'TMPL_PARSE_STRING'     =>  array(
        '__SAE_DOMAIN__'    => $url,
        '__PUBLIC__'        =>  $public
    ),
		
	'ENCRYPT_KEY'			=>	'G1200402B',

	'HOT_KEYWORDS'			=>	array('短外套', '新款', '小西装', '宽松毛衣', '平底鞋', '单鞋'),
);