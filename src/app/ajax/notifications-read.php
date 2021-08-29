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


// If ID's not array
if ( !is_array(request('notification_IDs')) ) {

	$status = "list-problem";

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => $status,
		'nonce' => request('nonce')
		//'S_nonce' => $_SESSION['pin_nonce'],
	)));

}



$allRead = Notification::ID( request('notification_IDs') )->readAll();
if ($allRead) $status = "success";



// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'nonce' => request('nonce')
	//'S_nonce' => $_SESSION['pin_nonce'],
)));
