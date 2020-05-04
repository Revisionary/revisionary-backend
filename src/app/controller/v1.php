<?php
//sleep(2);


// Headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://".$config['env']['dashboard_subdomain'].".".$config['env']['dashboard_domain']);
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header("Access-Control-Max-Age: 3600");


// Request Method Check
$method = $_SERVER['REQUEST_METHOD'];
if ($method != "GET" && $method != "PUT" && $method != "POST" && $method != "DELETE") {

	die(json_encode(array(
		"status" => "error",
		"description" => "Not allowed method"
	)));

}


// API Check
$api = $_url[1] ?? "no-api";
if ($api == "no-api") {

	die(json_encode(array(
		"status" => "error",
		"description" => "API not found"
	)));

}


// API Validation
if (
	   $api != "project"
	&& $api != "page"
	&& $api != "phase"
	&& $api != "device"
	&& $api != "user"
	&& $api != "pin"
	&& $api != "projectcategory"
	&& $api != "pagecategory"
) {

	die(json_encode(array(
		"status" => "error",
		"description" => "Not allowed API"
	)));

}



// ACTIONS:

// GET all the data
if ($method == "GET" && !isset($_url[2]) ) {


	$output = array();


	// PROJECS
	if ($api == "project") {

		$output = User::ID()->getProjects();

	}


	// PAGES
	if ($api == "project") {

		$output = User::ID()->getProjects();

	}


	die(json_encode($output));

}
















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
	"method" => "$method",
	$api."s" => $data
)));






// $class  = filter_input(INPUT_GET, 'class',  FILTER_SANITIZE_STRING);
// $method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_STRING);
//$log->info($_SERVER['HTTP_REFERER']);