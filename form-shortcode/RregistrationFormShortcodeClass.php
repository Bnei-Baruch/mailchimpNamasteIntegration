<?php
/* It's base on LoginWithAjax plugin - maybe more better to use this plugin for extending class */
class RregistrationFormShortcodeClass {
	public static function login() {
		global $wpdb;
		$return = array ();
		parse_str ( $_POST ['userData'], $userData );
		$user_login = get_user_by_email ( $userData ['user_login'] )->data->user_login;
		$user_password = $userData ['user_pass'];
		
		if (! empty ( $user_login ) && ! empty ( $user_password ) && trim ( $user_login ) != '' && trim ( $user_password ) != '') {
			$credentials = array (
					'user_login' => $user_login,
					'user_password' => $user_password 
			); // ,
			   // 'remember' => ! empty ( $_REQUEST ['rememberme'] )
			
			$loginResult = wp_signon ( $credentials, true );
			// $loginResult = wp_authenticate($user_login, $user_password);
			$user_role = 'null';
			if (strtolower ( get_class ( $loginResult ) ) == 'wp_user') {
				$return ['result'] = true;
				$return ['message'] = __ ( 'You have logged in successfully.' );
				// Able enroll user on login
				// self::_enrollToCourse ( $userData ['courseId'], $loginResult->ID );
			} elseif (strtolower ( get_class ( $loginResult ) ) == 'wp_error') {
				// User login failed
				// @var WP_Error $loginResult
				$return ['result'] = false;
				$return ['error'] = $loginResult->get_error_message ();
			} else {
				// Undefined Error
				$return ['result'] = false;
				$return ['error'] = __ ( 'An error occurred. Please try again later.' );
			}
		} else {
			$return ['result'] = false;
			$return ['error'] = __ ( '<strong>ERROR</strong>: Invalid username or incorrect password.' );
		}
		$return ['action'] = 'login';
		
		echo json_encode ( $return );
		wp_die ();
	}
	
