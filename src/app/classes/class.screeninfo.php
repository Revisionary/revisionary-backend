<?php

class Screen {


	// The screen ID
	public static $screen_ID;
	public static $screenInfo;



	// SETTERS:

	public function __construct() {

    }


	// ID Setter
    public static function ID($screen_ID = null) {
		global $db;


	    // Set the page ID
		if ($screen_ID != null && is_int($screen_ID)) {

			$db->join("screen_categories c", "c.screen_cat_ID = s.screen_cat_ID", "LEFT");
		    $db->where("s.screen_ID", $screen_ID);
			$screenInfo = $db->getOne("screens s");

			if ( $screenInfo ) {

				self::$screen_ID = $screen_ID;
				self::$screenInfo = $screenInfo;
				return new static;

			}


		}


	    // For the new screen
		if ($screen_ID == null || $screen_ID == "new") {

			self::$screen_ID = "new";
			return new static;

		}

		return false;

    }



	// GETTERS:

    // Get a screen info
    public function getInfo($column = null) {

		return $column == null ? self::$screenInfo : self::$screenInfo[$column];

    }



    // ACTIONS:

    // Add a new screen !!!
    public function addNew() {
		global $db, $cache;


		// INVALIDATE THE CACHES
		if ($added) $cache->deleteKeysByTag('screens');


	}

}