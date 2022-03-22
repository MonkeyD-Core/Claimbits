<?php
	/* Error Reporting */
	error_reporting(0);
	ini_set('display_errors', 0);

	/* Starting session */
	session_start();

	/* Define Base Path */
	define('BASE_PATH', realpath(dirname(__FILE__).'/..'));

	/* Load required files */
	require(BASE_PATH.'/system/database.php');
	require(BASE_PATH.'/system/libs/functions.php');
	require(BASE_PATH.'/system/libs/database/'.$config['sql_extenstion'].'.php');
	include(BASE_PATH.'/system/libs/CoinPayments/CoinpaymentsAPI.php');
	include(BASE_PATH.'/system/libs/faucetpay.php');
	include(BASE_PATH.'/system/libs/kswallet.php');
	include(BASE_PATH.'/system/libs/recaptcha/autoload.php');
	include(BASE_PATH.'/system/libs/solvemedialib.php');
	include(BASE_PATH.'/system/libs/PHPMailer/load.php');
	include(BASE_PATH.'/system/libs/JSPacker.php');
	include(BASE_PATH.'/system/libs/GoogleAuthenticator.php');

	/* Database connection */
	$db = new MySQLConnection($config['sql_host'], $config['sql_username'], $config['sql_password'], $config['sql_database']);
	$db->Connect();

	/* Run update */
	if(!defined('IS_AJAX') && file_exists(BASE_PATH.'/system/run_update.php')){
		include(BASE_PATH.'/system/run_update.php');
	}

	/* Load website settings */
	$config = array();
	$configs = $db->QueryFetchArrayAll("SELECT config_name,config_value FROM `site_config`");
	foreach ($configs as $con)
	{
		$config[$con['config_name']] = $con['config_value'];
	}
	unset($configs); 

	/* Script Version */
	$config['version'] = '2.1.6';

	/* Website Theme */
	if(!defined('IS_AJAX')) {
		$config['theme'] = (!empty($config['theme']) && file_exists(BASE_PATH.'/template/'.$config['theme'].'/index.php') ? $config['theme'] : 'default');
		include(BASE_PATH.'/template/'.$config['theme'].'/index.php');
		if(defined('IS_ADMIN')){
			$set_def_theme = '';
			foreach(glob(BASE_PATH.'/template/*/index.php') as $tm){
				include($tm);
				
				$selected = (isset($_POST['set']['theme']) && $_POST['set']['theme'] == $theme['code'] ? ' selected' : (!isset($_POST['set']['theme']) && $config['theme'] == $theme['code'] ? ' selected' : '')); 
				$set_def_theme .= '<option value="'.$theme['code'].'"'.$selected.'>'.$theme['name'].'</option>';
			}
		}
	}
	
	/* User Session */
	$is_online = false;
	$ip_address = ip2long(VisitorIP());
	if(isset($_SESSION['PT_User'])){
		$ses_id = $db->EscapeString($_SESSION['PT_User']);
		$data = $db->QueryFetchArray("SELECT a.*, b.membership AS mem_name, b.multiplier, b.ref_com, b.offer_com, b.short_com, b.fp_min_pay, b.btc_min_pay, b.ks_min_pay, b.hide_ads, b.hash_rate, b.fp_wait_time, b.btc_wait_time, b.ks_wait_time, b.lottery_price, c.hash FROM users a LEFT JOIN memberships b ON b.id = a.membership_id LEFT JOIN users_sessions c ON c.uid = a.id WHERE a.id = '".$ses_id."' AND a.disabled = '0' LIMIT 1");

		$is_online = true;
		if(empty($data['id']))
		{
			session_destroy();
			$is_online = false;
		}
		elseif(empty($data['hash']))
		{
			// Update Session Token
			$hash_key = GenerateKey(16);
			$browser = $db->EscapeString($_SERVER['HTTP_USER_AGENT']);
			$db->Query("INSERT INTO `users_sessions` (`uid`,`hash`,`browser`,`ip_address`,`timestamp`) VALUES ('".$data['id']."','".$hash_key."','".$browser."','".$ip_address."','".time()."') ON DUPLICATE KEY UPDATE `hash`='".$hash_key."', `browser`='".$browser."', `ip_address`='".$ip_address."', `timestamp`='".time()."'");
			$_SESSION['SesHashKey'] = $hash_key;

			if(isset($_COOKIE['SesToken'])){
				setcookie('SesHashKey', $hash_key, time()+604800, '/');
				setcookie('SesToken', 'ses_id='.$data['id'].'&ses_key='.$hash_key, time()+604800, '/');
			}
		}
		elseif($data['last_activity']+90 < time() && !defined('IS_AJAX'))
		{
			$db->Query("UPDATE `users` SET `last_activity`='".time()."' WHERE `id`='".$data['id']."' LIMIT 1");
			$_SESSION['PT_User'] = $data['id'];
			
			if(isset($_SESSION['SesHashKey']) && $_SESSION['SesHashKey'] == $data['hash'])
			{
				$_SESSION['SesHashKey'] = $data['hash'];
			}
		}
	}
	elseif(isset($_COOKIE['SesToken']))
	{
		$ses_id = '';
		$ses_key = '';
		$sesCookie = $db->EscapeString($_COOKIE['SesToken'], 0);
		$sesCookie = explode('&', $sesCookie);
		foreach($sesCookie as $sesCookie_part){
			$find_ses_exp = explode('=', $sesCookie_part);
			if($find_ses_exp[0] == 'ses_id'){
				$ses_id = $db->EscapeString($find_ses_exp[1]);
			}elseif($find_ses_exp[0] == 'ses_key'){
				$ses_key = $db->EscapeString($find_ses_exp[1]);
			}
		}

		if(!empty($ses_id) && !empty($ses_key))
		{
			$data = $db->QueryFetchArray("SELECT a.*, b.membership AS mem_name, b.multiplier, b.ref_com, b.offer_com, b.short_com, b.fp_min_pay, b.btc_min_pay, b.ks_min_pay, b.hide_ads, b.hash_rate, b.fp_wait_time, b.btc_wait_time, b.ks_wait_time, b.lottery_price, c.hash FROM users a LEFT JOIN memberships b ON b.id = a.membership_id LEFT JOIN users_sessions c ON c.uid = a.id WHERE a.id = '".$ses_id."' AND c.hash = '".$ses_key."' AND a.disabled = '0' LIMIT 1");

			if(empty($data['id']))
			{
				unset($_COOKIE['SesToken']); 
			}
			else
			{
				$db->Query("UPDATE `users` SET `log_ip`='".VisitorIP()."', `last_activity`='".time()."' WHERE `id`='".$data['id']."'");
				$_SESSION['PT_User'] = $data['id'];
				$is_online = true;

				$check_activity = $db->QueryGetNumRows("SELECT `id` FROM `user_logins` WHERE `uid`='".$data['id']."' AND DATE(`time`) = DATE(NOW()) LIMIT 1");
				if($check_activity == 0){
					$ip_address = ip2long(VisitorIP());

					if(!empty($ip_address))
					{
						$browser = $db->EscapeString($_SERVER['HTTP_USER_AGENT']);
						$db->Query("INSERT INTO `user_logins` (`uid`,`ip`,`info`,`time`) VALUES ('".$data['id']."','".$ip_address."','".$browser."',NOW())");
					}
				}
			}
		}
		else
		{
			unset($_COOKIE['SesToken']); 
		}
	}

	/* Check user membership */
	if($is_online && !defined('IS_AJAX'))
	{
		if($data['membership'] > 0 && $data['membership'] < time())
		{
			$db->Query("UPDATE `users` SET `membership`='0', `membership_id`='1' WHERE `id`='".$data['id']."'");
		}
	}

	/* Language system */
	$lang_select = '';
	if(defined('IS_ADMIN'))
	{ 
		$set_def_lang = ''; 
	}

	$CONF['language'] = (!empty($config['def_lang']) && file_exists('languages/'.$config['def_lang'].'/index.php') ? $config['def_lang'] : 'en');
	if(!defined('IS_AJAX'))
	{
		foreach(glob(BASE_PATH.'/languages/*/index.php') as $langname)
		{
			$langarray[] = str_replace(array(BASE_PATH.'/languages/', '/index.php'), '', $langname);
			include($langname);
			
			if(defined('IS_ADMIN'))
			{
				$selected = (isset($_POST['set']['def_lang']) && $_POST['set']['def_lang'] == $c_lang['code'] ? ' selected' : (!isset($_POST['set']['def_lang']) && $config['def_lang'] == $c_lang['code'] ? ' selected' : '')); 
				if($c_lang['active'] != 0)
				{
					$set_def_lang .= '<option value="'.$c_lang['code'].'"'.$selected.'>'.$c_lang['lang'].'</option>';
				}
			}

			if(isset($_GET['lang']))
			{
				$selected = ($_GET['lang'] == $c_lang['code'] ? ' active' : '');
			}
			elseif(isset($_COOKIE['lang']))
			{
				$selected = ($_COOKIE['lang'] == $c_lang['code'] ? ' active' : '');
			}
			else
			{
				$selected = ($CONF['language'] == $c_lang['code'] ? ' active' : '');
			}

			if($c_lang['active'] != 0) {
				$lang_select .= '<a class="dropdown-item'.$selected.'" href="javascript:void(0)" onclick="langSelect(\''.$c_lang['code'].'\');">'.$c_lang['lang'].'</a>';
				
				if(!empty($selected)) {
					$config['lang_name'] = $c_lang['lang'];
				}
			}
		}
	}

	if(isset($_GET['lang']))
	{
		if(in_array($_GET['lang'], $langarray))
		{
			setcookie('lang', $_GET['lang'], time()+360000);
			$CONF['language'] = $_GET['lang'];
		}
	}
	elseif (isset($_COOKIE['lang']) && !empty($_COOKIE['lang']))
	{
		$CONF['language'] = $_COOKIE['lang'];
	}

	// Load main language
	if($CONF['language'] != 'en') {
		if(file_exists(BASE_PATH.'/languages/en/base/lang.php')){ 
			include(BASE_PATH.'/languages/en/base/lang.php'); 
		}
	}
	
	// Load selected language
	if(file_exists(BASE_PATH.'/languages/'.$CONF['language'].'/base/lang.php'))
	{ 
		include(BASE_PATH.'/languages/'.$CONF['language'].'/base/lang.php'); 
	}
	
	/* Set default charset */
	$conf['lang_charset'] = (!empty($c_lang[$CONF['language'].'_charset']) ? $c_lang[$CONF['language'].'_charset'] : 'UTF-8');
	header('Content-type: text/html; charset='.$conf['lang_charset']);
?>