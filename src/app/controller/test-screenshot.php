<?php
use Cocur\BackgroundProcess\BackgroundProcess;

$page_ID = 27;



$chrome_dir = '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome';
$chrome_ss_command = "$chrome_dir --headless --hide-scrollbars --disable-gpu --screenshot=".Page::ID($page_ID)->projectDir."/screen.png --window-size=1440,900 ".Page::ID($page_ID)->remoteUrl;



// Initiate Internalizator
$process = new BackgroundProcess($chrome_ss_command);
$process->run(Page::ID($page_ID)->projectDir."/screenshot.log", true);


while ( $process->isRunning() ) {
	sleep(1);
}