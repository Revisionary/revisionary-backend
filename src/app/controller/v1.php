<?php
//sleep(2);


// Headers
header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Origin: https://".$config['env']['dashboard_subdomain'].".".$config['env']['dashboard_domain']);
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
// header("Access-Control-Max-Age: 3600");


if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    //header('Access-Control-Max-Age: 86400'); // cache for 1 day
    header('Access-Control-Max-Age: 10'); // cache for 1 day

}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}



$jwt = getBearerToken() ?? false;
$parameters = json_decode(file_get_contents("php://input"));
// respondJSON(array(
// 	"status" => "error",
// 	"token" => $jwt,
// 	"parameters" => $parameters,
// 	"AUTH" => $_SERVER['HTTP_AUTHORIZATION'],
// 	"Request" => $_REQUEST,
// 	"url" => $_url
// ), 200);



// Request Method Check
$method = $_SERVER['REQUEST_METHOD'];
if ($method != "GET" && $method != "PUT" && $method != "POST" && $method != "DELETE") {
	respondJSON(array(
		"status" => "error",
		"description" => "Not allowed method"
	), 401);
}


// API Existance
$api = $_url[1] ?? "no-api";
if ($api == "no-api") {
	respondJSON(array(
		"status" => "error",
		"description" => "API not found"
	), 401);
}




// ACTIONS:

// GET all the data
if ($method == "GET") {


	// JWT existance
	if (!$jwt) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// User check
	$API = User::ID(["token" => $jwt]);
	if (!$API) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// USER
	if ($api == "session") {
		$result = $API->get();
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PROJECT CATEGORIES
	if ($api == "projectcategories") {
		$result = $API->getProjectCategories_v2();
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PROJECTS
	if ($api == "projects") {
		$result = $API->getProjects_v2();
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PROJECT
	if ($api == "project") {

		// Project ID check
		$project_ID = isset($_url[2]) ? $_url[2] : null;
		if ( !is_numeric($project_ID) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong parameter"
			), 401);
		}


		// Sub Method Check
		$submethod = isset($_url[3]) ? $_url[3] : null;
		if ( is_numeric($submethod) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong sub method"
			), 401);	
		}


		// Get project categories
		if ($submethod == "categories") {
			$result = $API->getPageCategories_v2($project_ID);
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get project categories
		if ($submethod == "pages") {
			$result = $API->getPages_v2($project_ID);
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get single project info
		$result = $API->getProject($project_ID);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PAGE
	if ($api == "page") {

		// Page ID check
		$page_ID = isset($_url[2]) ? $_url[2] : null;
		if ( !is_numeric($page_ID) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong parameter"
			), 401);
		}


		// Sub Method Check
		$submethod = isset($_url[3]) ? $_url[3] : null;
		if ( is_numeric($submethod) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong sub method"
			), 401);	
		}


		// Get page phases
		if ($submethod == "phases") {
			$result = $API->getPhases_v2($page_ID);
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get single page info
		$result = $API->getPage($page_ID);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// PHASE
	if ($api == "phase") {

		// Phase ID check
		$phase_ID = isset($_url[2]) ? $_url[2] : null;
		if ( !is_numeric($phase_ID) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong parameter"
			), 401);
		}


		// Sub Method Check
		$submethod = isset($_url[3]) ? $_url[3] : null;
		if ( is_numeric($submethod) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong sub method"
			), 401);	
		}


		// Sub Method Check
		$device_ID = isset($_url[4]) ? $_url[4] : null;
		if ( $device_ID !== null && !is_numeric($device_ID) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong sub2 method"
			), 401);	
		}


		// Get phase pins
		if ($submethod == "pins") {

			// Get phase pins with device ID filter
			if (is_numeric($device_ID)) {
				$result = $API->getPins_v2($phase_ID, $device_ID);
				respondJSON($result, $result['status'] == "success" ? 200 : 401);
			}


			$result = $API->getPins_v2($phase_ID);
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get phase devices
		if ($submethod == "devices") {
			$result = $API->getDevices_v2($phase_ID);
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get single phase info
		$result = $API->getPhase($phase_ID);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);

	}


	// DEVICE
	if ($api == "device") {

		// Device ID check
		$device_ID = isset($_url[2]) ? $_url[2] : null;
		if ( !is_numeric($device_ID) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong parameter"
			), 401);
		}


		// Sub Method Check
		$submethod = isset($_url[3]) ? $_url[3] : null;
		if ( is_numeric($submethod) ) {
			respondJSON(array(
				"status" => "error",
				"description" => "Wrong sub method"
			), 401);	
		}


		// Get device pins
		if ($submethod == "pins") {
			$result = $API->getPins_v2($device_ID);
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get single device info
		$result = $API->getDevice($device_ID);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);

	}


	// NOTIFICATIONS
	if ($api == "notifications") {


		// Sub Method Check
		$submethod = isset($_url[2]) ? $_url[2] : null;


		// Get new notification count
		if ($submethod == "newcount") {
			$result = $API->newNotificationsCount();
			respondJSON($result, $result['status'] == "success" ? 200 : 401);
		}


		// Get pages of notifications
		$offset = 0;
		$limit = 10;
		if ( is_numeric($submethod) && $submethod > 0 )
			$offset = ($submethod - 1) * $limit;


		// Get Notifications
		$result = $API->getNotifications_v2($offset, $limit);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);

	}


	// USAGE
	if ($api == "usage") {

		// Get Usage
		$result = $API->usage();
		respondJSON($result, $result['status'] == "success" ? 200 : 401);

	}


}



// POST
if ($method == "POST" && !isset($_url[2])) {


	// LOGIN SESSION
	if ($api == "session") {

		$result = User::ID([])->login(
			$parameters->username,
			$parameters->password
		);

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// JWT Existance
	if (!$jwt) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// User check
	$API = User::ID(["token" => $jwt]);
	if (!$API) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}



	// USERS
	if ($api == "users") {
		$result = $API->getUsers($parameters->IDs);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


	// READ NOTIFICATIONS
	if ($api == "readnotifications") {
		$result = $API->readNotifications($parameters->IDs);
		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}

}



// DELETE
if ($method == "DELETE" && !isset($_url[2])) {


	if (!$jwt) {
		respondJSON(array(
			"status" => "error",
			"description" => "Access denied"
		), 401);
	}


	// LOGOUT SESSION
	if ($api == "session") {
		$result = User::ID([
			"token" => $jwt
		])->logout();

		respondJSON($result, $result['status'] == "success" ? 200 : 401);
	}


}



respondJSON(array(
	"status" => "error",
	"description" => "Internal Error"
), 400);



// $class  = filter_input(INPUT_GET, 'class',  FILTER_SANITIZE_STRING);
// $method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_STRING);
//$log->info($_SERVER['HTTP_REFERER']);