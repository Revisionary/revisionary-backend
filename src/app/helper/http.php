<?php

// Get Response Code
function get_http_response_code($url) {

	$response = "000";

	$OriginalUserAgent = ini_get('user_agent');
	ini_set('user_agent', 'Mozilla/5.0');

	$headers = @get_headers(urldecode($url), 1);

	ini_set('user_agent', $OriginalUserAgent);

	$response = intval(substr($headers[0], 9, 3));


	// If fails, try without context
	if ( $response != 200 ) {

		$headers = @get_headers(urldecode($url), 1);
		$response = intval(substr($headers[0], 9, 3));

	}



	if ($headers)
		return $response;
	else
		return false;
}



// Get content from remote URL
function getRemoteData($url, $timeout = 60) {


	return json_decode(file_get_contents($url, false, stream_context_create(array('http'=>
	    array(
	        'timeout' => $timeout,  // Seconds
	    )
	))));


}



// Get client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
