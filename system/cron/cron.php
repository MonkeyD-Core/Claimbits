<?php
define('BASEPATH', true);
require(realpath(dirname(__FILE__)).'/../init.php');

/* Define functions */
function write_cron($timestamp, $cron_name, $var_name){
	$filename = realpath(dirname(__FILE__)).'/times/'.$cron_name.'.php';
	$content = file_put_contents($filename, '<? $'.$var_name.'[\'time\'] = \''.$timestamp.'\'; ?>');

	$return = true;
	if(!$content){
		die('<center><b>System ERROR</b><br /><i>system/cron/times/'.$cron_name.'.php</i> must be writable (change permissions to 777)</center>');
		$return = false;
	}
	return $return;
}

/* Timestamps */
$timestamp = time();
$daily_time = strtotime(date('j F Y'));
$nextMonth = strtotime('first day of next month'); 
$nextMonth = date('Y-m-d', $nextMonth);
$nextMonth = strtotime($nextMonth);
$nextWeek = strtotime("next Sunday")-60;

/* Times */
$tc_duration = ($config['tc_duration'] == 0 ? $nextWeek : $nextMonth);
$sl_duration = ($config['sl_duration'] == 0 ? $nextWeek : $nextMonth);
$ref_duration = ($config['contest_duration'] == 0 ? $nextWeek : $nextMonth);
$lottery_duration = ($config['lottery_duration'] == 0 ? $nextWeek : $nextMonth);

/* ---------------Starting Crons------------------ */
$realPath = realpath(dirname(__FILE__));
if(!is_writable($realPath.'/times')){
	die('<center><b>System ERROR</b><br /><i>system/cron/times/</i> directory must be writable (change permissions to 777)</center>');
}

/* Cron 1 minute */
$db->Query("UPDATE `shortlinks_done` SET `count`='0' WHERE `time`<'".(time()-86400)."'");
$db->Query("DELETE FROM `shortlinks` WHERE `time`<'".(time()-900)."'");

// Get BTC Value
$checkPrice = $db->QueryFetchArray("SELECT `id`,`value` FROM `bitcoin_price` WHERE `time`>'".(time()-55)."' LIMIT 1");
if(empty($checkPrice['id']))
{
	$coindesk = get_data('https://api.coindesk.com/v1/bpi/currentprice/USD.json');
	$coindesk = json_decode($coindesk, true);
	$currentPrice = $coindesk['bpi']['USD']['rate_float'];
	
	if(!empty($coindesk['bpi']['USD']['rate_float'])) 
	{
		$db->Query("INSERT INTO `bitcoin_price` (`value`,`minute`,`time`) VALUES ('".$currentPrice."','".date('H:i')."','".time()."')");
	}
}
else
{
	$currentPrice = $checkPrice['value'];
}

// Investment Game
$investments = $db->QueryFetchArrayAll("SELECT `id`,`old_value`,`amount`,`user_id`,`type` FROM `bitcoin_investments` WHERE `time`<'".(time()-300)."' AND `status`='0'");
foreach($investments as $investment)
{	
	$status = 2;
	$prize = 0;
	if(($investment['type'] == 0 && $currentPrice > $investment['old_value']) || ($investment['type'] == 1 && $currentPrice < $investment['old_value']))
	{
		$status = 1;
		$prize = ($investment['amount']*$config['invest_win']);
	}
	elseif($currentPrice == $investment['old_value'])
	{
		$status = 3;
		$prize = $investment['amount'];
	}
	
	if($prize > 0)
	{
		$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$prize."' WHERE `id`='".$investment['user_id']."'");
	}

	$db->Query("UPDATE `bitcoin_investments` SET `new_value`='".$currentPrice."', `status`='".$status."' WHERE `id`='".$investment['id']."'");
}

/* Cron 5 minutes */
if(file_exists($realPath.'/times/5min_cron.php')){
	include($realPath.'/times/5min_cron.php');
}

