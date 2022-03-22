<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	$logo_image = $config['secure_url'].'/static/img/logo.png';
	if(!empty($config['logo_image']) && file_exists(BASE_PATH.'/'.$config['logo_image'])) 
	{
		$logo_image = $config['secure_url'].'/'.$config['logo_image'];
	}
?>
<!DOCTYPE html>
<html lang="<?php echo $CONF['language']; ?>">
  <head><title><?php echo (empty($page_title) ? $config['site_name'] : $page_title.' - '.$config['site_logo']); ?></title>
	<base href="<?php echo $config['secure_url']; ?>">
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?php echo $config['site_description']; ?>" />
	<meta name="keywords" content="<?php echo $config['site_keywords']; ?>" />
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5/css/all.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5/css/v4-shims.min.css" rel="stylesheet">
	<link href="template/<?php echo $config['theme']; ?>/static/theme.css?v=<?php echo $config['version']; ?>" rel="stylesheet">
	<link rel="shortcut icon" href="static/favicon.ico" type="image/x-icon">
	<script src="https://cdn.jsdelivr.net/combine/npm/jquery@3.4.1,npm/popper.js@1.16.0,npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <script>window.jQuery || document.write('<script src="static/js/bundle.js">\x3C/script>')</script>
	<script src="static/js/countdown-timer.min.js"></script>
  </head>
  <body class="bg-light">
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top bottom-border">
	  <div class="container">
        <a class="navbar-brand" href="<?php echo $config['secure_url']; ?>"><img src="<?php echo $logo_image; ?>" alt="<?php echo $config['site_logo']; ?>" title="<?php echo $config['site_logo']; ?>" /></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false"><span class="navbar-toggler-icon"></span></button>
        <div class="navbar-collapse collapse" id="navbar">
          <ul class="navbar-nav ml-auto">
			<?php 
				if($is_online)
				{
					$count_jobs = $db->QueryFetchArray("SELECT COUNT(`id`) AS `total` FROM `jobs` WHERE `id` NOT IN (SELECT `job_id` FROM `jobs_done` WHERE `uid`='".$data['id']."' AND `status`!='2')");
					$count_ptc = $db->QueryFetchArray("SELECT COUNT(`id`) AS `total` FROM `ptc_websites` WHERE `id` NOT IN (SELECT `site_id` FROM `ptc_done` WHERE `user_id`='".$data['id']."') AND `status`='1' AND `received`<`total_visits`AND (`daily_limit`>`received_today` OR `daily_limit`='0')");
					$count_sl = $db->QueryFetchArray("SELECT SUM(a.daily_limit) AS total, SUM(b.count) AS done FROM shortlinks_config a LEFT JOIN shortlinks_done b ON b.short_id = a.id AND b.user_id = '".$data['id']."' WHERE a.status = '1'");
					$count_sl = ($count_sl['total']-$count_sl['done']);
					$count_faucet = ($data['last_claim'] > (time()-($config['faucet_time']*60)) ? '0' : '1');
					$count_total = ($count_sl+$count_jobs['total']+$count_ptc['total']+$count_faucet);
			?>
				<li class="nav-item dropdown">
				  <a class="nav-link" href="javascript:void(0)" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="<?php echo getCurrency('icon_class'); ?> fa-fw"></i> <?=$lang['l_470']?> <span class="badge badge-light"><?=$count_total?></span></a>
				  <div class="dropdown-menu" aria-labelledby="dropdown">
					<a class="dropdown-item" href="<?php echo $config['secure_url']; ?>"><i class="<?php echo getCurrency('icon_class'); ?> fa-fw"></i> <?php echo $lang['l_235']; ?> <span class="badge badge-info"><?=$count_faucet?></span></a>
					<a class="dropdown-item" href="<?=GenerateURL('shortlinks')?>"><i class="fa fa-link fa-fw"></i> <?php echo $lang['l_425']; ?> <span class="badge badge-info"><?=$count_sl?></span></a>
					<a class="dropdown-item" href="<?=GenerateURL('ptc')?>"><i class="fa fa-external-link fa-fw"></i> <?php echo $lang['l_95']; ?> <span class="badge badge-info"><?=$count_ptc['total']?></span></a>
					<a class="dropdown-item" href="<?=GenerateURL('tasks')?>"><i class="fa fa-briefcase fa-fw"></i> <?php echo $lang['l_23']; ?> <span class="badge badge-info"><?=$count_jobs['total']?></span></a>
					<a class="dropdown-item" href="<?=GenerateURL('rewards')?>"><i class="fa fa-check-circle-o fa-fw"></i> <?=$lang['l_205']?></a>
					<a class="dropdown-item" href="<?=GenerateURL('mining')?>"><i class="fa fa-bolt fa-fw"></i> <?php echo $lang['l_402']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('invest')?>"><i class="fa fa-area-chart fa-fw"></i> <?php echo $lang['l_409']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('coupons')?>"><i class="fa fa-ticket fa-fw"></i> <?php echo $lang['l_434']; ?></a>
				   </div>
				</li>
				<li class="nav-item dropdown">
				  <a class="nav-link" href="javascript:void(0)" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-users fa-fw"></i> <?=$lang['l_20']?></a>
				  <div class="dropdown-menu" aria-labelledby="dropdown">
					<a class="dropdown-item" href="<?=GenerateURL('affiliates')?>"><i class="fa fa-users fa-fw"></i> <?php echo $lang['l_20']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('market')?>"><i class="fa fa-user-plus fa-fw"></i> <?=$lang['l_490']?></a>
				   </div>
				</li>
				<?php if($config['lottery_status'] == 1)  { ?>
				<li class="nav-item">
				  <a class="nav-link" href="<?=GenerateURL('lottery')?>"><i class="fa fa-clock-o fa-fw"></i> <?php echo $lang['l_265']; ?></a>
				</li>
				<?php } ?>
				<li class="nav-item dropdown">
				  <a class="nav-link" href="javascript:void(0)" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-trophy fa-fw"></i> <?=$lang['l_471']?></a>
				  <div class="dropdown-menu" aria-labelledby="dropdown">
					<a class="dropdown-item" href="<?=GenerateURL('contest')?>"><i class="fa fa-trophy fa-fw"></i> <?php echo $lang['l_278']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('contest&x=shortlinks')?>"><i class="fa fa-trophy fa-fw"></i> <?php echo $lang['l_424']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('contest&x=tasks')?>"><i class="fa fa-trophy fa-fw"></i> <?php echo $lang['l_319']; ?></a>
				   </div>
				</li>
				<li class="nav-item dropdown">
				  <a class="nav-link" href="javascript:void(0)" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-list-alt fa-fw"></i> <?=$lang['l_262']?></a>
				  <div class="dropdown-menu" aria-labelledby="dropdown">
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=bitswall')?>"><i class="fa fa-list-ol fa-fw"></i> BitsWall</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=wannads')?>"><i class="fa fa-list-ol fa-fw"></i> Wannads</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=adgem')?>"><i class="fa fa-list-ol fa-fw"></i> AdGem</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=adgem')?>"><i class="fa fa-list-ol fa-fw"></i> OfferToro</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=offerdaddy')?>"><i class="fa fa-list-ol fa-fw"></i> OfferDaddy</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=jungle')?>"><i class="fa fa-list-ol fa-fw"></i> JungleSurvey</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=kiwiwall')?>"><i class="fa fa-list-ol fa-fw"></i> KiwiWall</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=cpalead')?>"><i class="fa fa-list-ol fa-fw"></i> CPALead</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=theoremreach')?>"><i class="fa fa-list-ol fa-fw"></i> TheoremReach</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=adworkmedia')?>"><i class="fa fa-list-ol fa-fw"></i> AdWorkMedia</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=adscendmedia')?>"><i class="fa fa-list-ol fa-fw"></i> AdscendMedia</a>
					<a class="dropdown-item" href="<?=GenerateURL('offers&x=personaly')?>"><i class="fa fa-list-ol fa-fw"></i> Persona.ly</a>
				   </div>
				</li>
				<li class="nav-item">
				  <a class="nav-link text-light" href="<?=GenerateURL('blog')?>"><i class="fa fa-rss fa-fw"></i> <?php echo $lang['blog_7']; ?></span></a>
				</li>
				<li class="nav-item dropdown">
				  <a class="nav-link" href="javascript:void(0)" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars fa-fw"></i> <?php echo $lang['l_26']; ?></a>
				  <div class="dropdown-menu" aria-labelledby="dropdown">
					<a class="dropdown-item" href="<?=GenerateURL('withdraw')?>"><i class="<?php echo getCurrency('icon_class'); ?> fa-fw"></i> <?php echo $lang['l_160']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('deposits')?>"><i class="fa fa-shopping-cart fa-fw"></i> <?php echo $lang['l_328']; ?></a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?=GenerateURL('account')?>"><i class="fa fa-cog fa-fw"></i> <?php echo $lang['l_35']; ?></a>
					<a class="dropdown-item" href="<?=GenerateURL('membership')?>"><i class="fa fa-star fa-fw"></i> <?php echo $lang['l_19']; ?></a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?=$config['secure_url']?>/?logout"><i class="fa fa-power-off fa-fw"></i> <?php echo $lang['l_36']; ?></a>
					<?php if($is_online && $data['admin'] == 1){  ?>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?=$config['secure_url']?>/admin-panel/" target="_blank"><i class="fa fa-lock fa-fw"></i> Admin Panel</a>
					<?php } ?>
				   </div>
				</li>
			<?php } else { ?>
				<li class="nav-item">
				  <a class="nav-link" href="<?=GenerateURL('blog')?>"><i class="fa fa-rss fa-fw"></i> <?php echo $lang['blog_7']; ?></a>
				</li>
				<li class="nav-item">
				  <a class="nav-link btn btn-info text-white" href="javascript:void(0)" data-toggle="modal" data-target="#loginModal"><i class="fa fa-sign-in"></i> <?=$lang['l_24']?></a>
				</li>
				<li class="nav-item">
				  <a class="nav-link btn btn-warning btn-signup text-white" href="javascript:void(0)" data-toggle="modal" data-target="#registrationModal"><i class="fa fa-mouse-pointer"></i> <?=strtoupper($lang['l_25'])?></a>
				</li>
			<?php } ?>
		  </ul>
        </div>
      </div>
    </nav>
	<?php
		if($config['lottery_status'] == 1) 
		{
			$nextMonth = strtotime('first day of next month'); 
			$nextMonth = date('Y-m-d', $nextMonth);
			$nextMonth = strtotime($nextMonth);
			$remainingTime = date('Y-m-d H:i:s', ($config['lottery_duration'] == 0 ? (strtotime("next Sunday")-60) : $nextMonth));
			$lottery = $db->QueryFetchArray("SELECT * FROM `lottery` WHERE `closed`='0' ORDER BY `id` DESC LIMIT 1");
	?>
	<div class="container">
		<div class="lotteryTop">
			<div class="row">
				<div class="col-md-4 col-sm-12 text-center">
					<i class="fa fa-star"></i> <?php echo $lang['l_451']; ?> <a href="<?=GenerateURL('lottery')?>"><b>#<?php echo $lottery['id']; ?></b></a> <i class="fa fa-star"></i>
				</div>
				<div class="col-md-4 col-sm-6 col-12 text-center">
					 <span id="hlTime"></span>
				</div>
				<div class="col-md-4 col-sm-6 col-12 text-center">
					 <i class="fa fa-check-circle"></i> <?php echo $lang['l_268']; ?>: <b class="text-warning"><?php echo number_format($lottery['prize'], 2).' '.$lang['l_337']; ?></b>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			var hlDate = new Date('<?php echo $remainingTime; ?>');
			hlDate.setDate(hlDate.getDate());
			$("#hlTime").countdown(hlDate, function (event) {
				$(this).html(event.strftime('<i class="fa fa-clock-o"></i> %D d %H h %M m %S s'));
			});
		});
	</script>
