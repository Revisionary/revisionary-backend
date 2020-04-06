<?php

class Device {


	public static $device_ID;
	public static $deviceInfo;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($device_ID = null, $user_ID = null) {
	    global $db, $cache;


		// If specific device
		if ( is_int($device_ID) ) {


			$devices = User::ID($user_ID)->getDevices();
			$devices = array_filter($devices, function($deviceFound) use ($device_ID) {
				return $deviceFound['device_ID'] == $device_ID;
			});
			$deviceInfo = end($devices);


			if ( $deviceInfo ) {

				self::$device_ID = $device_ID;
				self::$deviceInfo = $deviceInfo;
				return new static;

			}


		}


	    // For the new page
		if ($device_ID == null || $device_ID == "new") {

			self::$device_ID = "new";
			return new static;

		}

		return false;

    }




	// GETTERS:

    // Get device info
    public function getInfo($column = null) {

	    return $column == null ? self::$deviceInfo : self::$deviceInfo[$column];

    }


	// Get the devices belong to specified page IDs
    public function getDevices($page_IDs) {
		global $db;


		// If no page ID specified
		if (!is_array($page_IDs) || count($page_IDs) == 0) return array();


		// Bring the screens
		$db->join("screens s", "s.screen_ID = d.screen_ID", "LEFT");


		// Bring the screen category info
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


		// Bring the phase info
		$db->join("phases v", "v.phase_ID = d.phase_ID", "LEFT");


		// Order by device ID
		$db->orderBy('d.device_ID', 'ASC');

		$db->where("v.page_ID", $page_IDs, "IN");
		return $db->get('devices d');
    }


    // Get device image
    public function getImage() {

		$phase_ID = $this->getInfo('phase_ID');
		$phase = Phase::ID($phase_ID);
		if (!$phase) return false;

		$page_ID = $phase->getInfo('page_ID');
		$page = Page::ID($page_ID);
		if (!$page) return false;

	    $image_dir = $page->getDir()."/phase-$phase_ID/screenshots/device-".self::$device_ID.".jpg";

		return $image_dir;

    }


    // Get device image URL
    public function getImageURL() {

		$image_path = "projects/project-".$this->getInfo('project_ID')."/page-".$this->getInfo('page_ID')."/phase-".$this->getInfo('phase_ID')."/screenshots/device-".$this->getInfo('device_ID').".jpg";
		

		// // Set the project independent phase directory, for the projects later than 6 March 2020
		// if ( $this->getInfo('device_created') > "2020-03-05 23:07:50" )
		// 	$image_path = "phases/phase-".$this->getInfo('phase_ID')."/screenshots/device-".$this->getInfo('device_ID').".jpg";


		$image_url = cache_url($image_path);

		return $image_url;

    }


	// Get page users
	public function getUsers($include_me = false) {
		global $db;


		$phase_ID = $this->getInfo('phase_ID');
		$page_ID = Phase::ID($phase_ID)->getInfo('page_ID');
		$pageData = Page::ID( $page_ID );


		// Get the page users
		$users = $pageData->getUsers($include_me);


		return $users;

	}



    // ACTIONS:

