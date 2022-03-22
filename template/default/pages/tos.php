<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
?>
 <main role="main" class="container">
      <div class="row">
		<?php 
			if($is_online) {
				require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
			}
		?>
		<div class="<?=($is_online ? 'col-xl-9 col-lg-8 col-md-7' : 'col-12')?>">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?=$lang['l_28']?>
					</div>
					<div class="content">
						<h2 class="text-light">1) User Account</h2>
						<ol>
							<li>Passwords are stored and encrypted in an irreversible format in our database. Passwords should be kept safe and we are not liable for any loss of accounts.</li>
							<li>You are not allowed to register or use your account throught proxy, vpn's, hide my "ass", Lan Groups, Internet caffe, etc.</li>
							<li>You are not allowed to create or use multiple accounts, use fake infos or share theirs payment processors between them and others members.</li>
							<li>You are not allowed to login more than one account on the same computer or IP address. This includes family, friends, co-workers and using your account on public computers.</li>
							<li>You are not allowed to use any VPS / Proxy server to access or use this website. We expect you to use this website from your personal device, using your real IP address. If you use VPS / Proxy Server, your payout request will be rejected and your account will be suspended. </li>
							<li>Any attempts to overload our server will lead to the permanent suspension of your account.</li>
							<li>If you bring directly or indirectly any kind of prejudicial to our website, his owner and to the staff team will lead to the permanent suspension of your account.</li>
							<li>If you don't login for more than <?php echo $config['penalty_time']; ?> days you will start losing <?php echo $config['penalty_amount']; ?> Bits every day until you login again or your account balance goes to 0.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">2) Referrals</h2>
						<ol>
							<li>You are allowed to refer an unlimited number of members</li>
							<li>Your referrals must be unique, have theirs owns e-mails, real names, etc...</li>
							<li>We dont allow using services that are selling referrals, our staff will verify and if you account will be suspected of breaking this rulle, this will lead to the permanent suspension of your account.</li>
							<li>You agree not to be compensated by the loss of any referrals referred by inactivity which they maybe either deleted or suspended by inactivity.</li>
							<li>You are never allowed to change your upline which will remain private.</li>
							<li>You must login every day to receive referral commissions. If you're not active for more than 24 hours, you won't receive any commissions from your referrals until you login again.</i>
						</ol>
						<hr class="global">
						<h2 class="text-light">3) Payments &amp; Purchase</h2>
						<ol>
							<li>All your orders are final and non refundable.</li>
							<li>All payments should be done through our website only.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">4) Advertisement</h2>
						<ol>
							<li>Advertisements can not contain pornographic, racist, discriminating, vulgar, illegal, or other adult materials of any kind.</li>
							<li>Advertisements can not contain any frame breakers.</li>
							<li>Advertisements must be English.</li>
							<li>Advertisements can not contain or promote any viruses.</li>
							<li>Advertisements can not contain any kind of background mining system.</li>
							<li>We reserve the rights to suspend any advertisment found violation our TOS, without providing any kind of refund.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">5) Refund Policy</h2>
						<ol>
							<li>The purchase of advertisments are non-refundable.</li>
							<li>The purchase of banner ads are non-refundable.</li>
							<li>The purchase of membership upgrade is non-refundable.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">6) Anticheat &amp; Account Suspension</h2>
						<ol>
							<li>You should know that each time you wanna do a "fishy" and "happy" thing our system will save this log.</li>
							<li>You should know that if you try to "hack", "sql injection", "xss passthrought" and others "happy" things this will lead to the permanent suspension of your account</li>
							<li>You must not Interfere with our system to prevent optimum security and/or reliability.</li>
							<li>We reserve the rights to give to suspend your account for any valid reason from our tos, or related to it.</li>
							<li>If your account is suspended for any reasson you cannot ask a refund, your account will be "wiped" (stats, referrals)</li>
							<li>You may not create or use any type of emulator, or a program to automate the process of clicking.</li>
							<li>You're not allowed to use any proxy or VCC to complete any offer available on our offerwalls.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">7) Liability</h2>
						<ol>
							<li>We won't be liable or any kind of delays or failures that are not directly related to us and therefore beyond our control.</li>
							<li>We won't be held responsible for any of its users, advertisers or advertisements, this include every 3rd party company we depend.</li>
							<li>We are not responsable for any tax payment for you on what you receive from us. Is your responsability to declare what you've received and pay your country taxes.</li>
							<li>We upon request can suspend your account if you dont agree our terms and your account will be suspended.</li>
							<li>We are not responsable for the investments you make.</li>
							<li>We are not responsable for the activity of Direct, Rented Referrals, and by buying or renting them you are fully aware of that.</li>
							<li>We have the right to change this agreement without any prior notice to our members, we will send out a notice to all of our members the new terms and/or terms that have changed changed.</li>
						<ol>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>