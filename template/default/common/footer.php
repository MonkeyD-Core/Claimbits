<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	// Load Footer Banner
	$internal_banner = true;
	if(rand(1,2) == 1)
	{
		$internal_banner = false;
	}
	
	$banner_code = '';
	$ad_banner = $db->QueryFetchArray("SELECT `code` FROM `ad_codes` WHERE `status`='1' AND (`size`='0' OR `size`='1') ORDER BY rand() LIMIT 1");
	if(!empty($ad_banner['code']))
	{
		$banner_code = '<div class="container"><div class="mx-auto"><div class="my-3 p-3 bg-white rounded box-shadow box-style text-center d-flex justify-content-center">'.html_entity_decode($ad_banner['code'], ENT_QUOTES).'</div></div></div>';
	}
	else
	{
		$internal_banner = true;
	}
	
	if($internal_banner)
	{
		$ad_banner = $db->QueryFetchArray("SELECT `id`,`banner_url` FROM `banners` WHERE `expiration`>'".time()."' AND `status`='1' ORDER BY rand() LIMIT 1");
		if(!empty($ad_banner['id']))
		{
			$db->Query("UPDATE `banners` SET `views`=`views`+'1' WHERE `id`='".$ad_banner['id']."'");
			
			$banner_code = '<div class="container"><div class="mx-auto"><div class="my-3 p-3 bg-white rounded box-shadow box-style text-center d-flex justify-content-center"><a href="'.$config['secure_url'].'/?go_banner='.$ad_banner['id'].'" target="_blank"><img src="'.$config['secure_url'].$ad_banner['banner_url'].'" class="img-fluid" alt="Banner #'.$ad_banner['id'].'" border="0" /></a></div></div></div>';
		}
	}
	
	echo $banner_code;
?>
	<script type="text/javascript">
		var url = window.location.href;
		function langSelect(selectobj){
			if(url.indexOf("?") > 0) {
				url += '&lang='+selectobj;
			} else {
				url += '?lang='+selectobj;
			}
			window.location.replace(url)
		} 
	</script>
	<div class="clearfix"></div>
	<footer class="mt-3">
		<nav class="navbar static-bottom navbar-expand-sm navbar-dark">
		 <div class="container">
		  <span class="navbar-brand copyright"><?=eval(base64_decode('ZWNobyAoZW1wdHkoJENPTkZbJ2RwYiddKSB8fCAkQ09ORlsnZHBiJ10gIT0gMSA/ICcmY29weTsgJy5kYXRlKCdZJykuJyA8YSBocmVmPSJodHRwczovL3QubWUvZm91cnNlZGV2IiBjbGFzcz0iZm9vdGVyX2NvcHlyaWdodCIgdGFyZ2V0PSJfYmxhbmsiPk1hZGUgV2l0aCBMb3ZlIGJ5IDRTRSBERVY8L2E+JyA6ICcmY29weTsgJy5kYXRlKCdZJykuJyAnLiRjb25maWdbJ3NpdGVfbG9nbyddKTs='))?></span>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#footer_collapse" aria-controls="footer_collapse" aria-expanded="false">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="footer_collapse">
			<ul class="navbar-nav ml-auto">
			  <li class="nav-item dropup">
				<a class="nav-link dropdown-toggle" href="javascript:void(0)" id="footer_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i> <?=$lang['l_171']?></a>
				<div class="dropdown-menu" aria-labelledby="footer_menu">
					<a class="dropdown-item" href="<?=GenerateURL('tos')?>"><i class="fa fa-check-square fa-fw"></i> <?php echo $lang['l_28']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('privacy')?>"><i class="fa fa-exclamation-circle fa-fw"></i> <?php echo $lang['l_289']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('payments')?>"><i class="<?php echo getCurrency('icon_class'); ?> fa-fw"></i> <?php echo $lang['l_439']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('faq')?>"><i class="fa fa-question fa-fw"></i> <?php echo $lang['l_29']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('contact')?>"><i class="fa fa-envelope fa-fw"></i> <?php echo $lang['l_30']; ?></a>
				</div>
			  </li>
			  <li class="nav-item dropup">
				<a class="nav-link dropdown-toggle" href="javascript:void(0)" id="language_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-language"></i> <?php echo $config['lang_name']; ?></a>
				<div class="dropdown-menu" aria-labelledby="language_menu">
					<?=$lang_select?>
				</div>
			  </li>
			</ul>
		  </div>
		 </div>
		</nav>
	</footer>
