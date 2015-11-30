<?php
function registerForm_func($args) {
	$isAdmin = wp_get_current_user ()->roles [0] == "administrator";
	
	if (is_user_logged_in () && ! $isAdmin)
		return '<h1>' . __ ( 'The user is logged in.', 'cfef' ) . '</h1>';
	elseif ($isAdmin) {
		return '<a href="" id="registerUsersFromExel" class="button medium submit">' . __ ( 'Register', 'cfef' ) . ' From EXEL</a></form>';
	}
	$fieldsId = get_option ( 'mailChimpFieldList' );
	$fieldList = array_merge ( UserProfile_GetDefaultFieldes (), $fieldsId );
	$fieldListNew = array ();
	
	foreach ( $fieldList as $key => $val ) {
		$sModalHtml = "<form id=\"formAddEmptyXProfile\">";
		$sModalHtml .= "<input type=\"text\" name=\"country\" \\>";
		$sModalHtml .= "<input type=\"text\" name=\"city\" \\>";
		$sModalHtml .= "</form>";
		$sModalHtml .= "</form>";
		$fieldType = 'wp';
		$formType = 'text';
		// TODO:David - this is crutch for separate default & customise properties
		$_id = gettype ( $val ) == "array" ? $key : $val;
		
		$fieldValue = $fieldList [$_id] != null ? $fieldList [$_id] ["value"] : "";
		switch ($_id) {
			case 'first_name' :
				$translateText = 'Your First Name';
				break;
			case 'last_name' :
				$translateText = 'Last Name';
				break;
			case 'user_email' :
				$translateText = 'Email';
				$formType = 'email';
				break;
			case 'country' :
				$translateText = 'country';
				$fieldType = "bp";
				break;
			case 'city' :
				$translateText = 'city';
				$fieldType = "bp";
				break;
			default :
				continue 2;
		}
		$fieldListNew [$_id] = array (
				translation => $translateText,
				value => $fieldValue,
				formType => $formType,
				fieldType => $fieldType,
				id => $_id 
		);
	}
	
	$str = '<form class="lr-form" id="registrationForm" actoin="#" metod="post">';
	$str .= '<div class="preloader"></div>';
	$str .= '<div class="errorMsg" style="color: red;display: none;"></div>';
	foreach ( $fieldListNew as $field ) {
		$str .= '<div class="form-field">				
	            <label for="' . $field ["id"] . '">' . __ ( $field ["translation"], 'cfef' ) . '</label>
	            <input id="' . $field ["id"] . '" type="' . $field ["formType"] . '" name="' . $field ["id"] . '" placeholder="' . __ ( $field ["translation"], 'cfef' ) . ' *" required>
	        </div>';
	}
	if (isset ( $args ['enrollto'] ))
		$str .= '<input type="hidden" id="enrollToCourse" name="enrollToCourse" value = "' . $args ["enrollto"] . '">';
	$str .= '<button type="submit" class="button medium submit">' . __ ( 'Register', 'cfef' ) . '</button></form>';
	
	return $str;
}
function loginForm_func($args) {
	
	$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";;
	//$password = isset($_COOKIE['password']) ? $_COOKIE['password'] : "";
	$forgetmenot = isset($_COOKIE['username']) ? " checked=\"checked\"" : "";
	
	if (is_user_logged_in ()) {
		return '<h1>' . __ ( 'The user is logged in.', 'cfef' ) . '</h1>';
	}
		
	$str = '<form class="lr-form" id="loginForm" actoin="#" metod="post">';
	$str .= '<div class="preloader"></div>';
	$str .= '<div class="errorMsg" style="color: red;display: none;"></div>';
	
	$str .= '<div class="form-field">
	            <label for="user_login">' . __ ( 'Email', 'cfef' ) . ' <strong>*</strong></label>
	            <input id="user_login" type="email" name="user_login" placeholder="' . __ ( 'Email', 'cfef' ) . '" value="'. $username .'" required>
	        </div>';
	$str .= '<div class="form-field">
	            <label for="user_pass">' . __ ( 'Password', 'cfef' ) . ' <strong>*</strong></label>
	            <input id="user_pass" type="password" name="user_pass" placeholder="' . __ ( 'Password', 'cfef' ) . '" required>
	        </div>';
	$str .= '<div class="rememberMe">
				<input name="rememberme" id="rememberme"  type="checkbox" '. $forgetmenot .' />
				<label for="rememberme">' . __ ( 'Remember Me' ) . '</label>
       			<input type="hidden" name="redirect_to" value="' . $_SERVER ['REQUEST_URI'] . '" >
    		</div>';
	$str .= '<button type="submit" class="button medium submit">' . __ ( 'Log In', 'cfef' ) . '</button>';
	$str .= '<a href = "../wp-login.php?action=lostpassword" id="rememberPass">' . __ ( 'Reset Password', 'cfef' ) . '</a> </form>';
	return $str;
}

?>