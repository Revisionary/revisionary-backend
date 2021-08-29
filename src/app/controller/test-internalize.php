<?php
use Cocur\BackgroundProcess\BackgroundProcess;

//header("content-type: image/png");
//echo file_get_contents($config['env']['chrome_url'] . '/screenshot/http://tr.bilaltas.net/');

//header("content-type: application/json");
//echo file_get_contents($config['env']['chrome_url'] . '/internalize/https://www.twelve12.com/?fullPage=false&width=1440&height=900&sitedir=/var/www/html/cache/');




$page_ID = 9;

$screen_ID = Page::ID($page_ID)->getInfo('screen_ID');
$width = Screen::ID($screen_ID)->getInfo('screen_width');
$height = Screen::ID($screen_ID)->getInfo('screen_height');


$link = $config['env']['chrome_url'] . "/";
$link .= "?url=".urlencode(Page::ID($page_ID)->remoteUrl);
$link .= "&action=internalize";
$link .= "&width=$width&height=$height";
$link .= "&sitedir=".urlencode(Page::ID($page_ID)->pageDir."/");


/*
//header("content-type: application/json");
$data = json_decode(file_get_contents($link));

echo "<pre>";
print_r($data->downloadedFiles);
echo "</pre>";
*/


echo "THE LINK: $link";
echo "<br /><br /><br />";

die();





$process_ID = get('pid');
if ( !empty($process_ID) ) {


	// Initiate Internalizator
	if ($process_ID == 'start') {


		// Remove the existing and wrong files
		if ( file_exists(Page::ID($page_ID)->pageDir) )
			deleteDirectory(Page::ID($page_ID)->pageDir);


		// Logger
		$logger = new Katzgrau\KLogger\Logger(Page::ID($page_ID)->logDir, Psr\Log\LogLevel::DEBUG, array(
			'filename' => Page::ID($page_ID)->logFileName,
		    'extension' => 'log', // changes the log file extension
		));


		// Add a new job to the queue !
		$queue = new Queue();
		$queueReturn = $queue->new_job('internalize', $page_ID, "Waiting other works to be done.", session_id());
		$process_ID = $queueReturn['process_ID'];


/*
		// Run Directly?
		$process = new Cocur\BackgroundProcess\BackgroundProcess('php '.dir.'/app/bgprocess/internalize.php '.$page_ID.' '.session_id().' '.$queue_ID);
		$process->run(Page::ID($page_ID)->logDir."/internalize-tasks-php.log", true);
		$process_ID = $process->getPid();
*/


		header('Location: /test-v4?pid='.$process_ID);

	} else $process = BackgroundProcess::createFromPID( $process_ID );


/*
	// Wait until process is done?
	while ( $process->isRunning() ) {
	  sleep(1);
	}
*/






	// Check the process
	if ( $process->isRunning() ) echo "<a href='/test-v4?pid=".$process_ID."'>$process_ID WORKING...</a>";
	else echo "DONE! <a href='/test-v4?pid=start'>Start Again?</a>";

} else {

	echo "READY! <br/>";
	echo "<a href='/test-v4?pid=start'> > Start < </a>";

}
