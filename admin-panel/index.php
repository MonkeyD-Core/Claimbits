<?php
	define('BASEPATH', true);
	define('IS_ADMIN', true);
	include('../system/init.php');
	if(!$is_online || ($is_online && $data['admin'] == 0)){
		redirect($config['secure_url']);
	}
	
	if(isset($_GET['version'])){
		echo get_data(base64_decode('aHR0cDovL21uLXNob3AuY29tL2RlbW8vY2xhaW1iaXRzL3ZlcnNpb24udHh0'), 4);
		exit;
	}

	/* Define allowed pages */
	$action = array(
		'users' => 1,
		'bank' => 1,
		'settings' => 1,
		'sites' => 1,
		'deposits' => 1,
		'transfers' => 1,
		'contests' => 1,
		'flag' => 1,
		'faq' => 1,
		'coupons' => 1,
		'withdrawals' => 1,
		'levels' => 1,
		'newsletter' => 1,
		'faucet' => 1,
		'shortlinks' => 1,
		'announcement' => 1,
		'blog' => 1,
		'memberships' => 1,
		'adcodes' => 1,
		'dashboard' => 1,
		'gateways' => 1,
		'banners' => 1,
		'captcha' => 1,
		'market' => 1,
		'mailset' => 1,
		'rewards' => 1,
		'wmp' => 1,
		'offers' => 1,
		'offerwalls' => 1,
		'pollfish' => 1,
		'proxy' => 1,
		'chat' => 1,
		'jobs' => 1
	);

	$jobs_done = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `jobs_done` WHERE `status`='0'");
	$jobs_done = number_format($jobs_done['total']);

	$page_name = 'dashboard';
	if (isset($_GET['x']) && isset($action[$_GET['x']])) {
		$page_name = $_GET['x'];
	}
?>
<html>
<head><title>ClaimBits - Admin Panel</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="author" content="MafiaNet (c) MN-Shop.com">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/fonts/font-awesome.css">
    <!--[if IE 8]><link rel="stylesheet" href="css/fonts/font-awesome-ie7.css"><![endif]-->
    <link rel="stylesheet" href="css/external/jquery.css">
    <link rel="stylesheet" href="css/elements.css">

	<!-- Load Javascript -->
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/libs/jquery-1.10.2.min.js"><\/script>')</script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/libs/jquery-migrate-1.2.1.min.js"><\/script>')</script>
    <script src="//code.jquery.com/ui/1.9.1/jquery-ui.min.js"></script>
    <script>window.jQuery.ui || document.write('<script src="js/libs/jquery-ui-1.9.1.min.js"><\/script>')</script>
    <!--[if gt IE 8]><!-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.8.2/lodash.min.js"></script>
    <script>window._ || document.write('<script src="js/libs/lo-dash.min.js"><\/script>')</script>
    <!--<![endif]-->
    <!--[if lt IE 9]><script src="//documentcloud.github.com/underscore/underscore.js"></script><![endif]-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.0.6/require.min.js"></script>
    <script>window.require || document.write('<script src="js/libs/require-2.0.6.min.js"><\/script>')</script>
    <script type="text/javascript">
        window.WebFontConfig = {
            google: { families: [ 'PT Sans:400,700' ] },
            active: function(){ $(window).trigger('fontsloaded') }
        };
    </script>
    <script defer async src="//ajax.googleapis.com/ajax/libs/webfont/1.0.28/webfont.js"></script>
    <script src="js/mylibs/polyfills/modernizr-2.6.1.min.js"></script>
    <!--[if lt IE 9]><script src="js/mylibs/polyfills/selectivizr.js"></script><![endif]-->
    <!--[if lt IE 10]><script src="js/mylibs/polyfills/excanvas.js"></script><![endif]-->
    <!--[if lt IE 10]><script src="js/mylibs/polyfills/classlist.js"></script><![endif]-->
    <script src="js/mylibs/jquery.lib.js"></script>
    <script src="js/mylibs/fullstats/jquery.css-transform.js"></script>
    <script src="js/mylibs/fullstats/jquery.animate-css-rotate-scale.js"></script>
    <script src="js/mylibs/forms/jquery.validate.js"></script>
    <script src="js/pespro.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/script.js"></script>
    <script src="js/jscolor.js"></script>
