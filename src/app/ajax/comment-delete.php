<?php

$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Are they numbers?
if ( !is_numeric(request('pin_ID')) || !is_numeric(trim(request('comment_ID'))) ) return;
$pin_ID = intval(request('pin_ID'));
$comment_ID = intval(trim(request('comment_ID')));


// DO THE SECURITY CHECKS !!!
// a. Current user can access this pin?



$pinData = Pin::ID($pin_ID);
if (!$pinData) return;



// Get the comments
$comment_deleted = $pinData->deleteComment($comment_ID);
if ($comment_deleted) $status = "Comment #$comment_ID DELETED";


// CREATE THE RESPONSE
$data = array();
$data['data'] = array(

	'status' => $status,
	'nonce' => request('nonce'),
	//'S_nonce' => $_SESSION['pin_nonce'],
	'pin_ID' => $pin_ID,
	'comment_ID' => $comment_ID

);

echo json_encode(array(
  'data' => $data
));