    // Add a new device
    public function addNew(
	    int $phase_ID,
	    array $screen_IDs = array(4),
    	int $device_width = null,
    	int $device_height = null,
    	bool $fromPage = false,
    	bool $fromPhase = false
    ) {
	    global $db, $log, $cache;



	    // DB Checks !!! (Page exists?, Screen exists?, etc.)



	    // If device width and height specified, make the screen ID 11 (custom)
	    if ($device_width != null && $device_height != null && count($screen_IDs) == 0)
	    	$screen_IDs = array(11);



		// START ADDING
		$first_device_ID = null;
		$screenCount = 0;
		$devices_list = "";
		$screens_list = "";
		foreach ($screen_IDs as $screen_ID) { $screenCount++;

			$screen_ID = intval($screen_ID);

			// Add the new page with the screen
			$device_ID = $db->insert('devices', array(
				"phase_ID" => $phase_ID,
				"screen_ID" => $screen_ID,
				"device_width" => count($screen_IDs) === 1 || $screen_ID == 11 ? $device_width : null,
				"device_height" => count($screen_IDs) === 1 || $screen_ID == 11 ? $device_height : null

			));

			// Stop it if error occurs
			if (!$device_ID) return false;


			$devices_list .= "$device_ID, ";
			$screens_list .= "$screen_ID, ";


			// Register the first device ID
			if ($screenCount == 1) $first_device_ID = $device_ID;


		}
		$devices_list = trim($devices_list, ", ");
		$screens_list = trim($screens_list, ", ");

		if ($first_device_ID) $log->info("Devices #$devices_list Added to: Phase #$phase_ID | Screens #$screens_list | User #".currentUserID());


		// INVALIDATE THE CACHES
		if ($first_device_ID) $cache->deleteKeysByTag('devices');


		// Notify the users
		if ($first_device_ID && !$fromPage) {

			$screen_ID = intval(reset($screen_IDs));
			$screenInfo = Screen::ID($screen_ID)->getInfo();
			$screen_width = $screen_ID == 11 ? $device_width : $screenInfo['screen_width'];
			$screen_height = $screen_ID == 11 ? $device_height : $screenInfo['screen_height'];



			// Get the users to notify
			$page_ID = Phase::ID($phase_ID)->getInfo('page_ID');



			// Get the users to notify
			$pageData = Page::ID($page_ID);
			$users = $pageData->getUsers();


			if ($fromPhase) {


				// Web notification
				Notify::ID($users)->web("new", "device", $first_device_ID, "new phase");


				// Email notification
				Notify::ID($users)->mail(
					getUserInfo()['fullName']." created a new phase on ".$pageData->getInfo('page_name')." [".$pageData->getInfo('project_name')."] page",
					getUserInfo()['fullName']." created a new phase on ".$pageData->getInfo('page_name')." [".$pageData->getInfo('project_name')."] page. <br><br>
					<b>Page URL</b>: <a href='".site_url('revise/'.$first_device_ID)."' target='_blank'>".site_url('revise/'.$first_device_ID)."</a>"
				);


			} else {


				// Web notification
				Notify::ID($users)->web("new", "device", $first_device_ID, $screenInfo['screen_cat_name']."(".$screen_width."x".$screen_height.")");


				// Email notification
				Notify::ID($users)->mail(
					getUserInfo()['fullName']." added a new screen on ".$pageData->getInfo('page_name')." [".$pageData->getInfo('project_name')."] page",
					getUserInfo()['fullName']." added a new screen: ".$screenInfo['screen_cat_name']."(".$screen_width."x".$screen_height.") on ".$pageData->getInfo('page_name')." [".$pageData->getInfo('project_name')."] page <br><br>
					<b>Page URL</b>: <a href='".site_url('revise/'.$first_device_ID)."' target='_blank'>".site_url('revise/'.$first_device_ID)."</a>"
				);


			}




		}



		// Return the first device ID
		return $first_device_ID;

	}


    // Remove a device
    public function remove() {
	    global $db, $log, $cache;


	    $page_ID = $this->getInfo('page_ID');
	    $pageDir = Page::ID($page_ID)->getDir();
    	$screenshot_file = "$pageDir/screenshots/device-".self::$device_ID.".jpg";


		// Remove screenshot if exists
		if ( file_exists($screenshot_file) ) unlink($screenshot_file);


		// Remove the device
		$db->where('device_ID', self::$device_ID);
		$deleted = $db->delete('devices');


		// Delete the notifications if exists
		$db->where('object_type', 'device');
		$db->where('object_ID', self::$device_ID);
		$db->delete('notifications');


		if ($deleted) $log->info("Device #".self::$device_ID." Deleted: Page #$page_ID | Screenshot '$screenshot_file' | User #".currentUserID());


		// INVALIDATE THE CACHES
		if ($deleted) $cache->deleteKeysByTag('devices');


		return $deleted;

    }

}