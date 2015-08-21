<?php
function UserProfile_GetDefaultFieldes($user_id = 0) {
	$user_id = $user_id == 0 ? get_current_user_id () : $user_id;
	$fieldList = array (
			/*
			nick_name => array (
					val => '',
					type => 'wp' 
			),*/
			first_name => array (
					val => '',
					type => 'wp' 
			),
			last_name => array (
					val => '',
					type => 'wp' 
			),
			display_name => array (
					val => '',
					type => 'wp' 
			),
			user_email => array (
					val => '',
					type => 'wp' 
			),
			user_pass => array (
					val => '',
					type => 'wp' 
			) 
	);
	
	foreach ( get_option ( 'mailChimpFieldList' ) as $key => $val ) {
		$fieldVal = $user_id == 0 ? "" : xprofile_get_field_data ( $val, $user_id );
		$fieldList [$val] = array (
				val => $fieldVal,
				type => 'bp' 
		);
	}
	
	if ($user_id == 0)
		return $fieldList;
	$currentUser = get_user_by ( "id", $user_id );
	$fieldList ['last_name'] ['val'] = $currentUser->last_name;
	$fieldList ['first_name'] ['val'] = $currentUser->first_name;
	$fieldList ['display_name'] ['val'] = $currentUser->data->display_name;
	$fieldList ['user_email'] ['val'] = $currentUser->data->user_email;
	
	return $fieldList;
}
function UserProfile_SetDefaultFieldes($fieldListWP, $fieldListBP, $user_id = 1) {
	if ($fieldListWP != null) {
		foreach ( $fieldListWP as $key => $val ) {
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
?>