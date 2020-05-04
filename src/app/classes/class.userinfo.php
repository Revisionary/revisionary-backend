<?php

class User {


	// The user ID
	public static $user_ID;
	public static $userInfo;




	// SETTERS:
	public function __construct() {

	}



	// ID Setter
	public static function ID($user_ID = null, bool $nocache = false) {
		global $db, $cache;


		// Use current user ID if not specified
		$user_ID = $user_ID == null && userLoggedIn() ? currentUserID() : $user_ID;


		// Set the user ID
		if ( is_int($user_ID) ) {

			self::$userInfo = getUserInfoDB($user_ID, $nocache);
			if ( !self::$userInfo) return false;

			self::$user_ID = $user_ID;
			return new static;

		}


		// For the email users
		if ( filter_var($user_ID, FILTER_VALIDATE_EMAIL) ) {

			self::$user_ID = $user_ID;
			self::$userInfo = false;
			return new static;

		}


		// For the new user
		if ($user_ID == "new" || $user_ID === 0) {

			self::$user_ID = "new";
			self::$userInfo = false;
			return new static;

		}

		return false;

	}




	// GETTERS:

	// Get the user info
	public function getInfo($column = null) {
		global $db;


		// If email is given
		if ( !is_numeric(self::$user_ID) ) return false;


		return $column == null ? self::$userInfo : self::$userInfo[$column];
	}



	// Get the categories of this us
	public function getProjectCategories(string $catFilter = null, string $order = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_categories = $cache->get('project_categories:'.self::$user_ID);
		if ( $cached_categories !== false ) $categories = $cached_categories;
		else {


			// Exclude other users
			$db->where('cat.user_ID', self::$user_ID);


			// Default order
			$db->orderBy("cat.cat_order_number", "asc");


			// GET THE DATA
			$categories = $db->get("projects_categories cat", null, '');


			// Add the uncategorized item
			array_unshift($categories , array(
				'cat_ID' => 0,
				'cat_name' => 'Uncategorized',
				'cat_order_number' => 0
			));


			// Set the cache
			$cache->set('project_categories:'.self::$user_ID, $categories);


		}


		// Do filters
		if ($catFilter !== null) {

			$categories = array_filter($categories, function($catFound) use ($catFilter) {


				// Show all categories if no filter
				if ($catFilter == "" || $catFilter == "mine") return true;


				// Show only Uncategorized when on deleted and archived
				if ($catFilter == "archived" || $catFilter == "deleted" || $catFilter == "shared") return $catFound['cat_ID'] == 0;


				// Category Filter
				if ( $catFilter == permalink($catFound['cat_name']) ) return true;


				return false;

			});

		}


		// Do ordering
		if ($order !== null) {

			if ($order == "name") array_multisort(array_column($categories, 'cat_name'), SORT_ASC, $categories);

		}


		return $categories;
	}



	// Get all the projects that user can access
	public function getProjects(int $project_cat_ID = null, string $catFilter = null, string $order = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_projects = $cache->get('projects:'.self::$user_ID);
		if ( $cached_projects !== false && !$nocache ) $projects = $cached_projects;
		else {


			// Get project IDs
			$pages = $this->getPages();
			$projectIDs = array_unique(array_column($pages, 'project_ID'));


			// Bring project share info
			$db->join("shares s", "p.project_ID = s.shared_object_ID", "LEFT");
			$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
			$db->joinWhere("shares s", "s.share_type", "project");


			// Bring the user info
			$db->join("users u", "p.user_ID = u.user_ID", "LEFT");


			// Bring the category connection
			$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_ID", "LEFT");


			// Bring the category info
			$db->join("projects_categories cat", "cat_connect.cat_ID = cat.cat_ID", "LEFT");
			$db->joinWhere("projects_categories cat", "cat.user_ID", self::$user_ID);


			// Bring the order info
			$db->join("projects_order o", "o.project_ID = p.project_ID", "LEFT");
			$db->joinWhere("projects_order o", "o.user_ID", currentUserID());


			// Check access if not admin
			if ( self::$userInfo['user_level_ID'] != 1 ) {

				// If shared pages exist
				$find_in = "";
				if ( count($projectIDs) > 0 ) {

					$project_IDs = join("','", $projectIDs);
					$find_in = "OR p.project_ID IN ('$project_IDs')";

				}


				$db->where("(
					p.user_ID = ".self::$user_ID."
					OR s.share_to = ".self::$user_ID."
					OR s.share_to = '".self::$userInfo['user_email']."'
					".$find_in."
				)");

			}


			// Default Sorting
			$db->orderBy("order_number", "asc");
			$db->orderBy("s.share_ID", "desc");
			$db->orderBy("cat.cat_name", "asc");
			$db->orderBy("p.project_name", "asc");


			// GET THE DATA
			$projects = $db->get(
				'projects p',
				null,
				'
					p.project_ID as project_ID,
					p.project_name,
					p.project_created,
					p.project_archived,
					p.project_deleted,
					p.project_image_device_ID,
					p.user_ID as user_ID,
					o.order_ID,
					o.order_number,
					cat.cat_ID,
					cat.cat_name,
					cat.cat_order_number,
					u.user_ID,
					u.user_first_name,
					u.user_last_name,
					u.user_email,
					u.user_picture,
					u.user_level_ID,
					s.share_ID,
					s.share_to as share_to,
					s.sharer_user_ID as sharer_user_ID
				'
			);


			// Set the cache
			$cache->set('projects:'.self::$user_ID, $projects);


		}


		// Do filters
		if ($project_cat_ID !== null && $catFilter != "archived" && $catFilter != "deleted" && $catFilter != "shared") {

			$projects = array_filter($projects, function($projectFound) use ($project_cat_ID) {

				// If zero
				if ($project_cat_ID === 0) return $projectFound['cat_ID'] == null || $projectFound['cat_ID'] == $project_cat_ID;
				return $projectFound['cat_ID'] == $project_cat_ID;

			});

		}

		if ($catFilter !== null) {

			// Archive and delete filters
			$projects = array_filter($projects, function($projectFound) use ($catFilter) {

				if ($catFilter == "archived") return $projectFound['project_archived'] == 1 && $projectFound['project_deleted'] != 1;
				if ($catFilter == "deleted") return $projectFound['project_deleted'] == 1;
				return $projectFound['project_archived'] == 0 && $projectFound['project_deleted'] == 0;

			});


			// Mine and shared filters
			$projects = array_filter($projects, function($projectFound) use ($catFilter) {

				if ($catFilter == "mine") return $projectFound['user_ID'] == self::$user_ID;
				if ($catFilter == "shared") return $projectFound['user_ID'] != self::$user_ID;
				return true;

			});

		}


		// Do ordering
		if ($order !== null) {

			if ($order == "name") array_multisort(array_column($projects, 'project_name'), SORT_ASC, $projects);
			if ($order == "date") array_multisort(array_column($projects, 'project_created'), SORT_DESC, $projects);

		}


		// Return the data
		return $projects;


	}



