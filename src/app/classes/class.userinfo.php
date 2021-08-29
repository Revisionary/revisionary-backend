<?php
use \Firebase\JWT\JWT;


class User {


	// The user ID
	public static $user_ID;
	public static $userInfo;




	// SETTERS:
	public function __construct() {

	}



	// ID Setter
	public static function ID($user_ID = null, bool $nocache = false) {
		global $config;


		// Authentication check !!!
		if ( is_array($user_ID) ) {


			// Auth with token
			if ( isset($user_ID['token']) && !empty($user_ID['token']) ) {

				$token = $user_ID['token'];

				try {


					$decoded = JWT::decode($token, $config['env']['jwt_secret_key'], array('HS256'));
					self::$user_ID = $decoded->user->ID;
					return new static;


				} catch (Exception $e){
		

					return array(
						"status" => "error",
						"message" => "Access denied (JWT ERROR)",
						"token" => $token,
						"error" => $e->getMessage()
					);


				}

				
			}


			return new static;

		}


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
	public function get(
		string $userName = null
	) {
		global $db;


		// If username
		if ( isset($userName) ) {

			// Check if any empty field
			if ( empty($userName) ) {
				return array(
					"status" => "error",
					"message" => "Please don't leave fields blank"
				);
			}


			$db->where("user_name", $userName);
			$db->orWhere("user_email", $userName);

		} else {

			// If email is given
			if ( !is_numeric(self::$user_ID) ) return false;

			// Limit the user
			$db->where("user_ID", self::$user_ID);

		}

		// Bring the level info
		$db->join("user_levels l", "u.user_level_ID = l.user_level_ID", "LEFT");


		// Get the data
		$user = $db->connection('slave')->getOne("users u", "
			u.user_ID as ID,
			u.user_email as email,
			u.user_password as password,
			u.user_first_name as first_name,
			u.user_last_name as last_name,
			u.user_job_title as job_title,
			u.user_department as department,
			u.user_company as company,
			u.user_picture as picture,
			u.user_email_notifications as email_notifications,
			u.trial_started_for,
			u.trial_expire_date,
			u.trial_expired_notified,
			u.user_level_ID as level_ID,
			l.user_level_name as level_name,
			l.user_level_max_project as max_projects,
			l.user_level_max_page as max_pages,
			l.user_level_max_screen as max_screens,
			l.user_level_max_content_pin as max_content_pins,
			l.user_level_max_comment_pin as max_comment_pins,
			l.user_level_max_load as max_load,
			l.user_level_max_client as max_clients
		");


		if ($user === null) {
			return array(
				"status" => "error",
				"message" => "User not found."
			);
		}


		return array(
			"status" => "success",
			"user" => $user
		);


	}


	// Get multiple users info
	public function getUsers(
		array $IDs
	) {
		global $db;

		// If email is given
		if ( !is_numeric(self::$user_ID) ) return false;


		$db->where("user_ID", $IDs, "IN");
		$users = $db->get("users", null, "
			user_ID as ID,
			user_email as email,
			user_first_name as first_name,
			user_last_name as last_name,
			user_job_title as job_title,
			user_department as department,
			user_company as company,
			user_picture as picture,
			user_email_notifications as email_notifications,
			trial_started_for,
			trial_expire_date,
			trial_expired_notified,
			user_level_ID as level_ID
		");


		if ($users === null) {
			return array(
				"status" => "error",
				"message" => "Users not found."
			);
		}

		$usersList = array();
		foreach ($users as $user)
			$usersList[$user["ID"]] = $user;


		return array(
			"status" => "success",
			"users" => $usersList
		);

	}


	// Login
	public function login(
		string $userName,
		string $password,
		string $redirect_to = ""
	) {
		global $db, $config;


		// Redirect detection
		$redirect_to = !empty($redirect_to) ? htmlspecialchars_decode($redirect_to) : site_url('projects');


		// Check if any empty field
		if (empty($userName) || empty($password)) {
			return array(
				"status" => "error",
				"message" => "Please don't leave fields blank"
			);
		}


		// Username / Email validation
		if (!filter_var($userName, FILTER_VALIDATE_EMAIL) ) {

			if (!preg_match('/^[A-Za-z][A-Za-z0-9]*(?:-[A-Za-z0-9\-]+)*$/', $userName)) {
				return array(
					"status" => "error",
					"message" => "Invalid username or email format"
				);
			}

		}


		// Get user info
		$userData = $this->get($userName);
		if ($userData['status'] === "error") {
			return array(
				"status" => "error",
				"message" => "Your username or password is wrong."
			);
		}


		// Password check
		if (isset($userData['user']) && !password_verify($password, $userData['user']["password"]) ) {
			return array(
				"status" => "error",
				"message" => "Your username or password is wrong."
			);
		}


		// SUCCESS - Generate the token
		$user = $userData['user'];
		$payload = array(
			"iss" => "https://" . $config['env']['subdomain'] . "." . $config['env']['domain'],
			"aud" => "https://" . $config['env']['dashboard_subdomain'] . "." . $config['env']['dashboard_domain'],
			// "iat" => time(),
			// "nbf" => time(),
			"iat" => 1356999524,
			"nbf" => 1357000000,
			//"exp" => time() + 2,
			"user" => array(
				"ID" => $user["ID"],
				"email" => $user["email"]
			)
		);

		$jwt = JWT::encode($payload, $config['env']['jwt_secret_key']);

		return array(
			"status" => "success",
			"token" => $jwt,
			"userInfo" => $user
		);


	}


	// Logout !!!
	public function logout() {


		return array(
			"status" => "success",
			"message" => "Logged out."
		);


	}



	// Get the project categories that user can access
	public function getProjectCategories_v2() {
		global $db;


		// Current user's categories
		$db->where('cat.user_ID', self::$user_ID);


		// Default order
		//$db->orderBy("cat.cat_order_number", "asc");


		// GET THE DATA
		$categories = $db->connection('slave')->get("projects_categories cat", null, '
			cat.cat_ID as ID,
			cat.cat_name as title,
			cat.cat_slug as slug,
			cat.cat_order_number as order_number
		');


		// Add the uncategorized item
		array_unshift($categories, array(
			'ID' => 0,
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'order_number' => 0
		));


		return array(
			"status" => "success",
			"categories" => $categories
		);
	}



	// Get all the projects that user can access
	public function getProjects_v2() {
		global $db;


		// Bring project share info
		$db->join("shares s", "p.project_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
		$db->joinWhere("shares s", "s.share_type", "project");


		// Get shared people
		$db->join("shares sh", "p.project_ID = sh.shared_object_ID", "LEFT");
		$db->joinWhere("shares sh", "sh.share_type", "project");


		// Bring the favorite info
		$db->join("projects_favorites f", "(p.project_ID = f.project_ID AND f.user_ID = ".self::$user_ID.")", "LEFT");


		// // Bring the user info
		// $db->join("users u", "p.user_ID = u.user_ID", "LEFT");


		// Bring the category connection
		$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_ID", "LEFT");


		// Bring the category info
		$db->join("projects_categories cat", "cat_connect.cat_ID = cat.cat_ID", "LEFT");
		$db->joinWhere("projects_categories cat", "cat.user_ID", self::$user_ID);


		// Bring the pages
		$db->join("pages pg", "p.project_ID = pg.project_ID", "LEFT");


		// Bring the phases
		$db->join("phases ph", "pg.page_ID = ph.page_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "ph.phase_ID = pin.phase_ID", "LEFT");


		// Bring the order info
		$db->join("projects_order o", "o.project_ID = p.project_ID", "LEFT");
		$db->joinWhere("projects_order o", "o.user_ID", self::$user_ID);


		// Filter the projects
		$db->where("(
			p.user_ID = ".self::$user_ID."
			OR s.share_to = ".self::$user_ID."
			OR s.share_to = '".self::$userInfo['user_email']."'
		)");


		// Default Sorting
		$db->orderBy("o.order_number", "asc");
		$db->orderBy("s.share_ID", "desc");
		$db->orderBy("cat.cat_name", "asc");
		$db->orderBy("p.project_name", "asc");


		// Project group for page counting
		$db->groupBy("p.project_ID, o.order_number, cat.cat_ID, s.share_ID, f.favorite_ID");


		// GET THE DATA
		$projects = $db->connection('slave')->get(
			'projects p',
			null,
			'
				p.project_ID as ID,
				p.project_name as title,
				p.project_description as description,
				p.project_created as date_created,
				p.project_modified as date_modified,
				p.project_archived as archived,
				p.project_deleted as deleted,
				p.project_image_device_ID as image_device_ID,
				p.user_ID as user_ID,
				o.order_number as order_number,
				cat.cat_ID as cat_ID,
				COUNT(DISTINCT pg.page_ID) as sub_count,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
				GROUP_CONCAT(DISTINCT sh.share_to) AS shares,
				f.favorite_ID as favorite
			'
		);


		// Arrangements
		foreach ($projects as $key => $project) { 

			// Create the URLs
			$projects[$key]['image_url'] = cache_url("screenshots/device-".$project['image_device_ID'].".jpg");
			unset($projects[$key]['image_device_ID']);

			// Cat ID corrections
			$projects[$key]['cat_ID'] = $projects[$key]['cat_ID'] ? $projects[$key]['cat_ID'] : 0;

			// Favorite corrections
			$projects[$key]['favorite'] = $projects[$key]['favorite'] ? true : false;

			// Project shares
			$projects[$key]['users'] = $project['shares'] ? array_values(array_unique(array_map('intval', explode(',', $project['shares'])))) : [];

		}


		// Return the data
		return array(
			"status" => "success",
			"projects" => $projects
		);


	}



	// Get single project info
	public function getProject(int $project_ID) {
		global $db;


		// Bring project share info
		$db->join("shares s", "p.project_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
		$db->joinWhere("shares s", "s.share_type", "project");


		// Get shared people
		$db->join("shares sh", "p.project_ID = sh.shared_object_ID", "LEFT");
		$db->joinWhere("shares sh", "sh.share_type", "project");


		// Bring the favorite info
		$db->join("projects_favorites f", "(p.project_ID = f.project_ID AND f.user_ID = ".self::$user_ID.")", "LEFT");


		// // Bring the user info
		// $db->join("users u", "p.user_ID = u.user_ID", "LEFT");


		// Bring the category connection
		$db->join("project_cat_connect cat_connect", "p.project_ID = cat_connect.project_ID", "LEFT");


		// Bring the category info
		$db->join("projects_categories cat", "cat_connect.cat_ID = cat.cat_ID", "LEFT");
		$db->joinWhere("projects_categories cat", "cat.user_ID", self::$user_ID);


		// Bring the pages
		$db->join("pages pg", "p.project_ID = pg.project_ID", "LEFT");


		// Bring the phases
		$db->join("phases ph", "pg.page_ID = ph.page_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "ph.phase_ID = pin.phase_ID", "LEFT");


		// Bring the order info
		$db->join("projects_order o", "o.project_ID = p.project_ID", "LEFT");
		$db->joinWhere("projects_order o", "o.user_ID", currentUserID());


		// Filter the projects
		$db->where("(
			p.user_ID = ".self::$user_ID."
			OR s.share_to = ".self::$user_ID."
			OR s.share_to = '".self::$userInfo['user_email']."'
		)");

		$db->where("p.project_ID", $project_ID);


		// Project group for page counting
		$db->groupBy("p.project_ID, o.order_number, cat.cat_ID, s.share_ID, f.favorite_ID");


		// GET THE DATA
		$project = $db->connection('slave')->getOne(
			'projects p',
			'
				p.project_ID as ID,
				p.project_name as title,
				p.project_description as description,
				p.project_created as date_created,
				p.project_modified as date_modified,
				p.project_archived as archived,
				p.project_deleted as deleted,
				p.project_image_device_ID as image_device_ID,
				p.user_ID as user_ID,
				o.order_number as order_number,
				cat.cat_ID as cat_ID,
				COUNT(DISTINCT pg.page_ID) as sub_count,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
				GROUP_CONCAT(DISTINCT sh.share_to) AS shares,
				f.favorite_ID as favorite
			'
		);

		if (!$project) {

			return array(
				"status" => "fail",
				"description" => "No project found"
			);

		}


		// Create the URLs
		$project['image_url'] = cache_url("screenshots/device-".$project['image_device_ID'].".jpg");
		unset($project['image_device_ID']);

		// Cat ID corrections
		$project['cat_ID'] = $project['cat_ID'] ? $project['cat_ID'] : 0;

		// Favorite corrections
		$project['favorite'] = $project['favorite'] ? true : false;

		// Project shares
		$project['users'] = $project['shares'] ? array_values(array_unique(array_map('intval', explode(',', $project['shares'])))) : [];


		// Return the data
		return array(
			"status" => "success",
			"project" => $project
		);


	}



	// Get the categories of a project
	public function getPageCategories_v2(int $project_ID) {
		global $db;


		// Default order
		//$db->orderBy("cat.cat_order_number", "asc");


		// Filter the project
		$db->where("cat.project_ID", $project_ID);


		// GET THE DATA
		$categories = $db->connection('slave')->get("pages_categories cat", null, '
			cat.cat_ID as ID,
			cat.cat_name as title,
			cat.cat_slug as slug,
			cat.cat_order_number as order_number,
			cat.project_ID as project_ID
		');


		// Add the uncategorized item
		array_unshift($categories, array(
			'ID' => 0,
			'title' => 'Uncategorized',
			'slug' => 'uncategorized',
			'order_number' => 0,
			'project_ID' => $project_ID
		));


		return array(
			"status" => "success",
			"categories" => $categories
		);
	

	}



	// Get the pages of a project
	public function getPages_v2(int $project_ID) {
		global $db;


		// Bring the category info
		$db->join("pages_categories cat", "cat.cat_ID = p.cat_ID", "LEFT");


		// Bring the project info
		$db->join("projects pr", "pr.project_ID = p.project_ID", "LEFT");


		// // Bring the user info
		// $db->join("users u", "p.user_ID = u.user_ID", "LEFT");


		// Bring page share info
		$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
		$db->joinWhere("shares s", "s.share_type", "page");


		// Get shared people
		$db->join("shares sh", "p.page_ID = sh.shared_object_ID", "LEFT");
		$db->joinWhere("shares sh", "sh.share_type", "page");


		// Bring the favorite info
		$db->join("pages_favorites f", "(p.page_ID = f.page_ID AND f.user_ID = ".self::$user_ID.")", "LEFT");


		// Bring project share info
		$db->join("shares sp", "p.project_ID = sp.shared_object_ID", "LEFT");
		$db->joinWhere("shares sp", "sp.share_type", "project");


		// Bring the phases
		$db->join("phases ph", "p.page_ID = ph.page_ID", "LEFT");


		// Bring the devices
		$db->join("devices d", "ph.phase_ID = d.phase_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "ph.phase_ID = pin.phase_ID", "LEFT");


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


		// Filter the pages
		$db->where("p.project_ID", $project_ID);


		// Default Sorting
		$db->orderBy("p.order_number", "asc");
		$db->orderBy("s.share_ID", "desc");
		$db->orderBy("cat.cat_name", "asc");
		$db->orderBy("p.page_name", "asc");


		// Project group for page counting
		$db->groupBy("p.page_ID, p.order_number, cat.cat_ID, s.share_ID, f.favorite_ID");


		// GET THE DATA
		$pages = $db->connection('slave')->get(
			'pages p',
			null,
			'
				p.page_ID as ID,
				p.page_name as title,
				p.page_description as description,
				p.page_created as date_created,
				p.page_modified as date_modified,
				p.page_archived as archived,
				p.page_deleted as deleted,
				p.user_ID as user_ID,
				p.project_ID as project_ID,
				p.order_number as order_number,
				cat.cat_ID as cat_ID,
				COUNT(DISTINCT ph.phase_ID) as sub_count,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
				GROUP_CONCAT(DISTINCT d.device_ID) AS devices,
				GROUP_CONCAT(DISTINCT sh.share_to) AS shares,
				GROUP_CONCAT(DISTINCT CONCAT(ph.phase_ID, \' | \', ph.phase_created)) AS phases,
				f.favorite_ID as favorite
			'
		);


		// Arrangements
		foreach ($pages as $key => $page) {

			// Devices
			$pages[$key]['devices'] = $page['devices'] ? array_values(array_unique(array_map('intval', explode(',', $page['devices'])))) : [];

			// Phases
			$pages[$key]['versions'] = [];
			foreach (explode(',', $page['phases']) as $phasekey => $phase) {

				$pages[$key]['versions'][$phasekey]["ID"] = explode(' | ', $phase)[0];
				$pages[$key]['versions'][$phasekey]["created"] = explode(' | ', $phase)[1];

			}
			unset($pages[$key]['phases']);

			// Reorder Phases
			usort($pages[$key]['versions'], function($a, $b) {
				return strcmp($a["created"], $b["created"]);
			});

			// Create the URLs
			$pages[$key]['image_url'] = cache_url("screenshots/device-".reset($pages[$key]['devices']).".jpg");

			// Cat ID corrections
			$pages[$key]['cat_ID'] = $pages[$key]['cat_ID'] ? $pages[$key]['cat_ID'] : 0;

			// Favorite corrections
			$pages[$key]['favorite'] = $pages[$key]['favorite'] ? true : false;

			// Project shares
			$pages[$key]['users'] = $page['shares'] ? array_values(array_unique(array_map('intval', explode(',', $page['shares'])))) : [];

		}


		// Return the data
		return array(
			"status" => "success",
			"pages" => $pages
		);


	}



	// Get single page info
	public function getPage(int $page_ID) {
		global $db;


		// Bring the category info
		$db->join("pages_categories cat", "cat.cat_ID = p.cat_ID", "LEFT");


		// Bring the project info
		$db->join("projects pr", "pr.project_ID = p.project_ID", "LEFT");


		// // Bring the user info
		// $db->join("users u", "p.user_ID = u.user_ID", "LEFT");


		// Bring page share info
		$db->join("shares s", "p.page_ID = s.shared_object_ID", "LEFT");
		$db->joinWhere("shares s", "(s.share_to = '".self::$user_ID."' OR s.share_to = '".self::$userInfo['user_email']."')");
		$db->joinWhere("shares s", "s.share_type", "page");


		// Get shared people
		$db->join("shares sh", "p.page_ID = sh.shared_object_ID", "LEFT");
		$db->joinWhere("shares sh", "sh.share_type", "page");


		// Bring the favorite info
		$db->join("pages_favorites f", "(p.page_ID = f.page_ID AND f.user_ID = ".self::$user_ID.")", "LEFT");


		// Bring project share info
		$db->join("shares sp", "p.project_ID = sp.shared_object_ID", "LEFT");
		$db->joinWhere("shares sp", "sp.share_type", "project");


		// Bring the phases
		$db->join("phases ph", "p.page_ID = ph.page_ID", "LEFT");


		// Bring the devices
		$db->join("devices d", "ph.phase_ID = d.phase_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "ph.phase_ID = pin.phase_ID", "LEFT");


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


		// Filter the pages
		$db->where("p.page_ID", $page_ID);


		// Default Sorting
		$db->orderBy("p.order_number", "asc");
		$db->orderBy("s.share_ID", "desc");
		$db->orderBy("cat.cat_name", "asc");
		$db->orderBy("p.page_name", "asc");


		// Project group for page counting
		$db->groupBy("p.page_ID, p.order_number, cat.cat_ID, s.share_ID, f.favorite_ID");


		// GET THE DATA
		$page = $db->connection('slave')->getOne(
			'pages p',
			'
				p.page_ID as ID,
				p.page_name as title,
				p.page_description as description,
				p.page_created as date_created,
				p.page_modified as date_modified,
				p.page_archived as archived,
				p.page_deleted as deleted,
				p.user_ID as user_ID,
				p.project_ID as project_ID,
				p.order_number as order_number,
				cat.cat_ID as cat_ID,
				COUNT(DISTINCT ph.phase_ID) as sub_count,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
				COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
				GROUP_CONCAT(DISTINCT d.device_ID) AS devices,
				GROUP_CONCAT(DISTINCT sh.share_to) AS shares,
				GROUP_CONCAT(DISTINCT CONCAT(ph.phase_ID, \' | \', ph.phase_created)) AS phases,
				f.favorite_ID as favorite
			'
		);



		// Devices
		$page['devices'] = $page['devices'] ? array_values(array_unique(array_map('intval', explode(',', $page['devices'])))) : [];

		// Phases
		$page['versions'] = [];
		foreach (explode(',', $page['phases']) as $phasekey => $phase) {

			$page['versions'][$phasekey]["ID"] = explode(' | ', $phase)[0];
			$page['versions'][$phasekey]["created"] = explode(' | ', $phase)[1];

		}
		unset($page['phases']);

		// Reorder Phases
		usort($page['versions'], function($a, $b) {
			return strcmp($a["created"], $b["created"]);
		});

		// Create the URLs
		$page['image_url'] = cache_url("screenshots/device-".reset($page['devices']).".jpg");

		// Cat ID corrections
		$page['cat_ID'] = $page['cat_ID'] ? $page['cat_ID'] : 0;

		// Favorite corrections
		$page['favorite'] = $page['favorite'] ? true : false;

		// Project shares
		$page['users'] = $page['shares'] ? array_values(array_unique(array_map('intval', explode(',', $page['shares'])))) : [];


		// Return the data
		return array(
			"status" => "success",
			"page" => $page
		);


	}



	// Get the phases
	public function getPhases_v2(int $page_ID) {
		global $db;


		// Filter the phases by page_ID
		$db->where('ph.page_ID', $page_ID);


		// Bring the devices
		$db->join("devices d", "ph.phase_ID = d.phase_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "ph.phase_ID = pin.phase_ID", "LEFT");


		// Project group for page counting
		$db->groupBy("ph.phase_ID");


		// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
		$phases = $db->connection('slave')->get('phases ph', null, '
			ph.phase_ID as ID,
			ph.phase_type as type,
			ph.phase_created as created,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
			ph.page_ID as page_ID
		');


		// Return the data
		return array(
			"status" => "success",
			"phases" => $phases
		);


	}



	// Get single phase info
	public function getPhase(int $phase_ID) {
		global $db;


		// Bring the devices
		$db->join("devices d", "ph.phase_ID = d.phase_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "ph.phase_ID = pin.phase_ID", "LEFT");


		// Project group for page counting
		$db->groupBy("ph.phase_ID");


		// Filter the phases by page_ID
		$db->where('ph.phase_ID', $phase_ID);


		// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
		$phase = $db->connection('slave')->get('phases ph', null, '
			ph.phase_ID as ID,
			ph.phase_type as type,
			ph.phase_created as created,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
			ph.page_ID as page_ID
		');


		// Return the data
		return array(
			"status" => "success",
			"phase" => $phase
		);


	}



	// Get the devices
	public function getDevices_v2(int $phase_ID) {
		global $db;


		// Bring the screens
		$db->join("screens s", "s.screen_ID = d.screen_ID", "LEFT");


		// Bring the screen category info
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


		// Filter the devices by phase_ID
		$db->where("d.phase_ID", $phase_ID);


		// Order by device ID
		$db->orderBy('d.device_ID', 'ASC');


		// Bring the pins
		$db->join("pins pin", "d.device_ID = pin.device_ID", "LEFT");


		// Project group for page counting
		$db->groupBy("d.device_ID");


		// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
		$devices = $db->connection('slave')->get('devices d', null, '
			d.device_ID as ID,
			d.device_width as width,
			d.device_height as height,
			s.screen_width as screen_width,
			s.screen_height as screen_height,
			d.device_created as created,
			d.device_modified as modified,
			d.screen_ID as screen_ID,
			s.screen_name as screen_name,
			s.screen_rotateable as rotateable,
			s.screen_cat_ID as cat_ID,
			s_cat.screen_cat_name as cat_name,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
			d.phase_ID as phase_ID
		');


		// Return the data
		return array(
			"status" => "success",
			"devices" => $devices
		);


	}



	// Get single device info
	public function getDevice(int $device_ID) {
		global $db, $config;


		// Bring the screen info
		$db->join("screens s", "s.screen_ID = d.screen_ID", "LEFT");


		// Bring the screen category info
		$db->join("screen_categories s_cat", "s.screen_cat_ID = s_cat.screen_cat_ID", "LEFT");


		// Bring the phase info
		$db->join("phases ph", "d.phase_ID = ph.phase_ID", "LEFT");


		// Bring the pins
		$db->join("pins pin", "d.device_ID = pin.device_ID", "LEFT");
		$db->joinOrWhere("pins pin", "d.phase_ID = pin.phase_ID AND pin.device_ID IS NULL");


		// Bring the page info
		$db->join("pages pg", "ph.page_ID = pg.page_ID", "LEFT");


		// Bring the phases info
		$db->join("phases phs", "pg.page_ID = phs.page_ID", "LEFT");


		// Bring the page info
		$db->join("projects pr", "pg.project_ID = pr.project_ID", "LEFT");


		// Project group for page counting
		$db->groupBy("d.device_ID");


		// Filter the phases by page_ID
		$db->where('d.device_ID', $device_ID);


		// GET THE DATA - LIMIT THE OUTPUTS HERE !!!
		$device = $db->connection('slave')->getOne('devices d', '
			d.device_ID as ID,
			d.device_width as width,
			d.device_height as height,
			s.screen_width as screen_width,
			s.screen_height as screen_height,
			d.device_created as created,
			d.device_modified as modified,
			d.screen_ID as screen_ID,
			s.screen_name as screen_name,
			s.screen_rotateable as rotateable,
			s.screen_cat_ID as cat_ID,
			s_cat.screen_cat_name as cat_name,
			d.phase_ID as phase_ID,
			ph.phase_type as phase_type,
			ph.phase_internalized as phase_internalized,
			ph.phase_created as phase_created,
			GROUP_CONCAT(DISTINCT CONCAT(phs.phase_ID, \' | \', phs.phase_created)) AS phases,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=0 THEN pin.pin_ID ELSE NULL END) as incomplete_tasks,
			COUNT(DISTINCT CASE WHEN pin.pin_complete=1 THEN pin.pin_ID ELSE NULL END) as complete_tasks,
			ph.page_ID as page_ID,
			pg.page_name as page_name,
			pg.page_url as page_url,
			pg.page_created as page_created,
			pg.page_modified as page_modified,
			pg.project_ID as project_ID,
			pr.project_name as project_name
		');


		// Phase URL
		$phase_protocol = substr($device['page_url'], 0, 8) == "https://" ? "https" : "http";
		$phase_protocol = "https"; // !!! Always HTTPS
		$phase_domain = $config['env']['dashboard_subdomain'].".".$config['env']['dashboard_domain'];
		//$phase_domain = "dapp.revisionary.co"; // TEMP !!!
		$phase_url = $phase_protocol."://".$phase_domain."/";
		$device['phase_url'] = $phase_url."sites/project-".$device['project_ID']."/page-".$device['page_ID']."/phase-".$device['phase_ID']."/index.html";


		// Phases
		$device['versions'] = [];
		foreach (explode(',', $device['phases']) as $phasekey => $phase) {

			$device['versions'][$phasekey]["ID"] = explode(' | ', $phase)[0];
			$device['versions'][$phasekey]["created"] = explode(' | ', $phase)[1];

		}
		unset($device['phases']);


		// Reorder Phases
		usort($device['versions'], function($a, $b) {
			return strcmp($a["created"], $b["created"]);
		});


		// Return the data
		return array(
			"status" => "success",
			"device" => $device
		);


	}



	// Get all the pins that user can access
	public function getPins_v2(int $phase_ID, int $device_ID = null) {
		global $db;


		// // Bring the device info
		// $db->join("devices d", "pin.device_ID = d.device_ID", "LEFT");


		// // Bring the phase info
		// $db->join("phases ph", "ph.phase_ID = pin.phase_ID", "LEFT");


		// // Bring the page info
		// $db->join("pages pg", "ph.page_ID = ph.page_ID", "LEFT");


		// // Bring the project info
		// $db->join("projects project", "pg.project_ID = project.project_ID", "LEFT");


		// Bring the user info
		$db->join("users u", "pin.user_ID = u.user_ID", "LEFT");


		// Hide private pins to other people
		$db->where ("(pin.user_ID = ".self::$user_ID." or (pin.user_ID != ".self::$user_ID." and pin.pin_private = 0))");


		if ($phase_ID !== null) {
			
			// Phase filter
			$db->where('pin.phase_ID', $phase_ID);

			// Show both global and device specific pins 
			if ($device_ID !== null) $db->where ("(pin.device_ID IS NULL or (pin.device_ID IS NOT NULL and pin.device_ID = $device_ID))");

		} else {

			// Show only device specific pins
			if ($device_ID !== null) $db->where ("pin.device_ID", $device_ID);

		}


		// // Default Sorting
		// $db->orderBy("pin.pin_ID", "asc");


		// GET THE DATA
		$pins = $db->connection('slave')->get('pins pin', null, '
			pin.pin_ID as ID,
			pin.pin_complete as complete,
			pin.pin_private as private,
			pin.pin_type as type,
			pin.pin_element_index as element_index,
			pin.pin_modification_type as modification_type,
			pin.pin_modification as modification,
			pin.pin_created as created,
			pin.pin_css as css,
			pin.pin_x as x,
			pin.pin_y as y,
			u.user_ID as user_ID,
			u.user_first_name as user_first_name,
			u.user_last_name as user_last_name,
			u.user_email as user_email,
			u.user_picture as user_picture,
			pin.phase_ID as phase_ID,
			pin.device_ID as device_ID
		');


		// Return the data
		return array(
			"status" => "success",
			"pins" => $pins
		);


	}



	// Get notifications
	public function getNotifications_v2($offset = 0, $limit = 10) {
		global $db;


		// Get the user connection
		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");

		// Bring the user info
		$db->join("users u", "n.sender_user_ID = u.user_ID", "LEFT");


		// Bring the project info
		$db->join("projects pr", "n.project_ID = pr.project_ID", "LEFT");

		// Bring the page info
		$db->join("pages pg", "n.page_ID = pg.page_ID", "LEFT");

		// Bring the phase info
		$db->join("phases ph", "n.phase_ID = ph.phase_ID", "LEFT");

		// Bring the device info
		$db->join("devices d", "n.device_ID = d.device_ID", "LEFT");

		// Bring the pin info
		$db->join("pins pin", "n.pin_ID = pin.pin_ID", "LEFT");

		// Bring the comment info
		$db->join("pin_comments c", "n.comment_ID = c.comment_ID", "LEFT");


		// Filter for the current user
		$db->where('con.user_ID', self::$user_ID);

		// Order by date
		$db->orderBy("notification_time", "DESC");


		// Get the data
		$notifications = $db->withTotalCount()->get("notifications n", array($offset, $limit), "
			n.notification_ID as ID,
			n.notification as notification,
			n.notification_time as time,
			n.notification_type as type,
			n.project_ID as project_ID,
			pr.project_name as project_name,
			n.page_ID as page_ID,
			pg.page_name as page_name,
			n.phase_ID as phase_ID,
			n.device_ID as device_ID,
			n.pin_ID as pin_ID,
			pin.device_ID as pin_device_ID,
			n.comment_ID as comment_ID,
			LEFT(c.pin_comment, 70) as pin_comment,
			n.sender_user_ID as user_ID,
			u.user_first_name as first_name,
			u.user_last_name as last_name,
			u.user_picture as picture,
			u.user_email as email,
			con.notification_read as isRead
		");


		// Return the data
		return array(
			"status" => "success",
			"notifications" => $notifications,
			"totalCount" => $db->totalCount
		);

	}



	// Get new notifications count
	public function newNotificationsCount() {
		global $db;


		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', self::$user_ID);
		$db->where('con.notification_read', 0);
		$count = $db->getValue("notifications n", "count(n.notification_ID)");


		// Return the data
		return array(
			"status" => "success",
			"new_count" => $count
		);

	}



	// Get notifications
	public function readNotifications(
		array $notification_IDs
	) {
		global $db;


		// Get the user connection
		$db->join("notifications n", "n.notification_ID = con.notification_ID", "LEFT");


		// Filter for the current user
		$db->where('con.user_ID', self::$user_ID);


		// Filter the 
		$db->where('n.notification_ID', $notification_IDs, 'IN');


		// Get the data
		$read = $db->update("notification_user_connection con", array("notification_read" => 1));


		// Return the data
		return array(
			"status" => "success",
			"read" => $read,
			"count" => $db->count
		);

	}



	// Get current usage
	public function usage() {
		global $db, $cache;


		// Get the data
		// $usage = $db->rawQuery("
		// 	SELECT
		// 		(SELECT COUNT(*) FROM projects where user_ID = ".self::$user_ID.") as projects, 
		// 		(SELECT COUNT(*) FROM pages where user_ID = ".self::$user_ID.") as pages,
		// 		(SELECT COUNT(*) FROM phases where user_ID = ".self::$user_ID.") as phases,
		// 		(SELECT COUNT(*) FROM devices where user_ID = ".self::$user_ID.") as devices,
		// 		(SELECT COUNT(*) FROM pins where user_ID = ".self::$user_ID." AND pin_type != 'comment') as contentPins,
		// 		(SELECT COUNT(*) FROM pins where user_ID = ".self::$user_ID." AND pin_type = 'comment') as commentPins
		// 	FROM
		// 		phases
		// ");


		// Bring the phases
		$db->join('phases ph', 'ph.user_ID=u.user_ID', 'LEFT');


		// Bring the pages
		$db->join('pages pg', 'ph.page_ID=pg.page_ID', 'LEFT');


		// Group for the concat?
		//$db->groupBy("u.user_ID");


		$db->where('u.user_ID', self::$user_ID);
		$usage = $db->getOne('users u', "
			(SELECT COUNT(*) FROM projects where user_ID = ".self::$user_ID.") as projects, 
			(SELECT COUNT(*) FROM pages where user_ID = ".self::$user_ID.") as pages,
			(SELECT COUNT(*) FROM devices where user_ID = ".self::$user_ID.") as devices,
			(SELECT COUNT(*) FROM pins where user_ID = ".self::$user_ID." AND pin_type != 'comment') as contentPins,
			(SELECT COUNT(*) FROM pins where user_ID = ".self::$user_ID." AND pin_type = 'comment') as commentPins,
			GROUP_CONCAT(DISTINCT CONCAT(ph.phase_ID, ' | ', pg.page_ID, ' | ', pg.project_ID)) AS phase_IDs
		");



		// Arrangements
		$phase_IDs = $usage["phase_IDs"] == "" ? [] : explode(',', $usage["phase_IDs"]);

		// Add the phases count
		$usage['phases'] = count($phase_IDs);

		// Delete the IDs list
		unset($usage['phase_IDs']);



		// Calculate the phases loads
		// $cached_userLoad = $cache->get('userload:'.self::$user_ID);
		// if ( $cached_userLoad !== false ) $loadCount = $cached_userLoad;
		// else {
		
			$filesLoadMb = 0;
			//$directories = [];
			foreach ($phase_IDs as $phase_ID) {

				$parse_phase = explode('|', $phase_ID);
				$phase_ID = trim($parse_phase[0]);
				$page_ID = trim($parse_phase[1]);
				$project_ID = trim($parse_phase[2]);
		
				$phaseDirectory = cache."/projects/project-$project_ID/page-$page_ID/phase-$phase_ID";
				//$directories[] = $phaseDirectory;
		
				$sizeMb = getDirectorySize($phaseDirectory, true); // True for the MB conversion
				$filesLoadMb += $sizeMb;
		
			}
			$loadCount = $filesLoadMb;
		
			// // Set the cache
			// $cache->set('userload:'.self::$user_ID, $loadCount);
		
		// }
		$usage['load'] = floatval( number_format((float)$loadCount, 1, '.', '') );



		// Return the data
		return array(
			"status" => "success",
			"usage" => $usage
			//"phase_IDs" => $phase_IDs,
			//"directories" => $directories
		);

	}






	
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
			$categories = $db->connection('slave')->get("projects_categories cat", null, '');


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
			$projects = $db->connection('slave')->get(
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
			$categories = $db->connection('slave')->get("pages_categories cat", null, '');


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
			$pages = $db->connection('slave')->get(
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
			$phases = $db->connection('slave')->get('phases ph');


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
			$devices = $db->connection('slave')->get('devices d');


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
			$pins = $db->connection('slave')->get('pins pin', null, '
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
		$screens = $db->connection('slave')->get('screens s');


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