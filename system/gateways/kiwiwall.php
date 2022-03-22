<?php
  define('BASEPATH', true);
  require('../init.php');

  // KiwiWall server IP addresses
  $allowed_ips = array(
      '34.193.235.172'
  );

  // Proceess only requests from KiwiWall IP addresses
  // This is optional validation
  if (!in_array(VisitorIP(), $allowed_ips)) {
    echo 0;
    die();
  }

  // Security Check
  $postback_password = $db->QueryFetchArray("SELECT config_value FROM `offerwall_config` WHERE `config_name`='kiwiwall_secret'");
  $postback_password = $postback_password['config_value'];
  
  // Get parameters
  $status = $db->EscapeString($_REQUEST['status']);
  $survey = $db->EscapeString($_REQUEST['trans_id']);
  $sub_id = $db->EscapeString($_REQUEST['sub_id']);
  $payout = $db->EscapeString($_REQUEST['gross']);
  $reward = $db->EscapeString($_REQUEST['amount']);
  $userIP = $db->EscapeString($_REQUEST['ip_address']);

  // Create validation signature
  $validation_signature = md5($sub_id . ':' . $reward . ':' . $postback_password);
  if ($_REQUEST['signature'] != $validation_signature) {
    echo 0;
    die();
  }

  if($status == 1 && !empty($sub_id) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$survey."' LIMIT 1") == 0)
  {
	$user = $db->QueryFetchArray("SELECT a.id,b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$sub_id."'");
	
	if(!empty($user['id'])) {
		$tc_points = (0.10*($payout*100));
		$tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
		$db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'");
		$db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
		$db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$user['code']."','".ip2long($userIP)."','".$payout."','".$reward."','kiwiwall','".time()."')");
        
        echo 1;
      die();
	}
  }
  
  echo 0;
  die();
?>