	// Get the categories of page
	public function getPageCategories(int $project_ID = null, string $catFilter = null, string $order = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_categories = $cache->get('page_categories:'.self::$user_ID);
		if ( $cached_categories !== false ) $categories = $cached_categories;
		else {


			// Default order
			$db->orderBy("cat.cat_order_number", "asc");


			// GET THE DATA
			$categories = $db->get("pages_categories cat", null, '');


			// Add the uncategorized item
			array_unshift($categories , array(
				'cat_ID' => 0,
				'cat_name' => 'Uncategorized',
				'cat_order_number' => 0,
				'project_ID' => 0
			));


			// Set the cache
			$cache->set('page_categories:'.self::$user_ID, $categories);


		}


		// Do filters
		if ($project_ID !== null) {

			$categories = array_filter($categories, function($pageCatFound) use ($project_ID) {

				return $pageCatFound['project_ID'] == $project_ID || $pageCatFound['project_ID'] == 0;

			});

		}

		if ($catFilter !== null) {

			$categories = array_filter($categories, function($catFound) use ($catFilter) {


				// Show all categories if no filter
				if ($catFilter == "" || $catFilter == "mine") return true;


				// Show only Uncategorized when on deleted and archived
				if ($catFilter == "archived" || $catFilter == "deleted" || $catFilter == "shared") return $catFound['cat_ID'] == 0;


				// Category Filter
				if ( $catFilter == permalink($catFound['cat_name']) ) return true;


				return false;

			});

		}


		// Do ordering
		if ($order !== null) {

			if ($order == "name") array_multisort(array_column($categories, 'cat_name'), SORT_ASC, $categories);

		}


		return $categories;
	}



	// Get all the pages that user can access
	public function getPages(int $project_ID = null, int $page_cat_ID = null, string $catFilter = null, string $order = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_pages = $cache->get('pages:'.self::$user_ID);
		if ( $cached_pages !== false && !$nocache ) $pages = $cached_pages;
		else {


			// Bring the category info
			$db->join("pages_categories cat", "cat.cat_ID = p.cat_ID", "LEFT");


			// Bring the project info
			$db->join("projects pr", "pr.project_ID = p.project_ID", "LEFT");


			// Bring the user info
			$db->join("users u", "p.user_ID = u.user_ID", "LEFT");


			// Bring page share info
			$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
			$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
			$db->joinWhere("shares s", "s.share_type", "page");


			// Bring project share info
			$db->join("shares sp", "p.project_ID = sp.shared_object_ID", "LEFT");
			$db->joinWhere("shares sp", "sp.share_type", "project");


			// Check access if not admin
			if ( self::$userInfo['user_level_ID'] != 1 ) {

				$db->where("(
					p.user_ID = ".self::$user_ID."
					OR s.share_to = ".self::$user_ID."
					OR s.share_to = '".self::$userInfo['user_email']."'
					OR pr.user_ID = ".self::$user_ID."
					OR sp.share_to = ".self::$user_ID."
					OR sp.share_to = '".self::$userInfo['user_email']."'
				)");

			}


			// Default Sorting
			$db->orderBy("order_number", "asc");
			$db->orderBy("s.share_ID", "desc");
			$db->orderBy("cat.cat_name", "asc");
			$db->orderBy("p.page_name", "asc");


			// GET THE DATA
			$pages = $db->get(
				'pages p',
				null,
				'
					p.page_ID as page_ID,
					p.page_name,
					p.page_url,
					p.page_user,
					p.page_created,
					p.page_modified,
					p.page_archived,
					p.page_deleted,
					p.order_number,
					p.user_ID as user_ID,
					cat.cat_ID,
					cat.cat_name,
					cat.cat_order_number,
					p.project_ID,
					pr.project_name,
					pr.project_created,
					pr.project_archived,
					pr.project_deleted,
					pr.project_image_device_ID,
					u.user_ID,
					u.user_first_name,
					u.user_last_name,
					u.user_email,
					u.user_picture,
					u.user_level_ID,
					s.share_ID,
					s.share_to as share_to,
					s.sharer_user_ID as sharer_user_ID
				'
			);


			// Set the cache
			$cache->set('pages:'.self::$user_ID, $pages);


		}


		// Do filters
		if ($project_ID !== null) {

			$pages = array_filter($pages, function($pageFound) use ($project_ID) {

				return $pageFound['project_ID'] == $project_ID;

			});

		}

		if ($page_cat_ID !== null && $catFilter != "archived" && $catFilter != "deleted" && $catFilter != "shared") {

			$pages = array_filter($pages, function($pageFound) use ($page_cat_ID) {

				// If zero
				if ($page_cat_ID === 0) return $pageFound['cat_ID'] == null || $pageFound['cat_ID'] == $page_cat_ID;
				return $pageFound['cat_ID'] == $page_cat_ID;

			});

		}

		if ($catFilter !== null) {

			// Archive and delete filters
			$pages = array_filter($pages, function($pageFound) use ($catFilter) {

				if ($catFilter == "archived") return $pageFound['page_archived'] == 1 && $pageFound['page_deleted'] != 1;
				if ($catFilter == "deleted") return $pageFound['page_deleted'] == 1;
				return $pageFound['page_archived'] == 0 && $pageFound['page_deleted'] == 0;

			});


			// Mine and shared filters
			$pages = array_filter($pages, function($pageFound) use ($catFilter) {

				if ($catFilter == "mine") return $pageFound['user_ID'] == self::$user_ID;
				if ($catFilter == "shared") return $pageFound['user_ID'] != self::$user_ID;
				return true;

			});

		}


		// BUG !!! - DB shows duplicate pages because of the project shares join
		$pages = array_unique($pages, SORT_REGULAR);


		// Do ordering
		if ($order !== null) {

			if ($order == "name") array_multisort(array_column($pages, 'page_name'), SORT_ASC, $pages);
			if ($order == "date") array_multisort(array_column($pages, 'page_created'), SORT_DESC, $pages);

		}


		// Return the data
		return $pages;


	}



