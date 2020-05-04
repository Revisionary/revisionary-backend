<?php

class Pin {


	// The pin ID
	public static $pin_ID;
	public static $pinInfo;

	public $phase_ID;
	public $phaseData;
	public $page_ID;
	public $device_ID;



	// SETTERS:

	public function __construct() {

		if (self::$pinInfo) {

			$this->phase_ID = self::$pinInfo['phase_ID'];
			$this->phaseData = Phase::ID( $this->phase_ID );
			if (!$this->phaseData) {
				return false;
			}
			$this->page_ID = $this->phaseData->getInfo('page_ID');
			$this->device_ID = self::$pinInfo['device_ID'];

		}

	}



	// ID Setter
	public static function ID($pin_ID = null) {
		global $db;


		// Set the pin ID
		if (is_int($pin_ID) || is_numeric($pin_ID)) {


			// Bring the user level info
			$db->where("pin_ID", $pin_ID);
			$pinInfo = $db->getOne("pins");

			if ( $pinInfo ) {

				self::$pin_ID = $pin_ID;
				self::$pinInfo = $pinInfo;
				return new static;

			}


		}


		// For the new pin
		if ($pin_ID = null || $pin_ID == "new") {

			self::$pin_ID = "new";
			return new static;

		}

		return false;

	}



	// GETTERS:

	// Get pin info
	public function getInfo($column = null) {

		return $column == null ? self::$pinInfo : self::$pinInfo[$column];

	}


	// Get page users
	public function getUsers($include_me = false) {
		global $db;


		// Get the page users
		$users = $this->phaseData->getUsers($include_me);


		return $users;

	}




	// ACTIONS

