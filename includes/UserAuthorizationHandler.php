<?php
/**
 * Add some functonal after authorization
 */
class UserAuthorizationHandler {
	public static function initActions() {
		add_action ( 'bp_core_activated_user', 'UserAuthorizationHandler::addToMailChimp', 100, 3 );
		
		// actions for change letters
		
		// add_filter ( 'bp_core_signup_send_validation_email_message', 'UserAuthorizationHandler::activationMessage', 10, 3 );
		add_filter ( 'bp_core_signup_send_validation_email_subject', 'UserAuthorizationHandler::activationSubject', 10, 5 );
		add_filter ( 'retrieve_password_message', 'UserAuthorizationHandler::retrieveMessage', 10, 2 );
		add_filter ( 'retrieve_password_title', 'UserAuthorizationHandler::retrieveTitle', 10, 1 );
	}
	public static function addToMailChimp($user_id, $key, $user) {
		if (is_numeric ( $user ['meta'] ['enrollToCourse'] )) {
			$user_pass = $user ['meta'] ['fieldListWP'] ['user_pass'];
			$courseId = $user ['meta'] ['enrollToCourse'];
			self::sendEmailWithEnroll ( $user_id, $user_pass, $courseId );
		} else {
			self::sendEmail ( $user_id, $user ['meta'] ['fieldListWP'] ['user_pass'] );
		}
		RregistrationFormShortcode::setUserFieldList ( $user ['meta'] ['fieldListWP'], $user ['meta'] ['fieldListBP'], $user_id );
		MailChimpActions::updateParams ( $user_id );
	}
	private function sendEmail($user_id, $user_pass) {
		$user = get_user_by ( 'id', $user_id );
		
		$subject = 'Логин и пароль для сайта kabacademy.com.';
		$message = "Вы успешно зарегистрированы на сайте Международной академии каббалы.<br /><br />";
		$message .= sprintf ( __ ( 'Username: %s' ), $user->user_login ) . "<br /><br />";
		$message .= __ ( 'Password: ' ) . $user_pass . '<br /><br />';
		$message .= 'Чтобы установить новый пароль, перейдите по ссылке: ' . wp_login_url ( home_url () . '/login/' ) . '&action=lostpassword';
		$message .= '<br /><br />';
		
		self::send ( $msg, $subject, $user->user_email );
	}
	private function sendEmailWithEnroll($user_id, $user_pass, $courseId) {
		$user = get_user_by ( 'id', $user_id );
		
		self::enroll ( $user_id, $courseId );
		
		$subject = 'Логин и пароль для сайта kabacademy.com';
		
		$msg = 'Ваши данные для входа на сайте kabacademy.com:<br /><br />';
		$msg .= 'Имя пользователя: ' . $user->user_email . '<br />';
		$msg .= 'Новый пароль: ' . $user_pass . '<br /><br />';
		$msg .= '<a href="' . get_site_url () . '/login">Авторизоваться на сайте >></a><br /><br />';
		$msg .= 'Чтобы установить новый пароль, перейдите по ссылке: ' . wp_login_url ( home_url () . '/login/' ) . '&action=lostpassword';
		$msg .= '<br /><br />';
		
		self::send ( $msg, $subject, $user->user_email );
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