	// Get all the phases that user can access
	public function getPhases(int $page_ID = null, int $project_ID = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_phases = $cache->get('phases:'.self::$user_ID);
		if ( $cached_phases !== false && !$nocache ) $phases = $cached_phases;
		else {


			// Get page IDs
			$pages = $this->getPages();
			if ( !count($pages) ) return array();
			$pageIDs = array_unique(array_column($pages, 'page_ID'));



			// Filter the phases by page_IDs
			$db->where('ph.page_ID', $pageIDs, 'IN');


			// Bring page info
			$db->join("pages p", "p.page_ID = ph.page_ID", "LEFT");


			// Bring project info
			$db->join("projects pr", "p.project_ID = pr.project_ID", "LEFT");


			// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
			$phases = $db->get('phases ph');


			// Set the cache
			$cache->set('phases:'.self::$user_ID, $phases);


		}


		// Do filters
		if ($page_ID !== null) {

			$phases = array_filter($phases, function($phaseFound) use ($page_ID) {

				return $phaseFound['page_ID'] == $page_ID;

			});

		}

		if ($project_ID !== null) {

			$phases = array_filter($phases, function($phaseFound) use ($project_ID) {

				return $phaseFound['project_ID'] == $project_ID;

			});

		}


		// Return the data
		return $phases;


	}



	// Get all the devices that user can access
	public function getDevices(int $phase_ID = null, int $page_ID = null, int $project_ID = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_devices = $cache->get('devices:'.self::$user_ID);
		if ( $cached_devices !== false && !$nocache ) $devices = $cached_devices;
		else {


			// Get all page IDs
			$pages = $this->getPages();
			if ( !count($pages) ) return array();
			$pageIDs = array_unique(array_column($pages, 'page_ID'));


			// Bring the phase info
			$db->join("phases v", "v.phase_ID = d.phase_ID", "LEFT");


			// Filter the devices by page_IDs
			$db->where("v.page_ID", $pageIDs, "IN");


			// Bring the page info
			$db->join("pages pg", "v.page_ID = pg.page_ID", "LEFT");


			// Bring the page info
			$db->join("projects pr", "pg.project_ID = pr.project_ID", "LEFT");


			// Bring the screens
			$db->join("screens s", "s.screen_ID = d.screen_ID", "LEFT");


			// Bring the screen category info
			$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


			// Order by device ID
			$db->orderBy('d.device_ID', 'ASC');


			// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
			$devices = $db->get('devices d');


			// Set the cache
			$cache->set('devices:'.self::$user_ID, $devices);


		}


		// Do filters
		if ($phase_ID !== null) {

			$devices = array_filter($devices, function($deviceFound) use ($phase_ID) {

				return $deviceFound['phase_ID'] == $phase_ID;

			});

		}

		if ($page_ID !== null) {

			$devices = array_filter($devices, function($deviceFound) use ($page_ID) {

				return $deviceFound['page_ID'] == $page_ID;

			});

		}

		if ($project_ID !== null) {

			$devices = array_filter($devices, function($deviceFound) use ($project_ID) {

				return $deviceFound['project_ID'] == $project_ID;

			});

		}


		// Return the data
		return $devices;


	}



