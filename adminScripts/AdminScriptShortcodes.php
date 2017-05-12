<?php
class MailchimpIntegrationAdminScripts {	
	function __construct() {
		$this->initShortcodes();
	}

	public function reloadUsers($attr) {
		if (!isset($attr['courseid'])) {
			return $attr['courseid'];
		}

		if($_POST['isUpdateMailchimp'] != "" && is_numeric  ($_POST['isUpdateMailchimp']) && $_POST['passwordForupdateMailchimp'] == 'BTvE2"BCJC9/aP@'){
			$this->runUserUpdate($_POST['isUpdateMailchimp']);
		}

		return 'Course id = '.$attr['courseid'].'</br>'.
		'<form action="" method="post">'.
		   '<input type="hidden" name="isUpdateMailchimp" value="'.$attr['courseid'].'" >'.
		   '<input type="password" name="passwordForupdateMailchimp" value="" >'.
			'<input type="submit" value="update" />'.
		'</form>';
	}


	private function runUserUpdate($courseid)
	{
		$userIds = MailchimpIntegrationUtilities::usersIdsByCourseId($courseid);
		foreach ($userIds as $key => $user) {
			/*if($key <  270 || $key > 300){
				continue;
			}*/
			$result = MailChimpActions::updateScores($user->user_id);

			MailchimpIntegrationUtilities::rightToLogFileDavgur('|| key = '.$key.' user_id = '.$user->user_id);
			if ($result->status =="error") {
				 MailchimpIntegrationUtilities::rightToLogFileDavgur('error ='. $result->error);
			}
			//var_dump(MailChimpActions::updateScores($user->user_id));			
		}

	}
	/*private function updateUsersWithIntervals (startIndex)
	{
		$userIds = MailchimpIntegrationUtilities::usersIdsByCourseId($courseid);
		foreach ($userIds as $key => $user) {
			echo $key.'---';
			//echo ('User id = '.$user->user_id.' || ');
			//var_dump(MailChimpActions::updateParams($user->user_id));
			//var_dump(MailChimpActions::updateScores($user->user_id));
		}
		var_dump(count($userIds));
	}*/

	private function initShortcodes()
	{		
		add_shortcode ( 'adminReloadMailchimpData', array( $this, reloadUsers) );
	}
}

?>