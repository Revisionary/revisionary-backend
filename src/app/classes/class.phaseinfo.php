<?php

class Phase {


	public static $phase_ID;
	public static $device_ID;
	public static $phaseInfo;

	public $remoteUrl;
	public $cachedUrl;

	public $phaseDir;
	public $phaseFileName = "index.html";
	public $phaseFile;
	public $phaseUri;

	public $logDir;
	public $logFileName = "internalize-process-php";
	public $logFileExtension = "log";
	public $logFile;

	public $internalizeCount;
	public $phaseStatus;



	// SETTERS:

	public function __construct() {


		$page_ID = self::$phaseInfo['page_ID'];
		$pageData = Page::ID($page_ID);


		$this->remoteUrl = $pageData->getInfo('page_url');
		$this->internalizeCount = self::$phaseInfo['phase_internalized'];


		// Phase directory
		$this->phaseDir = $this->getDir();
        $this->phaseFile = $this->phaseDir."/".$this->phaseFileName;


        // Paths
		$fullPath = $this->phaseDir."/";
		$fullPath = str_replace(cache."/projects/", "", $fullPath);


        // Set the phase cache directory URL
        $this->phaseUri = sites_url($fullPath, (substr($this->remoteUrl, 0, 8) == "https://"));
		$this->cachedUrl = $this->phaseUri.$this->phaseFileName."?v=$this->internalizeCount";


		// Log directory
		$this->logDir = $this->phaseDir."/logs";
		$this->logFile = $this->logDir."/".$this->logFileName.".".$this->logFileExtension;


		// Capture
		if (
			(file_exists(cache."/screenshots/device-".self::$device_ID.".jpg") && !file_exists($this->phaseFile)) ||
			$this->remoteUrl == "image"
		)
			$this->cachedUrl = site_url("serve-image/?device_ID=".self::$device_ID);


        // Set the phase status
        $this->phaseStatus = $this->getPhaseStatus();


    }


	// ID Setter
    public static function ID($phase_ID = null, $user_ID = null, $device_ID = null) {
		global $db;


		self::$device_ID = $device_ID;


	    // If specific phase
		if ( is_int($phase_ID) || is_numeric($phase_ID) ) {


			$phases = User::ID($user_ID)->getPhases();
			$phases = array_filter($phases, function($phaseFound) use ($phase_ID) {
				return $phaseFound['phase_ID'] == $phase_ID;
			});
			$phaseInfo = end($phases);


			if ( $phaseInfo ) {

				self::$phase_ID = $phase_ID;
				self::$phaseInfo = $phaseInfo;
				return new static;

			}


		}


	    // For the new phase
		if ($phase_ID == null || $phase_ID == "new") {

			self::$phase_ID = "new";
			return new static;

		}

		return false;

    }




	// GETTERS:

    // Get phase info
    public function getInfo($column = null) {

	    return $column == null ? self::$phaseInfo : self::$phaseInfo[$column];

    }


    // Get the phase download status
    public function getPhaseStatus() {


		// 0% - WAITING FOR THE QUEUE
		$process_status = [
			"status" => "waiting",
			"description" => "Waiting for the queue",
			"percentage" => 0
		];


		if (file_exists(cache."/screenshots/device-".self::$device_ID.".jpg") && !file_exists($this->phaseFile))
			return [
				"status" => "ready",
				"description" => "Ready! Loading the screenshot",
				"percentage" => 100
			];


		// IMAGE
		// 100% - READY
		if ($this->remoteUrl == "image" && $this->internalizeCount > 0)
			return [
				"status" => "ready",
				"description" => "Ready! Loading the design",
				"percentage" => 100
			];


		if (!file_exists($this->logDir))
			$process_status = [
				"status" => "Ready to Download",
				"description" => "Phase needs to be downloaded",
				"percentage" => 0
			];


		// 10% - VERSION IS DOWNLOADING
		if (
			file_exists($this->logDir."/browser.log")
		)
			$process_status = [
				"status" => "downloading-phase",
				"description" => "Downloading the page",
				"percentage" => 25
			];


		// 25% - VERSION IS DOWNLOADED
		if (
			file_exists($this->phaseFile)
		)
			$process_status = [
				"status" => "downloaded-phase",
				"description" => "Page is downloaded",
				"percentage" => 25
			];


		// 50% - UPDATING THE VERSION
		if (file_exists($this->logDir."/_html-filter.log"))
			$process_status = [
				"status" => "updating-html",
				"description" => "Updating the page",
				"percentage" => 50
			];


		// 70% - VERSION UPDATED
		if (file_exists($this->logDir."/html-filter.log"))
			$process_status = [
				"status" => "updated-html",
				"description" => "Page updated",
				"percentage" => 70
			];

		// 0% - HTML Filter Error
		if (file_exists($this->logDir."/__html-filter.log"))
			$process_status = [
				"status" => "updating-html-error",
				"description" => "The page couldn't be updated",
				"percentage" => 0
			];


		// 75% - FIXING THE STYLES
		if (file_exists($this->logDir."/_css-filter.log"))
			$process_status = [
				"status" => "updating-css",
				"description" => "Fixing the styles",
				"percentage" => 75
			];


		// 95% - STYLES ARE PERFECTED
		if (file_exists($this->logDir."/css-filter.log"))
			$process_status = [
				"status" => "updated-css",
				"description" => "Styles are perfected",
				"percentage" => 95
			];

		// 0% - CSS Filter Error
		if (file_exists($this->logDir."/__css-filter.log"))
			$process_status = [
				"status" => "updating-css-error",
				"description" => "The styles couldn't be fixed",
				"percentage" => 0
			];



		// 100% - READY
		if (
			file_exists($this->phaseFile) &&
			file_exists($this->logDir."/html-filter.log") &&
			file_exists($this->logDir."/css-filter.log")
		)
			$process_status = [
				"status" => "ready",
				"description" => "Ready! Loading the site",
				"percentage" => 100
			];


		return $process_status;

    }


