<?php

class Queue {



	// SETTERS:

	public function __construct() {


    }




	// GETTERS:

    // Get all the works on queue
    public function works($queue_type = "") {
	    global $db, $logger;

	    $db->where('queue_status', 'waiting');
	    $db->orWhere('queue_status', 'working');

		$db->orderBy('queue_ID', 'asc');

	    $works = $db->get('queues');
		if ($works) {

		    $logger->info("Current Queue:", $works);
		    return $works;

		}

	    return false;
    }


    // Is the job ready?
    public function isReady($queue_ID) {
	    global $logger;

		// Allow only one or two job at a time !!!
	    $allowed_simultanious_job = 2;

		// Current Queue
	    $current_works = $this->works();
	    $count = count($current_works) - 1;


		// Check the latest two works
		foreach($current_works as $i => $work) {


			if ($work['queue_ID'] == $queue_ID) {

				$logger->info("Job $queue_ID is ready!");
				return true;

				break;
			}


			// No more check after last two
			if ($i == $allowed_simultanious_job - 1) break;

		}


		$logger->info("Job $queue_ID is not ready yet. Waiting $count job(s).");
	    return false;
    }


    // Queue Info
    public function info($queue_ID) {
	    global $db;

		$db->where('queue_ID', $queue_ID);
		return $db->getOne('queues');
    }





    // JOBS:

    // Add a new job to the queue
    public function new_job($queue_type, $phase_ID, $page_ID, $device_ID, $queue_message = "", $page_type = "url") {
	    global $db, $logger;


		$data = array(
			"queue_type" => $queue_type,
			"queue_object_ID" => $phase_ID,
			"queue_message" => $queue_message,
			"user_ID" => currentUserID()
		);
		$queue_ID = $db->insert('queues', $data);



		if($queue_ID) {

			$logger->info('Queue Added: '.$queue_ID, $data);


			// START THE PROCESS IF TYPE IS INTERNALIZE
			if ($queue_type == 'internalize') {


				// Initiate Internalizator
				$process = new Cocur\BackgroundProcess\BackgroundProcess(
					"php ".dir."/app/bgprocess/internalize.php $phase_ID $page_ID $device_ID ".session_id()." $queue_ID $page_type"
				);
				$process->run(Phase::ID($phase_ID)->logDir."/internalize-tasks-php.log", true);
				$process_ID = $process->getPid();


				// Add the PID to the queue
				$this->update_status($queue_ID, "waiting", "Waiting other works to be done.", $process_ID);

				return [
					'queue_ID' => $queue_ID,
					'process_ID' => $process_ID
				];

			}


			// START THE PROCESS IF TYPE IS INTERNALIZE
			if ($queue_type == 'screenshot') {


				// Initiate Internalizator
				$process = new Cocur\BackgroundProcess\BackgroundProcess(
					"php ".dir."/app/bgprocess/screenshot.php $phase_ID $page_ID $device_ID ".session_id()." $queue_ID $page_type"
				);
				$process->run(Phase::ID($phase_ID)->logDir."/screenshot-tasks-php.log", true);
				$process_ID = $process->getPid();


				// Add the PID to the queue
				$this->update_status($queue_ID, "waiting", "Waiting other works to be done.", $process_ID);

				return [
					'queue_ID' => $queue_ID,
					'process_ID' => $process_ID
				];

			}


			return $queue_ID;

		}

		$logger->error( $db->getLastError() );
		return false;
    }


    // Update the queue status
    public function update_status($queue_ID, $queue_status, $queue_message = "", $queue_PID = null) {
	    global $db, $logger;


		$data = array(
			'queue_status' 	=> $queue_status,
			'queue_message' => $queue_message
		);


		// If PID is written
		if ($queue_PID !== null)
			$data['queue_PID'] = $queue_PID;


		$db->where ('queue_ID', $queue_ID);
		if ($db->update('queues', $data)) {

			$logger->info($db->count.' Queue Updated:', $data);
			return true;

		}


		$logger->error( $db->getLastError() );
		return false;
    }

}