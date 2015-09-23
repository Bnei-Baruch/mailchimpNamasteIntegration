<?php
/*
 * Plugin Name: AAAMailChimp Integration(Davgur)
 * Description:
 * License: MIT License
 */

load_plugin_textdomain( 'cfef', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

define ( 'MAILCHIMPINT_DIR', untrailingslashit ( dirname ( __FILE__ ) ) );
define ( 'MAILCHIMPINT_DIR_URL', untrailingslashit ( plugins_url ( '', __FILE__ ) ) );
require_once MAILCHIMPINT_DIR . '/includes/mailchimp-api.php';
require_once MAILCHIMPINT_DIR . '/includes/section-actions.php';
require_once MAILCHIMPINT_DIR . '/includes/list-actions.php';
require_once MAILCHIMPINT_DIR . '/includes/ajaxRequest.php';
require_once MAILCHIMPINT_DIR . '/includes/CreateGroupAndForumForCourse.php';
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

add_action ( 'publish_namaste_course', 'AddCourseToMailChimp', 10, 2 );

add_action ( 'namaste_earned_points', 'UpdateMailChimpScores');

add_action ( 'save_post_namaste_course', 'CreateGroupAndForumForCourse::SavePost', 99, 3 );

add_action ( 'namaste_enrolled_course', function ($a, $b, $c) {
	UpdateUserOnMailChimp ( $a, $b, $c );
	CreateGroupAndForumForCourse::EnrolledCourse ( $a, $b, $c );
}, 10, 3 );

// add_action ( 'updated_namaste_unenroll_meta', 'CreateGroupAndForumForCourse::UnsubscribeCourse');
function mailChimpInt_addToMailChimp($user_id, $key, $user) {
	if($user ['meta']['registerFromExel']){
		register_users_from_exel($user);
	} else {
		wp_new_user_notification ( $user_id, __ ( 'Your password', 'cfef' ) );		
	} 
	
	UserProfile_SetDefaultFieldes ( $user ['meta'] ['fieldListWP'], $user ['meta'] ['fieldListBP'], $user_id );
	UpdateMailChimpParam ( $user_id );
}

function register_users_from_exel($user){
	
	
	$subject = 'The subject.';
	$msg = 'Content of mail';
	$headers = array();
	$headers[] = 'Content-type: text/html';
	wp_mail( $user->user_email, stripslashes( $subject ), $msg, $headers );
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
