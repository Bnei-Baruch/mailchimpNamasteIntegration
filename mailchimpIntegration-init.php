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

add_action ( 'bp_core_activated_user', 'mailChimpInt_addToMailChimp', 100, 3 );

add_action ( 'delete_user', 'UnsubscribeMailChimp' );

add_action ( 'namaste_enrolled_course', 'UpdateUserOnMailChimp', 10, 3 );
add_action ( 'publish_namaste_course', 'AddCourseToMailChimp', 10, 2 );

add_action ( 'namaste_earned_points', 'UpdateMailChimpScores', 10, 2 );

function mailChimpInt_addToMailChimp($user_id, $key, $user) {
	wp_new_user_notification ( $user_id, __ ( 'Your password' ) );
	UserProfile_SetDefaultFieldes ( $user['meta']['fieldListWP'], $user['meta']['fieldListBP'], $user_id );
	UpdateMailChimpParam ( $user_id );
}
function rightToLogFileDavgur_PL($logText) {
	$msg = $logText;
	$path = MAILCHIMPINT_DIR . '/DavgurLog.txt';
	$f = fopen ( $path, "a+" );
	fwrite ( $f, $msg );
	fclose ( $f );
}
function mailChimpInt_init() {
	
	/*
	 * $group = bp_xprofile_get_groups ( $groupParam )[0];
	 * BP_XProfile_Field::delete_for_group($group->id);
	 */
	// $a = __FILE__;
	// create new top-level menu
	// add_options_page ( 'Integration with MailChimp dg', 'Integration with MailChimp', 'administrator', 'plugins.mailchimp-bp-integrator', 'my_plugin_options' );
	
	// create custom plugin settings menu
	register_mysettings ();
	do_action ( 'mailChimpInt_init' );
}
?>
