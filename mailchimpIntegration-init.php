<?php
/*
 * Plugin Name: MailChimp Integration
 * Description:
 * License: MIT License
 */
load_plugin_textdomain ( 'cfef', false, dirname ( plugin_basename ( __FILE__ ) ) . '/languages/' );

define ( 'MAILCHIMPINT_DIR', untrailingslashit ( dirname ( __FILE__ ) ) );
define ( 'MAILCHIMPINT_DIR_URL', untrailingslashit ( plugins_url ( '', __FILE__ ) ) );
require_once MAILCHIMPINT_DIR . '/includes/MailchimpIntegrationUtilities.php';

require_once MAILCHIMPINT_DIR . '/includes/MailChimpSendClass.php';
require_once MAILCHIMPINT_DIR . '/includes/MailChimpActions.php';
require_once MAILCHIMPINT_DIR . '/includes/CreateGroupAndForumForCourse.php';
require_once MAILCHIMPINT_DIR . '/includes/UserAuthorizationHandler.php';
require_once MAILCHIMPINT_DIR . '/form-shortcode/registre-form-init.php';

if (is_admin ()) {
	require_once MAILCHIMPINT_DIR . '/admin/admin.php';
	require_once MAILCHIMPINT_DIR . '/admin/MailChimpIntegratorAdmin.php';
	add_action ( 'admin_menu', 'mailChimpInt_init' );
}

// Called actions
add_action ( 'mailchimp_send', 'synchronization_wp_user' );

add_action ( 'profile_update', 'MailChimpActions::updateParams' );
add_action ( 'xprofile_updated_profile', 'MailChimpActions::updateParams' );

add_action ( 'delete_user', 'MailChimpActions::unsubscribe' );
add_action ( 'publish_namaste_course', 'MailChimpActions::addCourse', 10, 2 );
add_action ( 'namaste_earned_points', 'MailChimpActions::updateScores' );

add_action ( 'save_post_namaste_course', 'CreateGroupAndForumForCourse::SavePost', 99, 3 );
add_action ( 'namaste_enrolled_course', function ($studentId, $courseId, $status) {
	MailChimpActions::updateParams ( $studentId );
	CreateGroupAndForumForCourse::EnrolledCourse ( $studentId, $courseId, $status );
}, 10, 3 );

// add_action ( 'updated_namaste_unenroll_meta', 'CreateGroupAndForumForCourse::UnsubscribeCourse');

UserAuthorizationHandler::initActions ();
function mailChimpInt_init() {
	// create custom plugin settings menu
	register_mysettings ();
	do_action ( 'mailChimpInt_init' );
}
?>
