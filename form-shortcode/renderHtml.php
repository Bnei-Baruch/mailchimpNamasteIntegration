<?php
function registerForm_func($args) {
	$isAdmin = wp_get_current_user ()->roles [0] == "administrator";
	
	if (is_user_logged_in () && ! $isAdmin) {
		if (isset ( $args ['enrollto'] )) {
			$enrollArgs = array (
					course_id => $args ['enrollto'] 
			);
			return NamasteLMSShortcodesController::enroll ( $enrollArgs );
		} else {
			return '<h1>' . __ ( 'The user is logged in.', 'cfef' ) . '</h1>';
		}
	} elseif ($isAdmin) {
		return '<a href="" id="registerUsersFromExel" class="button medium submit">' . __ ( 'Register', 'cfef' ) . ' From EXEL</a></form>';
	}
	
	ob_start ();
	echo '<form class="lr-form" id="registrationForm" actoin="#" metod="post">';
	echo '<div class="preloader"></div>';
	echo '<div class="errorMsg" style="color: red;display: none;"></div>';
	?>
<div class="form-field">
	<label for="first_name"><?php echo __ ( 'Your First Name', 'cfef' ); ?> <strong>*</strong></label>
	<input id="first_name" type="text" name="first_name"
		placeholder="<?php echo __ ( 'Your First Name', 'cfef' ); ?>" required>
</div>
<div class="form-field">
	<label for="last_name"><?php echo __ ( 'Last Name', 'cfef' ); ?> <strong>*</strong></label>
	<input id="last_name" type="text" name="last_name"
		placeholder="<?php echo __ ( 'Last Name', 'cfef' ); ?>" required>
</div>
<div class="form-field">
	<label for="user_email"><?php echo __ ( 'Email', 'cfef' ); ?> <strong>*</strong></label>
	<input id="user_email" type="email" name="user_email"
		placeholder="<?php echo __ ( 'Email', 'cfef' ); ?>" required>
</div>
<div class="form-field" id="registerUsersCountryByText">
	<label for="country"><?php echo __ ( 'country', 'cfef' ); ?> <strong>*</strong></label>
	<input id="country" name="country"
		placeholder="<?php echo __ ( 'country', 'cfef' ); ?>" required>
</div>
<div class="form-field" id="registerUsersCityByText">
	<label for="city"><?php echo __ ( 'city', 'cfef' ); ?> <strong>*</strong></label>
	<input id="city" type="text" name="city"
		placeholder="<?php echo __ ( 'city', 'cfef' ); ?>" required>
</div>

<?php
	if (isset ( $args ['enrollto'] )) {
		?>
<input type="hidden" id="enrollToCourse" name="enrollToCourse"
	value="<?php echo  $args ["enrollto"]?>">
<?php }?>
<button type="submit" class="button medium submit"><?php echo  __ ( 'Register', 'cfef' ) ?></button>
</form>
<?php
	
	$data = ob_get_contents ();
	ob_end_clean ();
	
	return $data;
}
function loginForm_func($args) {
	$username = isset ( $_COOKIE ['username'] ) ? $_COOKIE ['username'] : "";
	$forgetmenot = isset ( $_COOKIE ['username'] ) ? " checked=\"checked\"" : "";
	
	if (is_user_logged_in ()) {
		return '<h1>' . __ ( 'The user is logged in.', 'cfef' ) . '</h1>';
	}
	
	ob_start ();
	?>

<form class="lr-form" id="loginForm" actoin="#" metod="post">
	<div class="preloader"></div>
	<div class="errorMsg" style="color: red; display: none;"></div>

	<div class="form-field">
		<label for="user_login"><?php echo __ ( 'Email', 'cfef' ); ?> <strong>*</strong></label>
		<input id="user_login" type="email" name="user_login"
			placeholder="<?php echo __ ( 'Email', 'cfef' ); ?>" required
			autocomplete="on">
	</div>
	<div class="form-field">
		<label for="user_pass"><?php echo __ ( 'Password', 'cfef' ); ?> <strong>*</strong></label>
		<input id="user_pass" type="password" name="user_pass"
			placeholder="<?php echo __ ( 'Password', 'cfef' ); ?>" required
			autocomplete="on">
	</div>
	<div class="rememberMe">
		<input name="rememberme" id="rememberme" type="checkbox"
			<?php echo $forgetmenot?> /> <label for="rememberme"><?php echo __ ( 'Remember Me' ); ?></label>
		<input type="hidden" name="redirect_to"
			value="<?php echo $_SERVER ['REQUEST_URI'];?>">
	</div>
	<button type="submit" class="button medium submit"><?php __ ( 'Log In', 'cfef' ); ?></button>
	<a href="../wp-login.php?action=lostpassword" id="rememberPass"><?php echo __ ( 'Log In', 'cfef' ); ?></a>
</form>
<?php
	$data = ob_get_contents ();
	ob_end_clean ();
	return $data;
}
