<?php
function UserProfile_GetDefaultFieldes($user_id = 0) {
	$user_id = $user_id == 0 ? get_current_user_id () : $user_id;
	$fieldList = array (
			'first_name' => array (
					'val' => '',
					'type' => 'wp',
					'translate' => __('Your First Name',  'cfef')					
			),
			'last_name' => array (
					'val' => '',
					'type' => 'wp',
					'translate' => __('Last Name',  'cfef') 
			),
			'display_name' => array (
					'val' => '',
					'type' => 'wp' ,
					'translate' => __('Display Name',  'cfef')
			),
			'user_email' => array (
					'val' => '',
					'type' => 'wp' ,
					'translate' => __('Email',  'cfef')
			)
	);
	
	foreach ( get_option ( 'mailChimpFieldList' ) as $key => $val ) {
		$fieldVal = $user_id == - 1 ? "" : xprofile_get_field_data ( $val, $user_id );
		$fieldList [$val] = array (
				'val' => $fieldVal,
				'type' => 'bp',
				'translate' => __($val,  'cfef')
		);
	}
	
	if ($user_id == - 1)
		return $fieldList;
	$currentUser = get_user_by ( "id", $user_id );
	$fieldList ['last_name'] ['val'] = get_userdata ( $user_id )->last_name;
	$fieldList ['first_name'] ['val'] = get_userdata ( $user_id )->first_name;
	$fieldList ['display_name'] ['val'] = $currentUser->data->display_name;
	$fieldList ['user_email'] ['val'] = $currentUser->data->user_email;
	
	return $fieldList;
}
function UserProfile_SetDefaultFieldes($fieldListWP, $fieldListBP, $user_id = 1) {
	if ($fieldListWP != null) {
		// If need more complexy display_name can use - bp_core_get_user_displayname
		if(empty($fieldListWP ['display_name'])) 
				$fieldListWP ['display_name'] = $fieldListWP ['last_name'] . ' ' . $fieldListWP ['first_name'];
		
		foreach ( $fieldListWP as $key => $val ) {
			if ($key == "user_pass")
				continue;
			
			update_user_meta ( $user_id, $key, $val );
		}
	}
	
	$groupParam = array (
			'user_id' => $user_id,
			'fetch_fields' => true,
			'fetch_field_data' => true 
	);
	$group = bp_xprofile_get_groups ( $groupParam )[0];
	$newFieldOpt = array (
			'field_group_id' => $group->id,
			'name' => '',
			'type' => 'textbox',
			'is_required' => true,
			'can_delete' => false 
	);
	
	foreach ( $fieldListBP as $key => $val ) {
		$hasCurrentField = true;
		foreach ( $group->fields as $field ) {
			// delete name field
			if ($field->name == 'name')
				xprofile_delete_field ( $field->id );
			if ($field->name == $key) {
				$hasCurrentField = false;
				break;
			}
		}
		if ($hasCurrentField) {
			$newFieldOpt ['name'] = $key;
			$fieldId = xprofile_insert_field ( $newFieldOpt );
		}
		$fieldSetId = xprofile_set_field_data ( $key, $user_id, $val );
	}
}
function mailchimpBpIntagration_activation_subject($subject, $user, $user_email, $key, $meta) {
	return __ ( 'Custom activation subject', 'cfef' );
}
function mailchimpBpIntagration_activation_message($message, $user_id, $activate_url) {
	add_filter ( 'wp_mail_content_type', 'set_bp_message_content_type' );
	return sprintf ( __ ( 'Custom activation body', 'cfef' ), $activate_url );
}
function mailchimpBpIntagration_retrieve_title($message) {
	return __ ( 'Custom retrieve title', 'cfef' );
}
function mailchimpBpIntagration_retrieve_message($message, $key) {
	add_filter ( 'wp_mail_content_type', 'set_bp_message_content_type' );
	
	$email = filter_input ( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );
	$user = get_user_by_email ( $email );
	$login_url = wp_login_url ( home_url () );
	$login_url .= '&action=rp&key=' . $key . '&login=' . $user->data->user_email;
	$massage = sprintf ( __ ( 'Custom retrieve body', 'cfef' ), 'kabacademy.com', $user->data->user_login, $login_url );
	include ("email_footer.php");
	return $massage;
}
?>