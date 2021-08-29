<?php

class Log {



}



// Site Logging
$logFile = logdir."/site.log";
if (!file_exists($logFile)) file_put_contents($logFile, '');
unset($logFile);


// Mail Logging
$logFile = logdir."/sent-mail-php.log";
if (!file_exists($logFile)) file_put_contents($logFile, '');
unset($logFile);


$log = new Katzgrau\KLogger\Logger(
	logdir,
	Psr\Log\LogLevel::DEBUG,
	array(
		'filename' => 'site',
	    'extension' => 'log'
	)
);