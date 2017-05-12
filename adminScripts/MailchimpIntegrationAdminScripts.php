<?php
/* It's base on LoginWithAjax plugin - maybe more better to use this plugin for extending class */
class MailchimpIntegrationAdminScripts {
	public static function login() {
		global $wpdb;
		$return = array ();
		parse_str ( $_POST ['userData'], $userData );
		$user_login = get_user_by_email ( $userData ['user_login'] )->data->user_login;
		$user_password = $userData ['user_pass'];
		
		if (! empty ( $user_login ) && ! empty ( $user_password ) && trim ( $user_login ) != '' && trim ( $user_password ) != '') {
			$credentials = array (
					'user_login' => $user_login,
					'user_password' => $user_password,
					'remember' => $userData ['rememerme'] 
			); // ,
			   // 'remember' => ! empty ( $_REQUEST ['rememberme'] )
			if (isset ( $userData ['rememberme'] )) { // if user check the remember me checkbox
				$expiration_date = 60 * 60 * 24 * 356 + time (); // year
				$domain = ($_SERVER ['HTTP_HOST'] != 'localhost') ? $_SERVER ['HTTP_HOST'] : false;
				$rc = setcookie ( 'username', $userData ['user_login'], $expiration_date, "/", $domain );
			}
			
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
	 * auto login by cookies
	 * TODO: David - I'm think that need remove dependence from post id
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
		$fieldList = self::getUserFieldList ( - 1 );
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
	public static function getUpdateProfile() {
		$isShowDialog = false;
		$data = self::getUserFieldList ();
		$userData = get_user_by_email ( $data ['user_email'] ['val'] )->data;
		foreach ( $data as $key => $val ) {
			if ($key == 'user_email')
				continue;
				// special functional for gender (becouse it's select and not text box)
			if ($key == 'gender') {
				$translate = array (
						'gender' => $val ['translate'],
						'male' => __ ( 'male', 'cfef' ),
						'female' => __ ( 'female', 'cfef' ) 
				);
				if (! empty ( $val ['val'] )) {
					$dataGender = array (
							"male" => '',
							"female" => '',
							'translate' => $translate 
					);
					$dataGender [$val ['val']] = 'selected';
					$data ["gender"] = $dataGender;
					continue;
				}
				
				$data ["gender"] ['translate'] = $translate;
				$isShowDialog = array (
						'val' => $key,
						'val1' => $val ['val'],
						'val2' => $translate 
				);
				continue;
			}
			// check is fields are empty or has default value
			if (empty ( $val ['val'] ) || strpos ( $val ['val'], $val ['translate'] ) !== false || ($key == 'display_name' && $val ['val'] == $userData->user_nicename) || ($key == 'display_name' && $val ['val'] == $userData->user_email) || ($key == 'display_name' && strpos ( $val ['val'], '-' ))) {
				$isShowDialog = array (
						'val' => $key,
						'val1' => $val ['val'],
						'val2' => $val ['translate'],
						'strpos' => strpos ( $val ['val'], $val ['translate'] ) 
				);
				continue;
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
		$fieldList = self::getUserFieldList ( - 1 );
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
		
		self::setUserFieldList ( $fieldListWP, $fieldListBP, $user_id );
		$args = array (
				'ID' => $user_id,
				'display_name' => $fieldListWP ['display_name'] 
		);
		wp_update_user ( $args );
		
		$data ['result'] = true;
		
		wp_die ( json_encode ( $data ) );
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
	public static function setUserFieldList($fieldListWP, $fieldListBP, $user_id = 1) {
		if ($fieldListWP != null) {
			// If need more complexy display_name can use - bp_core_get_user_displayname
			if (empty ( $fieldListWP ['display_name'] ))
				$fieldListWP ['display_name'] = $fieldListWP ['last_name'] . ' ' . $fieldListWP ['first_name'];
			
			$user = get_user_by ( "id", $user_id );
			// don't update password and email
			unset ( $fieldListWP ["user_pass"] );
			unset ( $fieldListWP ["user_email"] );
			$fieldListWP ["ID"] = $user_id;
			wp_update_user ( $fieldListWP );
		}
		
		$groupParam = array (
				'user_id' => $user_id,
				'fetch_fields' => true,
				'fetch_field_data' => true 
		);
		$group = bp_xprofile_get_groups ( $groupParam )[0];
		$newFieldOpt = array (
				'field_group_id' => $group->id,
				'name' => '',
				'type' => 'textbox',
				'is_required' => true,
				'can_delete' => false 
		);
		
		foreach ( $fieldListBP as $key => $val ) {
			$hasCurrentField = true;
			foreach ( $group->fields as $field ) {
				// delete name field
				if ($field->name == 'name')
					xprofile_delete_field ( $field->id );
				if ($field->name == $key) {
					$hasCurrentField = false;
					break;
				}
			}
			if ($hasCurrentField) {
				$newFieldOpt ['name'] = $key;
				$fieldId = xprofile_insert_field ( $newFieldOpt );
			}
			$fieldSetId = xprofile_set_field_data ( $key, $user_id, $val );
		}
	}
}

?>