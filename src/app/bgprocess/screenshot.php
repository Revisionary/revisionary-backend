<?php

// BG Process Settings
ignore_user_abort(true);
set_time_limit(0);


// Get the data
$phase_ID = intval($argv[1]);
$page_ID = intval($argv[2]);
$device_ID = intval($argv[3]);
$sessionID = $argv[4];
$queue_ID = isset($argv[5]) && is_numeric($argv[5]) ? intval($argv[5]) : "";
$page_type = $argv[6];


// Correct the session ID
session_id($sessionID);


// Call the system
require realpath('.').'/app/init.php';


// Needs to be closed to allow working other PHP codes
session_write_close();


// Get the page data
$phaseData = Phase::ID($phase_ID);


// Logger
$logger = new Katzgrau\KLogger\Logger(
	$phaseData->logDir,
	Psr\Log\LogLevel::DEBUG,
	array(
		'filename' => 'screenshot',
	    'extension' => $phaseData->logFileExtension, // changes the log file extension
	)
);


// Initiate Queue class
$queue = new Queue();


// Initialize screenshotter
$screenshot = new Screenshot($phase_ID, $page_ID, $device_ID, $queue_ID);


// Reset the variables
$job_ready = $browser_done = $complete = false;


echo "Queue ID: $queue_ID  \r\n";
echo "Page ID: $page_ID \r\n";
echo "Phase ID: $phase_ID \r\n";
echo "Device ID: $device_ID \r\n";
echo "SessionID: $sessionID \r\n";
echo "Page Type: $page_type \r\n";



// JOBS:

// 1. Wait for the queue
if ($queue_ID) $job_ready = $screenshot->waitForQueue();


// 2. 	If job is ready to get done, open the site with Chrome
// 2.1. Take a screenshot for the page, and project if not exist
if ($job_ready) $browser_done = $screenshot->browserWorks($page_type);


// 3. Complete the job!
if ($browser_done) $complete = $screenshot->completeTheJob();


if ($complete) echo "Screenshot taken \r\n";