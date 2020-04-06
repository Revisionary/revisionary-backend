<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;



// If not logged in
if ( !userLoggedIn() ) {

	$status = "not-logged-in";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


$user_ID = getUserInfo()['userLevelID'] == 1 && is_numeric(get('user_ID')) ? intval(get('user_ID')) : currentUserID();
$userInfo = getUserInfo($user_ID);


// File check
if (
	!isset($_FILES['image']['name'])
	|| !isset($_FILES['image']['tmp_name'])
	|| !is_uploaded_file($_FILES['image']['tmp_name'])
) {

	$status = "no-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// Size check
if ($_FILES['image']['size'] > 3145728) {

	$status = "large-file";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// File info
$image = $_FILES['image']['name'];
$temp_file_location = $_FILES['image']['tmp_name'];	
$image_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$image_name = generateRandomString().".".$image_extension;


// Extension check
if ( !in_array($image_extension, array('jpeg', 'jpg', 'png')) ) {

	$status = "invalid-extension";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// Resize to 250x250
resize_image($temp_file_location, 250, 250);


// Select file to upload
$file = new File($temp_file_location);


// Rename if exists
while ( $file->fileExists("avatars/$image_name") )
	$image_name = generateRandomString().".".$image_extension;


// Upload
$result = $file->upload("avatars/$image_name", "s3");
if ( !$result ) {

	$status = "not-uploaded";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// New local URL
$new_url = cache_url("users/user-$user_ID/$image_name");


// Detect whether or not new avatar on S3
if ( is_string($result) && strpos($result, '://') !== false ) { // On S3

	$image_name = $new_url = $result;

}


// Update on DB
$user_updated = User::ID($user_ID)->edit('user_picture', $image_name);
if ( !$user_updated ) {

	$status = "not-updated";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// Delete the old one
$old_image_name = $userInfo['userPic'];
$old_image = cache."/users/user-$user_ID/$old_image_name";
$location = "local";


// Detect whether or not old avatar on S3
if ( strpos($old_image_name, '://') !== false ) { // On S3

	$old_image = substr(parse_url($old_image_name, PHP_URL_PATH), 1);
	$location = "s3";

}


// Delete old ımage
$file = new File($old_image, $location);
$file->delete();


$status = "success";


// Site log
$log->info("User #$user_ID Changed Avatar: '$image_name'");


// INVALIDATE THE CACHE
$cache->delete('user:'.$user_ID);


die(json_encode(array(
	'status' => $status,
	'user_ID' => $user_ID,
	'new_url' => $new_url,
	'files' => $_FILES
)));