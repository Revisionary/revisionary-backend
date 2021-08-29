<?php
use Cocur\BackgroundProcess\BackgroundProcess;


// Validate device_ID
if ( !is_numeric(request('device_ID')) ) return;
$device_ID = intval(request('device_ID'));
$deviceData = Device::ID($device_ID);
if (!$deviceData) return;


// Get the phase data
$phase_ID = $deviceData->getInfo('phase_ID');
$phaseData = Phase::ID($phase_ID, null, $device_ID);
if (!$phaseData) return;
$phaseStatus = $phaseData->getPhaseStatus();


// Get queue_ID if exists
$queue_ID = is_numeric(request('queue_ID')) ? intval(request('queue_ID')) : false;


// Set the process ID to check
$process = BackgroundProcess::createFromPID( request('processID') );


// STATUS CHECK
$status = 'not-running';
if ( $process->isRunning() )
	$status = 'running';

// If not running
elseif ( !$queue_ID ) {


	// Logger
	$logger = new Katzgrau\KLogger\Logger($phaseData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $phaseData->logFileName,
	    'extension' => $phaseData->logFileExtension, // changes the log file extension
	));



	// If project is not complete when the process stopped
	$queue = new Queue();
	if ( $queue->info($queue_ID)['queue_status'] != "done" ) {

		$last_message = $queue->info($queue_ID)['queue_message'];

		// Update the queue status as an error
		$queue->update_status($queue_ID, "error", "Last Message: ".$last_message, $process->getPid());

	}


}

//$process->stop();


// CREATE THE RESPONSE
die(json_encode(array(
	'data' => array(
		'status' => $status,
		'processID' => $process->getPid(),
		'processStatus' => $phaseStatus['status'],
		'processDescription' => $phaseStatus['description'],
		'processPercentage' => $phaseStatus['percentage'],

		'queue_ID' => $queue_ID,
		'phaseUrl' => $phaseData->cachedUrl,
		'remoteUrl' => $phaseData->remoteUrl,
		'internalized' => $phaseData->internalizeCount
	)
)));
