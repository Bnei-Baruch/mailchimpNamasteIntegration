<?php
define ( 'REGFORM_DIR', untrailingslashit ( dirname ( __FILE__ ) ) );
define ( 'REGFORM_DIR_URL', untrailingslashit ( plugins_url ( '', __FILE__ ) ) );

require_once REGFORM_DIR . '/functions.php';
require_once REGFORM_DIR . '/renderHtml.php';
require_once REGFORM_DIR . '/ajaxRequest.php';
require_once REGFORM_DIR . '/RregistrationFormShortcodeClass.php';

wp_enqueue_script ( 'regFormJs', REGFORM_DIR_URL . '/js/script.js', array( 'jquery' ));
wp_enqueue_style( 'loginAndRegisterForm', REGFORM_DIR_URL . '/style.css');

add_action("init", "regForm_init");

function regForm_init() {
	add_shortcode('registerForm', 'registerForm_func');	
	add_shortcode('loginForm', 'loginForm_func');	
}

?>