<?php
	// Chatbro.com integration
	if($is_online && !empty($config['chatbro_id']) && $config['chatbro_status'] == 1) 
	{
		$domain = parse_url($config['site_url']);
		$permissions = ($data['admin'] == 1 ? 'deleteban' : '');
		$charusername = ($data['admin'] == 1 ? $data['username'].' <ad></ad>' : $data['username'].' <l class="'. userLevel($data['id'], 1 , $data['total_claims']).'"></l>');
		$chatSignature = md5($domain['host'].$data['id'].$charusername.$permissions.$config['chatbro_key']);
?>
<script id="chatBroEmbedCode">
	function ChatbroLoader(chats,async){async=!1!==async;var params={embedChatsParameters:chats instanceof Array?chats:[chats],lang:navigator.language||navigator.userLanguage,needLoadCode:'undefined'==typeof Chatbro,embedParamsVersion:localStorage.embedParamsVersion,chatbroScriptVersion:localStorage.chatbroScriptVersion},xhr=new XMLHttpRequest;xhr.withCredentials=!0,xhr.onload=function(){eval(xhr.responseText)},xhr.onerror=function(){console.error('Chatbro loading error')},xhr.open('GET','//www.chatbro.com/embed.js?'+btoa(unescape(encodeURIComponent(JSON.stringify(params)))),async),xhr.send()}
	ChatbroLoader({encodedChatId: '<?php echo $config['chatbro_id']; ?>', siteDomain: '<?php echo $domain['host']; ?>', siteUserExternalId: '<?php echo $data['id']; ?>', siteUserFullName: '<?php echo $charusername; ?>',<?php echo ($data['admin'] == 1 ? " permissions: [ 'delete', 'ban']," : ''); ?> signature: '<?php echo $chatSignature; ?>'});
</script>
<?php
	}

	// Load PopUp Ads
	if($is_online && $data['hide_ads'] == 0)
	{
		$popup = $db->QueryFetchArray("SELECT `code` FROM `ad_codes` WHERE `status`='1' AND `size`='11' LIMIT 1");
		if(!empty($popup['code']))
		{
			echo html_entity_decode($popup['code'], ENT_QUOTES);
		}
	}

	if(!empty($config['recaptcha_pub'])) {
		echo '<script src="https://www.google.com/recaptcha/api.js" async></script>';
	}

	if(!empty($config['solvemedia_c'])) {
		echo '<script type="text/javascript" src="https://api-secure.solvemedia.com/papi/challenge.ajax"></script>';
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
<?php 
	}

	if($is_online)
	{
		if($config['pollfish_enabled'])
		{
			echo '<script type="text/javascript" src="https://storage.googleapis.com/pollfish_production/sdk/webplugin/pollfish.min.js"></script>';
		}
?>
	<script src="static/js/noadblock.js"></script>
	<script> if(typeof  NoAdBlock === 'undefined') {$(document).ready(function() {window.location.replace("<?php echo GenerateURL('locked', true); ?>")});} else {<?php echo (!isset($adblock_locked) ? 'noAdBlock.on(true, function() {window.location.replace("'.GenerateURL('locked', true).'")});' : 'noAdBlock.on(false, function() {window.location.replace("'.$config['secure_url'].'")});'); ?>} </script>
<?php } ?>
	<!-- ClaimBits v<?php echo $config['version']; ?> - Developed by www.MN-Shop.com -->
  </body>
</html>