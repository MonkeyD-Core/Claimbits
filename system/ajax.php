<?php
define('BASEPATH', true);
define('IS_AJAX', true);
require('init.php');

if($is_online)
{
	if(isset($_GET['a']))
	{
		switch ($_GET['a']) {
			case 'calculatePTC':
				if(isset($_GET['pack']) && is_numeric($_GET['pack']) && isset($_GET['visits']) && is_numeric($_GET['visits']))
				{
					$pID = $db->EscapeString($_GET['pack']);
					$visits = $db->EscapeString($_GET['visits']);
					$redirect = ($_GET['redirect'] == 1 ? 1 : 0);
					$ad_pack = $db->QueryFetchArray("SELECT `price` FROM `ptc_packs` WHERE `id`='".$pID."' LIMIT 1");

					$value = $visits * ($ad_pack['price'] / 100000000);
					if($redirect)
					{
						$value = $value + ($value/100*$config['ptc_redirect_price']);
					}

					echo number_format($value, 8).' '.getCurrency();
				}
				else
				{
					echo '0.00000000 '.getCurrency();
				}

				break;
			case 'calculateRefs':
				if(isset($_GET['refs']) && is_numeric($_GET['refs']))
				{
					$price = $_GET['refs'] * $config['market_price'];

					echo number_format($price, 8).' '.getCurrency();
				}
				else
				{
					echo '0.00000000 '.getCurrency();
				}

				break;
			case 'getReward':
				if(is_numeric($_GET['rID'])){
					$rID = $db->EscapeString($_GET['rID']);
					$reward = $db->QueryFetchArray("SELECT a.*, b.membership AS mem_name FROM activity_rewards a LEFT JOIN memberships b ON b.id = a.membership WHERE a.id = '".$rID."' LIMIT 1");

					$leads = $db->QueryFetchArray("SELECT `total_offers` FROM `users_offers` WHERE `uid`='".$data['id']."' LIMIT 1");
					$refs = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `ref`='".$data['id']."'");

					$type = 'error';
					$msg = $lang['l_212'];
					if(!empty($reward['id']))
					{
						if($reward['req_type'] == 0 && $reward['requirements'] > $data['total_claims']){
							$type = 'error';
							$msg = lang_rep($lang['l_209'], array('-NUM-' => number_format($reward['requirements'])));
						}elseif($reward['req_type'] == 1 && $reward['requirements'] > $data['sl_total']){
							$type = 'error';
							$msg = lang_rep($lang['l_202'], array('-NUM-' => number_format($reward['requirements'])));
						}elseif($reward['req_type'] == 2 && $reward['requirements'] > $leads['total_offers']){
							$type = 'error';
							$msg = lang_rep($lang['l_488'], array('-NUM-' => number_format($reward['requirements'])));
						}elseif($reward['req_type'] == 3 && $reward['requirements'] > $refs['total']){
							$type = 'error';
							$msg = lang_rep($lang['l_489'], array('-NUM-' => number_format($reward['requirements'])));
						}elseif($db->QueryGetNumRows("SELECT * FROM `activity_rewards_claims` WHERE `reward_id`='".$reward['id']."' AND `user_id`='".$data['id']."' LIMIT 1") > 0){
							$type = 'error';
							$msg = $lang['l_213'];
						}else{
							if($reward['type'] == 1)
							{
								if($data['membership'] == 0) 
								{
									$premium = time()+(86400*$reward['reward']);
									$db->Query("UPDATE `users` SET `membership`='".$premium."', `membership_id`='".$reward['membership']."' WHERE `id`='".$data['id']."'");
								}
								else 
								{
									$premium = ((86400*$reward['reward'])+$data['membership']);
									$db->Query("UPDATE `users` SET `membership`='".$premium."' WHERE `id`='".$data['id']."'");
								}
							}
							elseif($reward['type'] == 2)
							{
								$satoshi = ($reward['reward']/100000000);
								$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`+'".$satoshi."' WHERE `id`='".$data['id']."'");
							}
							else
							{
								$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$reward['reward']."', `total_revenue`=`total_revenue`+'".$reward['reward']."' WHERE `id`='".$data['id']."'");
							}

							$db->Query("UPDATE `activity_rewards` SET `claims`=`claims`+'1' WHERE `id`='".$reward['id']."'");
							$db->Query("INSERT INTO `activity_rewards_claims` (`reward_id`,`user_id`,`reward`,`type`,`date`)VALUES('".$reward['id']."','".$data['id']."','".$reward['reward']."','".$reward['type']."','".time()."')");

							$type = 'success';
							$msg = lang_rep($lang['l_214'], array('-REWARD-' => ($reward['type'] == 1 ? number_format($reward['reward'], 0).' '.($data['membership_id'] == $reward['membership'] ? $reward['mem_name'] : ($data['membership_id'] > 1 ? $data['mem_name'] : $reward['mem_name'])).' '.$lang['l_234'] : number_format($reward['reward']).' '.$lang['l_337'])));
						}
					}

					$resultData = array('message' => $msg, 'type' => $type);

					header('Content-type: application/json');
					echo json_encode($resultData);
				}
					
				break;
			case 'bannerPacks':
				$type = ($_GET['type'] == 1 ? 1 : 0);
				$packs = $db->QueryFetchArrayAll("SELECT * FROM `ad_packs` WHERE `type`='".$type."' ORDER BY `price` ASC");
				foreach($packs as $pack){
					echo '<option value="'.$pack['id'].'">'.$pack['days'].' '.$lang['l_234'].' - '.$pack['price'].' '.getCurrency().'</option>';
				}

				break;
			case 'getBTCPrice':
				$getPrice = $db->QueryFetchArray("SELECT `value`,`minute` FROM `bitcoin_price` ORDER BY `time` DESC LIMIT 1");

				$resultData = array('price' => $getPrice['value'], 'time' => date('H:i'));

				header('Content-type: application/json');
				echo json_encode($resultData);
					
				break;
		}
	}
	
	if(isset($_POST['a']) && $_POST['a'] == 'getFaucet')
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']))
		{
			$resultData = array('number' => 0, 'reward' => 0,  'message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_504'].'</div>', 'status' => 0); 
		}
		elseif(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$captcha_valid = 1;
			if($config['faucet_recaptcha'] == 1 || $config['faucet_solvemedia'] == 1 || $config['faucet_raincaptcha'] == 1)
			{
				$captcha_valid = 0;
				if($_POST['captcha'] == 1 && $config['faucet_recaptcha'] == 1)
				{
					$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_sec']);
					$recaptcha = $recaptcha->verify($_POST['response'], $_SERVER['REMOTE_ADDR']);
				
					if($recaptcha->isSuccess()){
						$captcha_valid = 1;
					}
				}
				elseif($_POST['captcha'] == 0 && $config['faucet_solvemedia'] == 1)
				{
					$solvemedia_response = solvemedia_check_answer($config['solvemedia_v'],$_SERVER["REMOTE_ADDR"],$_POST['challenge'],$_POST['response'],$config['solvemedia_h']);
					if($solvemedia_response->is_valid)
					{
						$captcha_valid = 1;
					}
				}
				elseif($_POST['captcha'] == 2 && $config['faucet_raincaptcha'] == 1)
				{
					$client = new \SoapClient('https://raincaptcha.com/captcha.wsdl');
					$response = $client->send($config['raincaptcha_secret'], $_POST['response'], $_SERVER['REMOTE_ADDR']);
					if ($response->status === 1)
					{
						$captcha_valid = 1;
					}
				}
			}
			
			if(!$captcha_valid)
			{
				$resultData = array('number' => 0, 'reward' => 0, 'message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_142'].'</div>', 'status' => 0); 
			}
			elseif($data['sl_today'] < $config['faucet_sl_required'])
			{
				$resultData = array('number' => 0, 'reward' => 0, 'message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.lang_rep($lang['l_427'], array('-SUM-' => $config['faucet_sl_required'] - $data['sl_today'])).'</div>', 'status' => 0); 
			}
			elseif($data['last_claim'] < (time()-($config['faucet_time']*60)))
			{
				$prize = 0;
				$number = mt_rand(1,99999);
				if($number == 99999) {
					$prize = $config['jackpot_prize'];
				}
				else
				{
					$getPrize = $db->QueryFetchArray("SELECT `reward` FROM `faucet` WHERE `small`<='".$number."' AND `big`>='".$number."' LIMIT 1");
					if(!empty($getPrize['reward'])) 
					{
						$prize = $getPrize['reward'];
					}
				}

				$total_claims = $data['total_claims'] + 1;
				$level = $db->QueryFetchArray("SELECT `id`, `reward` FROM `levels` WHERE `requirements`<='".$total_claims."' ORDER BY `requirements` DESC LIMIT 1");
				
				$query = '';
				$multiplier = ($level['reward'] + $data['multiplier'])-1;
				$prize = $prize * $multiplier;
				if($level['id'] > $data['level']) 
				{
					$query = ", `level`='".$level['id']."'";
					add_notification($data['id'], 1, $level['id']);
				}
				
				$db->Query("INSERT INTO `faucet_claims` (`user_id`,`number`,`reward`,`time`) VALUES ('".$data['id']."','".$number."','".$prize."','".time()."')");
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$prize."', `today_revenue`=`today_revenue`+'".$prize."', `total_revenue`=`total_revenue`+'".$prize."', `today_claims`=`today_claims`+'1', `total_claims`=`total_claims`+'1', `last_claim`='".time()."'".$query." WHERE `id`='".$data['id']."'");
				
				if($data['ref'] > 0) {
					$ref_data = $db->QueryFetchArray("SELECT a.last_activity, b.ref_com FROM users a LEFT JOIN memberships b ON b.id = a.membership_id WHERE a.id = '".$data['ref']."' LIMIT 1");
					
					if(!empty($ref_data['last_activity']) && $ref_data['last_activity'] > (time() - ($config['ref_activity']*3600))) {
						$commission = (($ref_data['ref_com']/100)*$prize);
						ref_commission($data['ref'], $data['id'], $commission);
					}
				}
				
				$resultData = array('number' => $number, 'reward' => $prize, 'message' => '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle fa-fw"></i> Congratulations, your lucky number was '.number_format($number).' and you won '.number_format($prize, 2).' Bits!</div>', 'status' => 200);
			}
			else
			{
				$resultData = array('number' => 0, 'reward' => 0, 'message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> You already claimed your bits this hour.</div>', 'status' => 400);
			}
		} else {
			$resultData = array('number' => 0, 'reward' => 0,  'message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 0); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'proccessDeposit')
	{
		if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$amount = $db->EscapeString($_POST['amount']);
			$method = ($_POST['method'] == 1 ? 1 : 0);
			
			if($amount < $config['deposit_min']) {
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> Minimum amount to deposit is '.$config['deposit_min'].' '.getCurrency().'.</div>', 'status' => 100);
			}
			else
			{
				$db->Query("INSERT INTO `deposits` (`user_id`,`user_email`,`amount`,`method`,`user_ip`,`time`) VALUES ('".$data['id']."','".$data['email']."','".$amount."','".$method."','".VisitorIP()."','".time()."')");
				$depositID = $db->GetLastInsertId();

				if($method == 1)
				{
					$fp_form = '<form name="faucetpayform" action="https://faucetpay.io/merchant/webscr" method="post">
						<input type="hidden" name="merchant_username" value="'.$config['faucetpay_username'].'">
						<input type="hidden" name="item_description" value="'.('Deposit '.number_format($amount, 8, '.', '').' '.$config['currency_code'].' to '.$config['site_name']).'">
						<input type="hidden" name="amount1" value="'.number_format($amount, 8, '.', '').'">
						<input type="hidden" name="currency1" value="'.$config['currency_code'].'">
						<input type="hidden" name="currency2" value="'.$config['currency_code'].'">
						<input type="hidden" name="custom" value="'.($data['id'].'|'.$depositID.'|'.VisitorIP()).'">
						<input type="hidden" name="callback_url" value="'.$config['secure_url'].'/system/libs/Faucetpay/ipn.php">
						<input type="hidden" name="success_url" value="'.GenerateURL('deposits', true).'">
						<input type="hidden" name="cancel_url" value="'.GenerateURL('deposits', true).'">
						<input type="submit" name="submit" class="btn btn-success btn-md w-100 mt-1 text-center"  value="Click here to proceed">
					</form>';
					
					$resultData = array('message' => $fp_form, 'transaction' => $depositID, 'method' => 1, 'status' => 200);
				}
				else
				{
					// Initiate CoinPayments API
					$cps_api = new CoinpaymentsAPI($config['cp_private_key'], $config['cp_public_key'], 'json');	

					$custom = ($data['id'].'|'.$depositID.'|'.VisitorIP());
					$item_name = 'Deposit Funds';
					$ipn_url = $config['secure_url'].'/system/libs/CoinPayments/ipn.php';
					$currency = getCurrency();

					$transaction = $cps_api->CreateComplexTransaction($amount, $currency, $currency, $data['email'], '', $data['username'], $item_name, '', 'INV-'.date('Y').'-'.$depositID, $custom, $ipn_url);
					$qr_code = ($currency == 'BTC' ? 'https://blockchain.info/qr?data=bitcoin:'.$transaction['result']['address'].'?amount='.$amount.'&size=140' : 'https://chart.googleapis.com/chart?chs=148x148&cht=qr&chl='.$transaction['result']['address'].'&choe=UTF-8');

					if ($transaction['error'] == 'ok') {
						$resultData = array('message' => '<p>Please send exactly <b>'.$amount.' '.$currency.'</b> to address below</p><div class="form-row justify-content-center"><div class="col-md-12"><div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-copy" onclick="copy();"></i></div></div><input type="text" class="form-control text-center" id="codebox" value="'.$transaction['result']['address'].'" readonly></div></div></div><img src="'.$qr_code.'"><br /><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i><br />Time remaining:<div id="deposit-countdown"></div><small>Deposits reflect after '.$transaction['result']['confirms_needed'].' confirmations. After payment you can close this window.</small>', 'transaction' => $depositID, 'method' => 0, 'status' => 200);
					}
					else
					{
						$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> Unexpected error occured, please try again later!</div>', 'status' => 300);
					}
				}
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'verifyDeposit')
	{
		$id = $db->EscapeString($_POST['transaction']);
		$deposit = $db->QueryFetchArray("SELECT `id`, `amount`, `status` FROM `deposits` WHERE `id`='".$id."' AND `user_id`='".$data['id']."' LIMIT 1");
		
		if(empty($deposit['id']))
		{
			$resultData = array('status' => 999);
		}
		else
		{
			switch ($deposit['status']) 
			{
				case '1':
					$resultData = array('message' => '<div class="alert alert-success mb-0" role="alert"><i class="fa fa-check-circle fa-fw"></i> Your transaction was successfully completed! '.$deposit['amount'].' '.getCurrency().' was added into your Purchase Balance.</div>', 'status' => 200); 
					break;
				case '2':
					$resultData = array('message' => '<div class="alert alert-success mb-0" role="alert"><i class="fa fa-check-circle fa-fw"></i> We received your deposit, funds will be added into your Purchase Balance as soon as transaction receive 2 confirmations.</div>', 'status' => 200); 
					break;
				default:
					$resultData = array('status' => 100); 
					break;
			}
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'proccessWithdraw')
	{
		if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$method = $db->EscapeString($_POST['method']);
			
			if($method < 1 && $method > 3) {
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> Please select a valid withdrawal method.</div>', 'status' => 100);
			}
			else
			{
				$withdraw_min = number_format($method == 3 ? $data['fp_min_pay'] : ($method == 2 ? $data['ks_min_pay'] : $data['btc_min_pay']), 8);
				
				if($method == 3)
				{
					$withdraw_address = 'Payout will be sent to your FaucetPay Address: '.(empty($data['fp_id']) ? '<br /><a href="'.GenerateURL('account').'">Click here to set your address</a>' : '<span class="badge badge-light">'.$data['fp_id'].'</span>');
				}
				else if($method == 2)
				{
					$withdraw_address = 'Payout will be sent to your KSWallet Address: '.(empty($data['ks_id']) ? '<br /><a href="'.GenerateURL('account').'">Click here to set your address</a>' : '<span class="badge badge-light">'.$data['ks_id'].'</span>');
				}
				else
				{
					$withdraw_address = 'Payout will be sent to your '.getCurrency('name').' Address: '.(empty($data['btc_id']) ? '<br /><a href="'.GenerateURL('account').'">Click here to set your address</a>' : '<span class="badge badge-light">'.$data['btc_id'].'</span>');
				}
				
				$resultData = array('message' => '<div id="withdrawAlert"><div class="alert alert-info" role="alert">Please select the amount you want to withdraw.<br />'.$withdraw_address.'</div></div><form class="form-inline justify-content-center" onsubmit="sendWithdraw(); return false;"><input type="hidden" value="'.$method.'" id="withdrawMethod" /><input type="text" class="form-control my-1 mr-sm-2" style="max-width:140px" id="withdrawAmount" oninput="calculateWithdraw()" placeholder="Amount in Bits" required><label for="withdrawResult" class="my-1 mr-2">= </label><input type="text" class="form-control my-1 mr-sm-2" style="max-width:140px" id="withdrawResult" value="0.00000000 '.getCurrency().'" disabled><button type="submit" class="btn btn-primary">Withdraw</button></form><small id="depositHelp" class="form-text text-muted">Min. withdraw '.$withdraw_min.' '.getCurrency().'</small>', 'status' => 200);
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'sendWithdraw')
	{
		if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$method = $db->EscapeString($_POST['method']);
			$amount = $db->EscapeString($_POST['amount']);
			$satoshi = floor($amount*$config['bits_rate']);
			$bitcoin = number_format($satoshi/100000000, 8, '.', '');
			$withdraw_min = number_format($method == 3 ? $data['fp_min_pay'] : ($method == 2 ? $data['ks_min_pay'] : $data['btc_min_pay']), 8, '.', '');
			$payment_info = ($method == 3 ? $data['fp_id'] : ($method == 2 ? $data['ks_id'] : $data['btc_id']));
			$currency = getCurrency();
			
			if($method < 1 && $method > 3)
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> Please select a valid withdrawal method.</div>', 'status' => 100);
			}
			elseif($data['total_claims'] < $config['withdraw_min_claims'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> You must complete at least <b>'.number_format($config['withdraw_min_claims']).' faucet claims</b>, before being able to withdraw your funds!</div>', 'status' => 700);
			}
			elseif(!is_numeric($amount) || $bitcoin < $withdraw_min)
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> You can\'t withdraw less than '.$withdraw_min.' '.getCurrency().'.</div>', 'status' => 500);
			}
			elseif(strlen($payment_info) < 26)
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> Please complete your withdrawal address on <a href="'.GenerateURL('account').'">Edit Profile</a> section.</div>', 'status' => 600);
			}
			elseif($amount > $data['account_balance'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> You don\'t have enough bits into account balance!</div>', 'status' => 600);
			}
			else
			{
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`-'".$amount."' WHERE `id`='".$data['id']."'");
				
				$status = 0;
				$payout_id = '';
				$successMsg = 'Your withdrawal request was successfully received.';
				if($method == 3 && $data['fp_wait_time'] == 0) 
				{
					$faucetpay = new FaucetPay($config['fp_api_key'], $currency);
					$fp_result = $faucetpay->send($payment_info, $satoshi);

					if($fp_result['success'] == true)
					{
						$status = 1;
						$payout_id = $fp_result['payout_id'];
						$successMsg = number_format($satoshi).' satoshi were sent to your FaucetPay account!';
					}
				}
				else if($method == 1 && $data['btc_wait_time'] == 0) 
				{
					$cps_api = new CoinpaymentsAPI($config['cp_private_key'], $config['cp_public_key'], 'json');	
					$withdraw = array('amount' => $bitcoin, 'address' => $payment_info, 'currency' => $currency, 'auto_confirm' => 1, 'note' => 'Withdraw by '.$data['username']);
					$transaction = $cps_api->CreateWithdrawal($withdraw);

					if($transaction['error'] == 'ok')
					{
						$status = 1;
						$payout_id = $transaction['result']['id'];
						$successMsg = $bitcoin.' '.$currency.' was transfered to your '.getCurrency('name').' Wallet!';
					}
				}
				else if($method == 2 && $data['ks_wait_time'] == 0) 
				{
					$kswallet = new KSWallet($config['ks_api_key'], $currency);
					$ks_result = $kswallet->send($payment_info, $satoshi);

					if($ks_result['success'] == true)
					{
						$status = 1;
						$payout_id = $ks_result['hash'];
						$successMsg = number_format($satoshi).' satoshi were sent to your KSWallet account!';
					}
				}
				
				$db->Query("INSERT INTO `withdrawals` (`user_id`,`bits`,`btc`,`method`,`payment_info`,`payout_id`,`ip_address`,`time`,`status`) VALUES ('".$data['id']."','".$amount."','".$bitcoin."','".$method."','".$payment_info."','".$payout_id."','".VisitorIP()."','".time()."','".$status."')");
				
				$resultData = array('message' => '<div class="alert alert-success mb-0" role="alert"><i class="fa fa-check-circle fa-fw"></i> '.$successMsg.'</div>', 'status' => 200);
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'sendTransfer')
	{
		if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'] && $config['transfer_status'] == 1)
		{
			$amount = $db->EscapeString($_POST['amount']);
			$satoshi = ($amount*$config['bits_rate']);
			$bitcoin = number_format($satoshi/100000000, 8, '.', '');

			if(!is_numeric($amount) || $satoshi < $config['transfer_min'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.lang_rep($lang['l_459'], array('-NUM-' => (int)$config['transfer_min'])).'</div>', 'status' => 500);
			}
			elseif($amount > $data['account_balance'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_406'].'</div>', 'status' => 600);
			}
			else
			{
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`-'".$amount."', `purchase_balance`=`purchase_balance`+'".$bitcoin."' WHERE `id`='".$data['id']."'");
				$db->Query("INSERT INTO `funds_transfers` (`user_id`,`bits`,`satoshi`,`bits_rate`,`time`) VALUES ('".$data['id']."','".$amount."','".$satoshi."','".$config['bits_rate']."','".time()."')");

				$resultData = array('message' => '<div class="alert alert-success mb-0" role="alert"><i class="fa fa-check-circle fa-fw"></i> '.lang_rep($lang['l_460'], array('-BTC-' => $bitcoin, '-BITS-' => number_format($amount, 2))).'</div>', 'status' => 200);
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'getShortlink')
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']))
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_504'].'</div>', 'status' => 500); 
		}
		elseif(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			// Initialise captcha
			require('libs/captcha/session.class.php');
			require('libs/captcha/captcha.class.php');
			CBCaptcha::setIconsFolderPath('../../../static/img/captcha/');
			
			if(!CBCaptcha::validateSubmission($_POST)) 
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_481'].'</div>', 'status' => 600);
			} 
			else 
			{
				$sid = $db->EscapeString($_POST['data']);
				$linkData = $db->QueryFetchArray("SELECT * FROM `shortlinks_config` WHERE `id`='".$sid."' AND `status`='1' LIMIT 1");
				if(empty($linkData['shortlink']) || empty($linkData['password']))
				{
					$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_482'].'</div>', 'status' => 500);
				}
				else
				{
					$validate = $db->QueryFetchArray("SELECT `count` FROM `shortlinks_done` WHERE `user_id`='".$data['id']."' AND `short_id`='".$linkData['id']."' LIMIT 1");
					if($validate['count'] >= $linkData['daily_limit'])
					{
						$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_482'].'</div>', 'status' => 500);
					}
					else
					{
						$shortLink = false;
						$short_key = false;
						$countLinks = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `shortlinks` WHERE `short_id`='".$linkData['id']."'");
						if($countLinks['total'] < 10) 
						{
							$short_key = GenerateKey(32);
							$return_url = urlencode($config['secure_url'].'/shortlink.php?short_key='.$short_key);
							$api_url = 'http://'.$linkData['shortlink'].'/api?api='.$linkData['password'].'&url='.$return_url.'&alias=CB'.GenerateKey(9);
							$getLink = get_data($api_url);

							if(empty($getLink))
							{
								$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_146'].'</div>', 'status' => 500);
							}
							else
							{
								$getLink = json_decode($getLink, true);
								if($getLink['status'] === 'error' || empty($getLink['shortenedUrl'])) 
								{
									$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_146'].'</div>', 'status' => 500);
								}
								else
								{
									$shortLink = $db->EscapeString($getLink['shortenedUrl']);
									$db->Query("INSERT INTO `shortlinks` (`short_id`,`shortlink`,`hash`,`time`) VALUES ('".$linkData['id']."','".$shortLink."','".$short_key."','".time()."')");
								}
							}
						}
						else
						{
							$getLink = $db->QueryFetchArray("SELECT `shortlink`, `hash` FROM `shortlinks` WHERE `short_id`='".$linkData['id']."' ORDER BY rand() LIMIT 1");
							$shortLink = $getLink['shortlink'];
							$short_key = $getLink['hash'];
						}

						if(!$shortLink || !$short_key)
						{
							$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_146'].'</div>', 'status' => 500);
						}
						else
						{
							$_SESSION['shortlink_key'] = $short_key;
							$db->Query("INSERT INTO `shortlinks_session` (`user_id`,`short_id`,`time`) VALUES ('".$data['id']."','".$linkData['id']."','".time()."') ON DUPLICATE KEY UPDATE `time`='".time()."'");

							$resultData = array('message' => '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle fa-fw"></i> '.$lang['l_483'].'</div>', 'shortlink' => $shortLink, 'status' => 200);
						}
					}
				}
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 500); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'proccessPTC')
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']))
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_504'].'</div>', 'status' => 400); 
		}
		elseif(isset($_POST['token']) && $_POST['token'] == $_SESSION['ptc_token'])
		{
			// Initialise captcha
			require('libs/captcha/session.class.php');
			require('libs/captcha/captcha.class.php');
			CBCaptcha::setIconsFolderPath('../../../static/img/captcha/');
			
			if(!CBCaptcha::validateSubmission($_POST)) 
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> Captcha error, please try again!</div>', 'status' => 600);
			} 
			else 
			{
				$sid = $db->EscapeString($_POST['data']);
				$sit = $db->QueryFetchArray("SELECT a.id,a.website,a.redirect,b.reward FROM ptc_websites a LEFT JOIN ptc_packs b ON b.id = a.ptc_pack LEFT JOIN ptc_done c ON c.user_id = '".$data['id']."' AND c.site_id = a.id WHERE a.id = '".$sid."' AND a.status = '1' AND (a.daily_limit > a.received_today OR a.daily_limit = '0') AND a.total_visits > a.received AND c.site_id IS NULL LIMIT 1");

				if(empty($sit['id']) || empty($data['id']))
				{
					$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> This page is no longer available.</div>', 'status' => 100);
				}
				else
				{
					$mod_ses = $db->QueryFetchArray("SELECT ses_key FROM `ptc_sessions` WHERE `user_id`='".$data['id']."' AND `site_id`='".$sit['id']."' LIMIT 1");

					if($mod_ses['ses_key'] != '' && $mod_ses['ses_key'] <= time())
					{
						$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$sit['reward']."', `today_revenue`=`today_revenue`+'".$sit['reward']."', `total_revenue`=`total_revenue`+'".$sit['reward']."' WHERE `id`='".$data['id']."'");
						$db->Query("UPDATE `ptc_websites` SET `received`=`received`+'1', `received_today`=`received_today`+'1' WHERE `id`='".$sit['id']."'");
						$db->Query("INSERT INTO `ptc_done` (`user_id`, `site_id`, `time`) VALUES('".$data['id']."', '".$sit['id']."', '".time()."')");

						$resultData = array('message' => '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle fa-fw"></i> <b>SUCCESS</b> You received '.number_format($sit['reward'], 2).' Bits!</div>', 'redirect' => ($sit['redirect'] == 1 ? $sit['website'] : 'false'), 'status' => 200);
					}
					else
					{
						$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> This page is no longer available.</div>', 'status' => 100);
					}
				}
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'proccessInvest')
	{
		if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$amount = $db->EscapeString($_POST['amount']);
			$type = ($_POST['type'] == '1' ? 1 : 0);

			if(!is_numeric($amount) || $amount < $config['invest_min'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.lang_rep($lang['l_404'], array('-SUM-' => $config['invest_min'])).'</div>', 'status' => 100);
			}
			elseif($amount > $config['invest_max'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.lang_rep($lang['l_405'], array('-SUM-' => $config['invest_max'])).'</div>', 'status' => 100);
			}
			elseif($amount > $data['account_balance'])
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_406'].'</div>', 'status' => 100);
			}
			else
			{
				$check = $db->QueryFetchArray("SELECT `id` FROM `bitcoin_investments` WHERE `user_id`='".$data['id']."' AND `status`='0' LIMIT 1");
				
				if(!empty($check['id']))
				{
					$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_407'].'</div>', 'status' => 100);
				}
				else
				{
					$currentPrice = $db->QueryFetchArray("SELECT `value` FROM `bitcoin_price` ORDER BY `time` DESC LIMIT 1");
					$db->Query("INSERT INTO `bitcoin_investments` (`user_id`,`old_value`,`type`,`amount`,`time`) VALUES ('".$data['id']."','".$currentPrice['value']."','".$type."','".$amount."','".time()."')");
					$db->Query("UPDATE `users` SET `account_balance`=`account_balance`-'".$amount."' WHERE `id`='".$data['id']."'");

					$resultData = array('message' => '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle fa-fw"></i> '.lang_rep($lang['l_408'], array('-SUM-' => number_format($amount, 2))).'</div>', 'status' => 200);
				}
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'finishInvest')
	{
		if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$sid = $db->EscapeString($_POST['sid']);

			if(empty($sid))
			{
				$resultData = array('message' => '<div class="alert alert-danger" role="alert"><b><i class="fa fa-exclamation-triangle fa-fw"></i> ERROR:</b> Unexpected error occured!</div>', 'status' => 100);
			}
			else
			{
				$investment = $db->QueryFetchArray("SELECT * FROM `bitcoin_investments` WHERE `id`='".$sid."' AND `user_id`='".$data['id']."' AND `status`='0' LIMIT 1");
				
				if(empty($investment['id']))
				{
					$resultData = array('message' => '<div class="alert alert-danger" role="alert"><b><i class="fa fa-exclamation-triangle fa-fw"></i> ERROR:</b> Unexpected error occured!</div>', 'status' => 100);
				}
				elseif($investment['time'] > (time()-300))
				{
					$resultData = array('message' => '<div class="alert alert-danger" role="alert"><b><i class="fa fa-exclamation-triangle fa-fw"></i> ERROR:</b> Unexpected error occured!</div>', 'status' => 100);
				}
				else
				{
					$currentPrice = $db->QueryFetchArray("SELECT `value` FROM `bitcoin_price` ORDER BY `time` DESC LIMIT 1");

					$status = 2;
					$prize = 0;
					if(($investment['type'] == 0 && $currentPrice['value'] > $investment['old_value']) || ($investment['type'] == 1 && $currentPrice['value'] < $investment['old_value']))
					{
						$status = 1;
						$prize = ($investment['amount']*$config['invest_win']);
					}
					elseif($currentPrice['value'] == $investment['old_value'])
					{
						$status = 3;
						$prize = $investment['amount'];
					}

					$db->Query("UPDATE `bitcoin_investments` SET `new_value`='".$currentPrice['value']."', `status`='".$status."' WHERE `id`='".$investment['id']."'");

					if($prize > 0)
					{
						$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$prize."' WHERE `id`='".$data['id']."'");
						
						$resultData = array('message' => '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle fa-fw"></i> Congratulations, you have won '.number_format($prize, 2).' Bits! Please wait...</div>', 'status' => 200);
					}
					else
					{
						$resultData = array('message' => '<div class="alert alert-warning" role="alert"><i class="fa fa-check-circle fa-fw"></i> Too bad, you have didn\'t predicted Bitcoin evolution and you lost your investment! Please wait...</div>', 'status' => 200);
					}
				}
			}
		}
		else
		{
			$resultData = array('message' => '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> '.$lang['l_304'].'</div>', 'status' => 400); 
		}

		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	
	if($data['admin'] == 1 && isset($_GET['checkIP']) && isset($_GET['user_id']))
	{
		$uIP = $db->EscapeString($_GET['checkIP']);
		$uID = $db->EscapeString($_GET['user_id']);
		$UserIPData = $db->QueryFetchArray("SELECT `id`,`status`,`time` FROM `ip_checks` WHERE `user_id`='".$uID."' AND `ip_address`='".$uIP."' LIMIT 1");
		if(empty($UserIPData) || $UserIPData['time'] < (time()-86400))
		{
			$IPData = detectProxy($uIP);
			
			if($IPData['status'] != 99)
			{
				$db->Query("INSERT INTO `ip_checks` (`user_id`,`ip_address`,`country_code`,`status`,`time`)VALUES('".$uID."','".$uIP."','".$IPData['country']."','".$IPData['status']."','".time()."') ON DUPLICATE KEY UPDATE `status`='".$IPData['status']."', `time`='".time()."'");
				$result = $IPData['status'];
			}
		}
		else
		{
			$result = $UserIPData['status'];
		}
		
		
		echo $result;
		exit;
	}
}
else
{
	if(isset($_POST['a']) && $_POST['a'] == 'login' && isset($_POST['username']) && isset($_POST['password']))
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']))
		{
			$resultData = array('msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_504'].'</div>', 'status' => 0); 
		}
		elseif(!isset($_POST['access_key']) || ($_POST['access_key'] !== $_SESSION['authentication_key']))
		{
			$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_304'].'</div>'); 
		}
		elseif(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			// validate recaptcha
			$captcha_valid = 1;
			if(!empty($config['recaptcha_sec'])){
				if(!isset($_POST['recaptcha'])) {
					$captcha_valid = 0;
				}
				else
				{
					$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_sec']);
					$recaptcha = $recaptcha->verify($_POST['recaptcha'], $_SERVER['REMOTE_ADDR']);
				
					if(!$recaptcha->isSuccess()){
						$captcha_valid = 0;
					}
				}
			}
			
			if(!$captcha_valid)
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_142'].'</div>'); 
			}
			else
			{
				$ip_address = ip2long(VisitorIP());
				$attempts = $db->QueryFetchArray("SELECT `count`,`time` FROM `wrong_logins` WHERE `ip_address`='".$ip_address."' LIMIT 1");

				if($attempts['count'] >= $config['login_attempts'] && $attempts['time'] > (time() - (60*$config['login_wait_time'])))
				{
					$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.lang_rep($lang['l_14'], array('-TIME-' => $config['login_wait_time'])).'</div>'); 
				}
				else
				{
					$login = $db->EscapeString($_POST['username']);
					$data = $db->QueryFetchArray("SELECT `id`,`disabled`,`activate`,`auth_key`,`auth_status` FROM `users` WHERE (`username`='".$login."' OR `email`='".$login."') AND `password`='".securePassword($_POST['password'])."' LIMIT 1");

					$ga_status = true;
					if($data['auth_status'] == 1 && !empty($data['auth_key']))
					{
						$ga = new GoogleAuthenticator();
						$checkResult = $ga->verifyCode($data['auth_key'], $_POST['pin'], 2);

						if(!$checkResult)
						{
							$ga_status = false;
						}
					}

					if(empty($data['id']))
					{
						$db->Query("INSERT INTO `wrong_logins` (`ip_address`,`count`,`time`) VALUES ('".$ip_address."','1','".time()."') ON DUPLICATE KEY UPDATE `count`=`count`+'1', `time`='".time()."'");
						$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_17'].'</div>'); 
					}
					elseif($data['disabled'] > 0)
					{
						$reason = $db->QueryFetchArray("SELECT `reason` FROM `ban_reasons` WHERE `user`='".$data['id']."' LIMIT 1");
						$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_15'].' '.$reason['reason'].'</div>'); 
					}
					elseif($data['activate'] != '0')
					{
						$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_16'].'</div>'); 
					}
					elseif($ga_status === false)
					{
						$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_513'].'</div>'); 
					}
					else
					{
						$db->Query("UPDATE `users` SET `log_ip`='".VisitorIP()."', `last_activity`='".time()."' WHERE `id`='".$data['id']."'");
						$db->Query("DELETE FROM `wrong_logins` WHERE `ip_address`='".$ip_address."'");
			
						// Store login info
						$browser = $db->EscapeString($_SERVER['HTTP_USER_AGENT']);
						$db->Query("INSERT INTO `user_logins` (`uid`,`ip`,`info`,`time`) VALUES ('".$data['id']."','".$ip_address."','".$browser."',NOW())");
						
						// Update Session Token
						$hash_key = GenerateKey(16);
						$db->Query("INSERT INTO `users_sessions` (`uid`,`hash`,`browser`,`ip_address`,`timestamp`) VALUES ('".$data['id']."','".$hash_key."','".$browser."','".$ip_address."','".time()."') ON DUPLICATE KEY UPDATE `hash`='".$hash_key."', `browser`='".$browser."', `ip_address`='".$ip_address."', `timestamp`='".time()."'");
						$_SESSION['SesHashKey'] = $hash_key;
						
						// Auto-login user
						if(isset($_POST['remember'])){
							setcookie('SesHashKey', $hash_key, time()+604800, '/');
							setcookie('SesToken', 'ses_id='.$data['id'].'&ses_key='.$hash_key, time()+604800, '/');
						}
						
						// Set Session
						$_SESSION['PT_User'] = $data['id'];
						
						// Multi-account prevent
						setcookie('AccExist', $data['id'], time()+604800, '/');
						
						$resultData = array('status' => 1, 'msg' => '<div class="alert alert-success" role="alert">'.$lang['l_303'].'</div>'); 
					}
				}
			}
		} else {
			$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_304'].'</div>'); 
		}
		
		header('Content-type: application/json');
		echo json_encode($resultData);
	}
	elseif(isset($_POST['a']) && $_POST['a'] == 'register')
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']))
		{
			$resultData = array('msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_504'].'</div>', 'status' => 0); 
		}
		elseif(!isset($_POST['access_key']) || ($_POST['access_key'] !== $_SESSION['registration_key']))
		{
			$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_304'].'</div>'); 
		}
		elseif(isset($_POST['token']) && $_POST['token'] === $_SESSION['token'])
		{
			$ip_address = VisitorIP();
			$username = $db->EscapeString($_POST['username']);
			$country = $db->EscapeString($_POST['country']);
			$gender = $db->EscapeString($_POST['gender']);
			$bitcoin = $db->EscapeString(empty($_POST['bitcoin']) ? null : $_POST['bitcoin']);
			$email = $db->EscapeString($_POST['email']);
			
			// validate recaptcha
			$captcha_valid = 1;
			if(!empty($config['recaptcha_sec']))
			{
				if(!isset($_POST['recaptcha']))
				{
					$captcha_valid = 0;
				}
				else
				{
					$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_sec']);
					$recaptcha = $recaptcha->verify($_POST['recaptcha'], $_SERVER['REMOTE_ADDR']);
				
					if(!$recaptcha->isSuccess()){
						$captcha_valid = 0;
					}
				}
			}
			
			if(!$captcha_valid)
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_142'].'</div>'); 
			}
			elseif(!$_POST['tos'])
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_285'].'</div>'); 
			}
			elseif(!isUserID($username))
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_300'].'</div>'); 
			}
			elseif(!isEmail($email))
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_144'].'</div>'); 
			}
			elseif(!validatePassword($_POST['password']))
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_134'].'</div>'); 
			}
			elseif($gender < 1 && $gender > 2) 
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_147'].'</div>'); 
			}
			elseif(!empty($bitcoin) && strlen($bitcoin) < 26 || !empty($bitcoin) && !ctype_alnum($bitcoin))
			{
				$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_143'].'</div>'); 
			}
			else
			{
				$countries = $db->QueryFetchArrayAll("SELECT `id` FROM `list_countries`");
				$ctrs = array();
				foreach($countries as $row) {
					$ctrs[] = $row['id'];
				}
				
				$faucetpay = null;
				$faucetpay_hash = null;
				if(!empty($bitcoin))
				{
					$fp_result = new FaucetPay($config['fp_api_key'], getCurrency());
					$fp_result = $fp_result->checkAddress($bitcoin);

					if ($fp_result['status'] == 200)
					{
						$faucetpay = $bitcoin;
						$faucetpay_hash = $fp_result['payout_user_hash'];
					}
				}
				
				if($db->QueryGetNumRows("SELECT `id` FROM `users` WHERE `username`='".$username."' OR `email`='".$email."' LIMIT 1") > 0)
				{
					$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_148'].'</div>');
				}
				elseif($config['more_per_ip'] != 1 && isset($_COOKIE['AccExist']) || $config['more_per_ip'] != 1 && $db->QueryGetNumRows("SELECT id FROM `users` WHERE `reg_ip`='".$ip_address."' OR `log_ip`='".$ip_address."' LIMIT 1") > 0)
				{
					$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_149'].'</div>');
				}
				elseif(!in_array($country, $ctrs))
				{
					$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_150'].'</div>');
				}
				elseif(!empty($bitcoin) && $db->QueryGetNumRows("SELECT `id` FROM `users` WHERE `btc_id`='".$bitcoin."' OR `fp_id`='".$bitcoin."' LIMIT 1") > 0)
				{
					$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_443'].'</div>');
				}
				elseif(!empty($faucetpay_hash) && $db->QueryGetNumRows("SELECT `id` FROM `users` WHERE `fp_hash`='".$faucetpay_hash."' LIMIT 1") > 0)
				{
					$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_442'].'</div>');
				}
				else
				{
					$IPData = detectProxy($ip_address);
					if($IPData['status'] != 99 && $IPData['status'] == 1)
					{
						$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_503'].'</div>');
					}
					else
					{
						$referal = (isset($_COOKIE['PT_REF_ID']) ? $db->EscapeString($_COOKIE['PT_REF_ID']) : 0);
						if($referal != 0 && $db->QueryGetNumRows("SELECT `id` FROM `users` WHERE `id`='".$referal."' LIMIT 1") == 0) {
							$referal = 0;
						}

						$ref_source = 0;
						if(isset($_COOKIE['RefSource'])){
							$ref_source = $db->EscapeString($_COOKIE['RefSource']);
						}

						$activate = 0;
						if($config['reg_reqmail'] == 1){
							$activate = GenerateKey(32);
							if($config['mail_delivery_method'] == 1){
								$mailer->isSMTP();
								$mailer->Host = $config['smtp_host'];
								$mailer->Port = $config['smtp_port'];

								if(!empty($config['smtp_auth'])){
									$mailer->SMTPSecure = $config['smtp_auth'];
								}
								$mailer->SMTPAuth = (empty($config['smtp_username']) || empty($config['smtp_password']) ? false : true);
								if($mailer->SMTPAuth){
									$mailer->Username = $config['smtp_username'];
									$mailer->Password = $config['smtp_password'];
								}
							}
							
							$mailer->AddAddress($email, $username);
							$mailer->SetFrom((!empty($config['noreply_email']) ? $config['noreply_email'] : $config['site_email']), $config['site_name']);
							$mailer->Subject = $config['site_logo'].' - Activate your account';
							$mailer->MsgHTML('<html>
												<body style="font-family: Verdana; color: #333333; font-size: 12px;">
													<table style="width: 400px; margin: 0px auto;">
														<tr style="text-align: center;">
															<td style="border-bottom: solid 1px #cccccc;"><h1 style="margin: 0; font-size: 20px;"><a href="'.$config['site_url'].'" style="text-decoration:none;color:#333333"><b>'.$config['site_name'].'</b></a></h1><h2 style="text-align: right; font-size: 14px; margin: 7px 0 10px 0;">Activate your account</h2></td>
														</tr>
														<tr style="text-align: justify;">
															<td style="padding-top: 15px; padding-bottom: 15px;">
																Hello '.$username.',
																<br /><br />
																Click on this link to activate your account:<br />
																<a href="'.$config['site_url'].'/?activate='.$activate.'">'.$config['site_url'].'/?activate='.$activate.'</a>
															</td>
														</tr>
														<tr style="text-align: right; color: #777777;">
															<td style="padding-top: 10px; border-top: solid 1px #cccccc;">
																Best Regards!
															</td>
														</tr>
													</table>
												</body>
											</html>');
							$mailer->Send();
						}

						$db->Query("INSERT INTO `users`(`email`,`username`,`country_id`,`fp_id`,`fp_hash`,`btc_id`,`gender`,`reg_ip`,`password`,`ref`,`reg_time`,`activate`,`ref_source`) VALUES ('".$email."','".$username."','".$country."','".$faucetpay."','".$faucetpay_hash."','".$bitcoin."','".$gender."','".$ip_address."','".securePassword($_POST['password'])."','".$referal."','".time()."','".$activate."','".$ref_source."')");
						$user_id = $db->GetLastInsertId();
						
						if(empty($user_id))
						{
							$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_146'].'</div>');
						}
						else
						{
							// Store IP details
							$db->Query("INSERT INTO `ip_checks` (`user_id`,`ip_address`,`country_code`,`status`,`time`)VALUES('".$user_id."','".$ip_address."','".$IPData['country']."','".$IPData['status']."','".time()."') ON DUPLICATE KEY UPDATE `status`='".$IPData['status']."', `time`='".time()."'");

							if($referal > 0)
							{
								add_notification($referal, 2, $user_id);
							}
							
							if(!isset($_COOKIE['AccExist'])){
								setcookie('AccExist', $user_id, time()+604800, '/');
							}
							
							if($config['reg_reqmail'] != 1 && $user_id > 0) {
								$browser = $db->EscapeString($_SERVER['HTTP_USER_AGENT']);
								$db->Query("INSERT INTO `user_logins` (`uid`,`ip`,`info`,`time`) VALUES ('".$user_id."','".ip2long($ip_address)."','".$browser."',NOW())");
								$db->Query("UPDATE `users` SET `log_ip`='".$ip_address."', `last_activity`='".time()."' WHERE `id`='".$user_id."'");
							
								// Update Session Token
								$hash_key = GenerateKey(16);
								$ip_address = ip2long($ip_address);
								$browser = $db->EscapeString($_SERVER['HTTP_USER_AGENT']);
								$db->Query("INSERT INTO `users_sessions` (`uid`,`hash`,`browser`,`ip_address`,`timestamp`) VALUES ('".$user_id."','".$hash_key."','".$browser."','".$ip_address."','".time()."') ON DUPLICATE KEY UPDATE `hash`='".$hash_key."', `browser`='".$browser."', `ip_address`='".$ip_address."', `timestamp`='".time()."'");

								// Save Sessions
								$_SESSION['SesHashKey'] = $hash_key;
								$_SESSION['PT_User'] = $user_id;
								
								$resultData = array('status' => 1, 'loggedin' => 1, 'msg' => '<div class="alert alert-success" role="alert">'.$lang['l_152'].'</div>'); 
							}
							else
							{
								$resultData = array('status' => 1, 'loggedin' => 0, 'msg' => '<div class="alert alert-success" role="alert">'.$lang['l_151'].'</div>'); 
							}
						}
					}
				}
			}
		} else {
			$resultData = array('status' => 0, 'msg' => '<div class="alert alert-danger" role="alert">'.$lang['l_304'].'</div>'); 
		}
		
		header('Content-type: application/json');
		echo json_encode($resultData);
	}
}
?>