if($cron_5min['time'] < ($timestamp-300)){
	$write = write_cron($timestamp, '5min_cron', 'cron_5min');
	if($write){
		$db->Query("UPDATE `users` SET `membership`='0', `membership_id`='1' WHERE (`membership`>'0' AND `membership`<'".time()."') OR (`membership`='0' AND `membership_id`!='1')");
		$db->Query("DELETE FROM `wrong_logins` WHERE `time`<'".(time()-$config['login_wait_time'])."'");
		$db->Query("DELETE FROM `ptc_sessions` WHERE `ses_key`<'".(time()-60)."'");
		$db->Query("DELETE FROM `deposits` WHERE `time`<'".(time()-10800)."' AND `status`='0'");
		$db->Query("DELETE FROM `bitcoin_price` WHERE `time`<'".(time()-86400)."'");
		$db->Query("DELETE FROM `shortlinks_session` WHERE `time`<'".(time()-120)."'");
	}
}

/* Cron 10 minutes */
if(file_exists($realPath.'/times/15min_cron.php')){
	include($realPath.'/times/15min_cron.php');
}

if($cron_15min['time'] < ($timestamp-600)){
	require(BASE_PATH.'/system/libs/webminepool.php');

	$wmp = new WMP($config['wmp_secret']);
	$users = $wmp->users();
	if($users->success)
	{
		foreach($users->users as $user)
		{
			if(is_numeric($user->name) && $user->hashes > 0)
			{
				$paid = $wmp->withdraw($user->name, $user->hashes);

				if($paid->success) {
					$user_data = $db->QueryFetchArray("SELECT a.id, b.hash_rate FROM users a LEFT JOIN memberships b ON b.id = a.membership_id WHERE a.id = '".$db->EscapeString($user->name)."' LIMIT 1");

					if(!empty($user_data['id'])) {
						$db->Query("UPDATE `users` SET `pending_ch`=`pending_ch`+'".$user->hashes."', `today_ch`=`today_ch`+'".$user->hashes."', `total_ch`=`total_ch`+'".$user->hashes."' WHERE `id`='".$user_data['id']."'");
					}
				}
			}
		}
	}

	write_cron($timestamp, '15min_cron', 'cron_15min');
}

/* Daily Cron */
if(file_exists($realPath.'/times/daily_cron.php')){
	include($realPath.'/times/daily_cron.php');
}

