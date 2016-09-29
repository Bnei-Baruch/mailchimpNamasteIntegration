<?php
define ( 'REGFORM_DIR', plugin_dir_path ( __FILE__ ) );
define ( 'REGFORM_DIR_URL', plugin_dir_url ( __FILE__ ) );

require_once REGFORM_DIR . '/renderHtml.php';
require_once REGFORM_DIR . '/RregistrationFormShortcode.php';

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
	add_action ( 'wp_ajax_nopriv_registerRregistrationFormShortcode', 'RregistrationFormShortcode::register', 30 );
	add_action ( 'wp_ajax_nopriv_loginRregistrationFormShortcode', 'RregistrationFormShortcode::login', 30 );
	add_action ( 'wp_ajax_getUpdateProfileRregistrationFormShortcode', 'RregistrationFormShortcode::getUpdateProfile' );
	add_action ( 'wp_ajax_setUpdateProfileRregistrationFormShortcode', 'RregistrationFormShortcode::setUpdateProfile' );
	add_action('wp', 'RregistrationFormShortcode::autoLogin');
}
// add_action ( 'wp_ajax_nopriv_rememberRregistrationFormShortcode', 'RregistrationFormShortcode::remember', 30 );

add_action ( 'wp_ajax_fromExelRregistrationFormShortcode', 'RregistrationFormShortcode::fromExelRregistration', 30 );
function regForm_init() {
	add_shortcode ( 'registerForm', 'registerForm_func' );
	add_shortcode ( 'loginForm', 'loginForm_func' );
}

add_action('wp', 'RregistrationFormShortcode::autoLogin');
?>
