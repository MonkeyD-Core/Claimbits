<?php
	define('BASEPATH', true);

	// Load System Files
	require('system/init.php');

	// Redirect to Secure Page (HTTPS)
	if($config['force_secure'] == 1 && !isset($_SERVER['HTTPS']) || $config['force_secure'] == 1 && $_SERVER['HTTPS'] != 'on')
	{
		header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit;
	}

	// Logout system
	if(isset($_GET['logout']))
	{
		if(isset($_COOKIE['SesToken'])){
			unset($_COOKIE['SesToken']); 
			setcookie('SesToken', '', time(), '/');
		}

		// Delete user Session
		$db->Query("DELETE FROM `users_sessions` WHERE `uid`='".$data['id']."'");
		if(isset($_COOKIE['SesHashKey'])){
			unset($_COOKIE['SesHashKey']); 
			setcookie('SesHashKey', '', time(), '/');
		}

		// Destroy Sessions
		session_destroy();

		redirect($config['secure_url']);
	}

	// Referral System
	if(!$is_online && isset($_GET['ref']) && is_numeric($_GET['ref']))
	{
		setcookie('PT_REF_ID', $db->EscapeString($_GET['ref']), time()+7200);
	}
	
	// Detect visitor referrer
	if(!$is_online && isset($_SERVER['HTTP_REFERER']) && !isset($_COOKIE['RefSource'])){
		$main_domain = parse_url($config['site_url']);
		$http_referer = parse_url($_SERVER['HTTP_REFERER']);
		if(isset($http_referer['host']) && $http_referer['host'] != $main_domain['host']){
			setcookie('RefSource', $db->EscapeString($_SERVER['HTTP_REFERER']), time()+1800);
		}
	}
	
	// Banner System
	if(isset($_GET['go_banner']) && is_numeric($_GET['go_banner'])) {
		$banner_id = $db->EscapeString($_GET['go_banner']);
		$banner = $db->QueryFetchArray("SELECT site_url FROM `banners` WHERE `id`='".$banner_id."' LIMIT 1");
		
		if(!empty($banner['site_url'])) {
			$db->Query("UPDATE `banners` SET `clicks`=`clicks`+'1' WHERE `id`='".$banner_id."'");
			redirect($banner['site_url']);
		}
	}

	// Check User IP Address
	if($is_online)
	{
		$uIP = VisitorIP();
		$UserIPData = $db->QueryFetchArray("SELECT `id`,`status`,`time` FROM `ip_checks` WHERE `user_id`='".$data['id']."' AND `ip_address`='".$uIP."' LIMIT 1");
		if(empty($UserIPData) || $UserIPData['time'] < (time()-86400))
		{
			$IPData = detectProxy($uIP);
			
			if($IPData['status'] != 99)
			{
				$db->Query("INSERT INTO `ip_checks` (`user_id`,`ip_address`,`country_code`,`status`,`time`)VALUES('".$data['id']."','".$uIP."','".$IPData['country']."','".$IPData['status']."','".time()."') ON DUPLICATE KEY UPDATE `status`='".$IPData['status']."', `time`='".time()."'");
			}
		}
	}

	// Remove Footer Branding
	if(file_exists(BASE_PATH.'/system/copyright.php')) {
		include(BASE_PATH.'/system/copyright.php');
	}

	/*
		Load Website
	*/

	// Starting compression
	ob_start();

	// Content Settings
	$pages = array(
			// script name => (1 = valid, 0 = disabled), (0 = offline, 1 = online, 2 = doesn't matter), File Location, Page name
			'contact' => array(1, 2, 'pages/contact', 'Contact Us'),
			'faq' => array(1, 2, 'pages/faq', 'FAQ'),
			'tos' => array(1, 2, 'pages/tos', 'Terms & Conditions'),
			'privacy' => array(1, 2, 'pages/privacy', 'Privacy Policy'),
			'locked' => array(1, 1, 'pages/locked', 'AdBlock Detected'),
			'ptc' => array(1, 1, 'pages/ptc', 'PTC Ads'),
			'mining' => array(1, 1, 'pages/mining', 'CPU Mining'),
			'banners' => array(1, 2, 'pages/banners', 'Banner Ads'),
			'recover' => array(1, 0, 'pages/recover', 'Recover Password'),
			'tasks' => array(1, 1, 'pages/tasks', 'Jobs'),
			'invest' => array(1, 1, 'pages/invest', 'Investment Game'),
			'levels' => array(1, 1, 'pages/levels', 'Levels'),
			'coupons' => array(1, 1, 'pages/coupons', 'Coupons'),
			'notifications' => array(1, 1, 'pages/notifications', 'Notifications'),
			'offers' => array(1, 1, 'pages/offers', 'Complete Offers'),
			'affiliates' => array(1, 1, 'pages/affiliates', 'Affiliates'),
			'referrals' => array(1, 1, 'pages/referrals', 'Referrals'),
			'account' => array(1, 1, 'pages/account', 'Edit Account'),
			'deposits' => array(1, 1, 'pages/deposits', 'Deposits History'),
			'membership' => array(1, 1, 'pages/membership', 'Membership'),
			'payments' => array(1, 2, 'pages/payments', 'Sent Payments'),
			'advertise' => array(1, 1, 'pages/advertise', 'Advertise'),
			'market' => array(1, 1, 'pages/market', 'Purchase Referrals'),
			'blog' => array(1, 2, 'pages/blog', 'Blog'),
			'lottery' => array(1, 1, 'pages/lottery', 'Lottery'),
			'rewards' => array(1, 1, 'pages/rewards', 'Achievements'),
			'contest' => array(1, 1, 'pages/contest', (isset($_GET['x']) && $_GET['x'] == 'tasks' ? $lang['l_319'] : (isset($_GET['x']) && $_GET['x'] == 'shortlinks' ? $lang['l_424'] : $lang['l_278']))),
			'shortlinks' => array(1, 1, 'pages/shortlinks', 'Shortlinks'),
			'withdraw' => array(1, 1, 'pages/withdraw', 'Withdrawal History')
		);
		
	$valid = false;
	if (isset($_GET['page']) && $pages[$_GET['page']][0] == 1) {
		if($is_online && $pages[$_GET['page']][1] == 1){
			$valid = true;
		}elseif(!$is_online && $pages[$_GET['page']][1] == 0){
			$valid = true;
		}elseif($pages[$_GET['page']][1] == 2){
			$valid = true;
		}
	}

	$page = ($is_online ? 'pages/dashboard' : 'pages/home');
	$page_title = '';
	if($valid)
	{
		if(file_exists(BASE_PATH.'/template/'.$config['theme'].'/'.$pages[$_GET['page']][2].'.php'))
		{
			$page = $pages[$_GET['page']][2];
			$page_title = $pages[$_GET['page']][3];
		}
	}
	
	// Generate Security Token
	$token = GenGlobalToken();

	// Load Header
	require(BASE_PATH.'/template/'.$config['theme'].'/common/header.php');
	
	// Load Page
	require(BASE_PATH.'/template/'.$config['theme'].'/'.$page.'.php');
	
	// Load Footer
	require(BASE_PATH.'/template/'.$config['theme'].'/common/footer.php');
	
	// Show website
	ob_end_flush();
?>