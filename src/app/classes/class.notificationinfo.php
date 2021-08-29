<?php

class Notification {


	// The pin ID
	public static $notification_ID;
	public static $notificationInfo;



	// SETTERS:

	public function __construct() {

    }



	// ID Setter
    public static function ID($notification_ID = null) {
	    global $db;


	    // Set the notification ID
		if (is_numeric($notification_ID)) {

			$notification_ID = intval($notification_ID);

		    $db->where("notification_ID", $notification_ID);
			$notificationInfo = $db->getOne("notifications");

			if ( $notificationInfo ) {

				self::$notification_ID = $notification_ID;
				self::$notificationInfo = $notificationInfo;
				return new static;

			}


		}


	    // List of notifications
		if ( is_array($notification_ID) && count($notification_ID) > 0) {

			self::$notification_ID = $notification_ID;
			return new static;

		}


	    // For the new notification
		if ($notification_ID === null || $notification_ID == "new") {

			self::$notification_ID = "new";
			return new static;

		}

		return false;

    }



	// GETTERS:

    // Get pin info
    public function getInfo($column = null) {

		return $column == null ? self::$notificationInfo : self::$notificationInfo[$column];

    }




    // ACTIONS


    // Read all
    public function readAll() {
	    global $db;

		$db->join("notification_user_connection con", "n.notification_ID = con.notification_ID", "LEFT");
		$db->where('con.user_ID', currentUserID());
		$db->where('con.notification_read', 0);

		if ( is_array(self::$notification_ID) ) $db->where('n.notification_ID', self::$notification_ID, "IN");

		$allRead = $db->update("notifications n", array(
			"notification_read" => 1
		));

		return $allRead;

    }


	// Remove a notification
	public function remove() {
		global $db, $log;



	    // DB Checks !!!



		// Single Notification
	    if ( is_integer(self::$notification_ID) || is_numeric(self::$notification_ID) ) {


			// Remove the notification
			$db->where('notification_ID', intval(self::$notification_ID));
			$removed = $db->delete('notifications');

			if ($removed) $log->info("Notification #".self::$notification_ID." Removed: User #".currentUserID());


			return $removed;

		}


		if ( is_array(self::$notification_ID) && count(self::$notification_ID) > 0) {


			foreach (self::$notification_ID as $notification_ID) {


				// Remove the notification
				$db->where('notification_ID', self::$notification_ID);
				$removed = $db->delete('notifications');

				if ($removed) $log->info("Notification #".self::$notification_ID." Removed: User #".currentUserID());

			}


			return $removed;
		}


		return false;

	}




}