<?php    
	define('BASEPATH', true);
	require('../../init.php');

    function errorAndDie($error_msg) {
        global $config;
        if (!empty($config['site_email'])) {
            $report = 'Error: '.$error_msg."\n\n";
            $report .= "POST Data\n\n";
            foreach ($_POST as $k => $v) {
                $report .= "|$k| = |$v|\n";
            }
            mail($config['site_email'], 'CoinPayments IPN Error', $report);
        }
        die('IPN Error: '.$error_msg);
    }

    if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
        errorAndDie('IPN Mode is not HMAC');
    }

    if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
        errorAndDie('No HMAC signature sent.');
    }

    $request = file_get_contents('php://input');
    if ($request === FALSE || empty($request)) {
        errorAndDie('Error reading POST data');
    }

    if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($config['cp_id'])) {
        errorAndDie('No or incorrect Merchant ID passed');
    }

    $hmac = hash_hmac("sha512", $request, trim($config['cp_secret']));
    if ($hmac != $_SERVER['HTTP_HMAC']) {
        errorAndDie('HMAC signature does not match');
    }
    
    // HMAC Signature verified at this point, load some variables.
    $txn_id = $_POST['txn_id'];
    $payment_amount = floatval($_POST['amount1']);
    $amount2 = floatval($_POST['amount2']);
    $currency1 = $_POST['currency1'];
    $currency2 = $_POST['currency2'];
    $status = intval($_POST['status']);
    $status_text = $_POST['status_text'];

	$get_data = explode('|', $_POST['custom']);
	$user_id 		= $db->EscapeString($get_data[0]);
	$deposit_id	= $db->EscapeString($get_data[1]);
	$user_ip		= $db->EscapeString($get_data[2]);

    // Check the original currency to make sure the buyer didn't change it.
    if ($currency1 != $config['currency_code']) {
        errorAndDie('Original currency mismatch!');
    }

    if ($status >= 100 || $status == 2)
	{
		$user = $db->QueryFetchArray("SELECT `id` FROM `users` WHERE `id`='".$user_id."' LIMIT 1");
		$deposit = $db->QueryFetchArray("SELECT * FROM `deposits` WHERE `id`='".$deposit_id."' AND `status`!='1' LIMIT 1");

		if($deposit['amount'] <= $payment_amount)
		{
			if(!empty($user['id']) && !empty($deposit['id']))
			{
				$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`+'".$payment_amount."' WHERE `id`='".$user['id']."'");	
				$db->Query("UPDATE `deposits` SET `amount`='".$payment_amount."', `txn_id`='".$txn_id."', `status`='1', `time`='".time()."' WHERE `id`='".$deposit['id']."'");	
			
				add_notification($user['id'], 5, $payment_amount);
			}
		}
    }
	else if ($status < 0)
	{
		$db->Query("DELETE FROM `deposits` WHERE `id`='".$deposit_id."' AND `status`='0'");	
    }
	else if ($status == 1)
	{
		$deposit = $db->QueryFetchArray("SELECT * FROM `deposits` WHERE `id`='".$deposit_id."' AND `status`!='1' LIMIT 1");

		if($deposit['amount'] <= $payment_amount)
		{
			$db->Query("UPDATE `deposits` SET `txn_id`='".$txn_id."', `status`='2', `time`='".time()."' WHERE `id`='".$deposit['id']."'");	
		}
    }

    die('IPN OK'); 
?>