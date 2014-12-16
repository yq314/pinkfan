<?php
class TestAction extends Action{
	function index(){
		$str = '【专柜正品】Benefit贝玲妃淡粉珠光高光修饰液13ML 粉红色 包邮';
		$seg = new SaeSegment();
		$ret = $seg->segment($str, 1);
		foreach ($ret as $word){
			if ($word['word_tag'] == '95' || $word['word_tag'] == '171'){
				echo $word['word'].'<br/>';
			}
		}
		if ($ret === false)
			var_dump($seg->errno(), $seg->errmsg());
	}
}