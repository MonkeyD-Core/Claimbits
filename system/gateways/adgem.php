<?php
	define('BASEPATH', true);
	require('../init.php');

	// Hash Key
	$secret = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='adgem_hash'");
	$secret = $secret['config_value'];
	
	// Get postback
	$userId = $db->EscapeString($_REQUEST['user_id']);
	$app_id = $db->EscapeString($_REQUEST['app_id']);
	$reward = $db->EscapeString($_REQUEST['amount']);
	$campaign_id = $db->EscapeString($_REQUEST['campaign_id']);
	$transaction = $db->EscapeString($_REQUEST['transaction']);
	$country = $db->EscapeString($_REQUEST['country']);
	$ip = $db->EscapeString($_REQUEST['ip']);
	$payout = $db->EscapeString($_REQUEST['payout']);

	// validate signature
	if ($_REQUEST['hash'] != $secret){
		echo "ERROR: Signature doesn't match";
		return;
	}
	
	// validate signature
	if(!(is_numeric($app_id) && is_numeric($reward) && is_numeric($campaign_id) && is_numeric($payout)))
	{
		echo "ERROR: Invalid user";
		return;
	}

	if(!empty($userId) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$transaction."' AND `user_id`='".$subid."' LIMIT 1") == 0)
	{
		$user = $db->QueryFetchArray("SELECT `id` FROM `users` WHERE `id`='".$userId."' LIMIT 1");
		
		if(!empty($user['id'])) {
			$tc_points = (0.10*($amount*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$transaction."','".$country."','".ip2long($ip)."','".$payout."','".$reward."','adgem','".time()."')");
		}
		
		echo "OK";
	}
?>