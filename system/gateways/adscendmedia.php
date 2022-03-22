<?php
define('BASEPATH', true);
require('../init.php');

if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {	// Get Real IP				
	$IP = $_SERVER['HTTP_X_FORWARDED_FOR']; 
} else { 
	$IP = $_SERVER['REMOTE_ADDR'];
}

// Security Check
$ow_config = array();
$configs = $db->QueryFetchArrayAll("SELECT * FROM `offerwall_config`");
foreach ($configs as $con)
{
	$ow_config[$con['config_name']] = $con['config_value']; 
}

unset($configs); 

if ($IP == '54.204.57.82' && isset($_GET['secret']) && $_GET['secret'] == $ow_config['adscend_secret']) {
	$transaction = $db->EscapeString($_GET['transaction']);
	$amount = $db->EscapeString($_GET['rate']);
	$status = $db->EscapeString($_GET['status']);
	$userIP = ip2long($_GET['ip']);
	$reward = $db->EscapeString($_GET['reward']);

	$user = explode('-', $_GET['sub1']);
	$user = $db->EscapeString($user[1]);
	
	if ($status == 1) {	
		$user = $db->QueryFetchArray("SELECT a.*, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id ='".$user."'");

		if(!empty($user['id'])) {
			$tc_points = (0.10*($amount*100));
			$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
			$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
			$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
			$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$transaction."','".$user['code']."','".$userIP."','".$amount."','".$reward."','adscendmedia','".time()."')");
		}
	} elseif($status==2) {
		$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`-'".$reward."' WHERE `id`='".$user['id']."'");	
	}
}
?>