<?php

// print_r(

// 	file_get_contents('http://chrome:3000?url=https%3A%2F%2Fwww.cuneyt-tas.com%2F&action=internalize&width=1440&height=774&phase_ID=1')

// );

// // phpinfo();
// exit;

sleep(2);

// required headers
header("Access-Control-Allow-Origin: https://".$config['env']['dashboard_subdomain'].".".$config['env']['dashboard_domain']);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");




// $class  = filter_input(INPUT_GET, 'class',  FILTER_SANITIZE_STRING);
// $method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_STRING);
//$log->info($_SERVER['HTTP_REFERER']);


// CREATE THE RESPONSE
$data = array(

	array(
		"title" => "Post 1",
		"slug" => "post-1"
	),

	array(
		"title" => "Post 2",
		"slug" => "post-2"
	),

	array(
		"title" => "Post 3",
		"slug" => "post-3"
	)

);

die(json_encode(array(
  'posts' => $data
)));