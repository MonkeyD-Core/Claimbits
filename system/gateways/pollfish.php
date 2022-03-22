<?php    
	define('BASEPATH', true);
	require('../init.php');

	$postback_debug = false;
	$cpa = rawurldecode($_REQUEST["cpa"]);
	$device_id = rawurldecode($_REQUEST["device_id"]);
	$request_uuid = rawurldecode($_REQUEST["request_uuid"]);
	$reward_name = rawurldecode($_REQUEST["reward_name"]);
	$reward_value = rawurldecode($_REQUEST["reward_value"]);
	$status = rawurldecode($_REQUEST["status"]);
	$reason = rawurldecode($_REQUEST["reason"]);
	$timestamp = rawurldecode($_REQUEST["timestamp"]);
	$tx_id = rawurldecode($_REQUEST["tx_id"]);
	$url_signature = rawurldecode($_REQUEST["signature"]);

	$pollfish_data = $cpa . ":" . $device_id;
	if (!empty($request_uuid)) {
		$pollfish_data = $pollfish_data . ":" . $request_uuid;
	}
	$pollfish_data = $pollfish_data . ":" . $reward_name . ":" . $reward_value . ":" . $status . ":" . $reason . ":" . $timestamp . ":" . $tx_id;

	$computed_signature = base64_encode(hash_hmac("sha1" , $pollfish_data, $config['pollfish_secret'] , true));
	$is_valid = $url_signature == $computed_signature;

	if($is_valid && !empty($request_uuid) && $status == 'eligible' && (!isset($_REQUEST["debug"]) || $_REQUEST["debug"] == false))
	{
		if($db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$tx_id."' AND `method`='pollfish' LIMIT 1") == 0)
		{
			$user = $db->QueryFetchArray("SELECT a.id, a.log_ip, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$request_uuid."'");

			if(!empty($user['id'])) {
				$payout = $cpa / 100;
				$tc_points = (0.10*$cpa);
				$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
				$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward_value."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
				$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward_value."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward_value."', `last_offer`='".time()."'");
				$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$tx_id."','".$user['code']."','".ip2long($user['log_ip'])."','".$payout."','".$reward_value."','pollfish','".time()."')");
			}
		}
	}
	elseif($postback_debug)
	{
		if (!empty($config['site_email'])) {
			$report .= "POST Data\n\n";
			foreach ($_REQUEST as $k => $v) {
				$report .= "|$k| = |$v|\n";
			}
			$report .= "IS Valid = $is_valid\n";
			$report .= "URL Signature = $url_signature\n";
			$report .= "Computed Signature = $computed_signature\n";
			$report .= "Pollfish Data = $pollfish_data\n";
			mail($config['site_email'], 'PollFish Postback Failed', $report);
		}
	}
?>