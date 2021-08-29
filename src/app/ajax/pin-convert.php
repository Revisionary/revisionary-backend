<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Check the values
if (
	!is_numeric(request('pin_ID')) ||
	(request('pin_type') != "content" && request('pin_type') != "style" && request('pin_type') != "comment" ) ||
	(request('pin_private') != '0' && request('pin_private') != '1' )
)
	return;


// Get the pin info
$pin_ID = intval(request('pin_ID'));
$pin_type = request('pin_type');
$pin_private = request('pin_private');


// DO THE SECURITY CHECKS !!!
// a. Current user can add this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;


// Add the pin
$converted = $pinData->convert($pin_type, $pin_private);
if ($converted) $status = "Converted #$pin_ID to $pin_type, $pin_private";



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_type' => $pin_type,
	'pin_private' => $pin_private

);

echo json_encode(array(
  'data' => $data
));
