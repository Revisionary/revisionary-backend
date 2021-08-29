<?php

require 'app/init.php';

$_url = get('url');
$_url = array_values(array_filter(explode('/', $_url)));



// Development mode detection
$_parsed_current_url = parseUrl( current_url() );
if ($_parsed_current_url['subdomain'] == "www") {
	ob_start();
	$page_title = "Revisionary App";
	require view('coming-soon');
	ob_end_flush();
	die();
}



// Show the correct controller
if(!isset($_url[0])){
  $_url[0] = 'index';
}

if(!file_exists(controller($_url[0]))){
	http_response_code(404);

	// Show index
	$_url[0] = 'index';

	// If 404 page exists, better to show this one
	if(file_exists(controller('errors')))
		$_url[0] = 'errors';
}



// // Force secure URL
// if (!ssl && $_url[0] != 'revise' && $_url[0] != 'ajax' && $_url[0] != 'v1') {

// 	header( 'Location: '.secure_url.$_SERVER["REQUEST_URI"] );
// 	die();

// }


ob_start();
require controller($_url[0]);
ob_end_flush();


if ($_url[0] != "ajax" && $debug_mode) {

	echo "
	<pre>
		<details>
			<summary><h2 style='display: inline;'>TRACE COUNT (".count($db->trace).")</h2></summary>
			<p style='padding-left: 20px;'>".print_r( $db->trace, true )."</p>
		</details>
	</pre>";

}
