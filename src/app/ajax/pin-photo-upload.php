<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userLoggedIn() ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-logged-in"
	)));

}
$user_ID = currentUserID();


// Invalid pin_ID
if ( !is_numeric(get('pin_ID')) ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "invalid"
	)));

}
$pin_ID = intval(get('pin_ID'));


// Check pin
$pinData = Pin::ID($pin_ID);
if ( !$pinData ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "no-pin"
	)));

}
$pinInfo = $pinData->getInfo();


// Check pin modification type
if ( $pinInfo['pin_modification_type'] != "image" ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "invalid-pin-type"
	)));

}


// File check
if (
	!isset($_FILES['image']['name'])
	|| !isset($_FILES['image']['tmp_name'])
	|| !is_uploaded_file($_FILES['image']['tmp_name'])
) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "no-file"
	)));

}



// Size check
if ($_FILES['image']['size'] > 15000000) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "large-file"
	)));

}


// File info
$image = $_FILES['image']['name'];
$temp_file_location = $_FILES['image']['tmp_name'];	
$image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$image_name = generateRandomString().".".$image_extension;


// Extension check
if ( !in_array($image_extension, array('jpeg', 'jpg', 'png', 'gif')) ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "invalid-extension"
	)));

}


// Resize
resize_image($temp_file_location, 1920, 1920);


// Select file to upload
$file = new File($temp_file_location);


// Rename if exists
while ( $file->fileExists("pin-images/pin-$pin_ID/$image_name", "s3") )
	$image_name = generateRandomString().".".$image_extension;


// Upload
$pin_image_url = $file->upload("pin-images/pin-$pin_ID/$image_name", "s3");
if ( !$pin_image_url ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-uploaded"
	)));

}


// Modify the pin
$pin_modified = $pinData->modify($pin_image_url);
if ( !$pin_modified ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-modified"
	)));

}


// Delete the old Image
$old_image = $pinInfo['pin_modification'];
if ( strpos($old_image, '://') !== false ) { // On S3

	$old_image_path = substr(parse_url($old_image, PHP_URL_PATH), 1);

	// Delete old ımage
	$file = new File($old_image_path, "s3");
	$file->delete();

}


// Site log
$log->info("User #$user_ID Uploaded a Pin #$pin_ID image: '$pin_image_url'");


// INVALIDATE THE CACHES
$cache->deleteKeysByTag('pins');


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => "success",
	'pin_ID' => $pin_ID,
	'user_ID' => $user_ID,
	'new_url' => $pin_image_url,
	'files' => $_FILES
)));