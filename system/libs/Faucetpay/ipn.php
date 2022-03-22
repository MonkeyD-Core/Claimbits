<?php
define('BASEPATH', true);
require('../../init.php');

$merchant_username = $_POST['merchant_username'];
$currency1 = $_POST['currency1'];
$token = $_POST['token'];

$token_status = get_data("https://faucetpay.io/merchant/validate-token/" . $token);
$token_status = json_decode($token_status, true);
$token_status = $token_status['valid'];

if ($config['faucetpay_username'] == $merchant_username && $config['currency_code'] == $currency1 && $token_status == true) 
{
	$payment_amount 	= $db->EscapeString($_POST['amount1']);
	$txn_id					= $db->EscapeString($_POST['token']);
	
	$get_data = explode('|', $_POST['custom']);
	$user_id 		= $db->EscapeString($get_data[0]);
	$deposit_id	= $db->EscapeString($get_data[1]);
	$user_ip		= $db->EscapeString($get_data[2]);
	
	$user = $db->QueryFetchArray("SELECT `id` FROM `users` WHERE `id`='".$user_id."' LIMIT 1");
	$deposit = $db->QueryFetchArray("SELECT * FROM `deposits` WHERE `method`='1' AND `id`='".$deposit_id."' AND `status`!='1' LIMIT 1");

	if(!empty($user['id']) && !empty($deposit['id']) && $payment_amount >= $deposit['amount'])
	{
		$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`+'".$payment_amount."' WHERE `id`='".$user['id']."'");	
		$db->Query("UPDATE `deposits` SET `amount`='".$payment_amount."', `txn_id`='".$txn_id."', `status`='1', `time`='".time()."' WHERE `id`='".$deposit['id']."'");	
	
		add_notification($user['id'], 5, $payment_amount);	
	}

	echo $_POST['custom'].'|success';
	exit;
}
else
{
	echo $_POST['custom'].'|error';
}
?>