<?php
	require('lib.php');
	
	
	// format: METHOD path param1 value1 param2 value2 param3 value3
	// example: post order/new symbol DOGE_BTC amount 1 price 0.1 side BUY
	while($f = fgets(STDIN)) {
		$f = rtrim($f, "\n");
		$input = explode(' ', $f);
		$method = $input[0];
		$path = $input[1];
		$params = array();
		if(count($input) > 2) {
			$rest = array_slice($input, 2);
			$i = 0;
			$key = '';
			foreach($rest as $val) {
				$mod = ++$i % 2;
				
				if(0 === $mod) {
					$params[$key] = $val;
				} else {
					$key = $val;	
				}
		
			}
		}
		$response = apiQuery($method, $path, $params);
		echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
	}