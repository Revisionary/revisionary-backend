<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Validation
if ( !is_numeric(request('pin_ID')) || !is_numeric(request('device_ID')) ) return;
$pin_ID = intval(request('pin_ID'));
$device_ID = intval(request('device_ID'));


// DO THE SECURITY CHECKS!
// a. Current user can edit this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;


// Update the pin
$pin_updated = $pinData->deviceSpecific($device_ID);
if ($pin_updated) $status = "Pin #$pin_ID made only for Device #$device_ID";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID,
	'device_ID' => $device_ID

);

echo json_encode(array(
  'data' => $data
));
