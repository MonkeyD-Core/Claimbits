<?php
	define('BASEPATH', true);
	require('../init.php');

	// Security Check
	$allowed_ip = array('138.68.77.159', '206.81.25.203');

	// Hash Key
	$secret = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='mtt_hash'");
	$secret = $secret['config_value'];
	
	// Get postback
	$getIP = VisitorIP();
	$payout = isset($_REQUEST['payout']) ? $db->EscapeString($_REQUEST['payout']) : '0.00225';
	$userId = isset($_REQUEST['user_id']) ? $db->EscapeString($_REQUEST['user_id']) : null;
	$survey = 'JOW'.rand(100000,999999);

	// Validate Source
	if(!in_array($getIP, $allowed_ip))
	{
		echo "ERROR: Invalid source";
		return;
	}
	
	// validate signature
	if ($_GET['hash'] != $secret){
		echo "ERROR: Signature doesn't match";
		return;
	}
	
	// validate signature
	if (empty($userId)){
		echo "ERROR: Invalid user";
		return;
	}

	if(!empty($userId) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `timestamp`>'".(time()-30)."' LIMIT 1") == 0)
	{
		$user = $db->QueryFetchArray("SELECT a.id, a.log_ip, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id ='".$userId."'");
		
		if(!empty($user['id'])) {
			// Reward
			$reward = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='mtt_reward'");
			$reward = $payout * $reward['config_value'];
			
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'1' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$user['code']."','".ip2long($user['log_ip'])."','".$payout."','".$reward."','junglesurvey','".time()."')");
		}
		
		echo "OK";
	}
?>