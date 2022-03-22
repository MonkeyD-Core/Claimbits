<?php 
	define('BASEPATH', true);
	require('../init.php');

	// Security Check
	$ow_config = array();
	$configs = $db->QueryFetchArrayAll("SELECT * FROM `offerwall_config`");
	foreach ($configs as $con)
	{
		$ow_config[$con['config_name']] = $con['config_value']; 
	}

	unset($configs); 

	// Persona.ly server IP addresses
	$allowed_ips = array(
		'159.203.84.146',
		'52.200.142.249',
	);

	// Proceess only requests from Persona.ly IP addresses
	// This is optional validation
	if (!in_array(VisitorIP(), $allowed_ips)) {
		echo 0;
		die();
	}
    
	// Get params
	$user_id = $db->EscapeString($_REQUEST['user_id']);
	$amount = $db->EscapeString($_REQUEST['amount']);
	$payout = $db->EscapeString($_REQUEST['payout']);
	$offer_id = $db->EscapeString($_REQUEST['offer_id']);
	$app_hash = $db->EscapeString($_REQUEST['app_id']);

	// Create validation signature
	$validation_signature = md5($user_id . ':' . $ow_config['personaly_hash'] . ':' . $ow_config['personaly_secret']);
	if ($_REQUEST['signature'] != $validation_signature) {
		echo 0;
		die();
	}

	// Validation was successful. Credit user process.
	$user = $db->QueryFetchArray("SELECT a.*, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id ='".$user_id."'");

	if(!empty($user['id'])) {
		$tc_points = (0.10*($payout*100));
		$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
		$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$amount."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
		$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$amount."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$amount."', `last_offer`='".time()."'");
		$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$offer_id."','".$user['code']."','".ip2long($user['log_ip'])."','".$payout."','".$amount."','personaly','".time()."')");
	}
	
	echo 1;
	die();
?>