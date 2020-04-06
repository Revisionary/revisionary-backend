<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userLoggedIn() ) {

	$status = "not-logged-in";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status
	)));

}


// File check
if (
	!isset($_FILES['design-upload']['name'])
	|| !isset($_FILES['design-upload']['tmp_name'])
	|| !is_uploaded_file($_FILES['design-upload']['tmp_name'])
) {

	$status = "no-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status
	)));

}


// Size check
if ($_FILES['design-upload']['size'] > 15000000) {

	$status = "large-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status
	)));

}


// File info
$image = $_FILES['design-upload']['name'];
$temp_file_location = $_FILES['design-upload']['tmp_name'];	
$image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));


// Extension check
if ( !in_array($image_extension, array('jpeg', 'jpg', 'png', 'gif')) ) {

	$status = "invalid-extension";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status
	)));

}



$project_ID = request('project_ID');
$page_ID = request('page_ID');
if ( request('add_new') == "true" ) {


	// PROJECT WORKS
	$new_project = false;
	if (!$project_ID || $project_ID == "new") {


		$project_ID = Project::ID('new')->addNew(
			"image",
			request('project-name'),
			is_array(request('project_shares')) ? request('project_shares') : array(),
			request('category'),
			request('order')
		);
		if (!$project_ID) {


			// CREATE THE RESPONSE
			die(json_encode(array(
				'status' => "project-not-created",
				'project_ID' => $project_ID
			)));


		}
		$new_project = true;


	}



	// PAGE WORKS
	if (!$page_ID || $page_ID == "new") {


		$page_ID = Page::ID('new')->addNew(
			$project_ID,
			'image',
			request('page-name'),
			is_array(request('page_shares')) ? request('page_shares') : array(),
			$new_project ? 0 : request('category'),
			$new_project ? 0 : request('order')
		);
		if (!$page_ID) {


			// CREATE THE RESPONSE
			die(json_encode(array(
				'status' => "page-not-created",
				'project_ID' => $project_ID,
				'page_ID' => $page_ID
			)));


		}


	}


}



// PHASE WORKS
$phase_ID = request('phase_ID');
if (!$phase_ID || $phase_ID == "new") {


	$phase_ID = Phase::ID('new')->addNew(
		$page_ID,
		'image'
	);
	if (!$phase_ID) {


		// CREATE THE RESPONSE
		die(json_encode(array(
			'status' => "phase-not-created",
			'project_ID' => $project_ID,
			'page_ID' => $page_ID,
			'phase_ID' => $phase_ID
		)));


	}



}



// DEVICE WORKS
list($width, $height) = getimagesize($temp_file_location);
$device_ID = Device::ID('new')->addNew(
	intval($phase_ID),
	request('screens'),
	$width,
	$height
);
if (!$device_ID) {


	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "device-not-created",
		'project_ID' => $project_ID,
		'page_ID' => $page_ID,
		'phase_ID' => $phase_ID,
		'device_ID' => $device_ID
	)));


}




// Image Info
$image_name = "device-$device_ID.$image_extension";
$deviceData = Device::ID($device_ID);
$image_location = $deviceData->getImage();



// Select file to move
$file = new File($temp_file_location);


// Upload
$result = $file->upload($image_location, "local");
if ( !$result ) {


	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-uploaded",
		'project_ID' => $project_ID,
		'page_ID' => $page_ID,
		'phase_ID' => $phase_ID,
		'device_ID' => $device_ID,
		'image_name' => $image_name,
		'image_location' => $image_location
	)));


}



// Site log
//$log->info("User #$user_ID Changed Avatar: '$image_name'");



// CREATE THE RESPONSE
die(json_encode(array(
	'status' => "success",
	//'status' => print_r($_REQUEST, true),
	'project_ID' => $project_ID,
	'page_ID' => $page_ID,
	'phase_ID' => $phase_ID,
	'device_ID' => $device_ID,
	'image_name' => $image_name,
	'image_location' => $image_location,
	'files' => $_FILES
)));