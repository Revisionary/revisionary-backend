<?php
use \DrewM\MailChimp\MailChimp;


$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Check if the email and feature fields are exist
$email = request('email');
$feature = request('feature');
if ( !$email || !$feature ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "missing-fields",
		'email' => $email,
		'feature' => $feature
	)));

}


// Validate the features
if ($feature != "all" && $feature != "draw" && $feature != "integrations") {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "invalid-feature",
		'email' => $email,
		'feature' => $feature
	)));

}


// Validate the email
if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "invalid-email",
		'email' => $email,
		'feature' => $feature
	)));

}


// Do the subscription
$MailChimp = new MailChimp('cf46d8fcb49efd7ebbe51f02aec3ec58-us17');
$list_id = '74f14f8baf';

$result = $MailChimp->post("lists/$list_id/members", [
	'email_address' => $email,
	'status'        => 'subscribed'
]);


// If already exists
if ( $result['status'] == 400 ) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "already-exists",
		'email' => $email,
		'feature' => $feature,
		'result' => $result
	)));

}


// Any error
if ( !$MailChimp->success() ) {


	// Also subscribe for the "New Features" list
	$list_id_features = "5f5221d0cf";
	$result = $MailChimp->post("lists/$list_id_features/members", [
		'email_address' => $email,
		'status'        => 'subscribed'
	]);


	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-subscribed",
		'email' => $email,
		'feature' => $feature,
		'result' => $result,
		'error' => $MailChimp->getLastError()
	)));

}


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => "success",
	'email' => $email,
	'feature' => $feature,
	'result' => $result
)));