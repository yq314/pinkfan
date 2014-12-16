<?php

function pf_fetch_url($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	$body = curl_exec($ch);
	
	if (curl_errno ( $ch )) {
		echo 'Err: ' . curl_error ( $ch );
	}
	
	curl_close($ch);
	return $body;
}
?>
