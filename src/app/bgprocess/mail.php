<?php

// BG Process Settings
ignore_user_abort(true);
set_time_limit(0);


// Get the data
$to = $argv[1];
$subject = urldecode($argv[2]);
$message = urldecode($argv[3]);
$sessionID = $argv[4];


// Correct the session ID
session_id($sessionID);


// Call the system
require realpath('.').'/app/init.php';


// Needs to be closed to allow working other PHP codes
session_write_close();


$mailInfo = array(
	'$to' => $to,
	'$subject' => $subject,
	'$message' => $message,
	'$sessionID' => $sessionID,
);

print_r($mailInfo);


// SEND THE MAIL
$sent = Mail::SEND(
	$to,
	$subject,
	$message
);


// STATUS CHECK
if ($sent['status'] == 'sent') {
	echo "Email sent to $to \n \n";
}

if ($sent['status'] == 'failed') {
	echo $sent['message']." \n \n";
}