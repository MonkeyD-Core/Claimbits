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
						<?=$lang['l_289']?>
					</div>
					<div class="content">
						<h2 class="text-light">Cookies</h2>
						<ol>
							<li>Your browser must accept cookies.</li>
							<li>You allow us to use cookies to store any session, unique identifiers, preferences, or any other data that will help us among others to identify you as a visitor or logged in member, and provide you with the best browsing experience at our site.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">Account Security</h2>
						<ol>
							<li>We implement a variety of security measures to maintain the safety of your personal information when you enter, submit, or access your personal information.</li>
							<li>We are not responsible for personal information or account information stolen or accessed due to the failure of securing your device.</li>
							<li>Your email addresses will not be shown, given or sold.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">Personal Information</h2>
						<ol>
							<li>We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information (username, email, etc.)</li>
							<li>We will use your email address to contact you or help you reset your password.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">Passwords</h2>
						<ol>
							<li>Passwords are stored and encrypted in an irreversible format in our database.</li>
							<li>Plain-text passwords are not restorable and are never known.</li>
							<li>Encrypted passwords will never be sold, traded, or shared.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">Advertisements</h2>
						<ol>
							<li>We do our best to avoid offensive ads. However, we are not responsible for any content in advertisements. If you find any ad not complying with our rules, please report it.</li>
							<li>By clicking a link and viewing an ad, you are agreeing to take responsibility for your own actions on that website.</li>
						</ol>
						<hr class="global">
						<h2 class="text-light">Your Consent</h2>
						<p>By using our website, you have agreed to have read, understood, and accepted the above terms and conditions of this Privacy Policy.</p>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>