	// Get all the pins that user can access
	public function getPins(int $phase_ID = null, int $device_ID = null, int $page_ID = null, int $project_ID = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cache_name = "pins:".self::$user_ID;
		if ($phase_ID !== null) $cache_name .= ":phase:".$phase_ID;
		if ($device_ID !== null) $cache_name .= ":device:".$device_ID;
		if ($page_ID !== null) $cache_name .= ":page:".$page_ID;
		if ($project_ID !== null) $cache_name .= ":project:".$project_ID;


		$cached_pins = $cache->get($cache_name);
		if ( $cached_pins !== false && !$nocache ) $pins = $cached_pins;
		else {


			// Get all phase IDs
			if ($phase_ID === null) {

				$phases = $this->getPhases();
				if ( !count($phases) ) return array();
				$phaseIDs = array_unique(array_column($phases, 'phase_ID'));


				// Filter the pins by phase_IDs
				$db->where("pin.phase_ID", $phaseIDs, "IN");

			}


			// Phase filter
			if ($phase_ID !== null) $db->where('pin.phase_ID', $phase_ID);


			// Hide device specific pins
			if ($device_ID !== null) $db->where ("(pin.device_ID IS NULL or (pin.device_ID IS NOT NULL and pin.device_ID = $device_ID))");


			// Page filter
			if ($page_ID !== null) $db->where('page.page_ID', $page_ID);


			// Project filter
			if ($project_ID !== null) $db->where('project.project_ID', $project_ID);


			// Bring the phase info
			$db->join("phases ph", "ph.phase_ID = pin.phase_ID", "LEFT");


			// Bring the page info
			$db->join("pages page", "ph.page_ID = page.page_ID", "LEFT");


			// Bring the project info
			$db->join("projects project", "page.project_ID = project.project_ID", "LEFT");


			// Bring the user info
			$db->join("users u", "pin.user_ID = u.user_ID", "LEFT");


			// Hide private pins to other people
			$db->where ("(pin.user_ID = ".self::$user_ID." or (pin.user_ID != ".self::$user_ID." and pin.pin_private = 0))");


			// Default Sorting
			$db->orderBy("pin.pin_ID", "asc");


			// GET THE DATA
			$pins = $db->get('pins pin', null, '
				pin.pin_ID,
				pin.pin_complete,
				pin.pin_private,
				pin.pin_type,
				pin.pin_element_index,
				pin.pin_modification_type,
				pin.pin_modification,
				pin.pin_modification_original,
				pin.pin_created,
				pin.pin_css,
				pin.pin_x,
				pin.pin_y,
				u.user_ID,
				u.user_first_name,
				u.user_last_name,
				u.user_email,
				u.user_picture,
				project.project_ID,
				page.page_ID,
				pin.phase_ID,
				pin.device_ID
			');


			// Set the cache
			$cache->set($cache_name, $pins);


		}


		// // Do filters
		// if ($phase_ID !== null) {

		// 	$pins = array_filter($pins, function($pinFound) use ($phase_ID) {
		// 		return $pinFound['phase_ID'] == $phase_ID;
		// 	});

		// }

		// if ($device_ID !== null) {

		// 	$pins = array_filter($pins, function($pinFound) use ($device_ID) {
		// 		return $pinFound['device_ID'] == null || $pinFound['device_ID'] == $device_ID;
		// 	});

		// }


		// Return the data
		return $pins;


	}



	// Get notifications
	public function getNotifications($offset = 0, $limit = 10) {
		global $db;


		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', self::$user_ID);
		$db->orderBy("notification_time", "DESC");
		$notifications = $db->withTotalCount()->get("notifications n", array($offset, $limit));


		return array(
			'notifications' => $notifications,
			'totalCount' => $db->totalCount
		);
	}



	// Get new notifications
	public function getNewNotifications() {
		global $db;


		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', self::$user_ID);
		$db->where('con.notification_read', 0);
		$db->orderBy("notification_time", "DESC");
		$notifications = $db->withTotalCount()->get("notifications n");


		return array(
			'notifications' => $notifications,
			'totalCount' => $db->totalCount
		);
	}



	// Get the notifications HTML
	public function getNotificationsHTML(int $offset = 0) {
		global $db, $log;


		$notificationHTML = "";


		// Get only new notifications
		$newNotifications = $this->getNewNotifications()['notifications'];
		$newNotificationsCount = count($newNotifications);


		// All notifications
		$notificationData = $this->getNotifications($offset);
		$notifications = $notificationData['notifications'];
		$totalNotifications = $notificationData['totalCount'];


		// Merge all the notifications
		$notifications = array_unique(array_merge($newNotifications, $notifications), SORT_REGULAR);
		$notificationsCount = count($notifications);


		// List the notifications
		$realNotificationsCount = 0;
		$errorCount = 0;
		foreach ($notifications as $notification) {
			$notificationRow = "";

			$notification_ID = $notification['notification_ID'];
			$notification_type = $notification['notification_type'];
			$notificationNew = $notification['notification_read'] == 0;
			$notificationContent = $notification['notification'];


			$sender_ID = $notification['sender_user_ID'];
			$senderInfo = getUserInfo($sender_ID);

			// Skip if the user not found
			if (!$senderInfo) {

				$notificationHTML .= '<li data-offset="'.$offset.'" class="'.($notificationNew ? "new" : "").' error" data-error="user-'.$sender_ID.'-not-found" data-type="notification" data-id="'.$notification_ID.'">User not exist</li>';

				$errorCount++;
				continue;
			}

			$sender_full_name = $senderInfo['fullName'];


			// Object Info
			$object_ID = is_numeric($notification['object_ID']) ? intval($notification['object_ID']) : $notification['object_ID'];
			$object_type = $notification['object_type'];
			$object_data = ucfirst($object_type)::ID($object_ID, self::$user_ID);


			if ($notification_type != "unshare") {


				// Skip if the object not found
				if (!$object_data) {

					$notificationHTML .= "<li data-offset='$offset' data-id='$notification_ID' data-type='notification' data-error='$object_type-$object_ID-not-found' data-notification-type='$notification_type' class='".($notificationNew ? "new" : "")." error'>$object_type not exist</li> \n";

					$errorCount++;
					continue;

				}

				$object_name = $object_type != "device" && $object_type != "pin" ? $object_data->getInfo($object_type.'_name') : "";
				$object_link = site_url("$object_type/$object_ID");


			}


			// Open the list
			$notificationRow = "<li data-offset='$offset' data-id='$notification_ID' data-type='notification' class='".($notificationNew ? "new" : "")."'>";

			// Add user info
			$notificationRow .= '

				<div class="wrap xl-table xl-middle">
					<div class="col profile-image">

						<picture class="profile-picture" '.$senderInfo['printPicture'].'>
							<span>'.$senderInfo['nameAbbr'].'</span>
						</picture>

					</div>
					<div class="col content">
			';


			//$objectFound = $object_data ? "yes" : "no";
			// $notificationHTML .= "<li data-offset='$offset' data-id='$notification_ID' data-type='notification'>#$notification_ID($realNotificationsCount) | Type: $notification_type | Object Type: $object_type($object_ID) | Object Found: $objectFound | New: $notificationNew | Content: $notificationContent</li> \n";
			// continue;


			// NOTIFICATION TYPES
			if ($notification_type == "text") {



				// Notification Content
				$notificationRow .= "
					<div class='sender'>$sender_full_name ".$notification['notification']."</div>

					<div class='date'>".timeago($notification['notification_time'])."</div>
				";



			} elseif ($notification_type == "share") {



				$project_name = "";
				if ($object_type == "page") {

					$project_ID = $object_data->getInfo('project_ID');
					$project_name = "[".Project::ID($project_ID)->getInfo('project_name')."]";

				}


				// Notification Content
				$notificationRow .= "

					<div class='sender'>$sender_full_name shared a <b>$object_type</b> with you:</div>
					<div class='notif-text'><a href='$object_link'><b>$object_name</b>$project_name</a></div>

					<div class='date'>".timeago($notification['notification_time'])."</div>

				";



			} elseif ($notification_type == "unshare") {



				// Notification Content
				$notificationRow .= "

					<div class='sender'>$sender_full_name unshared a $object_type from you:</div>
					<div class='notif-text'><b>".$notification['notification']."</b></div>

					<div class='date'>".timeago($notification['notification_time'])."</div>

				";



			} elseif ($object_type == "pin") {


				$pin_type = $object_data->getInfo('pin_type');
				$pin_complete = $object_data->getInfo('pin_complete');
				$phase_ID = $object_data->getInfo('phase_ID');
				$device_ID = $object_data->getInfo('device_ID');
				$phaseData = Phase::ID($phase_ID);

				// Skip if the phase not found
				if (!$phaseData) {

					$notificationHTML .= '<li data-offset="'.$offset.'" data-error="phase-'.$phase_ID.'-not-found" class="'.($notificationNew ? "new" : "").' error" data-type="notification" data-id="'.$notification_ID.'">Phase not exist</li>';

					$errorCount++;
					continue;
				}

				$page_ID = $phaseData->getInfo('page_ID');
				$page_data = Page::ID($page_ID);

				// Skip if the page not found
				if (!$page_data) {

					$notificationHTML .= '<li data-offset="'.$offset.'" data-error="page-'.$page_ID.'-not-found" class="'.($notificationNew ? "new" : "").' error" data-type="notification" data-id="'.$notification_ID.'">Page not exist</li>';

					$errorCount++;
					continue;
				}

				$page_name = $page_data->getInfo('page_name');
				$project_ID = $page_data->getInfo('project_ID');
				$project_data = Project::ID($project_ID);

				// Skip if the page not found
				if (!$project_data) {

					$notificationHTML .= '<li data-offset="'.$offset.'" data-error="project-'.$project_ID.'-not-found" class="'.($notificationNew ? "new" : "").' error" data-type="notification" data-id="'.$notification_ID.'">Project not exist</li>';

					$errorCount++;
					continue;
				}

				$project_name = $project_data->getInfo('project_name');


				$object_link = site_url("phase/$phase_ID#$object_ID");
				if ( $device_ID != null ) $object_link = site_url("revise/$device_ID#$object_ID");



				if ($notification_type == "complete") {


					// Notification Content
					$notificationRow .= "

						<div class='sender'>$sender_full_name completed a <b>$pin_type pin</b>:</div>
						<div class='wrap xl-table xl-middle'>
							<div class='col pin' style='width: 30px;'>
								<a href='$object_link' data-go-pin='$object_ID'><pin class='small' data-pin-complete='1' data-pin-type='$pin_type'>$notificationContent</pin></a>
							</div>
							<div class='col notif-text'>
								<a href='$object_link' data-go-pin='$object_ID'><b>".$page_name."[".$project_name."]</b></a>
							</div>
						</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} elseif ($notification_type == "incomplete") {


					// Notification Content
					$notificationRow .= "

						<div class='sender'>$sender_full_name marked a pin <b>incomplete</b>:</div>
						<div class='wrap xl-table xl-middle'>
							<div class='col pin' style='width: 30px;'>
								<a href='$object_link' data-go-pin='$object_ID'><pin class='small' data-pin-complete='0' data-pin-type='$pin_type'>$notificationContent</pin></a>
							</div>
							<div class='col notif-text'>
								<a href='$object_link' data-go-pin='$object_ID'><b>".$page_name."[".$project_name."]</b></a>
							</div>
						</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} elseif ($notification_type == "comment") {


					// Notification Content
					$notificationRow .= "

						<div class='sender'>$sender_full_name wrote on a <b>$pin_type pin</b>:</div>
						<div class='notif-text'><a href='$object_link' class='comment' data-go-pin='$object_ID'>$notificationContent</a></div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} elseif ($notification_type == "new") { // New Pin

					$content = "";
					$style = "";
					$comment = "";

					if ( !empty($object_data->getInfo('pin_modification')) )
						$content = "<li><a href='$object_link' data-go-pin='$object_ID'> Content</a></li>";

					if ( !empty($object_data->getInfo('pin_css')) )
						$style = "<li><a href='$object_link' data-go-pin='$object_ID'> Style</a></li>";


					$comments = $object_data->comments();
					if ( count($comments) > 0 )
						$comment = "<li><a href='$object_link' data-go-pin='$object_ID'> Comment</a></li>";


					// Notification Content
					$notificationRow .= "

						<div class='sender'>$sender_full_name added a new <b>$pin_type pin</b>:</div>
						<div class='notif-text'>
							<a href='$object_link' data-go-pin='$object_ID'><b>".$page_name."[".$project_name."]</b></a><br>
							<ul>
								$content
								$style
								$comment
							</ul>
						</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				} else {

					$notificationRow .= "Notification";

				}



			} elseif ($notification_type == "new") {




				if ($object_type == "page") {


					$project_ID = $object_data->getInfo('project_ID');
					$project_data = Project::ID($project_ID);

					// Skip if the page not found
					if (!$project_data) {

						$notificationHTML .= '<li data-offset="'.$offset.'" data-error="project-'.$project_ID.'-not-found" class="'.($notificationNew ? "new" : "").' error" data-type="notification" data-id="'.$notification_ID.'">Project not exist</li>';

						continue;
					}

					$project_name = $project_data->getInfo('project_name');


					// Notification Content
					$notificationRow .= "

						<div class='sender'>$sender_full_name added a <b>new page</b>:</div>
						<div class='notif-text'><a href='$object_link'><b>$object_name</b>[$project_name]</a></div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}


				else if ($object_type == "device") {


					$page_ID = $object_data->getInfo('page_ID');
					$page_data = Page::ID($page_ID);

					// Skip if the page not found
					if (!$page_data) {

						$notificationHTML .= '<li data-offset="'.$offset.'" data-error="page-'.$page_ID.'-not-found" class="'.($notificationNew ? "new" : "").' error" data-type="notification" data-id="'.$notification_ID.'">Page not exist</li>';

						$errorCount++;
						continue;
					}

					$page_name = $page_data->getInfo('page_name');
					$project_ID = $page_data->getInfo('project_ID');
					$project_data = Project::ID($project_ID);

					// Skip if the project not found
					if (!$project_data) {

						$notificationRow = '<li data-offset="'.$offset.'" data-error="project-'.$project_ID.'-not-found" class="'.($notificationNew ? "new" : "").' error" data-type="notification" data-id="'.$notification_ID.'">Project not exist</li>';

						$errorCount++;
						continue;
					}

					$project_name = $project_data->getInfo('project_name');


					$title = "$sender_full_name added a <b>new screen</b>:";
					$content = "<a href='$object_link'><b>$notificationContent</b></a> <div>on <a href='$object_link'>".$page_name."[".$project_name."]</div></a>";

					if ($notificationContent == "new phase") {

						$title = "$sender_full_name created a <b>new phase</b>:";
						$content = "on <a href='$object_link'><b>".$page_name."[".$project_name."]</b></a>";

					}



					// Notification Content
					$notificationRow .= "

						<div class='sender'>$title</div>
						<div class='notif-text'>$content</div>

						<div class='date'>".timeago($notification['notification_time'])."</div>

					";


				}


				else {

					$notificationRow .= "Notification";

				}



			} else {

				$notificationHTML .= "Notification";

			}


			// Close the list
			$notificationRow .= "</div></div></li> \n";


			// Add to the real list
			$notificationHTML .= $notificationRow;


			// Count the shown notifications
			$realNotificationsCount++;


		}


		// Load more link
		if ( ($offset + $notificationsCount) < $totalNotifications ) {
			$notificationHTML .= '<li class="more-notifications"><a href="#" data-offset="'.($offset + $notificationsCount).'">Load older notifications <i class="fa fa-level-down-alt"></i></a></li>';
		}



		// If there is no notifications
		if ($realNotificationsCount == 0 && $offset == 0) {

			$notificationHTML .= "<li>There's nothing to mention now. <br>Your notifications will be here.</li>";

		}


		return $notificationHTML;

	}


	// Get count
	public function getNotificationsCount() {
		global $db;

		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', self::$user_ID);
		$db->where('con.notification_read', 0);

		$count = $db->getValue("notifications n", "count(n.notification_ID)");

		return $count;

	}



	// Get all the screens
	public function getScreens(int $screen_cat_ID = null, bool $nocache = false) {
		global $db, $cache;


		// CHECK THE CACHE FIRST
		$cached_screens = $cache->get('screens');
		if ( $cached_screens !== false && !$nocache ) return $cached_screens;


		// Bring the screen categories
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


		// Order
		$db->orderBy('s_cat.screen_cat_order', 'asc');
		$db->orderBy(' s.screen_order', 'asc');


		// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
		$screens = $db->get('screens s');


		// Set the cache
		$cache->set('screens', $screens, 0);


		// Return the data
		return $screens;


	}



	// Get screen data
	public function getScreenData() {
		global $db;


		$screens = $this->getScreens();


		// Prepare the screens data
		$screen_data = [];
		foreach ($screens as $screen) {

			if ( !isset($screen_data[$screen['screen_cat_ID']]['screens']) ) {

				$screen_data[$screen['screen_cat_ID']] = array(
					'screen_cat_icon' => $screen['screen_cat_icon'],
					'screen_cat_name' => $screen['screen_cat_name'],
					'screens' => array(),
				);

			}

			$screen_data[$screen['screen_cat_ID']]['screens'][$screen["screen_ID"]] = $screen;

		}


		return $screen_data;
	}



	// Can access?
	public function canAccess(
		int $object_ID,
		string $object_type
	) {


		// Check the object types
		if (
			$object_type != "project"
			&& $object_type != "page"
			&& $object_type != "phase"
			&& $object_type != "device"
			&& $object_type != "pin"
		) return false;



		// Capitalize for the class names
		$object_type = ucfirst($object_type);



		if (class_exists($object_type)) {

			$typeApi = $object_type::ID($object_ID, self::$user_ID);

			if (method_exists($typeApi, 'getUsers') && $typeApi) {


				// Do the action
				$users = $typeApi->getUsers(true);


			} else return false;

		} else return false;



		// Check whether or not user can access
		return in_array(self::$user_ID, $users) || getUserInfo()['userLevelID'] == 1;

	}




	// ACTIONS:

	// Add new user
	public function addNew(
		string $user_email,
		string $user_full_name,
		string $user_password,
		int $trial_level_ID = null
	) {
		global $db, $log;


		// Parse the full name
		$firstName = $user_full_name;
		$lastName = "";
		$parsedFullName = explode(' ', $user_full_name);
		if (count($parsedFullName) > 1) {
			$firstName = str_replace(' '.end($parsedFullName), '', $user_full_name);
			$lastName = end($parsedFullName);
		}


		// Username check !!! Performance issue?
		$user_name = permalink($user_full_name);
		$i = 0;
		while( !checkAvailableUserName($user_name) ) { $i++;

			// Clean the existing numbers
			$user_name = explode('_', $user_name)[0]."_".$i;

		}


		// Prepare the user data
		$new_user_data = array(
			'user_name' => $user_name,
			'user_email' => $user_email,
			'user_first_name' => $firstName,
			'user_last_name' => $lastName,
			'user_password' => password_hash($user_password, PASSWORD_DEFAULT),
			'user_IP' => get_client_ip(),
			'user_level_ID' => 2 // Free one by default
		);


		// Signup with trial
		if ($trial_level_ID) {

			$new_user_data['trial_started_for'] = $trial_level_ID;
			$new_user_data['trial_expire_date'] = currentTimeStamp('+1 week');

		}


		// Add to the DB
		$user_ID = $db->insert('users', $new_user_data);


		// If successful
		if ($user_ID) {


			// Site log
			$log->info("User #$user_ID Added: $user_name($user_full_name) | Email: $user_email | User Level ID 2");


			// Send a welcome email
			Notify::ID($user_ID)->mail(
				'Welcome to Revisionary App!',
				"Hi $firstName, <br><br>

				Thanks for joining us. You can now start revising websites. :) <br><br>

				<a href='".site_url()."' target='_blank'>".site_url()."</a>",
				true // Important
			);


			// Notify the admin
			Notify::ID(1)->mail(
				"New user registration by $user_full_name",
				"
				<b>User Information</b> <br>
				Email: $user_email <br>
				Full Name: $user_full_name <br>
				Username: $user_name
				"
			);

		}


		// Return the user ID
		return $user_ID;
	}