<?php
	}

	if(!$is_online) { 
		$secureFormID = array(
			'loginToken' => GenerateKey(rand(10,15)),
			'userLogin' => GenerateKey(rand(10,15)),
			'userPass' => GenerateKey(rand(10,15)),
			'userPIN' => GenerateKey(rand(10,15)),
			'regToken' => GenerateKey(rand(10,15)),
			'regLogin' => GenerateKey(rand(10,15)),
			'regPass' => GenerateKey(rand(10,15)),
			'regEmail' => GenerateKey(rand(10,15)),
			'regBTC' => GenerateKey(rand(10,15)),
			'regTOS' => GenerateKey(rand(10,15)),
			'login_form' => GenerateKey(rand(10,15)),
			'register_form' => GenerateKey(rand(10,15)),
			'token' => GenerateKey(rand(10,15), false, false),
			'username' => GenerateKey(rand(10,15), false, false),
			'password' => GenerateKey(rand(10,15), false, false),
			'email' => GenerateKey(rand(10,15), false, false),
			'pin' => GenerateKey(rand(10,15), false, false),
			'bitcoin' => GenerateKey(rand(10,15), false, false)
		);

		// Confirm Email
		$extra_script = '';
		if(isset($_GET['activate']) && !empty($_GET['activate'])) {
			$code = $db->EscapeString($_GET['activate']);
			if($db->QueryGetNumRows("SELECT id FROM `users` WHERE `activate`='".$code."' LIMIT 1") > 0){
				$db->Query("UPDATE `users` SET `activate`='0' WHERE `activate`='".$code."'");
				$extra_script = "$('#loginStatus').html('<div class=\"alert alert-success\" role=\"alert\">".$lang['l_09']."</div>'); $('#loginModal').modal('show');";
			}
		}
		
		// Add extra security token
		$login_key = GenLoginToken();
		$register_key = GenRegisterToken();

		// Protect Javascript
		$script = "$(document).ready(function() {
			".$extra_script."
			$('#".$secureFormID['login_form']."').on('submit', function(e) {
				e.preventDefault();
				$('#loginStatus').html('<div class=\"alert alert-info\" role=\"alert\">".$lang['l_299']."</div>');
				var ".$secureFormID['token']." = $('#".$secureFormID['loginToken']."').val();
				var ".$secureFormID['username']." = $('#".$secureFormID['userLogin']."').val();
				var ".$secureFormID['password']." = $('#".$secureFormID['userPass']."').val();
				var ".$secureFormID['pin']." = $('#".$secureFormID['userPIN']."').val();
				if(".$secureFormID['username']." == '') {
					$('#loginStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_300']."</div>');
				} else if(".$secureFormID['password']." == '') {
					$('#loginStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_301']."</div>');
				} else if(".$secureFormID['token']." == '') {
					$('#loginStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_302']."</div>');
				} else {
					$.post('system/ajax.php', {a: 'login', token: ".$secureFormID['token'].", access_key: '".$login_key."', username: ".$secureFormID['username'].", password: ".$secureFormID['password'].", pin: ".$secureFormID['pin'].", remember: $('#remember').val(), recaptcha: ".(empty($config['recaptcha_pub']) ? 'null' : 'grecaptcha.getResponse(0)')."},
					function(response) {
						if(response.status == 0){
							$('input[type=\"password\"]').val('');
							shakeModal();
							".(empty($config['recaptcha_pub']) ? '' : 'grecaptcha.reset(0);')."
							$('#loginStatus').html(response.msg).fadeIn('slow');
						}else{
							$('#loginStatus').html(response.msg).fadeIn('slow');
							window.setTimeout(function() {
								document.location.href = '".$config['secure_url']."';
							}, 750);
						}
					},'json');
				}
			});

			$('#".$secureFormID['register_form']."').on('submit', function(e) {
				e.preventDefault();
				$('#registrationStatus').html('<div class=\"alert alert-info\" role=\"alert\">".$lang['l_145']."</div>');
				var ".$secureFormID['token']." = $('#".$secureFormID['regToken']."').val();
				var ".$secureFormID['username']." = $('#".$secureFormID['regLogin']."').val();
				var ".$secureFormID['password']." = $('#".$secureFormID['regPass']."').val();
				var ".$secureFormID['email']." = $('#".$secureFormID['regEmail']."').val();
				var ".$secureFormID['bitcoin']." = $('#".$secureFormID['regBTC']."').val();
				var gender = $('#regGender').val();
				var country = $('#regCountry').val();
				var tos = $('#".$secureFormID['regTOS']."').is(':checked');
				if(tos == false) {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_285']."</div>');
				} else if(".$secureFormID['username']." == '') {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_300']."</div>');
				} else if(".$secureFormID['email']."== '' || !validateEmail(".$secureFormID['email'].")) {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_144']."</div>');
				} else if(".$secureFormID['password']." == '' || ".$secureFormID['password'].".length < 8) {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_134']."</div>');
				} else if(gender == 0) {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_147']."</div>');
				} else if(country == 0) {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_150']."</div>');
				} else if(".$secureFormID['token']." == '') {
					$('#registrationStatus').html('<div class=\"alert alert-danger\" role=\"alert\">".$lang['l_302']."</div>');
				} else {
					$.post('system/ajax.php', {a: 'register', token: ".$secureFormID['token'].", access_key: '".$register_key."', username: ".$secureFormID['username'].", password: ".$secureFormID['password'].", email: ".$secureFormID['email'].", bitcoin: ".$secureFormID['bitcoin'].", country: country, gender: gender, tos: tos, recaptcha: ".(empty($config['recaptcha_pub']) ? 'null' : 'grecaptcha.getResponse(1)')."},
					function(response) {
						if(response.status == 0){
								shakeModal();
								".(empty($config['recaptcha_pub']) ? '' : 'grecaptcha.reset(1);')."
								$('#registrationStatus').html(response.msg).fadeIn('slow');
							}else{
								$('#registrationStatus').html(response.msg).fadeIn('slow');
								window.setTimeout(function() {
									if(response.loggedin == 1) {
										document.location.href = '".$config['secure_url']."';
									}
								}, 1000);
							}
					},'json');
				}
			});
		});
		
		function validateEmail(email) {
			var re = /^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(String(email).toLowerCase());
		}

		function shakeModal() {
			$('.modal-dialog').addClass('shake');
			setTimeout( function(){ 
			   $('.modal-dialog').removeClass('shake'); 
			}, 750 ); 
		}

		function switchModal() {
		  $('#registrationModal').modal('hide');
		  $('#loginModal').modal('show');
		}";

		$packer = new JavaScriptPacker($script, 'Normal', true, false);
		$packed = $packer->pack();
		
		echo '<script>'.$packed.'</script>';
