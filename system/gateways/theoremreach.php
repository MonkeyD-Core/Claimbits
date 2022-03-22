<?php
	define('BASEPATH', true);
	require('../init.php');

	// Security Check
	$secret = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='tr_secret'");
	$secret = $secret['config_value'];

	// Get postback
	$userId = isset($_REQUEST['user_id']) ? $db->EscapeString($_REQUEST['user_id']) : null;
	$survey = isset($_REQUEST['tx_id']) ? $db->EscapeString($_REQUEST['tx_id']) : null;
	$reward = isset($_REQUEST['reward']) ? $db->EscapeString($_REQUEST['reward']) : null;
    $payout = isset($_REQUEST['currency']) ? $db->EscapeString($_REQUEST['currency']) : null;
	$status  = isset($_REQUEST['status']) ? $db->EscapeString($_REQUEST['status']) : null;
	$screenout = isset($_REQUEST['screenout']) ? $db->EscapeString($_REQUEST['screenout']) : null;
	$profiler = isset($_REQUEST['profiler']) ? $db->EscapeString($_REQUEST['profiler']) : null;
	$debug = isset($_REQUEST['debug']) ? true : false;

	// Generate HASH Key
	$URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$URL = preg_replace('/&?hash=[^&]*/', '', $URL);

	$encoded_key = utf8_encode($secret);
	$encoded_URL = utf8_encode($URL);
	$hashed = hash_hmac('sha1', $encoded_URL, $encoded_key);
	$digested_hash = pack('H*',$hashed);
	$base64_encoded_result = base64_encode($digested_hash);
	$hash_key = str_replace(["+","/","="],["-","_",""],utf8_decode($base64_encoded_result));

	// validate signature
	if ($_REQUEST['hash'] != $hash_key){
		echo "ERROR: Hash key doesn't match";
		return;
	}

	if($debug === false && !empty($userId) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$survey."' LIMIT 1") == 0)
	{
		$user = $db->QueryFetchArray("SELECT a.id, a.log_ip, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$userId."'");

		if(!empty($user['id'])) {
			$tc_points = (0.10*($payout*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$user['code']."','".ip2long($user['log_ip'])."','".$payout."','".$reward."','theoremreach','".time()."')");
		}
		
		echo "OK";
	}
?>