	// Edit a user
	public function edit(
		string $column,
		$new_value
	) {
		global $db, $log, $cache;



		// More DB Checks of arguments !!!



		// Update the user
		$db->where('user_ID', self::$user_ID);
		$user_updated = $db->update('users', array($column => $new_value));


		// Site log
		if ($user_updated) $log->info("User #".self::$user_ID." Updated: '$column => $new_value'");


		// INVALIDATE THE CACHE
		if ($user_updated) $cache->delete('user:'.self::$user_ID);


		return $user_updated;
	}


	// Reorder
	public function reorder(
		array $orderData
	) {
		global $db, $cache;




		$status = "initated";
		foreach($orderData as $data) {



			// Security Check !!! Needs more: ID, Cat ID check from DB. Order number check?
			if (
				(
					$data['type'] != "projectcategory" &&
					$data['type'] != "pagecategory" &&
					$data['type'] != "project" &&
					$data['type'] != "page"
				) ||
				!is_numeric( $data['ID'] ) ||
				!is_numeric( $data['catID'] ) ||
				!is_numeric( $data['order'] )
			) return false;

			$ID = intval($data['ID']);
			$cat_ID = intval($data['catID']);
			$order = intval($data['order']);



			// Pass on category 0
			if ($data['type'] == "projectcategory" && $ID == 0) continue;
			if ($data['type'] == "pagecategory" && $ID == 0) continue;




			// DB Checks !!! If exists...




			if ($data['type'] == "project") {



				// ORDERING:

				// Delete the old record
				$db->where('project_ID', $ID);
				$db->where('user_ID', currentUserID());
				$db->delete('projects_order');


				// Add the new record
				$ordered = $db->insert('projects_order', array(
					"order_number" => $order,
					"project_ID" => $ID,
					"user_ID" => currentUserID()
				));
				if ($ordered) $status = "ordering-successful";



				// CATEGORIZING:

				// Delete the old connection
				$db->where('project_ID', $ID);
				$db->where('user_ID', currentUserID());
				$db->delete('project_cat_connect');


				if ($cat_ID != 0) {


					// Add the new record
					$categorized = $db->insert('project_cat_connect', array(
						"cat_ID" => $cat_ID,
						"project_ID" => $ID,
						"user_ID" => currentUserID()
					));
					if ($categorized) $status = "categorizing-successful";


				}



				// INVALIDATE THE CACHES
				$cache->deleteKeysByTag('projects');



			}




			if ($data['type'] == "page") {



				// ORDERING:
				$ordered = Page::ID($ID)->edit('order_number', $order);
				if ($ordered) $status = "ordering-successful";



				// CATEGORIZING
				$categorized = Page::ID($ID)->edit('cat_ID', ($cat_ID == 0 ? null : $cat_ID));
				if ($categorized) $status = "categorizing-successful";



				// INVALIDATE THE CACHES
				$cache->deleteKeysByTag('pages');



			}


			// CATEGORY ORDER
			if ($data['type'] == "projectcategory" || $data['type'] == "pagecategory") {


				$cat_ordered = $data['type']::ID($ID)->reorder($order);
				if ($cat_ordered) $status = "cat-ordering-successful";



				// INVALIDATE THE CACHES
				$cache->deleteKeysByTag(['pages', 'projects']);


			}



		} // Loop


		return $status == "ordering-successful" || $status == "categorizing-successful" || $status == "cat-ordering-successful";

	}