</head>
<body>
	<div id="loading-overlay"></div>
    <div id="loading">
        <span>Loading...</span>
    </div>
	<section id="toolbar">
		<div class="container_12">
			<div class="left">
				<ul class="breadcrumb">
					<li><a href="<?=$config['secure_url']?>" target="_blank"><?=$config['site_logo']?></a></li>
					<li><a href="<?=$config['secure_url']?>/admin-panel/">Admin Panel</a></li>
				</ul>
			</div>
			<div class="right">
				<ul>
					<li><a href="https://mn-shop.com/" target="_blank"><span id="time"><?=date('H:i A')?></span>Server Time</a></li>
                    <li class="space"></li>
					<li><a href="https://mn-shop.com/claimbits-ultimate-bitcoin-faucet"><span>v<?=$config['version']?></span>ClaimBits</a></li>
					<li class="red"><a href="<?=$config['secure_url']?>/?logout">Logout</a></li>
				</ul>
			</div>
			<div class="phone">
                <li><a class="navigation" href="#"><span class="icon icon-list"></span></a></li>
            </div>
		</div>
	</section>
	<header class="container_12"><br></header>
	<div role="main" id="main" class="container_12 clearfix">
		<section class="toolbar">
			<div class="user">
                <div class="avatar">
                    <img src="//www.gravatar.com/avatar/<?=md5(strtolower(trim($data['email'])))?>?s=28">
                </div>
                <span><?=$data['username']?></span>
                <ul>
                    <li><a href="index.php?x=users&edit=<?=$data['id']?>">Account Settings</a></li>
                    <li><a href="<?=$config['secure_url']?>">View Website</a></li>
                    <li class="line"></li>
                    <li><a href="<?=$config['secure_url']?>/?logout">Logout</a></li>
                </ul>
            </div>
            <ul class="shortcuts">
				<li>
                    <a href="javascript:void(0);"><span class="icon i24_user-business"></span></a>
                    <div class="large">
                        <h3>Create an user account</h3>
						<form method="post" action="index.php?x=users">
							<button class="button flat left grey" onclick="$(this).parent().fadeToggle($$.config.fxSpeed).parent().removeClass('active')">Close</button>
							<button type="submit" name="user_add" class="button flat right">Create</button>
							<div class="content">
								<div class="full grid">
									<div class="row no-bg">
										<p class="_50 small">
											<label for="user_username">Username</label>
											<input type="text" name="user_username" id="user_username" placeholder="John_Doe" />
										</p>
										<p class="_50 small">
											<label for="user_admin">Type</label>
											<select name="user_admin" id="user_admin">
												<option value="0">Member</option>
												<option value="1">Admin</option>
											</select>
										</p>
									</div>
									<div class="row no-bg">
										<p class="_50 small">
											<label for="user_email">Email Address</label>
											<input type="text" name="user_email" id="user_email" placeholder="email@domain.com" />
										</p>
										<p class="_50 small">
											<label for="user_email_2">Repeat Email</label>
											<input type="text" name="user_email_2" id="user_email_2" placeholder="email@domain.com" />
										</p>
									</div>
									<div class="row no-bg">
										<p class="_50 small">
											<label for="user_password">Password</label>
											<input type="password" name="user_password" id="user_password" placeholder="X8df!90EO" />
										</p>
										<p class="_50 small">
											<label for="user_password_2">Repeat Password</label>
											<input type="password" name="user_password_2" id="user_password_2" placeholder="X8df!90EO" />
										</p>
									</div>
									<div class="row no-bg">
										<p class="_50 small">
											<label for="user_gender">Gender</label>
											<select name="user_gender" id="user_gender">
												<option value="0">Choose...</option>
												<option value="1">Male</option>
												<option value="2">Female</option>
											</select>
										</p>
										<p class="_50 small">
											<label for="user_country">Country</label>
											<select name="user_country" id="user_country" class="search" data-placeholder="Select Country">
												<?php
													$countries = $db->QueryFetchArrayAll("SELECT country,id FROM `list_countries` ORDER BY country ASC"); 
													echo '<option value="0">Choose...</option>';
													foreach($countries as $country){ 
														echo '<option value="'.$country['id'].'">'.$country['country'].'</option>';
													}
												?>
											</select>
										</p>
									</div>
								</div>
							</div>
                        </form>
					</div>
                </li>
                <li>
                    <a href="javascript:void(0);"><span class="icon i24_application-blue"></span></a>
                    <div class="medium">
                        <h3>Announcement</h3>
						<form method="post" action="index.php?x=announcement">
							<button class="button flat left grey" onclick="$(this).parent().fadeToggle($$.config.fxSpeed).parent().removeClass('active')">Close</button>
							<button type="submit" name="gm_add" class="button flat right">Add</button>
							<div class="content">
								<div class="full grid">
									<div class="row no-bg">
										<p class="_100 small">
											<label for="gm_message">Message</label>
											<textarea rows="4" class="full-width" name="gm_message" id="gm_message" placeholder="Message" required></textarea>
										</p>
										<p class="_100 small">
											<label for="gm_url">URL</label>
											<input type="text" name="gm_url" id="gm_url" placeholder="http://" />
										</p>
										<p class="_100 small">
											<label for="gm_color">Color</label>
											<select name="gm_color" id="gm_color">
												<option value="0">Blue</option>
												<option value="1">Green</option>
												<option value="2">Red</option>
											</select>
										</p>
									</div>
								</div>
							</div>
						</form>
                    </div>
                </li>
            </ul>
		</section>
		<aside>
			<div class="top">
				<nav><ul class="collapsible accordion">
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/dashboard.png" alt="" height="16" width="16">Statistics</a>
						<ul>
							<li><a href="index.php"><span class="icon icon-arrow-right"></span> Dashboard</a></li>
							<li><a href="index.php?x=offers"><span class="icon icon-arrow-right"></span> Completed Offers</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/coins.png" alt="" height="16" width="16">Transactions</a>
						<ul>
							<li><a href="index.php?x=deposits"><span class="icon icon-arrow-right"></span> Deposits</a></li>
							<li><a href="index.php?x=withdrawals"><span class="icon icon-arrow-right"></span> Withdrawals</a></li>
							<li><a href="index.php?x=transfers"><span class="icon icon-arrow-right"></span> Transfers</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/users.png" alt="" height="16" width="16">Users</a>
						<ul>
							<li><a href="index.php?x=users"><span class="icon icon-arrow-right"></span> All Users</a></li>
							<li><a href="index.php?x=users&today"><span class="icon icon-arrow-right"></span> Registered Today</a></li>
							<li><a href="index.php?x=users&online"><span class="icon icon-arrow-right"></span> Online Users</a></li>
							<li><a href="index.php?x=users&premium"><span class="icon icon-arrow-right"></span> Upgraded Users</a></li>
							<li><a href="index.php?x=users&unverified"><span class="icon icon-arrow-right"></span> Unconfirmed Email</a></li>
							<li><a href="index.php?x=users&banned"><span class="icon icon-arrow-right"></span> Banned Users</a></li>
							<li><a href="index.php?x=users&countries"><span class="icon icon-arrow-right"></span> Countries Overview</a></li>
							<li><a href="index.php?x=users&multi_accounts" style="color:#17a2b8"><span class="icon icon-arrow-right"></span> <b>Multiple Accounts</b></a></li>
							<li><a href="index.php?x=flag" style="color:#dc3545"><span class="icon icon-arrow-right"></span> <b>VPN / Proxy Detected</b></a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/user-plus.png" alt="" height="16" width="16">Referral Market</a>
						<ul>
							<li><a href="index.php?x=market"><span class="icon icon-arrow-right"></span> Sold Referrals</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/levels.png" alt="" height="16" width="16">Levels</a>
						<ul>
							<li><a href="index.php?x=levels"><span class="icon icon-arrow-right"></span> Levels</a></li>
							<li><a href="index.php?x=levels&add"><span class="icon icon-arrow-right"></span> Add Level</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/medal.png" alt="" height="16" width="16">Achievements</a>
						<ul>
							<li><a href="index.php?x=rewards"><span class="icon icon-arrow-right"></span> Achievements</a></li>
							<li><a href="index.php?x=rewards&claims"><span class="icon icon-arrow-right"></span> Claimed Achievements</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/cards.png" alt="" height="16" width="16">Coupons</a>
						<ul>
							<li><a href="index.php?x=coupons"><span class="icon icon-arrow-right"></span> Coupons</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/pages.png" alt="" height="16" width="16">PTC Ads</a>
						<ul>
							<li><a href="index.php?x=sites&add"><span class="icon icon-arrow-right"></span> Add Website</a></li>
							<li><a href="index.php?x=sites"><span class="icon icon-arrow-right"></span> Websites</a></li>
							<li><a href="index.php?x=sites&packs"><span class="icon icon-arrow-right"></span> PTC Packs</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/ads.png" alt="" height="16" width="16">Banner Ads</a>
						<ul>
							<li><a href="index.php?x=banners"><span class="icon icon-arrow-right"></span> Manage Banners</a></li>
							<li><a href="index.php?x=banners&packs"><span class="icon icon-arrow-right"></span> Manage Packs</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/jobs.png" alt="" height="16" width="16">Jobs <span class="badge"><?php echo $jobs_done; ?></span></a>
						<ul>
							<li><a href="index.php?x=jobs"><span class="icon icon-arrow-right"></span> Jobs</a></li>
							<li><a href="index.php?x=jobs&add"><span class="icon icon-arrow-right"></span> Add Job</a></li>
							<li><a href="index.php?x=jobs&pending=only"><span class="icon icon-arrow-right"></span> Pending Jobs</a></li>
							<li><a href="index.php?x=jobs&pending"><span class="icon icon-arrow-right"></span> Completed Jobs</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/question.png" alt="" height="16" width="16">FAQ</a>
						<ul>
							<li><a href="index.php?x=faq"><span class="icon icon-arrow-right"></span> Manage FAQ</a></li>
							<li><a href="index.php?x=faq&add"><span class="icon icon-arrow-right"></span> Add</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/balloon.png" alt="" height="16" width="16">Blog</a>
						<ul>
							<li><a href="index.php?x=blog"><span class="icon icon-arrow-right"></span> Manage Blog</a></li>
							<li><a href="index.php?x=blog&add"><span class="icon icon-arrow-right"></span> Add Blog</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/plus.png" alt="" height="16" width="16">Other Options</a>
						<ul>
							<li><a href="index.php?x=newsletter"><span class="icon icon-arrow-right"></span> Send Newsletter</a></li>
							<li><a href="index.php?x=announcement"><span class="icon icon-arrow-right"></span> Announcements</a></li>
							<li><a href="index.php?x=adcodes"><span class="icon icon-arrow-right"></span> Advertisments</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);"><img src="img/icons/packs/fugue/16x16/sett.png" alt="" height="16" width="16">Settings</a>
						<ul>
							<li><a href="index.php?x=settings"><span class="icon icon-arrow-right"></span> General Settings</a></li>
							<li><a href="index.php?x=faucet"><span class="icon icon-arrow-right"></span> Faucet Settings</a></li>
							<li><a href="index.php?x=shortlinks"><span class="icon icon-arrow-right"></span> Shortlinks Settings</a></li>
							<li><a href="index.php?x=memberships"><span class="icon icon-arrow-right"></span> Membership Settings</a></li>
							<li><a href="index.php?x=contests"><span class="icon icon-arrow-right"></span> Contests Settings</a></li>
							<li><a href="index.php?x=mailset"><span class="icon icon-arrow-right"></span> Mailing Settings</a></li>
							<li><a href="index.php?x=gateways"><span class="icon icon-arrow-right"></span> Payment Gateways</a></li>
							<li><a href="index.php?x=captcha"><span class="icon icon-arrow-right"></span> Captcha Settings</a></li>
							<li><a href="index.php?x=wmp"><span class="icon icon-arrow-right"></span> WebMinePool Settings</a></li>
							<li><a href="index.php?x=pollfish"><span class="icon icon-arrow-right"></span> Pollfish Surveys Settings</a></li>
							<li><a href="index.php?x=offerwalls"><span class="icon icon-arrow-right"></span> OfferWalls Settings</a></li>
							<li><a href="index.php?x=proxy"><span class="icon icon-arrow-right"></span> Proxy Detector Settings</a></li>
							<li><a href="index.php?x=chat"><span class="icon icon-arrow-right"></span> Chat Settings</a></li>
						</ul>
					</li>
				</ul></nav>		
			</div>
			<div class="bottom sticky">
				<div class="divider"></div>
				<div style="font-size:11px;margin:10px 15px"><b>Script Version:</b> <span style="float:right"><strong style="color:green"><?=$config['version']?></strong></span></div>
				<div style="font-size:11px;margin:10px 15px"><b>Latest Version:</b> <span style="float:right"><strong style="color:blue" id="latest_version"><img src="img/ajax-loader.gif" border="0" alt="<?=$config['version']?>" /></strong></span></div>
			</div>
		</aside>
	<?php
		include('sections/'.$page_name.'.php'); 
	?>
	</div>
	<script> 
		$$.loaded();
		$(document).ready(function() {
			var current_version = '<?=$config['version']?>';

			 function getVersion() {
				 $.ajax({
					 url: "index.php?version",
					 timeout: 7500,
					 success: function(b) {
						 $("#latest_version").html(b);
						 if (b != "") {
							 if (b > current_version) {
								 $("#version_alert").show()
							 }
						 }
					 },
					 error: function(b) {
						 $("#latest_version").html(current_version)
					 }
				 })
			 }

			setTimeout(getVersion(), 1000);
		}); 
	</script>
	<footer class="container_12">
		<ul class="grid_6">
			<li><a href="http://mn-shop.com/" target="_blank">Store</a></li>
			<li><a href="http://forum.mn-shop.com/" target="_blank">Support Forum</a></li>
		</ul>
		<span class="grid_6">&copy; <?=date('Y')?> <a href="http://mn-shop.com" target="_blank">MN-Shop.com</a></span>
	</footer>
</body>
</html>