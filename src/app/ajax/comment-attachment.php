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


// File check
if (
	!isset($_FILES['comment-attachment']['name'])
	|| !isset($_FILES['comment-attachment']['tmp_name'])
	|| !is_uploaded_file($_FILES['comment-attachment']['tmp_name'])
) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "no-file"
	)));

}



// Size check
if ($_FILES['comment-attachment']['size'] > 15000000) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "large-file",
		'file_size' => $_FILES['comment-attachment']['size']
	)));

}


// File info
$file_original_name = $_FILES['comment-attachment']['name'];
$temp_file_location = $_FILES['comment-attachment']['tmp_name'];	
$file_extension = strtolower(pathinfo($file_original_name, PATHINFO_EXTENSION));
$file_name = generateRandomString().".".$file_extension;


// Extension check
if ( !in_array($file_extension, array('jpeg', 'jpg', 'png', 'gif', 'svg', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'pages', 'numbers')) ) { // !!! Update extensions

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "invalid-extension",
		'extension' => $file_extension
	)));

}


// Create thumbnails !!!
//resize_image($temp_file_location, 1920, 1920);


// Select file to upload
$file = new File($temp_file_location);


// Rename if exists
while ( $file->fileExists("pin-images/pin-$pin_ID/attachments/$file_name", "s3") )
	$file_name = generateRandomString().".".$file_extension;


// Upload
$pin_file_url = $file->upload("pin-images/pin-$pin_ID/attachments/$file_name", "s3");
if ( !$pin_file_url ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-uploaded"
	)));

}


// Add the comment
$comment_sent = Pin::ID($pin_ID)->addComment("$pin_file_url | $file_original_name", "attachment");
if (!$comment_sent) {
	
	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "comment-not-added",
		'pin_file_url' => $pin_file_url
	)));

}


// Site log
$log->info("User #$user_ID attached a file on Pin #$pin_ID: '$pin_file_url'");


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => "success",
	'pin_ID' => $pin_ID,
	'user_ID' => $user_ID,
	'new_url' => $pin_file_url,
	'files' => $_FILES
)));