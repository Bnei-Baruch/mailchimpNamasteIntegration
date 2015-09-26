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
				$return ['message'] = __ ( "Login Successful, redirecting...", 'login' );
				// Able enroll user on login
				self::_enrollToCourse ( $userData ['courseId'], $loginResult->ID );
			} elseif (strtolower ( get_class ( $loginResult ) ) == 'wp_error') {
				// User login failed
				// @var WP_Error $loginResult
				$return ['result'] = false;
				$return ['error'] = $loginResult->get_error_message ();
			} else {
				// Undefined Error
				$return ['result'] = false;
				$return ['error'] = __ ( 'An undefined error has ocurred', 'login' );
			}
		} else {
			$return ['result'] = false;
			$return ['error'] = __ ( 'Please supply your username and password.', 'login' );
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
		$fieldList = UserProfile_GetDefaultFieldes ();
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
		if ($fieldsData ['password_confirmation'] != $fieldsData ['user_pass']) {
			$return ['result'] = false;
			$return ['error'] = __ ( 'Password not confirmed.', 'login' );
			$return ['msg'] = $fieldsData ['user_pass'];
			$return ['$pass_conf'] = $fieldsData ['password_confirmation'];
			echo json_encode ( $return );
			if (! $isFromExel) {
				wp_die ();
			}
		}
		
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
			$userByEmail = get_user_by_email ( $fieldListWP ['user_email'] );
			
			if ($userByEmail) {
				$return ['result'] = false;
				$return ['error'] = __ ( 'This email was registred.', 'login' );
				echo json_encode ( $return );
				if (! $isFromExel) {
					wp_die ();
				}
			}
			$fieldList = array (
					fieldListWP => $fieldListWP,
					fieldListBP => $fieldListBP 
			);
			if ($isFromExel)
				$fieldList ['registerFromExel'] = true;
			
			$registerResult = bp_core_signup_user ( $fieldListWP ['user_login'], $fieldListWP ['user_pass'], $fieldListWP ['user_email'], $fieldList );
			if (! is_wp_error ( $registerResult )) {
				// Success
				$return ['result'] = true;
				$return ['userId'] = $registerResult;
				$return ['message'] = __ ( 'Registration complete. Please check your e-mail.', 'login' );
				self::_enrollToCourse ( $userData ['courseId'], $registerResult->ID );
			} else {
				// Something's wrong
				$return ['result'] = false;
				$return ['error'] = ($registerResult == false) ? __ ( 'An undefined error has ocurred', 'login' ) : $registerResult->get_error_message ();
			}
			$return ['action'] = 'register';
		} else {
			$return ['result'] = false;
			$return ['error'] = __ ( 'Registration has been disabled.', 'login' );
		}
		echo json_encode ( $return );
		if (! $isFromExel) {
			wp_die ();
		}
	}
	private static function _enrollToCourse($courseId, $userId) {
		if (is_null ( $courseId )) {
			return;
		}
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
							'user_pass' => '12345',
							'password_confirmation' => '12345',
							'last_name' => '',
							'display_name' => ''
					);
					foreach ( $arrOfIndex as $key => $c ) {
						$arrOfData [$key] = $data [$c];
					}
					self::register ( $arrOfData );
				}
				$row ++;
			}
			fclose ( $handle );
		}
	}
}

?>