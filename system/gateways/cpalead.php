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

// Get postback repsponse
$subid 			= $db->EscapeString($_REQUEST['subid']);
$campaign_id	= $db->EscapeString($_REQUEST['campaign_id']);
$transaction	= $db->EscapeString($_REQUEST['lead_id']);
$country			= $db->EscapeString($_REQUEST['country_iso']);
$amount			= $db->EscapeString($_REQUEST['payout']);
$reward			= $db->EscapeString($_REQUEST['virtual_currency']);
$userIP			= ip2long($_REQUEST['ip_address']);

// Check Password
if (!empty($ow_config['cpalead_password']) && isset($_REQUEST['password']) && !empty($_REQUEST['password']) && ($ow_config['cpalead_password'] != $_REQUEST['password']))
{
	die('Wrong security password!');
}

// Validate Postback IP
if (VisitorIP() != '52.0.65.65')
{
	die('This call wasn\'t made from CPALead servers!');
}

if ($db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$transaction."' AND `user_id`='".$subid."' LIMIT 1") == 0) {	
	$user = $db->QueryFetchArray("SELECT * FROM `users` WHERE `id`='".$subid."'");

	if(!empty($user['id'])) {
		$tc_points = (0.10*($amount*100));
		$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
		$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
		$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
		$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`campaign_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$transaction."','".$campaign_id."','".$country."','".$userIP."','".$amount."','".$reward."','cpalead','".time()."')");
	
		echo 'OK';
	}
}
?>