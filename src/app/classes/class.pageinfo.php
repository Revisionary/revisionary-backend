<?php

class Page {


	public static $page_ID;
	public static $pageInfo;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($page_ID = null, $user_ID = null) {
	    global $db;


	    // If specific page
		if ( is_int($page_ID) ) {


			$pages = User::ID($user_ID)->getPages();
			$pages = array_filter($pages, function($pageFound) use ($page_ID) {
				return $pageFound['page_ID'] == $page_ID;
			});
			$pageInfo = end($pages);


			if ( $pageInfo ) {

				self::$page_ID = $page_ID;
				self::$pageInfo = $pageInfo;
				return new static;

			}


		}


	    // For the new page
		if ($page_ID == null || $page_ID == "new") {

			self::$page_ID = "new";
			return new static;

		}

		return false;

    }




	// GETTERS:

    // Get page info
    public function getInfo($column = null) {

	    return $column == null ? self::$pageInfo : self::$pageInfo[$column];

    }


    // Get page directory
    public function getDir() {

		// Paths
        $projectPath = Project::ID(self::$pageInfo['project_ID'])->getDir();
        $pagePath = "page-".self::$page_ID;


        // Set the page directory
        return "$projectPath/$pagePath";
    }


	// Get page users
	public function getUsers($include_me = false) {
		global $db;


		$project_ID = $this->getInfo('project_ID');
		$projectData = Project::ID($project_ID);


		$users = array();


		// Get the page owner
		$users[] = $this->getInfo('user_ID');

		// Get the shared people of the page
		$db->where('share_type', 'page');
		$db->where('shared_object_ID', self::$page_ID);
		$db->where("share_to REGEXP '^[0-9]+$'");
		$shared_IDs = array_column($db->connection('slave')->get('shares', null, 'share_to'), 'share_to');
		$users = array_merge($users, $shared_IDs);


		// Get the project users
		$users = array_merge($users, $projectData->getUsers($include_me));


		// Remove duplicates
		$users = array_unique($users);


		// Exclude myself
		if ( !$include_me && ($user_key = array_search(currentUserID(), $users)) !== false ) {
		    unset($users[$user_key]);
		}


		return $users;

	}




    // ACTIONS

    // Add a new page
    public function addNew(
    	int $project_ID = 0, // The project_ID that new page is belong to
    	string $page_url,
    	string $page_name = '',
    	array $page_shares = array(), // Array of users that needs to be shared to
    	int $category_ID = 0, // The category_ID that new page is belong to
    	int $order_number = 0 // The order number
    ) {
	    global $db, $log, $cache;



		// More DB Checks of arguments !!!



		// If not image type page
		if ($page_url != "image") {


			// URL check
			if (!filter_var($page_url, FILTER_VALIDATE_URL)) return false;



			// Standardize the URL before saving
			$page_url = urlStandardize($page_url);



			// Check for redirects
			$page_url = get_redirect_final_target($page_url);


		}



		// Parse the URL
		$parsed_url = parseUrl($page_url);



		// If no name added, try finding page name from URL
		if ($page_name == '') {
			$parsed_path = pathinfo($parsed_url['path']);
			$file_name = isset($parsed_path['filename']) ? $parsed_path['filename'] : "";
			$page_name = ucwords(str_replace('-', ' ', $file_name));
		}


		// If still empty, name it as 'Home'
		if ($page_name == '')
			$page_name = 'Home';



		// Create a project
		if ($project_ID === 0) {

			$project_ID = Project::ID("new")->addNew($page_url);

		}



		// If Category ID is zero, make it null
		if ($category_ID == 0) $category_ID = null;



		// Add the page
		$page_ID = $db->insert('pages', array(
			"page_url" => $page_url,
			"page_name" => $page_name,
			"project_ID" => $project_ID,
			"order_number" => $order_number,
			"cat_ID" => $category_ID,
			"user_ID" => currentUserID()
		));



		// If page added
		if ( is_int($page_ID) ) {



			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pages');



			$page_link = site_url('page/'.$page_ID);
			$project_name = " [".Project::ID($project_ID)->getInfo('project_name')."]";



			// Get the users to notify
			$users = Page::ID($page_ID)->getUsers();



			// SHARE - Use share API later !!!
			if ( count($page_shares) > 0 ) {

				foreach ($page_shares as $user_ID) {


					// Don't add the user to the shares if already shared
					if ( in_array($user_ID, $users) ) continue;


					// Share
					$share_ID = $db->insert('shares', array(
						"share_type" => 'page',
						"shared_object_ID" => $page_ID,
						"share_to" => $user_ID,
						"sharer_user_ID" => currentUserID()
					));


					// Web notification
					if ( is_integer($user_ID) )
						Notify::ID($user_ID)->web("share", "page", $page_ID, null, $project_ID, $page_ID);


					// Email notification
					Notify::ID($user_ID)->mail(
						getUserInfo()['fullName']." shared the \"$page_name"."$project_name\" page with you.",

						"Hello, ".
						getUserInfo()['fullName']." shared the \"$page_name"."$project_name\" page with you from Revisionary App. Here is the link to access this page: <br>
						<a href='$page_link' target='_blank'>$page_link</a>",
						true // Important
					);

				}

			}



			// Site log
			$log->info("Page #$page_ID Added: $page_name($page_url) | Project #$project_ID | Project Name:$project_name | User #".currentUserID());



			return $page_ID;

		}

		return false;

	}



