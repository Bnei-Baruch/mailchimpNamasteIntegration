<?php
function AddCourseToMailChimp( $post_ID, $post) {
	$condition = array (
			'field' => 'COURSES',
			'op' => 'like',
			'value' => ('[' . strval ( $post_ID ) . ']') 
	);
	$segParam = array (
			'match' => 'all',
			'conditions' => array($condition) 
	);
	$optsParam = array (
			'type' => 'saved',
			'name' => strval ( $post_ID ),
			'segment_opts' => $segParam 
	);
	$sendObj = new MailChimpSend ( 'createSegment' );
	$sendObj->parameters['opts'] = $optsParam;
	$sendObj->SendToMailChimp ();
}
function UpdateUserOnMailChimp($studentId, $courseId, $status) {
	$wpCurrentUser = wp_get_current_user ();
	if ($status == 'enrolled') {
		$sendGetUserObj = new MailChimpSend ( 'getUserData', $wpCurrentUser->user_email );
		
		$userMChObjJson = $sendGetUserObj->SendToMailChimp ();
		$userMChObjJson = json_decode ( $userMChObjJson )->data;
		
		$sOldCourses = $userMChObjJson [0]->merges->COURSES;
		$sNewCourse = '[' . $courseId . ']';
		$pos = strripos ( $sOldCourses, $sNewCourse );
		if ($pos === false) {
			// Ð½Ðµ Ð¿Ð¾Ð´Ð¿Ð¸Ñ�Ð°Ð½ -> Ð¿Ð¾Ð´Ð¿Ð¸Ñ�Ñ‹Ð²Ð°ÐµÐ¼
			$sendObj = new MailChimpSend ( 'setUserData' );
			$sendObj->parameters->merge_vars->COURSES = $sOldCourses . $sNewCourse;
			
			$sendObj->SendToMailChimp ();
		}
	}
}

?>