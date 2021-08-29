<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Validations
if ( !is_numeric(request('pin_ID')) || !is_numeric(request('pin_x')) || !is_numeric(request('pin_y')) ) return;


// Get the pin info
$pin_ID = intval(request('pin_ID'));
$pin_x = floatval(request('pin_x'));
$pin_y = floatval(request('pin_y'));


// DO THE SECURITY CHECKS!
// a. Current user can edit this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;


// Update the pin
$pin_updated = $pinData->reLocate($pin_x, $pin_y);
if ($pin_updated) $status = "Pin relocated: $pin_ID";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_x' => $pin_x,
	'pin_y' => $pin_y,
	'pin_ID' => $pin_ID

);

echo json_encode(array(
  'data' => $data
));
