<?php
define('BASEPATH', true);
require('../init.php');

if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {	// Get Real IP				
	$IP = $_SERVER['HTTP_X_FORWARDED_FOR']; 
} else { 
	$IP = $_SERVER['REMOTE_ADDR'];
}	

if ($IP=="67.227.230.75" || $IP=="67.227.230.76" || $IP=="2607:fad0:3704:2::" || $IP=="2607:fad0:3704:2::1" || $IP=="2607:fad0:3704:2::2" || $IP=="2607:fad0:3704:2::3") {
	$campaign_id = $db->EscapeString($_GET['campaign_id']);	// Campaign ID
	$transaction = $db->EscapeString($_GET['leadID']);				// Lead ID
	$country = $db->EscapeString($_GET['country']);					// Country
	$subid = $db->EscapeString($_GET['sid']);	 							// Primary SubID
	$subid2 = $db->EscapeString($_GET['sid2']);  				   		// Secondary SubID2
	$subid3 = $db->EscapeString($_GET['sid3']);  						// Secondary SubID3
	$amount = $db->EscapeString($_GET['commission']);			// Commission		
	$status = $db->EscapeString($_GET['status']);  					// Status: 1 (Credited) or 2 (Reversed)
	$userIP = ip2long($_GET['ip']);  												// User IP Address
	$reward = $db->EscapeString($_GET['vc_value']);  				// Virtual Currency Only - VC Ratio * Commission

	if ($status==1) {	
		$user = $db->QueryFetchArray("SELECT * FROM `users` WHERE `id`='".$subid."'");
	
		if(!empty($user['id'])) {
			$tc_points = (0.10*($amount*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`campaign_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$transaction."','".$campaign_id."','".$country."','".$userIP."','".$amount."','".$reward."','adworkmedia','".time()."')");
		}
	} elseif($status==2) {
		$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`-'".$reward."' WHERE `id`='".$user['id']."'");	
	}
}
?>