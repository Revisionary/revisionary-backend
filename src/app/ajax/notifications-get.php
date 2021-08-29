<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// If not logged in
if ( !$User ) {

	$status = "not-logged-in";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}


// Offset numerical?
if ( !is_numeric(request("offset")) || request("offset") < 0 ) {

	$status = "wrong-offset";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}
$offset = request("offset");



$notifications = $User->getNotificationsHTML($offset);
if ($notifications) $status = "success";



// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'notifications' => $notifications,
	'nonce' => request('nonce')
	//'S_nonce' => $_SESSION['pin_nonce'],
)));
