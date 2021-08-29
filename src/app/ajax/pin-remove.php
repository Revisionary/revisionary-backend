<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
if ( !is_numeric(request('pin_ID')) ) return;
$pin_ID = request('pin_ID');


// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;


// Delete the pin
$pin_deleted = $pinData->remove();
if ($pin_deleted) $status = "Pin deleted: $pin_ID";


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID
)));