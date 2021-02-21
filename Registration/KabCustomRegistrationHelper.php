<?php
class KabCustomRegistrationHelper {
	public static function getUpdateProfileJSON() {
		wp_die ( json_encode ( self::getUpdateProfile () ) );
	}
	public static function setUpdateProfileJSON() {
		wp_die ( json_encode ( self::setUpdateProfile () ) );
	}
	public static function getUpdateProfile() {
		$isShowDialog = false;
		$data = self::getUserFieldList ();
		$userData = get_user_by_email ( $data ['user_email'] ['val'] )->data;
		foreach ( $data as $key => $val ) {
			if ($key == 'user_email')
				continue;
				// special functional for gender (becouse it's select and not text box)
			if ($key == 'gender') {
				$translate = array (
						'gender' => $val ['translate'],
						'male' => __ ( 'male', 'cfef' ),
						'female' => __ ( 'female', 'cfef' ) 
				);
				if (! empty ( $val ['val'] )) {
					$dataGender = array (
							"male" => '',
							"female" => '',
							'translate' => $translate 
					);
					$dataGender [$val ['val']] = 'selected';
					$data ["gender"] = $dataGender;
					continue;
				}
				
				$data ["gender"] ['translate'] = $translate;
				$isShowDialog = array (
						'val' => $key,
						'val1' => $val ['val'],
						'val2' => $translate 
				);
				continue;
			}
			// check is fields are empty or has default value
			if (empty ( $val ['val'] ) || strpos ( $val ['val'], $val ['translate'] ) !== false || ($key == 'display_name' && $val ['val'] == $userData->user_nicename) || ($key == 'display_name' && $val ['val'] == $userData->user_email) || ($key == 'display_name' && strpos ( $val ['val'], '-' ))) {
				$isShowDialog = array (
						'val' => $key,
						'val1' => $val ['val'],
						'val2' => $val ['translate'],
						'strpos' => strpos ( $val ['val'], $val ['translate'] ) 
				);
				continue;
			}
		}
		
		$data ['result'] = $isShowDialog;
		$data ['translate'] = array (
				'title' => __ ( 'Update profile title', 'cfef' ),
				'save' => __ ( 'Save' ),
				'cancel' => __ ( 'Cancel' ) 
		);
		return $data;
	}
	public static function setUpdateProfile() {
		$fieldList = self::getUserFieldList ();
		$fieldListWP = array ();
		$fieldListBP = array ();
		$return = array ();
		
		$user_id = get_current_user_id ();
		$userData = parse_str ( $_POST ['userData'], $fieldsData );
		foreach ( $fieldList as $key => $val ) {
			if ($fieldList [$key] ['type'] == "wp")
				$fieldListWP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
			else
				$fieldListBP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
		}
		
		self::setUserFieldList ( $fieldListWP, $fieldListBP, $user_id );
		$args = array (
				'ID' => $user_id,
				'display_name' => $fieldListWP ['display_name'] 
		);
		wp_update_user ( $args );
		
		$data ['result'] = true;
		
		return $data;
	}
	public static function getUserFieldList($user_id = 0) {
		$user_id = $user_id == 0 ? get_current_user_id () : $user_id;
		$fieldList = array (
				'first_name' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Your First Name', 'cfef' ) 
				),
				'last_name' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Last Name', 'cfef' ) 
				),
				'display_name' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Display Name', 'cfef' ) 
				),
				'user_email' => array (
						'val' => '',
						'type' => 'wp',
						'translate' => __ ( 'Email', 'cfef' ) 
				) 
		);
		
		foreach ( get_option ( 'mailChimpFieldList' ) as $key => $val ) {
			$fieldVal = $user_id == - 1 ? "" : xprofile_get_field_data ( $val, $user_id );
			$fieldList [$val] = array (
					'val' => $fieldVal,
					'type' => 'bp',
					'translate' => __ ( $val, 'cfef' ) 
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
	public static function setUserFieldList($fieldListWP, $fieldListBP, $user_id = 1) {
		if ($fieldListWP != null) {
			// If need more complexy display_name can use - bp_core_get_user_displayname
			if (empty ( $fieldListWP ['display_name'] ))
				$fieldListWP ['display_name'] = $fieldListWP ['last_name'] . ' ' . $fieldListWP ['first_name'];
			
			$user = get_user_by ( "id", $user_id );
			// don't update password and email
			unset ( $fieldListWP ["user_pass"] );
			unset ( $fieldListWP ["user_email"] );
			$fieldListWP ["ID"] = $user_id;
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
}

?>