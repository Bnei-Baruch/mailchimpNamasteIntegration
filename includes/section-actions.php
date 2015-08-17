<?php
function AddCourseToMailChimp($post_ID, $post) {
	$condition = array (
			'field' => 'COURSES',
			'op' => 'like',
			'value' => ('[' . strval ( $post_ID ) . ']') 
	);
	$segParam = array (
			'match' => 'all',
			'conditions' => array (
					$condition 
			) 
	);
	$optsParam = array (
			'type' => 'saved',
			'name' => strval ( $post_ID ),
			'segment_opts' => $segParam 
	);
	$sendObj = new MailChimpSend ( 'createSegment' );
	$sendObj->parameters ['opts'] = $optsParam;
	$sendObj->SendToMailChimp ();
}
function UpdateUserOnMailChimp($studentId, $courseId, $status) {
	global $wpdb;
	$wpCurrentUser = wp_get_current_user ();
	if ($status == 'enrolled') {	
		$aOldCourses = $wpdb->get_col ( $wpdb->prepare ( "SELECT course_id FROM " . NAMASTE_STUDENT_COURSES . "
						 	WHERE user_id = %d AND status = %d", $studentId, 'enrolled' ) );
		$sCourses = '[' . implode ( "],[", $aOldCourses ) . ']';
		$sendObj = new MailChimpSend ( 'setUserData' );
		$sendObj->parameters ["merge_vars"]->COURSES = $sCourses;
		$sendObj->SendToMailChimp ();
	}
}

?>