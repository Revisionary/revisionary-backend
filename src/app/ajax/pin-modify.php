<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
if ( !is_numeric(request('pin_ID')) ) return;
$pin_ID = intval(request('pin_ID'));
$modification = htmlentities(request('modification', true), ENT_QUOTES);


// Null detection
if ($modification == "{%null%}")
	$modification = null;


// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;
$pinInfo = $pinData->getInfo();


// Modify the pin
$pin_modified = $pinData->modify($modification);
if ($pin_modified) $status = "Pin Modified: $pin_ID";
else $status = "error";


// Delete the old Image
$old_image = $pinInfo['pin_modification'];
if ( $pinInfo['pin_modification_type'] == "image" && strpos($old_image, '://') !== false ) { // On S3

	$old_image_path = substr(parse_url($old_image, PHP_URL_PATH), 1);

	// Delete old ımage
	$file = new File($old_image_path, "s3");
	$file->delete();

}


// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce'),
	'modification' => $modification,
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID

);

die(json_encode(array(
  'data' => $data
)));