    // Edit a page
    public function edit(
	    string $column,
	    $new_value
    ) {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!!



    	// Return if no access
    	if ( !$User->canAccess(self::$page_ID, 'page') ) return false;



		// Update the page
		$db->where('page_ID', self::$page_ID);
		$page_updated = $db->update('pages', array($column => $new_value));


		// Site log
		if ($page_updated) $log->info("Page #".self::$page_ID." Updated: '$column => $new_value' | Project #".$this->getInfo('project_ID')." | User #".currentUserID());



		// INVALIDATE THE CACHES
		if ($page_updated) $cache->deleteKeysByTag('pages');


		return $page_updated;
    }



    // Edit a page
    public function updateModified() {
	    global $db, $log;



		// More DB Checks of arguments !!!



		$page_modified = $this->edit('page_modified', date('Y-m-d H:i:s'));

		return $page_modified;
    }


    // Archive a page
    public function archive() {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!!



    	// Return if no access
    	if ( !$User->canAccess(self::$page_ID, 'page') ) return false;



		$archived = $this->edit("page_archived", 1);


		// Site log
		if ($archived) $log->info("Page #".self::$page_ID." Archived: '".$this->getInfo('page_name')."' | Project #".$this->getInfo('project_ID')." | User #".currentUserID());



		// INVALIDATE THE CACHES
		if ($archived) $cache->deleteKeysByTag('pages');


		return $archived;

    }


    // Delete a page
    public function delete() {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!!



    	// Return if no access
    	if ( !$User->canAccess(self::$page_ID, 'page') ) return false;



		$deleted = $this->edit("page_deleted", 1);


		// Site log
		if ($deleted) $log->info("Page #".self::$page_ID." Deleted: '".$this->getInfo('page_name')."' | Project #".$this->getInfo('project_ID')." | User #".currentUserID());



		// INVALIDATE THE CACHES
		if ($deleted) $cache->deleteKeysByTag('pages');


		return $deleted;

    }


