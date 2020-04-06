<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Validation
if ( !is_numeric(request('phase_ID')) || !is_numeric(request('device_ID')) ) return;


// Get the pin info
$phase_ID = request('phase_ID');
$device_ID = request('device_ID');
$pins = array();


// If not logged in
if ( !$User ) $status = "not-logged-in";


// If device is not exist
else if ( !$User->canAccess($phase_ID, "phase") ) $status = "no-access";


// Get the pins
else {

	$pins = $User->getPins($phase_ID, $device_ID);
	$status = $pins ? "Pins received" : "No Pins Found";

}


// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	//'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'phase_ID' => $phase_ID,
	'device_ID' => $device_ID

);

die(json_encode(array(
  'data' => $data,
  'pins' => $pins
)));
