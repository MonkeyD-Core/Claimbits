<?php
	define('BASEPATH', true);
	require('../init.php');

	// Security Check
	$secret = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='wannads_secret'");
	$secret = $secret['config_value'];

	// Get postback
	$userId = isset($_GET['subId']) ? $db->EscapeString($_GET['subId']) : null;
	$survey = isset($_GET['transId']) ? $db->EscapeString($_GET['transId']) : null;
	$reward = isset($_GET['reward']) ? $db->EscapeString($_GET['reward']) : null;
    $payout = isset($_GET['payout']) ? $db->EscapeString($_GET['payout']) : null;
	$signature = isset($_GET['signature']) ? $db->EscapeString($_GET['signature']) : null;
	$action = isset($_GET['status']) ? $db->EscapeString($_GET['status']) : null;
	$userIP = isset($_GET['userIp']) ? $db->EscapeString($_GET['userIp']) : '0.0.0.0';
	$country = isset($_GET['country']) ? $db->EscapeString($_GET['country']) : null;

	// validate signature
	if (md5($userId.$survey.$reward.$secret) != $signature){
		echo "ERROR: Signature doesn't match";
		return;
	}

	if(!empty($userId) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$survey."' LIMIT 1") == 0)
	{
		$user = $db->QueryFetchArray("SELECT `id` FROM `users` WHERE `id`='".$userId."'");
		
		if(!empty($user['id'])) {
			$tc_points = (0.10*($payout*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$country."','".ip2long($userIP)."','".$payout."','".$reward."','wannads','".time()."')");
		}
		
		echo "OK";
	}
?>