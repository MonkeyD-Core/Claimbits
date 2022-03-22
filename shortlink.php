<?php
	define('BASEPATH', true);
	require('system/init.php');

	if($is_online && isset($_GET['short_key']) && isset($_SESSION['shortlink_key']) && $_GET['short_key'] == $_SESSION['shortlink_key'])
	{
		$short_key = $db->EscapeString($_GET['short_key']);
		$linkData = $db->QueryFetchArray("SELECT b.id AS short_id, b.daily_limit, b.reward FROM shortlinks a LEFT JOIN shortlinks_config b ON b.id = a.short_id WHERE a.hash = '".$short_key."' LIMIT 1");
		
		$validate = $db->QueryFetchArray("SELECT `count` FROM `shortlinks_done` WHERE `user_id`='".$data['id']."' AND `short_id`='".$linkData['short_id']."' LIMIT 1");
		if($validate['count'] >= $linkData['daily_limit'])
		{
			redirect(GenerateURL('shortlinks&x=limit', true));
		}
		else
		{
			$getSession = $db->QueryFetchArray("SELECT `time` FROM `shortlinks_session` WHERE `user_id`='".$data['id']."' AND `short_id`='".$linkData['short_id']."' LIMIT 1");
			if($getSession['time'] > (time() - 11))
			{
				$_SESSION['shortlink_time'] = $getSession['time'];
				redirect(GenerateURL('shortlinks&x=time', true));
			}
			else
			{
				$contest_reward = (rand(1,9) == 5 ? 2 : 1);
				$db->Query("INSERT INTO `shortlinks_done` (`short_id`,`user_id`,`count`,`time`) VALUES ('".$linkData['short_id']."','".$data['id']."','1','".time()."') ON DUPLICATE KEY UPDATE `count`=`count`+'1', `time`='".time()."'");
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$linkData['reward']."', `sl_earnings`=`sl_earnings`+'".$linkData['reward']."', `sl_today_earnings`=`sl_today_earnings`+'".$linkData['reward']."', `sl_total`=`sl_total`+'1', `sl_today`=`sl_today`+'1', `shortlinks_contest`=`shortlinks_contest`+'".$contest_reward."', `today_revenue`=`today_revenue`+'".$linkData['reward']."', `total_revenue`=`total_revenue`+'".$linkData['reward']."' WHERE `id`='".$data['id']."'");
				$db->Query("UPDATE `shortlinks_config` SET `today_views`=`today_views`+'1', `total_views`=`total_views`+'1' WHERE `id`='".$linkData['short_id']."'");
				$db->Query("DELETE FROM `shortlinks_session` WHERE `user_id`='".$data['id']."' AND `short_id`='".$linkData['short_id']."'");
				
				// Referral Commission
				if($data['ref'] > 0) {
					$ref_data = $db->QueryFetchArray("SELECT a.last_activity, b.short_com FROM users a LEFT JOIN memberships b ON b.id = a.membership_id WHERE a.id = '".$data['ref']."' LIMIT 1");
					
					if(!empty($ref_data['last_activity']) && $ref_data['last_activity'] > (time() - ($config['ref_activity']*3600))) {
						$commission = (($ref_data['short_com']/100)*$linkData['reward']);
						ref_commission($data['ref'], $data['id'], $commission);
					}
				}

				// Destroy Session
				$_SESSION['shortlink_key'] = '';
				unset($_SESSION['shortlink_key']);

				// Redirect back to website
				$_SESSION['shortlink_reward'] = $linkData['reward'];
				redirect(GenerateURL('shortlinks&x=success', true));
			}
		}
	}
	else
	{
		redirect(GenerateURL('shortlinks', true));
	}
?>