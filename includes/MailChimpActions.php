<?php
class MailChimpActions {
	public static function addCourse($post_ID, $post) {
		$condition = array (
				'field' => 'COURSES',
				'op' => 'like',
				'value' => ('[' . strval ( $post_ID ) . ']') 
		);
		$segParam = array (
				'match' => 'all',
				'conditions' => array (
						$condition 
				) 
		);
		$optsParam = array (
				'type' => 'saved',
				'name' => strval ( $post_ID ),
				'segment_opts' => $segParam 
		);
		$sendObj = new MailChimpSend ( 'createSegment' );
		$sendObj->parameters ['opts'] = $optsParam;
		$sendObj->SendToMailChimp ();
	}
	public static function updateScores($userId = NULL, $lastScores = 0) {
		global $wpdb;
		$userId = ($userId == NULL) ? get_current_user_id () : $userId;
		
		$scores = get_user_meta ( $userId, 'namaste_points', true );
		$aCourseList = $wpdb->get_col ( $wpdb->prepare ( "SELECT course_id FROM " . NAMASTE_STUDENT_COURSES . " WHERE user_id = %d AND status = %d", $userId, 'enrolled' ) );
		$sendObj = new MailChimpSend ( 'setUserData', get_userdata ( $userId )->data->user_email );
		$sendObj->parameters ['merge_vars'] = array (
				'COURSES' => '[' . implode ( "],[", $aCourseList ) . ']',
				'SCORES' => $scores 
		);
		$request = $sendObj->SendToMailChimp ();
		return $request;
	}
	public static function updateParams($userId = NULL) {
		global $wpdb;
		$userId = ($userId == NULL) ? get_current_user_id () : $userId;
		$fieldList = self::getUserFieldList ( $userId );
		$aCourseList = $wpdb->get_col ( $wpdb->prepare ( "SELECT course_id FROM " . NAMASTE_STUDENT_COURSES . " WHERE user_id = %d AND status = %d", $userId, 'enrolled' ) );
		$scores = get_user_meta ( $userId, 'namaste_points', true );
		
		$mergeVars = array (
				'FNAME' => $fieldList ['first_name'] ["val"],
				'LNAME' => $fieldList ['last_name'] ["val"],
				'CITY' => $fieldList ['city'] ["val"],
				'COUNTRY' => $fieldList ['country'] ["val"],
				'AGE' => $fieldList ['age'] ["val"],
				'GENDER' => $fieldList ['gender'] ["val"],
				'COURSES' => '[' . implode ( "],[", $aCourseList ) . ']',
				'SCORES' => $scores 
		);
		
		$defParam = array (
				'merge_vars' => $mergeVars,
				'double_optin' => false,
				'update_existing' => false,
				'send_welcome' => false 
		);
		
		$mailChimpid = get_option ( 'mailChimpConstant' )['mailchimpId'];
		$userEmail = $fieldList ["user_email"] ["val"];
		$sendObj = new MailChimpSend ( 'listSubscribe', $userEmail, $mailChimpid );
		$userInfo = $sendObj->GetCurrentUserInfo ();
		$paramTemp = $sendObj->GetParametrs ();
		
		if (count ( $userInfo->data ) > 0) {
			$sendObj->metodId = "setUserData";
			$paramTemp ['replace_interests'] = true;
			$paramTemp ['email'] = $userInfo->data [0];
		}
		$newParamTemp = array_merge ( ( array ) $paramTemp, $defParam );
		$sendObj->SetParametrs ( $newParamTemp );
		$request = $sendObj->SendToMailChimp ();
		return $request;
	}
	public static function unsubscribe($user_id) {
		$user_obj = get_userdata ( $user_id );
		
		$sendObj = new MailChimpSend ( 'unsubscribe', $user_obj->user_email );
		$sendObj->parameters ["delete_member"] = true;
		$request = $sendObj->SendToMailChimp ();
		return $request;
	}
	public static function synchronization_wp_user() {
		/*
		 * if(!is_user_logged_in() || get_user_meta(get_current_user_id(), 'updated_from_bp', true) == '1')
		 * return;
		 *
		 * $last_name = bp_get_profile_field_data( array( 'field' => 'Ð¤Ð°Ð¼Ð¸Ð»Ð¸Ñ�', 'user_id' => get_current_user_id()));
		 * $first_name = bp_get_profile_field_data( array( 'field' => 'Ð˜Ð¼Ñ�', 'user_id' => get_current_user_id()));
		 * $display_name = (isset($last_name) ? $last_name.' '.$first_name : $first_name);
		 *
		 * wp_update_user(array(
		 * 'ID' => get_current_user_id(),
		 * 'display_name' => $display_name,
		 * 'first_name' => $first_name,
		 * 'last_name' => $last_name
		 * ));
		 * update_user_meta(get_current_user_id(), 'updated_from_bp', '1');
		 */
	}
	public static function getUserFieldList($user_id = 0) {
		$user_id = $user_id == 0 ? get_current_user_id () : $user_id;
		$fieldList = array (
				'first_name' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Your First Name', 'cfef' ) 
				),
				'last_name' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Last Name', 'cfef' ) 
				),
				'display_name' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Display Name', 'cfef' ) 
				),
				'user_email' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Email', 'cfef' ) 
				) 
		);
		
		foreach ( get_option ( 'mailChimpFieldList' ) as $key => $val ) {
			$fieldVal = $user_id == - 1 ? "" : xprofile_get_field_data ( $val, $user_id );
			$fieldList [$val] = array (
					'val' => $fieldVal,
					'type' => 'bp',
					'translate' => __ ( $val, 'cfef' ) 
			);
		}
		
		if ($user_id == - 1)
			return $fieldList;
		$currentUser = get_user_by ( "id", $user_id );
		$fieldList ['last_name'] ['val'] = get_userdata ( $user_id )->last_name;
		$fieldList ['first_name'] ['val'] = get_userdata ( $user_id )->first_name;
		$fieldList ['display_name'] ['val'] = $currentUser->data->display_name;
		$fieldList ['user_email'] ['val'] = $currentUser->data->user_email;
		
		return $fieldList;
	}
}

?>