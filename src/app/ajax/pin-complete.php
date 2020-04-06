<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Get the pin info
if ( !is_numeric(request('pin_ID')) ) return;
$pin_ID = intval(request('pin_ID'));

$complete = request('complete') == "complete";


// DO THE SECURITY CHECKS !!!
// a. Current user can edit this pin?


$pinData = Pin::ID($pin_ID);
if (!$pinData) return;


// Complete/Incomplete the pin
$pin_completed = $complete ? $pinData->complete() : $pinData->inComplete();
if ($pin_completed) $status = "Pin ".($complete ? "completed" : "incompleted").": $pin_ID";


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