	// Unshare
	public function unshare(
		string $share_type,
		int $shared_object_ID
	) {
		global $db, $log, $cache;


		// Check the ownership
		$sharedUserID = self::$user_ID;
		$objectData = ucfirst($share_type)::ID($shared_object_ID, currentUserID());
		if (!$objectData) return false;
		self::$user_ID = $sharedUserID;


		$object_user_ID = $objectData->getInfo('user_ID');
		$objectName = $objectData->getInfo($share_type.'_name');


		//$log->debug("UNSHARE: \$share_type: $share_type->$objectName | \$shared_object_ID: $shared_object_ID | \$object_user_ID: $object_user_ID | \$User ID: ".self::$user_ID);


		$iamowner = $object_user_ID == currentUserID();
		$iamshared = self::$user_ID == currentUserID();


		// Remove share from DB
		$db->where('share_type', $share_type);
		$db->where('shared_object_ID', $shared_object_ID);
		$db->where('share_to', strval(self::$user_ID));

		if (!$iamowner && !$iamshared) $db->where('sharer_user_ID', currentUserID());


		$unshared = $db->delete('shares');


		// Notifications
		if ($unshared) {


			// Notifications
			if ( self::$user_ID != currentUserID() ) {


				$project_name = $share_type == "page" ? $project_name = "[".$objectData->getInfo('project_name')."]" : "";


				// Notify User via web notification
				if ( is_integer(self::$user_ID) )
					Notify::ID(self::$user_ID)->web("unshare", $share_type, $shared_object_ID, "$objectName".$project_name);


				// Notify User via email notification
				Notify::ID(self::$user_ID)->mail(
					getUserInfo()['fullName']." unshared the \"$objectName".$project_name."\" $share_type from you.",

					"Hello, ".
					getUserInfo()['fullName']." unshared the \"$objectName".$project_name."\" $share_type from you on Revisionary App.",
					true // Important
				);


			}


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag(['pages', 'projects']);


			// Site log
			$log->info("User #".self::$user_ID." Unshared: $share_type #$shared_object_ID | Username '".$this->getInfo('user_name')."' | Email '".$this->getInfo('user_email')."'");

		}



		return $unshared;

	}


