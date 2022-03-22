<?php
	define('BASEPATH', true);
	require('../init.php');

	// Security Check
	$postback_password = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='offerdaddy_secret'");
	$postback_password = $postback_password['config_value'];

	// Get parameters
	$survey = $db->EscapeString(urldecode($_GET["transaction_id"]));
	$offer_id = $db->EscapeString(urldecode($_GET["offer_id"]));
	$reward = $db->EscapeString(urldecode($_GET["amount"]));
	$sub_id = $db->EscapeString(urldecode($_GET["userid"]));
	$payout = $db->EscapeString(urldecode($_GET["payout"]));
	$signature = urldecode($_GET["signature"]);

	//Check the signature
	$validation_signature = md5($survey."/".$offer_id."/".$postback_password);

	if($validation_signature != trim($signature)){
	  echo "0";
	  exit(0);
	}

	if(!empty($sub_id) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$survey."' LIMIT 1") == 0)
	{
		$user = $db->QueryFetchArray("SELECT a.id,a.log_ip,b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$sub_id."'");

		if(!empty($user['id'])) {
			$tc_points = (0.10*($payout*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$user['code']."','".$user['log_ip']."','".$payout."','".$reward."','offerdaddy','".time()."')");
			
			echo 1;
		  die();
		}
	}

	echo 0;
	die();
?>