if($cron_day['time'] < $daily_time){
	$write = write_cron($daily_time, 'daily_cron', 'cron_day');
	if($write && $cron_day['time'] > 0){
		// Faucet History
		$faucet_history = $db->QueryFetchArray("SELECT COUNT(`id`) AS `users`, SUM(`today_claims`) AS `claims`, SUM(`sl_today`) AS `links`, SUM(`today_revenue`) AS `revenue` FROM `users` WHERE `last_claim`>='".$cron_day['time']."'");
		$db->Query("INSERT INTO `faucet_history`(`total_claims`,`total_link`,`total_revenue`,`total_users`,`date`)VALUES('".$faucet_history['claims']."','".$faucet_history['links']."','".$faucet_history['revenue']."','".$faucet_history['users']."','".date('Y-m-d', $cron_day['time'])."')");

		// Delete / Update temporary stats
		$db->Query("UPDATE `users` SET `today_claims`='0', `today_revenue`='0', `today_ch`='0', `sl_today_earnings`='0', `sl_today`='0'");
		$db->Query("UPDATE `ptc_websites` SET `received_today`='0' WHERE `received_today`>'0'");
		$db->Query("UPDATE `shortlinks_config` SET `today_views`='0' WHERE `today_views`>'0'");
		$db->Query("DELETE FROM `ptc_done`");
		$db->Query("DELETE FROM `user_logins` WHERE UNIX_TIMESTAMP(`time`) < '".(time()-(86400*90))."'");
		
		// Update Shortlinks
		if($config['shortlink_reset'] != 1)
		{
			$db->Query("UPDATE `shortlinks_done` SET `count`='0'");
		}

		// Delete Inactive Users
		if($config['cron_users'] > 0) {
			$del_time = (time() - (86400*$config['cron_users']));
			$db->Query("DELETE FROM `users` WHERE `last_activity`< '".$del_time."'");
		}
		
		// Inactivity penalty system
		if($config['penalty_time'] > 0 && $config['penalty_amount'] > 0) {
			$del_time = (time() - (86400*$config['penalty_time']));
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`-'".$config['penalty_amount']."' WHERE `last_activity`< '".$del_time."' AND `account_balance`>'".$config['penalty_amount']."'");
		}
	}
}

/* Referrals Contest */
if(file_exists($realPath.'/times/ref_cron.php')){
	include($realPath.'/times/ref_cron.php');
}

if($cron_ref['time'] < $ref_duration){
	$write = write_cron($ref_duration, 'ref_cron', 'cron_ref');
	if($write && $cron_ref['time'] > 0){
		$prizes = explode(',', $config['contest_prizes']);
		$totPrizes = count($prizes);
		$currentRound = $db->QueryFetchArray("SELECT `id`,`start_date` FROM `referral_contest` WHERE `end_date`='0' ORDER BY `id` DESC LIMIT 1");
		$winners = $db->QueryFetchArrayAll("SELECT a.ref, b.id, COUNT(a.id) AS total FROM users a INNER JOIN users b ON b.id = a.ref WHERE a.ref != '0' AND a.reg_time >= '".$currentRound['start_date']."' AND a.total_claims >= '".$config['contest_claims']."' AND a.disabled = '0' GROUP BY a.ref ORDER BY total DESC LIMIT ".$totPrizes);

		$i = 0;
		$logPrizes = array();
		$logWinners = array();
		$logReferrals = array();
		foreach($winners as $winner) {
			if($winner['total'] >= $config['contest_referrals']) {
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$prizes[$i]."', `total_revenue`=`total_revenue`+'".$prizes[$i]."' WHERE `id`='".$winner['id']."'");
				$logPrizes[] = $prizes[$i];
				$logWinners[] = $winner['id'];
				$logReferrals[] = $winner['total'];
				
				add_notification($winner['id'], 3, $prizes[$i]);
			}

			$i++;
		}
		
		$logPrizes = implode(',',$logPrizes);
		$logWinners = implode(',',$logWinners);
		$logReferrals = implode(',',$logReferrals);

		$db->Query("INSERT IGNORE INTO `referral_contest` (`start_date`) VALUES ('".$cron_ref['time']."')");
		$db->Query("UPDATE `referral_contest` SET `end_date`='".$cron_ref['time']."', `winners`='".$logWinners."', `total_referrals`='".$logReferrals."', `prizes`='".$logPrizes."' WHERE `id`='".$currentRound['id']."'");
	}
}

/* Tasks Contest */
if(file_exists($realPath.'/times/tc_cron.php')){
	include($realPath.'/times/tc_cron.php');
}

if($cron_tc['time'] < $tc_duration){
	$write = write_cron($tc_duration, 'tc_cron', 'cron_tc');
	if($write && $cron_tc['time'] > 0){
		$prizes = explode(',', $config['tc_prizes']);
		$totPrizes = count($prizes);
		$currentRound = $db->QueryFetchArray("SELECT `id` FROM `tasks_contest` WHERE `end_date`='0' ORDER BY `id` DESC LIMIT 1");
		$winners = $db->QueryFetchArrayAll("SELECT `id`,`tasks_contest` FROM `users` ORDER BY `tasks_contest` DESC LIMIT ".$totPrizes);
		
		$i = 0;
		$logPrizes = array();
		$logWinners = array();
		$logPoints = array();
		foreach($winners as $winner) {
			if($winner['tasks_contest'] >= $config['tc_points']) {
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$prizes[$i]."', `total_revenue`=`total_revenue`+'".$prizes[$i]."' WHERE `id`='".$winner['id']."'");
				$logPrizes[] = $prizes[$i];
				$logWinners[] = $winner['id'];
				$logPoints[] = $winner['tasks_contest'];
				
				add_notification($winner['id'], 4, $prizes[$i]);
			}

			$i++;
		}
		
		$logPrizes = implode(',',$logPrizes);
		$logWinners = implode(',',$logWinners);
		$logPoints = implode(',',$logPoints);
		
		$db->Query("INSERT IGNORE INTO `tasks_contest` (`start_date`) VALUES ('".$cron_tc['time']."')");
		$db->Query("UPDATE `users` SET `tasks_contest`='0' WHERE `tasks_contest`>'0'");
		$db->Query("UPDATE `tasks_contest` SET `end_date`='".$cron_tc['time']."', `winners`='".$logWinners."', `points`='".$logPoints."', `prizes`='".$logPrizes."' WHERE `id`='".$currentRound['id']."'");
	}
}

/* Shortlinks Contest */
if(file_exists($realPath.'/times/sl_cron.php')){
	include($realPath.'/times/sl_cron.php');
}

if($cron_sl['time'] < $sl_duration){
	$write = write_cron($sl_duration, 'sl_cron', 'cron_sl');
	if($write && $cron_sl['time'] > 0){
		$prizes = explode(',', $config['sl_prizes']);
		$totPrizes = count($prizes);
		$currentRound = $db->QueryFetchArray("SELECT `id` FROM `shortlinks_contest` WHERE `end_date`='0' ORDER BY `id` DESC LIMIT 1");
		$winners = $db->QueryFetchArrayAll("SELECT `id`,`shortlinks_contest` FROM `users` ORDER BY `shortlinks_contest` DESC LIMIT ".$totPrizes);
		
		$i = 0;
		$logPrizes = array();
		$logWinners = array();
		$logPoints = array();
		foreach($winners as $winner) {
			if($winner['shortlinks_contest'] >= $config['sl_points']) {
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$prizes[$i]."', `total_revenue`=`total_revenue`+'".$prizes[$i]."' WHERE `id`='".$winner['id']."'");
				$logPrizes[] = $prizes[$i];
				$logWinners[] = $winner['id'];
				$logPoints[] = $winner['shortlinks_contest'];
				
				add_notification($winner['id'], 6, $prizes[$i]);
			}

			$i++;
		}
		
		$logPrizes = implode(',',$logPrizes);
		$logWinners = implode(',',$logWinners);
		$logPoints = implode(',',$logPoints);
		
		$db->Query("INSERT IGNORE INTO `shortlinks_contest` (`start_date`) VALUES ('".$cron_sl['time']."')");
		$db->Query("UPDATE `users` SET `shortlinks_contest`='0' WHERE `shortlinks_contest`>'0'");
		$db->Query("UPDATE `shortlinks_contest` SET `end_date`='".$cron_sl['time']."', `winners`='".$logWinners."', `points`='".$logPoints."', `prizes`='".$logPrizes."' WHERE `id`='".$currentRound['id']."'");
	}
}

/* Lottery */
if(file_exists($realPath.'/times/lottery_cron.php')){
	include($realPath.'/times/lottery_cron.php');
}

if($cron_lottery['time'] < $lottery_duration){
	$write = write_cron($lottery_duration, 'lottery_cron', 'cron_lottery');
	if($write && $cron_lottery['time'] > 0){
		$lottery = $db->QueryFetchArray("SELECT * FROM `lottery` WHERE `closed`='0' ORDER BY id DESC LIMIT 1");

		if(!empty($lottery['id'])) {
			$winner = $db->QueryFetchArray("SELECT * FROM `lottery_tickets` WHERE `lottery_id`='".$lottery['id']."' ORDER BY rand() LIMIT 1");
			$winner_tickets = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `lottery_tickets` WHERE `lottery_id`='".$lottery['id']."' AND `user_id`='".$winner['user_id']."'");
			
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$lottery['prize']."', `total_revenue`=`total_revenue`+'".$lottery['prize']."' WHERE `id`='".$winner['user_id']."'");
			$db->Query("UPDATE `lottery` SET `end_date`='".$cron_lottery['time']."', `winner_id`='".$winner['user_id']."', `winner_tickets`='".$winner_tickets['total']."', `winning_ticket`='".$winner['id']."', `closed`='1' WHERE `id`='".$lottery['id']."'");
			$db->Query("DELETE FROM `lottery_tickets` WHERE `lottery_id`='".$lottery['id']."'");

			add_notification($winner['user_id'], 8, $lottery['prize']);
		}

		if ($db->QueryGetNumRows("SELECT * FROM `lottery` WHERE `closed`='0' LIMIT 1") == 0) {
			$db->Query("INSERT INTO lottery (`prize`,`tickets_purchased`,`date`) VALUES ('".$config['lottery_default']."', '0', '".time()."')");
		}
	}
}
?>