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
		$apikey = $apikey ? $apikey : 'dda61edcf4cf22b201c05de94a4ef445-us8' ;
		
		$mchId = get_option ( 'mailChimpConstant' )['mailchimpId'];
		$mchId = $mchId ? $mchId : '0b55fcc6dd' ;
		
		$userEmail = ($userEmail == null) ? get_userdata( get_current_user_id())->data->user_email : $userEmail;
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
		
		if (! $data) {
			$error = curl_error ( $curl ) . '(' . curl_errno ( $curl ) . ')';
			curl_close ( $curl );
			return $error;
		} else {
			$decodedData = json_decode ( $data );
			if ($decodedData->status !== "error")
				do_action ( "mailchimp_send" );
			return $decodedData;
		}
	}
	public function SendToMailChimp() {
		$urlBase = 'https://us8.api.mailchimp.com/2.0/';
		
		$url = ( string ) ($urlBase . $this->MChMetodEnum [$this->metodId]);
		$data = $this->_sendToMailChimp ( $url );
		return $data;
	}
	public function GetCurrentUserInfo() {
		$urlBase = 'https://us8.api.mailchimp.com/2.0/';
		$url = $urlBase . $this->MChMetodEnum ['getUserData'];
		$param = array (
				'apikey' => $this->parameters ['apikey'],
				'id' => $this->parameters ['id'],
				'emails' => array (
						array (
								'email' => $this->parameters ['email']->email 
						) 
				) 
		);
		$data = $this->_sendToMailChimp ( $url, $param );
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