	/**
	 * Checks post data and registers user, then exits
	 *
	 * @return string
	 */
	public static function register($userData = NULL) {
		global $wpdb;
		$fieldList = UserProfile_GetDefaultFieldes ( - 1 );
		$fieldListWP = array ();
		$fieldListBP = array ();
		$return = array ();
		$isFromExel = false;
		
		if (is_null ( $userData ) || empty ( $userData ))
			$userData = parse_str ( $_POST ['userData'], $fieldsData );
		else {
			$isFromExel = true;
			$fieldsData = $userData;
		}
		
		// break if two password fieldes are not equal
		/*
		 * if ($fieldsData ['password_confirmation'] != $fieldsData ['user_pass']) {
		 * $return ['result'] = false;
		 * $return ['error'] = __ ( 'Password not confirmed.', 'login' );
		 * $return ['msg'] = $fieldsData ['user_pass'];
		 * $return ['$pass_conf'] = $fieldsData ['password_confirmation'];
		 * echo json_encode ( $return );
		 * if (! $isFromExel) {
		 * wp_die ();
		 * }
		 * }
		 */
		
		$fieldListWP ['user_pass'] = wp_generate_password ();
		foreach ( $fieldList as $key => $val ) {
			if ($fieldList [$key] ['type'] == "wp")
				$fieldListWP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
			else
				$fieldListBP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
		}
		
		$fieldListWP ['user_login'] = $fieldListWP ['user_email'];
		$fieldListWP ['nick_name'] = empty ( $fieldListWP ['first_name'] ) ? $fieldListWP ['user_login'] : $fieldListWP ['first_name'];
		
		if (get_option ( 'users_can_register' )) {
			
			if (! function_exists ( 'register_new_user' )) {
				// in ajax we don't have access to this function, so include our own copy of the function
				include_once ('registration.php');
			}
			// if it's not error - this is user id
			$fieldListWP ['user_email'] = sanitize_email ( $fieldListWP ['user_email'] );
			if (empty ( $fieldListWP ['user_email'] )) {
				$return ['result'] = false;
				$return ['error'] = __ ( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' );
				echo json_encode ( $return );
				wp_die ();
			}
			
			$userByEmail = get_user_by_email ( $fieldListWP ['user_email'] );
			
			if ($userByEmail) {
				$return ['result'] = false;
				$return ['error'] = __ ( 'Sorry, that email address is already used!' );
				if (! $isFromExel) {
					echo json_encode ( $return );
					wp_die ();
				} else {
					echo '+';
					return;
				}
			}
			$fieldList = array (
					fieldListWP => $fieldListWP,
					fieldListBP => $fieldListBP 
			);
			
			if (is_numeric ( $fieldsData ['enrollToCourse'] ))
				$fieldList ['enrollToCourse'] = $fieldsData ['enrollToCourse'];
			
			$registerResult = bp_core_signup_user ( $fieldListWP ['user_login'], $fieldListWP ['user_pass'], $fieldListWP ['user_email'], $fieldList );
			if (! is_wp_error ( $registerResult )) {
				// Success
				$return ['result'] = true;
				$return ['userId'] = $registerResult;
				
				if (is_numeric ( $fieldsData ['enrollToCourse'] ))
					self::_enrollToCourse ( $fieldsData ['enrollToCourse'], $registerResult );
				
				$return ['message'] = array (
						'title' => __ ( 'Registration, check you mail. Title', 'qode' ),
						'content' => __ ( 'Registration, check you mail. Content', 'qode' ) 
				);
				$return ['buttons'] = array (
						'toHome' => array (
								'text' => __ ( 'Go to home page' ),
								'url' => get_site_url () 
						) 
				);
			} else {
				// Something's wrong
				$return ['result'] = false;
				$return ['error'] = ($registerResult == false) ? __ ( 'An error occurred. Please try again later.' ) : $registerResult->get_error_message ();
			}
			$return ['action'] = 'register';
		} else {
			$return ['result'] = false;
			$return ['error'] = __ ( 'User registration has been disabled.' );
		}
		echo json_encode ( $return );
		if (! $isFromExel) {
			wp_die ();
		}
	}
	private static function _enrollToCourse($courseId, $userId) {
		$_course = new NamasteLMSCourseModel ();
		// enroll in course
		$course = $_course->select ( $courseId );
		$enroll_mode = get_post_meta ( $course->ID, 'namaste_enroll_mode', true );
		
		// if already enrolled, just skip this altogether
		$_course->enroll ( $userId, $course->ID, 'enrolled' );
	}
	
	// Reads ajax login creds via POSt, calls the login script and interprets the result
	public static function remember() {
		global $wpdb, $wp_hasher;
		$return = array (); // What we send back
		$result = retrieve_password ();
		if ($result === true) {
			// Password correctly remembered
			$return ['result'] = true;
			$return ['message'] = __ ( "We have sent you an email", 'login' );
		} elseif (strtolower ( get_class ( $result ) ) == 'wp_error') {
			// Something went wrong
			/* @var $result WP_Error */
			$return ['result'] = false;
			$return ['error'] = $result->get_error_message ();
		} else {
			// Undefined Error
			$return ['result'] = false;
			$return ['error'] = __ ( 'An undefined error has ocurred', 'login' );
		}
		$return ['action'] = 'remember';
		// Return the result array with errors etc.
		return $return;
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
					self::register ( $arrOfData );
				}
				$row ++;
			}
			fclose ( $handle );
		}
	}
	public static function getUpdateProfile() {
		$isShowDialog = false;
		$data = UserProfile_GetDefaultFieldes ();
		$userData = get_user_by_email ( $data ['user_email'] ['val'] )->data;
		foreach ( $data as $key => $val ) {
			if($key == 'user_email')
				continue;
			if (empty ( $val ['val'] ) || strpos ( $val ['val'], $val ['translate'] ) !== false) {
				$isShowDialog = array (
						'val' => $key,
						'val1' => $val ['val'],
						'val2' => $val ['translate'],
						'strpos' => strpos ( $val ['val'], $val ['translate'] ) 
				);
				break;
			}
		}
		
		$data ['result'] = $isShowDialog;
		$data ['translate'] = array (
				'title' => __ ( 'Update profile title', 'cfef' ),
				'save' => __ ( 'Save' ),
				'cancel' => __ ( 'Cancel' ) 
		);
		
		wp_die ( json_encode ( $data ) );
	}
	public static function setUpdateProfile() {
		$fieldList = UserProfile_GetDefaultFieldes ( - 1 );
		$fieldListWP = array ();
		$fieldListBP = array ();
		$return = array ();
		
		$user_id = get_current_user_id ();
		$userData = parse_str ( $_POST ['userData'], $fieldsData );
		foreach ( $fieldList as $key => $val ) {
			if ($fieldList [$key] ['type'] == "wp")
				$fieldListWP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
			else
				$fieldListBP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
		}
		
		UserProfile_SetDefaultFieldes ( $fieldListWP, $fieldListBP, $user_id );
		$args = array (
				'ID' => $user_id,
				'display_name' => $fieldListWP ['display_name'] 
		);
		wp_update_user ( $args );
		
		$data ['result'] = true;
		
		wp_die ( json_encode ( $data ) );
	}
}

?>