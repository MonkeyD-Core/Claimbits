<?php
	define('BASEPATH', true);
	require('../init.php');
	
	
	// Security Check
	$allowed_ip = array('54.175.173.245');
	$postback_password = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='offertoro_secret'");
	$postback_password = $postback_password['config_value'];

	// Get parameters
	$getIP = VisitorIP();
	$survey = $db->EscapeString($_REQUEST['id']);
	$offer_id = $db->EscapeString($_REQUEST['oid']);
	$reward = $db->EscapeString($_REQUEST['amount']);
	$userId = $db->EscapeString($_REQUEST['user_id']);
	$userIP = $db->EscapeString($_REQUEST['ip_address ']);
	$payout = $db->EscapeString($_REQUEST['payout']);

	// Validate Source
	if(!in_array($getIP, $allowed_ip))
	{
		echo "ERROR: Invalid source";
		return;
	}

	// Create validation signature
	$validation_signature = md5($offer_id . '-' . $userId . '-' . $postback_password);
	if ($_REQUEST['sig'] != $validation_signature) {
		echo 0;
		die();
	}
	
	if(!empty($userId) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$survey."' AND `method`='offertoro' LIMIT 1") == 0)
	{
		$user = $db->QueryFetchArray("SELECT a.id, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$userId."'");

		if(!empty($user['id'])) {
			$tc_points = (0.10*($payout*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$user['code']."','".ip2long($userIP)."','".$payout."','".$reward."','offertoro','".time()."')");
		}
	}

	echo 1;
	die();
?>