    // Get phase directory
    public function getDir() {


		// // Set the project independent phase directory, for the projects later than 6 March 2020
		// if ( self::$phaseInfo['phase_created'] > "2020-03-05 23:07:50" )
		// 	return cache."/phases/phase-".self::$phase_ID;


		// Paths !!! OLD ONES
        $pagePath = Page::ID(self::$phaseInfo['page_ID'])->getDir();
		$phasePath = "phase-".self::$phase_ID;


        // Set the phase directory
        return "$pagePath/$phasePath";
    }


	// Get phase users
	public function getUsers($include_me = false) {
		global $db;


		$page_ID = self::$phaseInfo['page_ID'];
		$pageData = Page::ID($page_ID);


		$users = array();


		// Get the project users
		$users = array_merge($users, $pageData->getUsers($include_me));


		// Remove duplicates
		$users = array_unique($users);


		// Exclude myself
		if ( !$include_me && ($user_key = array_search(currentUserID(), $users)) !== false ) {
		    unset($users[$user_key]);
		}


		return $users;

	}




    // ACTIONS

    // Add a new phase
    public function addNew(
		int $page_ID, // The page_ID that new phase is belong to
		string $phase_type = "url",
		bool $internalized = false
    ) {
	    global $db, $log, $cache;



		// More DB Checks of arguments !!!



		// Add the phase
		$phase_ID = $db->insert('phases', array(
			"page_ID" => $page_ID,
			"phase_type" => $phase_type,
			"phase_internalized" => $internalized,
			"user_ID" => currentUserID()
		));



		// If phase added
		if ( is_int($phase_ID) ) {


			$phase_link = site_url('phase/'.$phase_ID);

			$pageData = Page::ID($page_ID);
			$page_name = $pageData->getInfo('page_name');

			$project_ID = $pageData->getInfo('project_ID');
			$projectData = Project::ID($project_ID);
			$project_name = " [".$projectData->getInfo('project_name')."]";


			// Site log
			$log->info("Phase #$phase_ID Added in $page_name.$project_name | Page #$page_ID | Project #$project_ID | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag(['phases', 'userload']);


			return $phase_ID;

		}


		return false;

	}



    // Edit a phase
    public function edit(
	    string $column,
	    $new_value
    ) {
	    global $db, $log, $cache;



		// More DB Checks of arguments !!!



		// Update the phase
		$db->where('phase_ID', self::$phase_ID);
		$phase_updated = $db->update('phases', array($column => $new_value));


		// Site log
		if ($phase_updated) $log->info("Phase #".self::$phase_ID." Updated: '$column => $new_value' | Page #".$this->getInfo('page_ID')." | User #".currentUserID());


		// INVALIDATE THE CACHES
		if ($phase_updated) $cache->deleteKeysByTag('phases');


		return $phase_updated;
    }



    // Remove a phase
    public function remove() {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!! (Do I have access to this phase?!)



		// Get the page info
    	$page_ID = self::$phaseInfo['page_ID'];
    	$pageData = Page::ID($page_ID);
    	$pageInfo = $pageData->getInfo();


    	// Get the project info
    	$project_ID = $pageInfo['project_ID'];
    	$projectData = Project::ID($project_ID);
    	$projectInfo = $projectData->getInfo();



    	if ( !$User->canAccess( self::$phase_ID, 'phase') ) return false;



		// VERSION REMOVAL
		$db->where('phase_ID', self::$phase_ID);
		$phase_removed = $db->delete('phases');



		// Delete the phase folder
		deleteDirectory( cache."/projects/project-$project_ID/page-$page_ID/phase-".self::$phase_ID."/" );



		// Delete the notifications if exists
		$db->where('object_type', 'phase');
		$db->where('object_ID', self::$phase_ID);
		$db->delete('notifications');



		// Site log
		if ($phase_removed) $log->info("Phase #".self::$phase_ID." Removed | Project Name: ".$projectData->getInfo('project_name')." | Project #".$projectData->getInfo('project_ID')." | Page Name: ".$pageData->getInfo('page_name')." | Page #".$this->getInfo('page_ID')."User #".currentUserID());


		// INVALIDATE THE CACHES
		if ($phase_removed) $cache->deleteKeysByTag(['phases', 'devices', 'pins', 'userload']);


		return $phase_removed;

    }

}
