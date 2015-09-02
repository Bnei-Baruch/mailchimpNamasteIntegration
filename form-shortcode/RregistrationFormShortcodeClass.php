<?php
/* It's base on LoginWithAjax plugin - maybe more better to use this plugin for extending class */
class RregistrationFormShortcodeClass {
	public static function login($user_login, $user_password) {
		$return = array ();
		$user_login = get_user_by_email ( $user_login )->data->user_login;
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
			} elseif (strtolower ( get_class ( $loginResult ) ) == 'wp_error') {
				// User login failed
				/* @var WP_Error $loginResult */
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
		// Return the result array with errors etc.
		return $return;
	}
	
	/**
	 * Checks post data and registers user, then exits
	 *
	 * @return string
	 */
	public static function register($fieldListWP, $fieldListBP, $pass_conf) {
		$return = array ();
		if ($pass_conf != $fieldListWP ['user_pass']) {
			$return ['result'] = false;
			$return ['error'] = __ ( 'Password not confirmed.', 'login' );
			$return ['msg'] = $fieldListWP ['user_pass'];
			$return ['$pass_conf'] = $pass_conf;
			return $return;
		}
		if (get_option ( 'users_can_register' )) {
			
			if (! function_exists ( 'register_new_user' )) {
				include_once ('registration.php'); // in ajax we don't have access to this function, so include our own copy of the function
			}
			// if it's not error - this is user id
			$fieldListWP ['user_email'] = sanitize_email ( $fieldListWP ['user_email'] );
			$userByEmail = get_user_by_email ( $fieldListWP ['user_email'] );
			
			if ($userByEmail) {
				$return ['result'] = false;
				$return ['error'] = __ ( 'This email was registred.', 'login' );
				return $return;
			}
			$fieldList = array (
					fieldListWP => $fieldListWP,
					fieldListBP => $fieldListBP 
			);
			$errors = bp_core_signup_user ( $fieldListWP ['user_login'], $fieldListWP ['user_pass'], $fieldListWP ['user_email'], $fieldList );
			if (! is_wp_error ( $errors )) {
				// Success
				$return ['result'] = true;
				$return ['userId'] = $errors;
				$return ['message'] = __ ( 'Registration complete. Please check your e-mail.', 'login' );
			} else {
				// Something's wrong
				$return ['result'] = false;
				$return ['error'] = ($errors == false) ? __ ( 'An undefined error has ocurred', 'login' ) : $errors->get_error_message ();
			}
			$return ['action'] = 'register';
		} else {
			$return ['result'] = false;
			$return ['error'] = __ ( 'Registration has been disabled.', 'login' );
		}
		return $return;
	}
	
	// Reads ajax login creds via POSt, calls the login script and interprets the result
	public static function remember() {
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
	private static function wpmu_signup_user($user, $user_pass, $user_email, $meta = array()) {
		global $wpdb;
		
		// Format data
		$user = preg_replace ( '/\s+/', '', sanitize_user ( $user, true ) );
		$user_email = sanitize_email ( $user_email );
		$key = substr ( md5 ( time () . rand () . $user_email ), 0, 16 );
		// $meta['password'] = $user_pass;
		$meta = serialize ( $meta );
		$table = ($wpdb->signups != null) ? $wpdb->signups : 'wp_signups';
		
		$temp = $wpdb->insert ( $table, array (
				'domain' => '',
				'path' => '',
				'title' => '',
				'new_role' => "subscriber",
				'add_to_blog' => get_current_blog_id (),
				'user_login' => $user,
				'user_email' => $user_email,
				'registered' => current_time ( 'mysql', true ),
				'activation_key' => $key,
				'meta' => $meta 
		), '%s' );
		
		wpmu_signup_user_notification ( $user, $user_email, $key, $meta );
	}
}

?>