<?php
	define('BASEPATH', true);
	require('system/init.php');
	if(!$is_online){
		redirect($config['secure_url']);
	}
	
	// Initialise captcha
	require('system/libs/captcha/session.class.php');
    require('system/libs/captcha/captcha.class.php');
    CBCaptcha::setIconsFolderPath('../../../static/img/captcha/');
	
	// Prevent multiple sessions
	$alert = '';
	$valid_session = true;
	if((isset($_COOKIE['SesHashKey']) && $_COOKIE['SesHashKey'] != $data['hash']) || (isset($_SESSION['SesHashKey']) && $_SESSION['SesHashKey'] != $data['hash']) || (!isset($_COOKIE['SesHashKey']) && !isset($_SESSION['SesHashKey']))){
		$iframe = 'static/ptc/errors/session.html';
		$alert = '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-circle fa-fw"></i> Session expired! <a href="'.$config['secure_url'].'/?logout">Click here</a> to disconnect and login again...</div>';
		$valid_session = false;
	}

	// Banner Ads
	$banner_code = '';
	if(rand(0,1) == 0)
	{
		$ad_banner = $db->QueryFetchArray("SELECT `id`,`banner_url` FROM `banners` WHERE `expiration`>'".time()."' AND `status`='1' ORDER BY rand() LIMIT 1");
		if(!empty($ad_banner['id']))
		{
			$db->Query("UPDATE `banners` SET `views`=`views`+'1' WHERE `id`='".$ad_banner['id']."'");
			$banner_code = '<a href="'.$config['secure_url'].'/?go_banner='.$ad_banner['id'].'" target="_blank"><img src="'.$ad_banner['banner_url'].'" style="max-width:468px" class="img-fluid" border="0" /></a>';
		}
		else
		{
			$ad_banner = $db->QueryFetchArray("SELECT `code` FROM `ad_codes` WHERE `status`='1' AND `size`='0' ORDER BY rand() LIMIT 1");
			if(!empty($ad_banner['code']))
			{
				$banner_code = html_entity_decode($ad_banner['code'], ENT_QUOTES);
			}
		}
	}
	else
	{
		$ad_banner = $db->QueryFetchArray("SELECT `code` FROM `ad_codes` WHERE `status`='1' AND `size`='0' ORDER BY rand() LIMIT 1");
		if(!empty($ad_banner['code']))
		{
			$banner_code = html_entity_decode($ad_banner['code'], ENT_QUOTES);
		}
	}

	// Load Website
	if($valid_session)
	{
		$sit['id'] = 0;
		$iframe = 'static/ptc/errors/nopage.html';
		if(isset($_GET['sid']) && is_numeric($_GET['sid']))
		{
			$sid = $db->EscapeString($_GET['sid']);
			if($db->QueryGetNumRows("SELECT * FROM `ptc_done` WHERE `site_id`='".$sid."' AND `user_id`='".$data['id']."' LIMIT 1") == 0)
			{
				$sit = $db->QueryFetchArray("SELECT a.id, a.website, a.title, b.reward, b.time FROM ptc_websites a LEFT JOIN ptc_packs b ON b.id = a.ptc_pack LEFT JOIN ptc_done c ON c.user_id = '".$data['id']."' AND c.site_id = a.id WHERE a.id = '".$sid."' AND a.status = '1' AND (a.daily_limit > a.received_today OR a.daily_limit = '0') AND a.received < a.total_visits LIMIT 1");
			}
		}
		else
		{
			$alert = '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-circle fa-fw"></i> Session expired! <a href="'.$config['secure_url'].'/?logout">Click here</a> to disconnect and login again...</div>';
		}

		if($sit['id'] > 0)
		{
			$alert = '<div class="alert alert-info" role="alert"><i class="fa fa-cog fa-spin fa-fw"></i> Please wait...</div>';
			$db->Query("INSERT INTO `ptc_sessions` (`user_id`,`site_id`,`ses_key`)VALUES('".$data['id']."','".$sit['id']."','".($sit['time']+time())."') ON DUPLICATE KEY UPDATE `ses_key`='".($sit['time']+time())."'");

			$iframe = $sit['website'];
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$config['site_logo'].(empty($sit['title']) ? '' : ' - '.$sit['title'])?></title>
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="shortcut icon" href="static/favicon.ico" type="image/x-icon" />
    <link href="static/css/icon-captcha.min.css" rel="stylesheet" type="text/css">
	<style>body,html{margin:0;padding:0;width:100%;height:100%;overflow:hidden;font-size:12px}#banner,#progress,#status,#extra{display:inline-block}.surfbar{background:#474346;color:#fff;font-family:Arial,Helvetica,sans-serif;margin:0;font-weight:700;height:80px;border-bottom:1px solid #212121}.logo{margin-top:13px;float:left;padding:0 30px 0 20px;font-size:32px;text-shadow:-2px 1px 1px #000,0 2px 1px #000,2px 0 1px #000,0 -2px 1px #000}#progress{padding-top:34px;width:20%}#status{padding-top:20px}#banner{float:right;margin-right:20px;margin-top:10px}#frame{border:0;height:100%;width:100%}</style>
</head>
<body>
	<div class="surfbar">
		<span class="logo"><img src="<?php echo $config['secure_url']; ?>/static/img/logo.png" alt="<?php echo $config['site_logo']; ?>" title="<?php echo $config['site_logo']; ?>" /></span>    
		<span id="status"><?=$alert?></span>
		<span id="progress"></span>
		<span id="banner"><?=(empty($banner_code) ? '' : $banner_code)?></span>
	</div>
	<iframe src="<?=$iframe?>" id="frame" frameborder="0"></iframe>
<?php if($valid_session && !empty($sit['id'])) { ?>
	<script src="https://cdn.jsdelivr.net/combine/npm/jquery@3.4.1,npm/popper.js@1.14.6,npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    <script>window.jQuery || document.write('<script src="static/js/bundle.js">\x3C/script>')</script>
	<script async src="static/js/captcha.min.js"></script>
	<script async src="static/ptc/main.js"></script>
	<script>
		var secs = <?=$sit['time']?>;
		var token = '<?=GenPTCToken()?>';
		var sid = '<?=(empty($sit['id']) ? 'no_page' : $sit['id'])?>';
		var waitMsg = "<?php echo $lang['l_145']; ?>";
		var captchaMsg = "<?php echo $lang['l_142']; ?>";
		var surf_file = 'surf.php';
		var window_focus = true;
		$(document).ready(function(){window.setTimeout(function(){showadbar()}, 2000);});
	</script>
	<div class="modal fade" id="validateVisit" tabindex="-1" role="dialog" aria-labelledby="validateVisit" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
		  <div class="modal-body">
			<center><div class="captcha-holder"></div></center>
		  </div>
		</div>
	  </div>
	</div>
<?php 
	}

	if(!empty($config['analytics_id'])) { 
?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $config['analytics_id']; ?>"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', '<?php echo $config['analytics_id']; ?>');
	</script>
<?php } ?>
</body>
</html>
<?php $db->Close(); ?>