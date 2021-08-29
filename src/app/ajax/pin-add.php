<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Are they numbers?
if ( 
	!is_numeric(request('pin_phase_ID')) 
	|| !is_numeric(request('pin_device_ID'))
	|| !is_numeric(request('pin_x'))
	|| !is_numeric(request('pin_y'))
	|| !is_numeric(request('pin_element_index'))
) return;

// Get the pin info
$pin_phase_ID = intval(request('pin_phase_ID'));
$pin_device_ID = intval(request('pin_device_ID'));
$pin_x = floatval(request('pin_x'));
$pin_y = floatval(request('pin_y'));
$pin_element_index = intval(request('pin_element_index'));


// Pin type validation
if ( request('pin_type') != "content" && request('pin_type') != "style" && request('pin_type') != "comment" ) return;
$pin_type = request('pin_type');


$pin_private = boolval(request('pin_private'));
$pin_modification_type = request('pin_modification_type') == "{%null%}" ? null : request('pin_modification_type');


// DO THE SECURITY CHECKS !!!
// a. Current user can add this pin?



// Add the pin
$pin_ID = Pin::ID('new')->addNew(
	$pin_phase_ID,
	$pin_type,
	$pin_private,
	$pin_x,
	$pin_y,
	$pin_element_index,
	$pin_modification_type,
	$pin_device_ID
);

if ($pin_ID) $status = "Added: $pin_ID";



// CREATE THE RESPONSE
$data = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_x' => $pin_x,
	'pin_y' => $pin_y,
	'real_pin_ID' => $pin_ID,
	'dateCreated' => Pin::ID($pin_ID)->getInfo('pin_created')

);

echo json_encode(array(
  'data' => $data
));
