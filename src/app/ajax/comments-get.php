<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Pin ID
if ( !is_numeric(request('pin_ID')) ) return;
$pin_ID = intval(request('pin_ID'));



// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



$pinData = Pin::ID($pin_ID);
if (!$pinData) return;



// Get the comments
$comments =$pinData->comments();
$status = $comments ? "Comments received" : "No Comments Found";


// CREATE THE RESPONSE
die(json_encode(array(
	'status' => $status,
	'pin_ID' => $pin_ID,
	'comments' => $comments
)));