?>
	<div id="loginModal" class="modal fade">
		<div class="modal-dialog modal-dialog-centered modal-login animated">
			<div class="modal-content">
				<div class="modal-header">				
					<h4 class="modal-title"><?php echo $lang['l_174']; ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" id="<?php echo $secureFormID['login_form']; ?>">
						<div id="loginStatus"></div>
						<input type="hidden" id="<?php echo $secureFormID['loginToken']; ?>" value="<?php echo $token; ?>" />
						<div class="form-group">
							<i class="fa fa-user"></i>
							<input type="text" class="form-control" id="<?php echo $secureFormID['userLogin']; ?>" placeholder="<?=$lang['l_31']?>" required="required">
						</div>
						<div class="form-group">
							<i class="fa fa-lock"></i>
							<input type="password" class="form-control" id="<?php echo $secureFormID['userPass']; ?>" placeholder="<?=$lang['l_32']?>" required="required">					
						</div>
						<div class="form-group">
							<i class="fas fa-mobile-alt"></i>
							<input type="number" class="form-control" id="<?php echo $secureFormID['userPIN']; ?>" placeholder="<?=$lang['l_512']?>">					
						</div>
						<?php 
							if(!empty($config['recaptcha_pub'])){
								echo '<div class="g-recaptcha" data-sitekey="'.$config['recaptcha_pub'].'"></div>';
							}
						?>
						<div class="custom-control custom-checkbox my-1">
						  <input type="checkbox" class="custom-control-input" id="remember">
						  <label class="custom-control-label" for="remember"><?=$lang['l_33']?></label>
						</div>
						<div class="form-group mt-3">
							<button type="submit" class="btn btn-primary btn-block btn-lg"><?=$lang['l_24']?></button>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<a href="<?=GenerateURL('recover')?>"><?=$lang['l_34']?></a>
				</div>
			</div>
		</div>
	</div>
	<div id="registrationModal" class="modal fade">
		<div class="modal-dialog modal-dialog-centered modal-login animated">
			<div class="modal-content">
				<div class="modal-header">				
					<h4 class="modal-title"><?php echo $lang['l_221']; ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<form method="POST" id="<?php echo $secureFormID['register_form']; ?>">
						<div id="registrationStatus"><?php echo ($config['reg_reqmail'] == 1 ? '<div class="alert alert-warning" role="alert"><small>'.$lang['l_422'].'</small></div>' : ''); ?></div>
						<input type="hidden" id="<?php echo $secureFormID['regToken']; ?>" value="<?php echo $token; ?>" />
							<div class="form-group">
								<i class="fa fa-user"></i>
								<input type="text" class="form-control" id="<?php echo $secureFormID['regLogin']; ?>" placeholder="<?=$lang['l_153']?>" required="required">
							</div>
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<div class="form-group">
									<i class="fa fa-envelope"></i>
									<input type="email" class="form-control" id="<?php echo $secureFormID['regEmail']; ?>" placeholder="<?=$lang['l_46']?>" required="required">
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group">
									<i class="fa fa-lock"></i>
									<input type="password" class="form-control" id="<?php echo $secureFormID['regPass']; ?>" placeholder="<?=$lang['l_32']?>" required="required">					
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<div class="form-group">
									<i class="fa fa-venus-mars"></i>
									<select class="form-control custom-select form-control-sm" id="regGender">
										<option value="0"><?php echo $lang['l_155']; ?></option>
										<option value="1"><?php echo $lang['l_156']; ?></option>
										<option value="2"><?php echo $lang['l_157']; ?></option>
									</select>		
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group mb-0">
									<i class="fa fa-globe"></i>
									<select class="form-control custom-select form-control-sm" id="regCountry">
									  <?php
											echo '<option value="0">'.$lang['l_158'].'</option>';
											
											$countries = $db->QueryFetchArrayAll("SELECT id,country,code FROM `list_countries` ORDER BY country ASC");
											$IPCountry = detectCountry(VisitorIP());
											
											foreach($countries as $country){ 
												echo '<option value="'.$country['id'].'"'.($IPCountry == $country['code'] ? ' selected' : '').'>'.$country['country'].'</option>';
											}
										?>
									</select>		
								</div>
							</div>
						</div>
						<div class="elseConnect tagline"><span><?php echo $lang['l_233']; ?></span></div>
						<div class="form-group">
							<i class="<?php echo getCurrency('icon_class'); ?>"></i>
							<input type="text" class="form-control" id="<?php echo $secureFormID['regBTC']; ?>" placeholder="<?php echo $lang['l_169']; ?>">
						</div>
						<?php 
							if(!empty($config['recaptcha_pub'])){
								echo '<div class="g-recaptcha" data-sitekey="'.$config['recaptcha_pub'].'"></div>';
							}
						?>
						<div class="custom-control custom-checkbox my-1">
						  <input type="checkbox" class="custom-control-input" id="<?php echo $secureFormID['regTOS']; ?>">
						  <label class="custom-control-label" for="<?php echo $secureFormID['regTOS']; ?>"><?=$lang['l_283']?></label>
						</div>
						<div class="form-group mt-3">
							<button type="submit" class="btn btn-primary btn-block btn-lg"><?=$lang['l_221']?></button>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<a href="javascript:void(0)" onclick="switchModal()"><?=$lang['l_228']?></a>
				</div>
			</div>
		</div>
	</div>
<?php
	}

	// Load Header Ads
	$ad_banner = $db->QueryFetchArray("SELECT `code` FROM `ad_codes` WHERE `status`='1' AND (`size`='2' OR `size`='3') ORDER BY rand() LIMIT 1");
	if(!empty($ad_banner['code']))
	{
		echo '<div class="container"><div class="mx-auto"><div class="mt-3 py-1 bg-white rounded box-shadow box-style text-center d-flex justify-content-center">'.html_entity_decode($ad_banner['code'], ENT_QUOTES).'</div></div></div>';
	}
?>