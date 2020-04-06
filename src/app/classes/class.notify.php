<?php

class Notify {


	// The user ID
	public static $user_ID;



	// SETTERS:
	public function __construct() {

    }



	// ID Setter
    public static function ID($user_ID = null) {


		// If no user set
		if ($user_ID === null)
			$user_ID = currentUserID();


		// If multiple user set
		if ( is_array($user_ID) )
			$user_ID = array_filter(array_unique($user_ID));


		// If the ID is numerical
		if ( is_numeric($user_ID) )
			$user_ID = intval($user_ID);


	    // Set the user ID
		self::$user_ID = $user_ID;
		return new static;

    }



    // ACTIONS:

    // Email notifications
    public function mail(
	    string $subject,
	    string $notification,
	    bool $important = false
    ) {
	    global $config;

	    if ($config['env']['name'] != "remote-dev") return true;



		$recipients = "";
		if ( is_array(self::$user_ID) ) {

			foreach (self::$user_ID as $user_ID) {

				if ( is_string($user_ID) )
					$user_ID = intval($user_ID);

				// Bring the user info
				$userInfo = getUserInfo($user_ID);

				// Add the list if needs to be send
				if ($important || $userInfo['emailNotifications']) $recipients .= $userInfo['email'].",";

			}

		} elseif ( is_string(self::$user_ID) ) {

			// ID written as a string email
			$recipients = self::$user_ID;

		} elseif ( is_integer(self::$user_ID) ) {

			// Bring the user info
			$userInfo = getUserInfo(self::$user_ID);
			if ($important || $userInfo['emailNotifications']) $recipients = $userInfo['email'];

		} else {

			error_log('Undefined type of user ID.');

		}




		// Send the email
		$recipients = trim($recipients, ',');
		return Mail::ASYNCSEND(
			$recipients,
			urlencode($subject),
			urlencode($notification)
		);

    }



    // Web notifications
    public function web(
	    string $notification_type = "text",
	    string $object_type = null,
	    int $object_ID = null,
	    $notification = null
    ) {
	    global $db;


	    // Do not send web notifications to emails
	    if ( is_string(self::$user_ID) ) return false;
	    if ( is_array(self::$user_ID) && count(self::$user_ID) == 0 ) return false;




		// ADD THE NOTIFICATION
		$notification_ID = $db->insert('notifications', array(
			"notification_type" => $notification_type,
			"notification" => $notification,
			"object_type" => $object_type,
			"object_ID" => $object_ID,
			"sender_user_ID" => currentUserID()
		));
		if (!$notification_ID) return false;



		// ADD THE CONNECTIONS:

		// If multiple user set
		if ( is_array(self::$user_ID) ) {


			foreach (self::$user_ID as $user_ID) {

				// Pass the non-user IDs like emails
				if ( !is_numeric($user_ID) ) $user_ID = intval($user_ID);


				// Add the connection
				$connection_ID = $db->insert('notification_user_connection', array(
					"notification_ID" => $notification_ID,
					"user_ID" => $user_ID
				));
				//if (!$connection_ID) return false;

			}


		// If only one user set
		} else {


			// Add the connection
			$connection_ID = $db->insert('notification_user_connection', array(
				"notification_ID" => $notification_ID,
				"user_ID" => self::$user_ID
			));
			//if (!$connection_ID) return false;


		}


		return $notification_ID;

    }


}