<?php
load_plugin_textdomain( 'cfef', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
define ( 'REGFORM_DIR', plugin_dir_path ( __FILE__ ) ) ;
define ( 'REGFORM_DIR_URL', plugin_dir_url ( __FILE__ ) );


require_once REGFORM_DIR . '/functions.php';
require_once REGFORM_DIR . '/renderHtml.php';
require_once REGFORM_DIR . '/RregistrationFormShortcodeClass.php';

wp_enqueue_script ( 'regFormJs', REGFORM_DIR_URL . '/js/script.js', array( 'jquery' ));
wp_enqueue_style( 'loginAndRegisterForm', REGFORM_DIR_URL . '/style.css');

add_action("init", "regForm_init");


add_action ( 'wp_ajax_nopriv_registerRregistrationFormShortcode', 'RregistrationFormShortcodeClass::register', 30 );
add_action ( 'wp_ajax_nopriv_loginRregistrationFormShortcode', 'RregistrationFormShortcodeClass::login', 30 );
add_action ( 'wp_ajax_loginRregistrationFormShortcode', 'RregistrationFormShortcodeClass::login', 30 );


function regForm_init() {
	add_shortcode('registerForm', 'registerForm_func');	
	add_shortcode('loginForm', 'loginForm_func');	
}

?>