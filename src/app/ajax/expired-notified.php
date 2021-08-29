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


// Do the action
$result = $User->edit('trial_expired_notified', 1);
if (!$result) {

	// CREATE THE RESPONSE
	die(json_encode(array(
		'status' => "not-updated"
	)));

}


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => "success"
)));