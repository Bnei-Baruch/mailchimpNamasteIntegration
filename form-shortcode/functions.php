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
		error_log(  'Davgur'.print_r($val, true ) );
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

		$user = get_user_by("id", $user_id);
		//don't update password and email
		unset($fieldListWP["user_pass"]);
		unset($fieldListWP["user_email"]);
		$fieldListWP["ID"] = $user_id;
		wp_update_user ( $fieldListWP );		
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

?>