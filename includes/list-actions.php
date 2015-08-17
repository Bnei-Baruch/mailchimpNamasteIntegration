<?php
function UpdateMailChimpScores($userId = NULL, $lastScores = 0) {
	$userId = ($userId == NULL) ? get_current_user_id () : $userId;
	
	$scores = get_user_meta($userId, 'namaste_points', true);
	
	$sendObj = new MailChimpSend ( 'setUserData');
	$sendObj->parameters ['merge_vars'] = array ( 'SCORES' => $scores );
	$request = $sendObj->SendToMailChimp ();
}

function UpdateMailChimpParam($userId = NULL) {
	$userId = ($userId == NULL) ? get_current_user_id () : $userId;
	$fieldList = UserProfile_GetDefaultFieldes ( $userId );
	
	$mergeVars = array (
			'FNAME' => $fieldList ['first_name'] ["val"],
			'LNAME' => $fieldList ['last_name'] ["val"],
			'CITY' => $fieldList ['city'] ["val"],
			'COUNTRY' => $fieldList ['country'] ["val"]
	);
	$defParam = array (
			'merge_vars' => $mergeVars,
			'double_optin' => false,
			'update_existing' => false,
			'send_welcome' => false 
	);
	
	$mailChimpid = get_option ( 'mailChimpConstant' )['mailchimpId'];
	$userEmail = $fieldList ["user_email"] ["val"];
	$sendObj = new MailChimpSend ( 'listSubscribe', $userEmail, $mailChimpid );
	$userInfo = $sendObj->GetCurrentUserInfo ();
	
	$paramTemp = $sendObj->GetParametrs ();
	if (count ( $userInfo->data ) > 0) {
		$sendObj->metodId = "setUserData";
		$paramTemp ['replace_interests'] = true;
		$paramTemp ['email'] = $userInfo->data [0];
	}
	$newParamTemp = array_merge ( ( array ) $paramTemp, $defParam );
	$sendObj->SetParametrs ( $newParamTemp );
	$request = $sendObj->SendToMailChimp ();
	return $request;
}
function UnsubscribeMailChimp($user_id) {
	$user_obj = get_userdata ( $user_id );
	
	$sendObj = new MailChimpSend ( 'unsubscribe', $user_obj->user_email );
	$sendObj->parameters ["delete_member"] = true;
	$request = $sendObj->SendToMailChimp ();
	return $request;
}
function synchronization_wp_user() {
	/*
	 * if(!is_user_logged_in() || get_user_meta(get_current_user_id(), 'updated_from_bp', true) == '1')
	 * return;
	 *
	 * $last_name = bp_get_profile_field_data( array( 'field' => 'Ð¤Ð°Ð¼Ð¸Ð»Ð¸Ñ�', 'user_id' => get_current_user_id()));
	 * $first_name = bp_get_profile_field_data( array( 'field' => 'Ð˜Ð¼Ñ�', 'user_id' => get_current_user_id()));
	 * $display_name = (isset($last_name) ? $last_name.' '.$first_name : $first_name);
	 *
	 * wp_update_user(array(
	 * 'ID' => get_current_user_id(),
	 * 'display_name' => $display_name,
	 * 'first_name' => $first_name,
	 * 'last_name' => $last_name
	 * ));
	 * update_user_meta(get_current_user_id(), 'updated_from_bp', '1');
	 */
}

?>