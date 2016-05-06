<?php
define ( 'REGFORM_DIR', plugin_dir_path ( __FILE__ ) );
define ( 'REGFORM_DIR_URL', plugin_dir_url ( __FILE__ ) );

require_once REGFORM_DIR . '/functions.php';
require_once REGFORM_DIR . '/renderHtml.php';
require_once REGFORM_DIR . '/RregistrationFormShortcodeClass.php';

wp_enqueue_script ( 'handlebars', REGFORM_DIR_URL . '/js/handlebars.js' );
wp_enqueue_script ( 'regFormJs', REGFORM_DIR_URL . '/js/script.js', array (
		'jquery' 
) );
wp_enqueue_script ( 'jqueryUI' );
wp_enqueue_script ( "jquery-ui-dialog" );
wp_enqueue_style ( 'wp-jquery-ui-dialog');
wp_enqueue_style ( 'loginAndRegisterForm', REGFORM_DIR_URL . '/style.css' );

add_action ( "init", "regForm_init" );

if (defined ( 'DOING_AJAX' ) && DOING_AJAX) {
	add_action ( 'wp_ajax_nopriv_registerRregistrationFormShortcode', 'RregistrationFormShortcodeClass::register', 30 );
	add_action ( 'wp_ajax_nopriv_loginRregistrationFormShortcode', 'RregistrationFormShortcodeClass::login', 30 );
	add_action ( 'wp_ajax_getUpdateProfileRregistrationFormShortcode', 'RregistrationFormShortcodeClass::getUpdateProfile' );
	add_action ( 'wp_ajax_setUpdateProfileRregistrationFormShortcode', 'RregistrationFormShortcodeClass::setUpdateProfile' );
	add_action('wp', 'RregistrationFormShortcodeClass::autoLogin');
}
// add_action ( 'wp_ajax_nopriv_rememberRregistrationFormShortcode', 'RregistrationFormShortcodeClass::remember', 30 );

add_action ( 'wp_ajax_fromExelRregistrationFormShortcode', 'RregistrationFormShortcodeClass::fromExelRregistration', 30 );
function regForm_init() {
	add_shortcode ( 'registerForm', 'registerForm_func' );
	add_shortcode ( 'loginForm', 'loginForm_func' );
}

function auto_login(){
	
	$loginpageid = 190;
	if (!is_user_logged_in() && is_page($loginpageid)) { //only attempt to auto-login if at www.site.com/auto-login/ (i.e. www.site.com/?p=190 )
		if (isset($_COOKIE['username'])){
		
			$user = get_user_by('email', $_COOKIE['username']);
			$user_id = $user->ID;
			//login
			wp_set_current_user($user_id, $_COOKIE['username']);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $_COOKIE['username']);
			//redirect to home page after logging in (i.e. don't show content of www.site.com/?p=1234 )
			wp_redirect( home_url() );
			exit;
		}
	}
}
add_action('wp', 'auto_login');
?>