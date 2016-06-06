<?php
class MailChimpSend {
	public $metodId;
	private $MChMetodEnum = array (
			'createSegment' => 'lists/segment-add.json',
			'removeSegment' => 'lists/segment-del.json',
			'getSegments' => 'lists/segments.json',
			'getUserData' => 'lists/member-info.json',
			'setUserData' => 'lists/update-member.json',
			'listSubscribe' => 'lists/subscribe',
			'unsubscribe' => '/lists/unsubscribe' 
	);
	public $parameters = array ();
	function __construct($metodId, $userEmail = NULL) {
		$apikey = get_option ( 'mailChimpConstant' )['mailChimpApiKey'];
		$apikey = $apikey ? $apikey : 'dda61edcf4cf22b201c05de94a4ef445-us8';
		
		$mchId = get_option ( 'mailChimpConstant' )['mailchimpId'];
		$mchId = $mchId ? $mchId : '0b55fcc6dd';
		
		$userEmail = ($userEmail == NULL) ? get_userdata ( get_current_user_id () )->data->user_email : $userEmail;
		$emailObj = new StdClass ();
		$mergeVarsObj = new StdClass ();
		
		$this->metodId = $metodId;
		$emailObj->email = $userEmail;
		
		$this->parameters = array (
				'apikey' => $apikey,
				'id' => $mchId,
				'email' => $emailObj,
				'merge_vars' => $mergeVarsObj 
		);
	}
	/**
	 * Call mailchimp API
	 *
	 * @param unknown $url        	
	 * @param string $param        	
	 * @return string|mixed
	 */
	private function _sendToMailChimp($url, $param = NULL) {
		$curl = curl_init ( $url );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, array (
				'Content-Type: application/json' 
		) );
		$param = $param == NULL ? $this->parameters : $param;
		
		curl_setopt ( $curl, CURLOPT_POST, true );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, json_encode ( $param ) );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$data = curl_exec ( $curl );
		return $data;
	}
	/**
	 * If no mailchimp user with current user_email create him
	 *
	 * @param unknown $method        	
	 * @param string $param        	
	 * @return string|mixed
	 */
	private function _validateMailChimpResponse($method, $param = NULL) {
		$url = 'https://us8.api.mailchimp.com/2.0/' . $method;
		$data = $this->_sendToMailChimp ( $url );
		
		try {
			$msg = "data";
			$msg .= print_r ( $data, true );
			MailchimpIntegrationUtilities::rightToLogFileDavgur ( $msg );
		} catch ( Exception $e ) {
			$msg = print_r ( $e, true );
		}
		
		if (! $data) {
			$error = curl_error ( $curl ) . '(' . curl_errno ( $curl ) . ')';
			curl_close ( $curl );
			return $error;
		} else {
			$decodedData = json_decode ( $data );
			
			// register on "List_NotSubscribed" error
			if ($decodedData->status == "error") {
				switch ($decodedData->code) {
					case "233" :
					case "232" :
					case "215" :
						_validateMailChimpResponse ( $this->MChMetodEnum ["listSubscribe"] );
						break;
				}
				// $addUserRequest = json_decode ( MailChimpActions::updateParams ( $user_id ) );
				/*
				 * if($addUserRequest->status != "error" )
				 * $data = $this->_sendToMailChimp ( $url );
				 */
			}
			if ($decodedData->status == "error") {
				do_action ( "mailchimp_send" );
				return $decodedData;
			} else {
				do_action ( "mailchimp_send" );
				return $decodedData;
			}
		}
	}
	public function SendToMailChimp() {
		$method = $this->MChMetodEnum [$this->metodId];
		$data = $this->_validateMailChimpResponse ( $method );
		return $data;
	}
	public function GetCurrentUserInfo() {
		$method = $this->MChMetodEnum ['getUserData'];
		$param = array (
				'apikey' => $this->parameters ['apikey'],
				'id' => $this->parameters ['id'],
				'emails' => array (
						array (
								'email' => $this->parameters ['email']->email 
						) 
				) 
		);
		$data = $this->_validateMailChimpResponse ( $method, $param );
		return $data;
	}
	public function GetParametrs() {
		return ( array ) $this->parameters;
	}
	public function SetParametrs($paramTemp) {
		$this->parameters = $paramTemp;
	}
}

?>