<?php
add_action ( 'wp_ajax_nopriv_registerRregistrationFormShortcode', 'RregistrationFormShortcode_register', 30 );
add_action ( 'wp_ajax_nopriv_loginRregistrationFormShortcode', 'RregistrationFormShortcode_login', 30 );
add_action ( 'wp_ajax_loginRregistrationFormShortcode', 'RregistrationFormShortcode_login', 30 );
function RregistrationFormShortcode_register() {
	global $wpdb;
	$fieldList = UserProfile_GetDefaultFieldes ();
	$fieldListWP = array ();
	$fieldListBP = array ();
	parse_str ( $_POST ['userData'], $fieldsData );
	foreach ( $fieldList as $key => $val ) {
		if ($fieldList [$key] ['type'] == "wp")
			$fieldListWP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
		else
			$fieldListBP [$key] = $fieldsData [$key] ? $fieldsData [$key] : $fieldList [$key] ['val'];
	}
	
	$fieldListWP ['user_login'] = $fieldListWP ['user_email'];
	$fieldListWP ['nick_name'] = empty ( $fieldListWP ['first_name'] ) ? $fieldListWP ['user_login'] : $fieldListWP ['first_name'];
	
	$return = RregistrationFormShortcodeClass::register ( $fieldListWP , $fieldListBP, $fieldsData ['password_confirmation'] );
	echo json_encode ( $return );
	wp_die ();
}
function RregistrationFormShortcode_login() {
	global $wpdb;
	parse_str ( $_POST ['userData'], $userData );
	$return = RregistrationFormShortcodeClass::login ( $userData ['user_login'], $userData ['user_pass']);
	//UserProfile_SetDefaultFieldes ( $fieldListWP, $fieldListBP, $return ["userId"] );
	echo json_encode ( $return );
	wp_die ();
}
?>
