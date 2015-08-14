<?php
/*
 * Plugin Name: AAAMailChimp Integration(Davgur)
 * Description: Ð”Ð¾Ð±Ð°Ð²Ð»Ñ�ÐµÑ‚ Ñ‡ÐµÐºÐ±Ð¾ÐºÑ� "ÐŸÐ¾Ð´Ð¿Ð¸Ñ�ÐºÐ° Ð½Ð° Ð½Ð¾Ð²Ð¾Ñ�Ñ‚Ð¸" Ð² Ñ„Ð¾Ñ€Ð¼Ñƒ Ñ€ÐµÐ³Ð¸Ñ�Ñ‚Ñ€Ð°Ñ†Ð¸Ð¸
 * Version: 0.2
 * Author: CasePress
 * Author URI: http://casepress.org
 * License: MIT License
 */
define ( 'MAILCHIMPINT_DIR', untrailingslashit ( dirname ( __FILE__ ) ) );
define ( 'MAILCHIMPINT_DIR_URL', untrailingslashit ( plugins_url ( '', __FILE__ ) ) );

require_once MAILCHIMPINT_DIR . '/includes/mailchimp-api.php';
require_once MAILCHIMPINT_DIR . '/includes/section-actions.php';
require_once MAILCHIMPINT_DIR . '/includes/list-actions.php';
require_once MAILCHIMPINT_DIR . '/includes/ajaxRequest.php';
require_once MAILCHIMPINT_DIR . '/form-shortcode/registre-form-init.php';

if (is_admin ()) {
	require_once MAILCHIMPINT_DIR . '/admin/admin.php';
	add_action ( 'admin_menu', 'mailChimpInt_init' );
}

/* Called actions */
add_action ( 'mailchimp_send', 'synchronization_wp_user' );

add_action ( 'profile_update', 'UpdateMailChimpParam' );
add_action ( 'xprofile_updated_profile', 'UpdateMailChimpParam' );

add_action ( 'bp_core_activated_user', 'mailChimpInt_addToMailChimp', 10, 5 );

add_action ( 'delete_user', 'UnsubscribeMailChimp' );

add_action ( 'namaste_enrolled_course', 'UpdateUserOnMailChimp', 99, 3 );
add_action ( 'publish_namaste_course', 'AddCourseToMailChimp', 10, 2);

function mailChimpInt_addToMailChimp($user_id, $user_password, $usermeta) {
	$user = get_user_by ( "id", $user_id );
	UserProfile_SetDefaultFieldes ( $usermeta ["meta"] ["fieldListWP"], $usermeta ["meta"] ["fieldListBP"], $user_id );
	wp_new_user_notification ( $user->id, $user_password );
	UpdateMailChimpParam ( $user_id );
}
function mailChimpInt_init() {
	// $a = __FILE__;
	// create new top-level menu
	// add_options_page ( 'Integration with MailChimp dg', 'Integration with MailChimp', 'administrator', 'plugins.mailchimp-bp-integrator', 'my_plugin_options' );
	
	// create custom plugin settings menu
	register_mysettings ();
	do_action ( 'mailChimpInt_init' );
}

?>