	// Add a new pin
	public function addNew(
		int $pin_phase_ID,
		string $pin_type = 'style',
		bool $pin_private = false,
		float $pin_x = 50,
		float $pin_y = 50,
		int $pin_element_index = null,
		string $pin_modification_type = null,
		int $pin_device_ID = null
	) {
		global $db, $log, $cache;


		// Security check !!!
		if ($pin_type != "live" && $pin_type != "style" && $pin_type != "comment") return false;



		$device_ID = $pin_device_ID;
		$phase_ID = $pin_phase_ID;
		$phaseData = Phase::ID( $phase_ID );

		$page_ID = $phaseData->getInfo('page_ID');
		$pageData = Page::ID( $page_ID );



		// More DB Checks of arguments !!!



		// NULL FOR NOW !!!
		$pin_device_ID = $phaseData->getInfo('phase_type') != "url" ? $pin_device_ID : null;

		// Add the pin
		$pin_ID = $db->insert('pins', array(
			"user_ID" => currentUserID(),
			"phase_ID" => $pin_phase_ID,
			"pin_type" => $pin_type,
			"pin_private" => $pin_private,
			"pin_x" => $pin_x,
			"pin_y" => $pin_y,
			"pin_element_index" => $pin_element_index,
			"pin_modification_type" => $pin_modification_type,
			"device_ID" => $pin_device_ID
		));


		// Notify the users
		if ($pin_ID) {

			$pageData->updateModified();

			$project_ID = $pageData->getInfo('project_ID');


			// Site log
			$log->info(ucfirst($pin_type)." Pin #$pin_ID Added to: '".$pageData->getInfo('page_name')."' Page #$page_ID | Phase #$phase_ID | Device #$device_ID | Project #$project_ID | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}



		return $pin_ID;

	}


	// Relocate a pin
	public function reLocate(
		float $pin_x = 50,
		float $pin_y = 50
	) {
		global $db, $cache;



		// More DB Checks of arguments !!!



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_locations = array(
			'pin_x' => $pin_x,
			'pin_y' => $pin_y
		);
		$pin_updated = $db->update('pins', $pin_locations);


		// Update the page modification date
		if ($pin_updated) Page::ID( $this->page_ID )->updateModified();


		// INVALIDATE THE CACHES
		if ($pin_updated) $cache->deleteKeysByTag('pins');


		return $pin_updated;

	}


	// Delete a pin
	public function remove() {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can delete?)



		// Delete the pin folder on S3
		$file = new File("pin-images/pin-".self::$pin_ID, "s3");
		$file->delete();


		// Delete the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_deleted = $db->delete('pins');


		// Delete the notifications if exists
		$db->where('object_type', 'pin');
		$db->where('object_ID', self::$pin_ID);
		$db->delete('notifications');


		if ($pin_deleted) {


			// Pin type
			$pin_type = ucfirst( $this->getInfo('pin_type') );


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." Removed from: '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}


		return $pin_deleted;

	}


	// Complete a pin
	public function complete() {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 1));

		// If successful
		if ($pin_updated) {


			// Add to the story
			$this->addComment("completed this task.", "action");


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." Completed: '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}


		return $pin_updated;

	}


	// inComplete a pin
	public function inComplete() {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_complete' => 0));


		// If successful
		if ($pin_updated) {


			// Add to the story
			$this->addComment("incompleted this task.", "action");


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." Incompleted: '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}


		return $pin_updated;

	}


	// Make device specific
	public function deviceSpecific($device_ID) {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('device_ID' => $device_ID));

		// If successful
		if ($pin_updated) {


			// Add to the story
			$this->addComment("made this pin device specific.", "action");


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("Pin #".self::$pin_ID." has been made only for Device #$device_ID '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}


		return $pin_updated;

	}


	// Make for all devices
	public function deviceAll() {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('device_ID' => null));

		// If successful
		if ($pin_updated) {


			// Add to the story
			$this->addComment("made this pin for all devices.", "action");


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("Pin #".self::$pin_ID." has been made for all devices: '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}


		return $pin_updated;

	}


	// Convert a pin
	public function convert(
		string $pin_type,
		string $pin_private
	) {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		$current_pin_type = $this->getInfo('pin_type');
		$current_pin_private = $this->getInfo('pin_private');


		// Update the pin
		$db->where('pin_ID', self::$pin_ID);

		// Don't convert the other's pin private status
		if ($pin_private == "1") $db->where('user_ID', currentUserID());


		$pin_data = array(
			'pin_type' => $pin_type,
			'pin_private' => $pin_private,
		);


		// Pin type conversion
		if ($current_pin_type != $pin_type) {


			// If the new type is style, reset the modifications
			if ($pin_type == 'style' || $pin_type == 'comment') {
				$pin_data['pin_modification'] = null;
				$pin_data['pin_modification_type'] = null;
			}


			// If the new type is comment, reset the CSS
			if ($pin_type == 'comment') {
				$pin_data['pin_css'] = null;
			}


		}


		$pin_updated = $db->update('pins', $pin_data);
		if ($pin_updated) {


			// Add to the story
			if ($current_pin_type != $pin_type) $this->addComment("made this a $pin_type pin", "action");


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info(ucfirst($current_pin_type)." Pin #".self::$pin_ID." Converted to ".ucfirst($pin_type)." ".($pin_private == "1" ? "(Private)" : "(Public)" )." -Before: ".ucfirst($current_pin_type)." ".($current_pin_private == "1" ? "(Private)" : "(Public)" )."-: '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}



		return $pin_updated;

	}


	// Modify a pin
	public function modify($modification) {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_modification' => $modification));


		if ($pin_updated) {


			// Add to the story
			// if ($modification == null) $this->addComment("reset content.", "action");


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." Modified: '$modification' | '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}



		return $pin_updated;

	}


	// Update CSS
	public function updateCSS($css) {
		global $db, $log, $cache;



		// More DB Checks of arguments !!! (This user can complete?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Update the pin
		$db->where('pin_ID', self::$pin_ID);
		$pin_updated = $db->update('pins', array('pin_css' => $css));


		if ($pin_updated) {


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." CSS Updated: '$css' | '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


			// INVALIDATE THE CACHES
			$cache->deleteKeysByTag('pins');


		}



		return $pin_updated;

	}


	// Get the comments
	public function comments() {
		global $db;



		// More DB Checks of arguments !!! (This user can complete?)



		// Get the comments
		$db->join("users u", "c.user_ID = u.user_ID", "LEFT");
		$db->orderBy('c.comment_added', 'ASC');
		$db->where('pin_ID', self::$pin_ID);
		$comments = $db->get('pin_comments c', null, "c.comment_ID, c.comment_modified, c.pin_comment, c.comment_type, c.comment_added, c.comment_modified, u.user_first_name, u.user_ID, u.user_last_name, u.user_picture, u.user_email");

		return $comments;

	}


	// Add a new comment
	public function addComment(
		string $message,
		string $comment_type = "comment"
	) {
		global $db, $log;



		// More DB Checks of arguments !!! (This user can complete?)
		// Filter the html codes !!! ?



		if (
			$comment_type != "comment" &&
			$comment_type != "attachment" &&
			$comment_type != "action"
		) return false;



		// Add the comment
		$comment_ID = $db->insert('pin_comments', array(
			"pin_comment" => $message,
			"comment_type" => $comment_type,
			"pin_ID" => self::$pin_ID,
			"user_ID" => currentUserID()
		));



		if ($comment_ID) {


			// Pin type
			$pin_type = ucfirst( $this->getInfo('pin_type') );


			// Send it to all related users
			$users = $this->getUsers();


			// Web notification
			if ($comment_type != "action") Notify::ID($users)->web("comment", "pin", self::$pin_ID, substr($message, 0, 70));


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." ".ucfirst($comment_type)." Added: '$message' | '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());


		}



		return $comment_ID;

	}


	// Delete a comment
	public function deleteComment(
		int $comment_ID
	) {
		global $db, $log;



		// More DB Checks of arguments !!! (This user can delete this comment?)



		$pin_type = ucfirst( $this->getInfo('pin_type') );



		// Get the comment info
		$db->where('pin_ID', self::$pin_ID);
		$db->where('comment_ID', $comment_ID);
		$db->where('user_ID', currentUserID());
		$comment = $db->getOne('pin_comments');
		if (!$comment) return false;


		// Delete the attachments
		if ($comment['comment_type'] == "attachment") {

			$attachment = explode(' | ', $comment['pin_comment']);
			$fileURL = $attachment[0];
			$fileName = $attachment[1];
			$file_path = substr(parseUrl($fileURL)['path'], 1);


			$file = new File($file_path, "s3");
			$file->delete();

		}



		// Delete the comment
		$db->where('pin_ID', self::$pin_ID);
		$db->where('comment_ID', $comment_ID);
		$db->where('user_ID', currentUserID());
		$comment_deleted = $db->delete('pin_comments');


		if ($comment_deleted) {


			// Update the page modification date
			$pageData = Page::ID( $this->page_ID );
			$pageData->updateModified();


			// Site log
			$log->info("$pin_type Pin #".self::$pin_ID." Comment Deleted: Comment #$comment_ID | '".$pageData->getInfo('page_name')."' Page #".$this->page_ID." | Phase #".$this->phase_ID." | Device #".$this->device_ID." | Project #".$pageData->getInfo('project_ID')." | User #".currentUserID());

		}



		return $comment_deleted;

	}


	// New pin notification
	public function newNotification(
		int $pin_number
	) {
		global $db;


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		if ( $this->getInfo('user_ID') != currentUserID() ) return true;




		// Prepare the message
		$template = $this->emailTemplate($pin_number, getUserInfo()['fullName']." added a new ".$this->getInfo('pin_type')." pin", "View Pin");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();


		// Delete old notifications related to this pin
		$db->where('object_type', 'pin');
		$db->where('object_ID', self::$pin_ID);
		$db->delete('notifications');



		// Web notification
		Notify::ID($users)->web("new", "pin", self::$pin_ID, $pin_number);


		// Email notification
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);


		return true;

	}


	// New comment notification
	public function newCommentNotification(
		int $pin_number
	) {


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		//if ( $this->getInfo('user_ID') != currentUserID() ) return true;



		// Prepare the message
		$template = $this->emailTemplate($pin_number, getUserInfo()['fullName']." wrote a new comment", "View Comments");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();


		// Email notification
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);




		return true;

	}


	// Complete notification
	public function completeNotification(
		int $pin_number
	) {


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		//if ( $this->getInfo('user_ID') != currentUserID() ) return true;



		// Prepare the message
		$template = $this->emailTemplate($pin_number, getUserInfo()['fullName']." completed a pin task", "View Pin");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();


		// Web notification
		Notify::ID($users)->web("complete", "pin", self::$pin_ID, $pin_number);


		// Email notification
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);



		return true;

	}


	// Incomplete notification
	public function inCompleteNotification(
		int $pin_number
	) {


		// Don't send notification if the pin is private
		if ( $this->getInfo('pin_private') == "1" ) return true;

		// Don't send notification if the current user is not pin owner
		//if ( $this->getInfo('user_ID') != currentUserID() ) return true;



		// Prepare the message
		$template = $this->emailTemplate($pin_number, getUserInfo()['fullName']." marked a pin task as incomplete", "View Pin");
		$notificationSubject = $template['subject'];
		$notificationMessage = $template['message'];



		// Send it to all related users
		$users = $this->getUsers();


		// Web notification
		Notify::ID($users)->web("incomplete", "pin", self::$pin_ID, $pin_number);


		// Email notification
		Notify::ID($users)->mail(
			$notificationSubject,
			$notificationMessage
		);



		return true;

	}




	// TEMPLATES
	private function emailTemplate(
		int $pin_number,
		string $pin_message,
		string $button_text
	) {


		// Pin info
		$pin_ID = self::$pin_ID;
		$pin_type = $this->getInfo('pin_type');
		$pin_complete = $this->getInfo('pin_complete');


		// Draw the pin
		$pinShadow = " 0px 0px 18px 1px rgba(0, 0, 0, 0.5)";
		if ($pin_type == "live") $pinShadow = "0px 0px 18px 1px #149440";
		if ($pin_type == "style") $pinShadow = "0px 0px 18px 1px #1DBCC9";
		$pinShape = "


			<div style='

				position: relative;
				display: inline-block;
				border-radius: 50%;
				letter-spacing: 0;
				text-align: center;

				background-color: black;
				color: white;

				width: 45px;
				height: 45px;
				line-height: 45px;
				font-size: 14px;

				box-shadow: $pinShadow;

			'>$pin_number</div>


		";

		if ($pin_complete == 1) $pinShape = "

			<style>
				.pin:hover > div {
					opacity: 0;
				}
			</style>


			<div class='pin' style='

				position: relative;
				display: inline-block;
				border-radius: 50%;
				letter-spacing: 0;
				text-align: center;

				background-color: white;
				color: black;
				border: 1px solid black;

				width: 45px;
				height: 45px;
				line-height: 45px;
				font-size: 14px;

				box-shadow: $pinShadow;

			'><div style='

					font-weight: 900;
					font-size: 17px;
					display: flex;
					justify-content: center;
					align-items: center;
					position: absolute;
					z-index: 2;
					width: inherit;
					height: inherit;
					text-align: center;
					background-color: white;
					border-radius: 50%;
					box-sizing: border-box;
					transition: 500ms;

				'><img src='".asset_url('icons/icon-check.svg')."' alt='Completed' style='max-width: 18px; height: auto;' /></div>$pin_number</div>


		";


		// Page/Project/Phase info
		$phase_ID = $this->phase_ID;
		$device_ID = $this->device_ID;
		$page_ID = $this->page_ID;
		$pageData = Page::ID( $page_ID );
		$project_ID = $pageData->getInfo('project_ID');
		$projectData = Project::ID( $project_ID );



		// Screenshot !!!
		$screenshot = "

			<tr>
				<td colspan='2'>


				</td>
			</tr>

		";



		// Modification info
		$modification_type = $this->getInfo('pin_modification_type');
		$modification = $this->getInfo('pin_modification');


		// CSS changes info
		$css = $this->getInfo('pin_css');


		// Comments info
		$commentsHTML = "";
		$comments = array_reverse( $this->comments() );

		// Print comments if exists
		$commentsList = "No comments";
		$comment_count = 0;
		if ( count($comments) > 0 ) {

			$commentsList = "<table>";

			$previous_user_ID = 0;
			foreach ($comments as $comment) { $comment_count++;

				$different_user = $previous_user_ID != $comment['user_ID'];

				$date = new DateTime($comment['comment_modified']);
				$dateFormatted = $date->format('g:i A - F j, Y');

				$has_photo = $comment['user_picture'] != null;
				$avatar = "
					<div style='

						display: inline-block;
						border: 1px solid black;
						border-radius: 50%;

						background-color: white;
						color: black;

						width: 30px;
						height: 30px;
						line-height: 30px;
						font-size: 12px;
						letter-spacing: 0;
						text-align: center;

						background-repeat: no-repeat;
						background-size: cover;
						background-position: 50% 50%;
						background-image: ".($has_photo ? "url(".$comment['user_picture'].")" : "none").";

					'>".($has_photo ? "" : mb_substr($comment['user_first_name'], 0, 1).mb_substr($comment['user_last_name'], 0, 1))."</div>
				";

				$user_full_name = $different_user ? "<b>".$comment['user_first_name']." ".$comment['user_last_name']."</b><br>" : "";
				$user_pic = $different_user ? $avatar : "";

				$commentsList .= "
				<tr>
					<td width='40' valign='top'>
						".($different_user && $comment_count > 1 ? "<br><br>" : "")."

						$user_pic

					</td>";
					$commentsList .= "
					<td valign='top'>
						".($different_user && $comment_count > 1 ? "<br><br>" : "")."

						$user_full_name
						".$comment['pin_comment']." <span style='display: inline-block; padding-right: 5px; font-size: 10px; line-height: normal; opacity: 0.4; margin-bottom: 10px;'>".$dateFormatted."</span>

					</td>
				</tr>";

				$previous_user_ID = $comment['user_ID'];

			}

			$commentsList .= "</table>";



			// Print HTML
			$commentsHTML = "
			<tr>
				<td colspan='2'>

					<br>
					<h2>Comments ".($comment_count > 0 && $button_text == "View Comments" ? "<span style='display: inline-block; font-size: 10px; color: white; padding: 0 2px; background-color: red; border-radius: 3px;'>NEW</span>" : "")."</h2>
					$commentsList

				</td>
			</tr>";

		}




		return [ 'message' => "

		<table width='100%' style='max-width:600px; margin: 20px 0;'>

			<tr>
				<td colspan='2'>

					<h1>
						<a href='".site_url("phase/$phase_ID#$pin_ID")."' style='text-decoration: none;'>".$projectData->getInfo('project_name')." - ".$pageData->getInfo('page_name')."</a>
					</h1>

				</td>
			</tr>

			<tr>
				<td width='50'>$pinShape</td>
				<td>

					$pin_message. <br>
					<a href='".site_url("phase/$phase_ID#$pin_ID")."'><b>View Comments</b></a>

				</td>
			</tr>

			$commentsHTML

			<tr>
				<td colspan='2'>

					<br>
					<h2>Modifications</h2>
					<ul>
						".($modification_type == "image" && $modification != null ? "<li>Image updated</li>" : "")."
						".($modification_type == "html" && $modification != null ? "<li>Some content changes</li>" : "")."
						".($css != null ? "<li>Some view changes</li>" : "")."
						".($modification == null && $css == null ? "<li>No changes</li>" : "")."
					</ul>


				</td>
			</tr>

			$screenshot

		</table>

		<br><br><br>

		<small>If you don't want to receive these email notifications, update your email notification preference in your account's <a href='".site_url('account/email')."'>Email Settings</a> page.</small>


		", 'subject' => "$pin_message on ".$pageData->getInfo('page_name')."[".$projectData->getInfo('project_name')."] page"];

	}

}