	// Change Share Access
	public function changeshareaccess(
		string $share_type,
		$shared_object_ID,
		$new_shared_object_ID = null
	) {
		global $db, $log, $cache;


		$shared_object_ID = is_numeric($shared_object_ID) ? intval($shared_object_ID) : $shared_object_ID;
		$new_shared_object_ID = is_numeric($new_shared_object_ID) ? intval($new_shared_object_ID) : $new_shared_object_ID;


		// Check the ownership
		$sharedUserID = self::$user_ID;
		$objectData = ucfirst($share_type)::ID($shared_object_ID, currentUserID());
		if (!$objectData) return false;
		self::$user_ID = $sharedUserID;

		$objectInfo = $objectData->getInfo();
		$object_user_ID = $objectInfo['user_ID'];
		$iamowner = $object_user_ID == currentUserID();



		// Page to project
		if ($share_type == "page") {

			// Find the project info
			$new_shared_object_ID = $objectInfo['project_ID'];
			$new_share_type = "project";

		}


		// Project to page
		if ($share_type == "project") {

			// Find the project info
			$new_share_type = "page";

		}


		// Remove share from DB
		$db->where('share_type', $share_type);
		$db->where('shared_object_ID', $shared_object_ID);
		$db->where('share_to', strval(self::$user_ID));
		if (!$iamowner) $db->where('sharer_user_ID', currentUserID());


		$changed = $db->update('shares', array(
			'share_type' => $new_share_type,
			'shared_object_ID' => $new_shared_object_ID
		));


		// INVALIDATE THE CACHES
		if ($changed) $cache->deleteKeysByTag(['pages', 'projects']);


		// Site log
		if ($changed) $log->info("User #".self::$user_ID." Share Access Changed: '$share_type => $new_share_type' | '#$shared_object_ID => #$new_shared_object_ID' | Username '".$this->getInfo('user_name')."' | Email '".$this->getInfo('user_email')."'");



		return $changed;

	}


	// Make owner
	public function makeownerof(
		string $data_type,
		int $object_ID
	) {
		global $db, $log, $cache;


		if ( !is_int(self::$user_ID) ) return false;


		// Is object exist
		$sharedUserID = self::$user_ID;
		$objectData = ucfirst($data_type)::ID($object_ID, currentUserID());
		if (!$objectData) return false;
		self::$user_ID = $sharedUserID;



		// Remove me from the shares
		$db->where('share_type', $data_type);
		$db->where('shared_object_ID', $object_ID);
		$db->where('share_to', strval(self::$user_ID));
		$deleted = $db->delete('shares');


		// Make me owner
		$made_owner = false;
		if ($deleted) $made_owner = $objectData->changeownership(self::$user_ID);


		// Site log
		if ($made_owner) $log->info("User #".self::$user_ID." has became owner of: ".ucfirst($data_type)." #$object_ID | Username '".$this->getInfo('user_name')."' | Email '".$this->getInfo('user_email')."'");


		// INVALIDATE THE CACHES
		if ($made_owner) $cache->deleteKeysByTag(['pages', 'projects']);



		return $made_owner;
	}


}