    // Recover a page
    public function recover() {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!!



    	// Return if no access
    	if ( !$User->canAccess(self::$page_ID, 'page') ) return false;



		// Remove from archives
		$arc_recovered = $this->edit("page_archived", 0);


		// Remove from deletes
		$del_recovered = $this->edit("page_deleted", 0);



		if ($arc_recovered && $del_recovered) {


			// Site log
			$log->info("Page #".self::$page_ID." Recovered: '".$this->getInfo('page_name')."' | Project #".$this->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pages');


			return true;
		}


		return false;

    }


    // Remove a page
    public function remove() {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!!



    	// Return if no access
    	if ( !$User->canAccess(self::$page_ID, 'page') ) return false;



		// Get the page info
    	$page_user_ID = self::$pageInfo['user_ID'];
    	$project_ID = self::$pageInfo['project_ID'];
    	$projectData = Project::ID($project_ID);


    	$iamadmin = getUserInfo()['userLevelID'] == 1;
    	$iamowner = $page_user_ID == currentUserID();
    	$iamprojectowner = $projectData->getInfo('user_ID') == currentUserID();
    	//$iamshared = false;


    	//if (!$iamadmin && !$iamowner && !$iamprojectowner) return false; // !!!




		// SHARE REMOVAL
		$db->where('share_type', 'page');
		$db->where('shared_object_ID', self::$page_ID);
		if (!$iamowner && !$iamadmin) $db->where('(sharer_user_ID = '.currentUserID().' OR share_to = '.currentUserID().')');
		$db->delete('shares');



		// PAGE REMOVAL
		$db->where('page_ID', self::$page_ID);
		$page_removed = $db->delete('pages');



		// Delete the page folder
		deleteDirectory( cache."/projects/project-$project_ID/page-".self::$page_ID."/" );



		// Delete the notifications if exists
		$db->where('object_type', 'page');
		$db->where('object_ID', self::$page_ID);
		$db->delete('notifications');



		// Site log
		if ($page_removed) $log->info("Page #".self::$page_ID." Removed: '".$this->getInfo('page_name')."' | Project Name: ".$projectData->getInfo('project_name')." | Project #".$this->getInfo('project_ID')." | User #".currentUserID());



		// INVALIDATE THE CACHES
		if ($page_removed) $cache->deleteKeysByTag(['projects', 'pages', 'phases', 'devices', 'pins', 'userload']);


		return $page_removed;

    }


    // Rename a page
    public function rename(
	    string $text
    ) {
	    global $db, $log, $cache, $User;



		// More DB Checks of arguments !!!



    	// Return if no access
    	if ( !$User->canAccess(self::$page_ID, 'page') ) return false;



		$current_page_name = $this->getInfo('page_name');


    	$db->where('page_ID', self::$page_ID);
		//$db->where('user_ID', currentUserID()); // !!! Only rename my page?

		$page_renamed = $db->update('pages', array(
			'page_name' => $text
		));


		// Site log
		if ($page_renamed) $log->info("Page #".self::$page_ID." Renamed: '$current_page_name => $text' | Project #".$this->getInfo('project_ID')." | User #".currentUserID());



		// INVALIDATE THE CACHES
		if ($page_renamed) $cache->deleteKeysByTag('pages');



		return $page_renamed;

    }


    // Change owner
    public function changeownership(
	    int $user_ID
    ) {
		global $db, $log, $cache;



		// More DB Checks of arguments !!!



		$old_owner_ID = self::$pageInfo['user_ID'];


		if ($old_owner_ID != currentUserID() && getUserInfo()['userLevelID'] != 1) return false;


		// Share to old owner
		$db->insert('shares', array(

			"share_to" => $old_owner_ID,
			"share_type" => 'page',
			"shared_object_ID" => self::$page_ID,
			"sharer_user_ID" => $user_ID

		));


		$db->where('page_ID', self::$page_ID);
		$db->where('user_ID', currentUserID());
		$ownership_changed = $db->update('pages', array(
			'user_ID' => $user_ID
		));


		// Site log
		if ($ownership_changed) $log->info("Page #".self::$page_ID." Ownership Changed: '$old_owner_ID => $user_ID' | Project #".$this->getInfo('project_ID')." | User #".currentUserID());


		// INVALIDATE THE CACHES
		if ($ownership_changed) $cache->deleteKeysByTag(['pages', 'projects', 'phases']);


		return $ownership_changed;

    }

}