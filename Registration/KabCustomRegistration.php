<?php
require_once MAILCHIMPINT_DIR . './Registration/KabCustomRegistrationHelper.php';
class KabCustomRegistration {
	public static function login() {
		global $wpdb;
		parse_str ( $_POST ['userData'], $userData );
		$user_login = get_user_by_email ( $userData ['user_login'] )->data->user_login;
		$user_password = $userData ['user_pass'];
		
		if (! empty ( $user_login ) && ! empty ( $user_password ) && trim ( $user_login ) != '' && trim ( $user_password ) != '') {
			$credentials = array (
					'user_login' => $user_login,
					'user_password' => $user_password,
					'remember' => $userData ['rememerme'] 
			);
			if (isset ( $userData ['rememberme'] )) { // if user check the remember me checkbox
				self::saveCookiesOnRemembeMe ( $userData ['user_login'] );
			}
			
			$return = self::parseLoginResult( wp_signon ( $credentials, true ) );
		} else {
			$return = array (
					'result' => false,
					'error' => __ ( '<strong>ERROR</strong>: Invalid username or incorrect password.' ) 
			);
		}
		$return ['action'] = 'login';
		
		echo json_encode ( $return );
		wp_die ();
	}
	/**
	 * auto login by cookies
	 */
	public static function autoLogin() {
		$loginpageid = 190;
		if (! is_user_logged_in () && is_page ( $loginpageid )) { // only attempt to auto-login if at www.site.com/auto-login/ (i.e. www.site.com/?p=190 )
			if (isset ( $_COOKIE ['username'] )) {
				
				$user = get_user_by ( 'email', $_COOKIE ['username'] );
				$user_id = $user->ID;
				// login
				wp_set_current_user ( $user_id, $_COOKIE ['username'] );
				wp_set_auth_cookie ( $user_id );
				do_action ( 'wp_login', $_COOKIE ['username'] );
				// redirect to home page after logging in (i.e. don't show content of www.site.com/?p=1234 )
				wp_redirect ( home_url () );
				exit ();
			}
		}
	}
	
	/**
	 * Checks post data and registers user, then exits
	 *
	 * @return string
	 */
	public static function register($userData = NULL) {
		global $wpdb;
		$fieldList = KabCustomRegistrationHelper::getUserFieldList ( - 1 );
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
		
		$fieldListWP ['user_pass'] = wp_generate_password ();
		foreach ( $fieldList as $key => $val ) {
			if ($fieldList [$key] ['type'] == "wp")
				$fieldListWP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
			else
				$fieldListBP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
		}
		
		$fieldListWP ['user_login'] = $fieldListWP ['user_email'];
		// $fieldListWP ['display_name'] = empty ( $fieldListWP ['first_name'] ) ? $fieldListWP ['user_login'] : $fieldListWP ['first_name'];
		
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
				if (is_numeric ( $fieldsData ['enrollToCourse'] )) {
					$return ['result'] = true;
					$return ['to_page'] = '/login/';
					echo json_encode ( $return );
					wp_die ();
				}
				
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
	
	/*
	 * Private functions
	 */
	private static function saveCookiesOnRemembeMe($userName) {
		$expiration_date = 60 * 60 * 24 * 356 + time (); // year
		$domain = ($_SERVER ['HTTP_HOST'] != 'localhost') ? $_SERVER ['HTTP_HOST'] : false;
		$rc = setcookie ( 'username', $userName, $expiration_date, "/", $domain );
	}
	private static function parseLoginResult($result) {
		$return = array ();
		$user_role = 'null';
		if (strtolower ( get_class ( $result ) ) == 'wp_user') {
			$return ['result'] = true;
			$return ['message'] = __ ( 'You have logged in successfully.' );
			// Able enroll user on login
			// self::_enrollToCourse ( $userData ['courseId'], $result->ID );
		} elseif (strtolower ( get_class ( $result ) ) == 'wp_error') {
			// User login failed
			$return ['result'] = false;
			$return ['error'] = $result->get_error_message ();
		} else {
			// Undefined Error
			$return ['result'] = false;
			$return ['error'] = __ ( 'An error occurred. Please try again later.' );
		}
		return $return;
	}
}

?>