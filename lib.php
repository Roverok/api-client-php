<?php

require_once('config.php');

function apiQuery($method = "GET", $path, array $req = array()) {
	
	
	$method = strtoupper($method);

	$nonce = $milliseconds = round(microtime(true) * 1000);
	
	$req['apiKey'] = $apiKey;
	$req['nonce'] = $nonce;
	
	$queryString = http_build_query($req, '', '&');
	
	$sign = hash_hmac('sha256', $queryString, $privateKey);
	
	$queryString .= '&hash=' . $sign;
	
	static $curlHandler = null;
		
	if (is_null($curlHandler)) {
		$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandler, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; SWISSCEX API PHP client; ' . php_uname('s') . '; PHP/' . phpversion() . ')');
	}

	$requestUrl = $apiUrl . $path;

	if("GET" === $method) {
		$requestUrl .= '?' . $queryString;
	} else {
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $queryString);
	}
	
	curl_setopt($curlHandler, CURLOPT_URL, $requestUrl);
	curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, FALSE); // disable since the API does not support SSL yet
		
	// run the query
	$response = curl_exec($curlHandler);
	
	if ($response === false) {
		throw new Exception('Could not get reply: ' . curl_error($curlHandler));
	}
	
	$json = json_decode($response, true);
	if (!$json) {
		throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
	}
	return $json;
}