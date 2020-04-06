<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
if ( !is_numeric(request('pin_ID')) ) return;
$pin_ID = request('pin_ID');


// DO THE SECURITY CHECKS!
// a. Current user can edit this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;


// Update the pin
$pin_updated = $pinData->deviceAll();
if ($pin_updated) $status = "Pin #$pin_ID made for all devices";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID

);

echo json_encode(array(
  'data' => $data
));
