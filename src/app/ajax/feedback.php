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



$feedback_type = request('feedback-type');
$feedback_url = request('feedback-url');
$stars = request('stars');
$feedback = request('feedback');


if (
	empty($feedback_type) ||
	empty($feedback_url) ||
	empty($stars) ||
	($feedback_type == "issue" && empty($feedback))
) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "empty-fields"
	)));

}


// File check
$screenshot = false;
$screenshot_url = "";
if (
	isset($_FILES['screenshot']['name'])
	&& isset($_FILES['screenshot']['tmp_name'])
	&& is_uploaded_file($_FILES['screenshot']['tmp_name'])
) {

	$screenshot = $_FILES['screenshot'];


	// File info
	$file_original_name = $screenshot['name'];
	$temp_file_location = $screenshot['tmp_name'];	
	$file_extension = strtolower(pathinfo($file_original_name, PATHINFO_EXTENSION));
	$file_name = generateRandomString().".".$file_extension;


	// Size check
	if ($screenshot['size'] > 5000000) {

		// CREATE THE RESPONSE
		die(json_encode(array(
			'status' => "large-file",
			'file_size' => $screenshot['size']
		)));

	}


	// Extension check
	if ( !in_array($file_extension, array('jpeg', 'jpg', 'png', 'gif')) ) {

		// CREATE THE RESPONSE
		die(json_encode(array(
			'status' => "invalid-extension",
			'extension' => $file_extension
		)));

	}


	// Select file to upload
	$file = new File($temp_file_location);
	
	
	// Rename if exists
	while ( $file->fileExists("feedback/user-$user_ID/$file_name", "s3") )
		$file_name = generateRandomString().".".$file_extension;


	// Upload
	$screenshot_url = $file->upload("feedback/user-$user_ID/$file_name", "s3");
	if ( !$screenshot_url ) {
	
		// CREATE THE RESPONSE
		die(json_encode(array(
			'status' => "not-uploaded"
		)));
	
	}


}



// Web Notification
$feedback_notification = $feedback_type == "bug" ? "sent an issue report: <br>" : "sent a feedback($stars): <br>";
if ( $feedback != "" ) $feedback_notification .= $feedback;
$feedback_notification .= "<br><a href='".queryArg("login_to=$user_ID", $feedback_url)."' target='_blank'>Page URL</a>";
if ($screenshot_url != "") $feedback_notification .= "<br><a href='$screenshot_url' target='_blank'>Screenshot Attached</a>";
$notified = Notify::ID(1)->web("text", "user", $user_ID, $feedback_notification);
if (!is_int($notified)) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-notified",
		'notified' => $notified
	)));

}



// Notify admins
$feedback_subject = $feedback_type == "bug" ? "Issue report received from ".getUserInfo()['fullName'] : "Feedback($stars) received from ".getUserInfo()['fullName'];

$feedback_body = "Sender: ".getUserInfo()['fullName']." (".getUserInfo()['email'].") <br>";
if ($feedback_type == "feedback") $feedback_body .= "Stars: $stars <br>";
if ($screenshot_url != "") $feedback_body .= "Screenshot: $screenshot_url <br>";
if ($feedback != "") $feedback_body .= "Feedback: $feedback <br>";
$feedback_body .= "Page URL: $feedback_url <br>";


Notify::ID(1)->mail(
	$feedback_subject,
	$feedback_body
);

Notify::ID("revisionaryapp@gmail.com")->mail(
	$feedback_subject,
	$feedback_body
);


// Site log
$log->info("User #$user_ID (".getUserInfo()['fullName'].") sent a feedback ($feedback_type): '$feedback' | Stars: $stars | Screenshot: $screenshot_url");


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => "success",
	'notified' => $notified,
	'user' => getUserInfo()['fullName'],
	'user_ID' => $user_ID,
	'stars' => $stars,
	'feedback_type' => $feedback_type,
	'feedback' => $feedback,
	'feedback_url' => $feedback_url,
	'screenshot' => $screenshot,
	'screenshot_url' => $screenshot_url
)));