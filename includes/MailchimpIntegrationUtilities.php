<?php
class MailchimpIntegrationUtilities {
	public static function addUserFromCourseToGroup($courseId, $groupId) {
		global $wpdb;
		if (get_current_user_id () != 30) {
			return;
		}
		$bp = buddypress ();
		$group = $wpdb->get_row ( $wpdb->prepare ( "SELECT g.* FROM {$bp->groups->table_name} g WHERE g.id = %d", $groupId ) );
		$userIdList = $wpdb->get_results ( $wpdb->prepare ( "SELECT user_id FROM `wp_namaste_student_courses` WHERE course_id=%d", $courseId ) );
		
		foreach ( $userIdList as $userId ) {
			groups_invite_user ( array (
					'user_id' => $userId->user_id,
					'group_id' => $groupId,
					'inviter_id' => $group->creator_id,
					'date_modified' => bp_core_current_time (),
					'is_confirmed' => 1 
			) );
			
			$forums = bbp_get_group_forum_ids ( $groupId );
			bbp_add_user_forum_subscription ( $userId, $forums [0] );
		}
	}
	
	/**
	 * $msg = "test";
	 * try {
	 * $msg .
	 * = print_r(print, true);
	 * } catch (Exception $e) {
	 * $msg .= print_r($e, true);
	 * }
	 * MailchimpIntegrationUtilities::rightToLogFileDavgur($msg);
	 */
	public static function rightToLogFileDavgur($logText) {
		$msg = $logText;
		$path = MAILCHIMPINT_DIR . '/DavgurLog.txt';
		$f = fopen ( $path, "a+" );
		fwrite ( $f, $msg );
		fclose ( $f );
	}
	public static function fromExelRregistration() {
		$row = 0;
		$arrOfIndex = array (
				'first_name' => - 1,
				'last_name' => - 1,
				'user_email' => - 1,
				'country' => - 1,
				'city' => - 1 
		);
		if (($handle = fopen ( MAILCHIMPINT_DIR . "/users.csv", "r" )) !== FALSE) {
			while ( ($data = fgetcsv ( $handle, 1000, "," )) !== FALSE ) {
				if ($row == 0) {
					$maxI = count ( $data );
					for($cI = 0; $cI < $maxI; $cI ++) {
						$arrOfIndex [$data [$cI]] = $cI;
					}
				} else {
					$arrOfData = array (
							'last_name' => ' ',
							'display_name' => ' ',
							'city' => ' ' 
					);
					foreach ( $arrOfIndex as $key => $c ) {
						$arrOfData [$key] = $data [$c];
					}
					// id of course for enroll
					$arrOfData ['enrollToCourse'] = 1957;
					RregistrationFormShortcode::register ( $arrOfData );
				}
				$row ++;
			}
			fclose ( $handle );
		}
	}
	public static function addToMailChimpByCourse($courseId) {
		global $wpdb;
		// just for davgur.ru@gmail.com
		if (get_current_user_id () != 30) {
			return;
		}
		$userIdList = $wpdb->get_results ( $wpdb->prepare ( "SELECT user_id FROM `wp_namaste_student_courses` WHERE course_id=%d", $courseId ) );
		self::addToMailChimpByUsers ( $userIdList, 0 );
	}
	public static function addToMailChimpByUsers($userIdList, $index) {
		if (count ( $userIdList ) > $index) {
			$request = UpdateMailChimpScores ( $userIdList [$index]->user_id );
			self::addToMailChimpByUsers ( $userIdList, ++ $index );
		}
	}
}
?>
