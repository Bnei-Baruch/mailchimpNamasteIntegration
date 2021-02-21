<?php
/**
 * Add some functonal after authorization
 */
class UserAuthorizationHandler {
	public static function initActions() {
		add_action ( 'bp_core_activated_user', 'UserAuthorizationHandler::addToMailChimp', 100, 3 );		
		add_action ( 'wsl_process_login_create_wp_user_start', 'UserAuthorizationHandler::sendEmailFromHybrid', 10, 4 );
		
		// actions for change letters
		// add_filter ( 'bp_core_signup_send_validation_email_message', 'UserAuthorizationHandler::activationMessage', 10, 3 );
		add_filter ( 'bp_core_signup_send_validation_email_subject', 'UserAuthorizationHandler::activationSubject', 10, 5 );
		add_filter ( 'retrieve_password_message', 'UserAuthorizationHandler::retrieveMessage', 10, 2 );
		add_filter ( 'retrieve_password_title', 'UserAuthorizationHandler::retrieveTitle', 10, 1 );
	}
	public static function sendEmailFromHybrid($provider, $hybridauth_user_profile, $request_user_login, $request_user_email) {
		if ($request_user_login != null) {
			$hybridauth_user_profile->displayName =  $request_user_login;
		}
		self::sendEmail ( $request_user_email, $request_user_login, null, null );
	}
	public static function addToMailChimp($user_id, $key, $user) {
		$user_pass = $user ['meta'] ['fieldListWP'] ['user_pass'];
		if (is_numeric ( $user ['meta'] ['enrollToCourse'] )) {
			self::enroll ( $user_id, $user ['meta'] ['enrollToCourse'] );
		}
		KabCustomRegistrationHelper::setUserFieldList ( $user ['meta'] ['fieldListWP'], $user ['meta'] ['fieldListBP'], $user_id );
		
		$user = get_user_by ( 'id', $user_id );
		self::sendEmail ( $user->user_email, $user->display_name, $user_pass, $user->user_login );
	}
	private static function sendEmail($user_email, $display_name, $user_pass, $user_login) {
		if (! is_null ( $user_pass )) {
			$subject = 'Логин и пароль для сайта kabacademy.com.';
		} else {
			$subject = 'Регистрация на сайте kabacademy.com.';
		}
		include_once 'userAutorisationEmail.php';
		self::send ( $message, $subject, $user_email );
	}
	private static function enroll($userId, $courseId) {
		$_course = new NamasteLMSCourseModel ();
		// enroll in course
		$course = $_course->select ( $courseId );
		$enroll_mode = get_post_meta ( $course->ID, 'namaste_enroll_mode', true );
		
		// if already enrolled, just skip this altogether
		$_course->enroll ( $userId, $course->ID, 'enrolled' );
	}
	private static function send($msg, $subject, $email) {
		$headers = array (
				'Content-type: text/html' 
		);
		wp_mail ( $email, stripslashes ( $subject ), $msg, $headers );
	}
	
	/**
	 * Rewrite activation letter subject
	 *
	 * @param unknown $subject        	
	 * @param unknown $user        	
	 * @param unknown $user_email        	
	 * @param unknown $key        	
	 * @param unknown $meta        	
	 * @return Ambigous <string, mixed>
	 */
	public static function activationSubject($subject, $user, $user_email, $key, $meta) {
		return __ ( 'Custom activation subject', 'cfef' );
	}
	public static function activationMessage($message, $user_id, $activate_url) {
		add_filter ( 'wp_mail_content_type', 'set_bp_message_content_type' );
		return sprintf ( __ ( 'Custom activation body', 'cfef' ), $activate_url );
	}
	public static function retrieveTitle($message) {
		return __ ( 'Custom retrieve title', 'cfef' );
	}
	public static function retrieveMessage($message, $key) {
		add_filter ( 'wp_mail_content_type', 'set_bp_message_content_type' );
		
		$email = filter_input ( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );
		$user = get_user_by_email ( $email );
		$login_url = wp_login_url ( home_url () );
		$login_url .= '&action=rp&key=' . $key . '&login=' . $user->data->user_email;
		$massage = sprintf ( __ ( 'Custom retrieve body', 'cfef' ), 'kabacademy.com', $user->data->user_login, $login_url );
		include ("email_footer.php");
		return $massage;
	}
}
