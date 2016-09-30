<?php
add_action ( 'wp_ajax_getRregistrationFormFields', 'MailChimpIntegratorAdmin::load' );
add_action ( 'wp_ajax_deleteRregistrationFormFields', 'MailChimpIntegratorAdmin::delete' );
add_action ( 'wp_ajax_submitRregistrationFormBuilde', 'MailChimpIntegratorAdmin::update' );


class MailChimpIntegratorAdmin {
	public static function delete() {
		self::canAjax ();
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
	public static function update() {
		self::canAjax ();
		global $wpdb;
		if (! $_POST ['submitFields'])
			return;
		$dataConst = array ();
		$dataFields = array ();
		
		parse_str ( $_POST ['submitFields'], $fieldsList );
		foreach ( $fieldsList as $key => $val ) {
			if ($key == 'mailChimpApiKey' || $key == 'mailchimpId') {
				$dataConst [$key] = $val;
				continue;
			}
			$dataFields [$key] = $val;
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
	public static function load() {
		self::canAjax ();
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
	private static function canAjax() {
		if (! $_POST ['fieldName'] || $_POST ['fieldName'] == "") {
			$request = array (
					'data' => null,
					'message' => "No fields selected",
					'hasError' => 1 
			);
			return json_encode ( $request );
		}
	}
}

?>