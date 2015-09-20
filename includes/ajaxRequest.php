<?php
$request = array (
		'data' => null,
		'message' => "It's some error",
		'hasError' => 1 
);
add_action ( 'wp_ajax_getRregistrationFormFields', 'get_RregistrationFormFields' );
add_action ( 'wp_ajax_deleteRregistrationFormFields', 'delete_RregistrationFormFields' );
add_action ( 'wp_ajax_submitRregistrationFormBuilde', 'submit_RregistrationFormBuilde' );
function ifCanAjax_RregistrationFormFields() {
	if (! $_POST ['fieldName'] || $_POST ['fieldName'] == "") {
		$request->message = "No fields selected";
		return json_encode ( $request );
	}
}
function get_RregistrationFormFields() {
	ifCanAjax_RregistrationFormFields ();
	global $wpdb;
	$dataFields = get_option ( 'mailChimpFieldList' );
	$dataConst = get_option ( 'mailChimpConstant' );
	$data = array_merge ( $dataConst, $dataFields );
	$request = array (
			'data' => $data,
			'hasError' => 0,
			'message' => 'ok' 
	);
	echo json_encode ( $request );
	wp_die ();
}
function delete_RregistrationFormFields() {
	ifCanAjax_RregistrationFormFields ();
	global $wpdb;
	
	$tempOptions = get_option ( 'mailChimpFieldList' );
	$tempOptions [$_POST ['fieldName']] = $_POST ['newValue'];
	update_option ( 'mailChimpFieldList', $tempOptions );
	
	$data = get_option ( 'mailChimpFieldList' );
	
	$request = array (
			'data' => $data,
			'hasError' => 0,
			'message' => 'ok'
	);
	echo json_encode ( $request );
	wp_die ();
}
function submit_RregistrationFormBuilde() {
	ifCanAjax_RregistrationFormFields ();
	global $wpdb;	
	if (! $_POST ['submitFields'])
		return;
	$dataConst = array();
	$dataFields = array();
	
	parse_str ( $_POST ['submitFields'], $fieldsList );
	foreach ( $fieldsList as $key => $val ) {
		if ($key == 'mailChimpApiKey' || $key == 'mailchimpId') {
			$dataConst[$key] = $val;
			continue;
		}
		$dataFields[$key] = $val;
	}
	update_option ( 'mailChimpFieldList', $dataFields );
	update_option ( 'mailChimpConstant', $dataConst );
	
	$data = array_merge ( $dataConst, $dataFields );
	$request = array (
			'data' => $data,
			'hasError' => 0,
			'message' => 'ok'
	);
	echo json_encode ( $request );
	wp_die ();
}

?>