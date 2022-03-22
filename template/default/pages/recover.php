<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$errMessage = '';
	$change_pass = false;
	$captcha_valid = true;
	if(isset($_GET['x']) && !empty($_GET['x'])){
		$hash = $db->EscapeString($_GET['x']);
		$rec = $db->QueryFetchArray("SELECT `id` FROM `users` WHERE `rec_hash`='".$hash."' LIMIT 1");
		
		if(!empty($rec['id'])){
			$change_pass = true;
		}

		if($change_pass && isset($_POST['change'])) {
			if(!empty($config['recaptcha_pub']) && !empty($config['recaptcha_sec'])){
				$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_sec']);
				$recaptcha = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
			
				if($recaptcha->isSuccess()){
					$captcha_valid = 1;
				}else{
					$captcha_valid = 0;
				}
			}
			
			if(!$captcha_valid){
				$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_133'].'</div>';
			}elseif(!checkPwd($_POST['pass1'],$_POST['pass2'])) {
				$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_134'].'</div>';
			}else{
				$db->Query("UPDATE `users` SET `password`='".securePassword($_POST['pass1'])."', `rec_hash`='0', `activate`='0' WHERE `id`='".$rec['id']."'");
				$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_135'].'</div>';
			}
		}
	}

	if(isset($_POST['send'])) {
		$email = $db->EscapeString($_POST['email']);
		$rec = $db->QueryFetchArray("SELECT id,username FROM `users` WHERE `email`='".$email."' LIMIT 1");

		if(!empty($config['recaptcha_pub']) && !empty($config['recaptcha_sec'])){
			$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_sec']);
			$recaptcha = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		
			if($recaptcha->isSuccess()){
				$captcha_valid = 1;
			}else{
				$captcha_valid = 0;
			}
		}

		if(!$captcha_valid){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_136'].'</div>';
		}elseif(!isEmail($email)){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_137'].'</div>';
		}elseif(empty($rec['username'])){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_138'].'</div>';
		}else{
			$newhash = GenerateKey(32);
			$subject = $config['site_logo'].' - Password Recovery';
			$recover_url = GenerateURL('recover&x='.$newhash, true);
			$db->Query("UPDATE `users` SET `rec_hash`='".$newhash."' WHERE `email`='".$email."'");

			if($config['mail_delivery_method'] == 1){
				$mailer->isSMTP();
				$mailer->Host = $config['smtp_host'];
				$mailer->Port = $config['smtp_port'];

				if(!empty($config['smtp_auth'])){
					$mailer->SMTPSecure = $config['smtp_auth'];
				}
				$mailer->SMTPAuth = (empty($config['smtp_username']) || empty($config['smtp_password']) ? false : true);
				if(!empty($config['smtp_username']) && !empty($config['smtp_password'])){
					$mailer->Username = $config['smtp_username'];
					$mailer->Password = $config['smtp_password'];
				}
			}

			$mailer->AddAddress($email, $rec['username']);
			$mailer->SetFrom((!empty($config['noreply_email']) ? $config['noreply_email'] : $config['site_email']), $config['site_name']);
			$mailer->Subject = $subject;
			$mailer->MsgHTML('<html>
								<body style="font-family: Verdana; color: #333333; font-size: 12px;">
									<table style="width: 400px; margin: 0px auto;">
										<tr style="text-align: center;">
											<td style="border-bottom: solid 1px #cccccc;"><h1 style="margin: 0; font-size: 20px;"><a href="'.$config['site_url'].'" style="text-decoration:none;color:#333333"><b>'.$config['site_name'].'</b></a></h1><h2 style="text-align: right; font-size: 14px; margin: 7px 0 10px 0;">'.$subject.'</h2></td>
										</tr>
										<tr style="text-align: justify;">
											<td style="padding-top: 15px; padding-bottom: 15px;">
												Hello '.$rec['username'].',
												<br /><br />
												You asked to recover your password.<br />To get your new password, access this URL:<br />
												<a href="'.$recover_url.'">'.$recover_url.'</a>
											</td>
										</tr>
										<tr style="text-align: right; color: #777777;">
											<td style="padding-top: 10px; border-top: solid 1px #cccccc;">
												Best Regards!
											</td>
										</tr>
									</table>
								</body>
							</html>');
			$mailer->Send();

			$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_139'].'</div>';
		}
	}
?>
    <main role="main" class="container">
      <div class="row">
		<div class="col-12">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?=($change_pass ? $lang['l_62'] : $lang['l_140'])?>
					</div>
					<div class="content">
						<?=$errMessage?>
						<form method="post">
						<?php
							if($change_pass) {
						?>
						  <div class="form-row">
							<div class="form-group col-md-6">
							  <label for="password"><?=$lang['l_62']?></label>
							  <div class="input-group mb-2 mr-sm-2">
								<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key"></i></div></div>
								<input type="password" class="form-control" id="password" name="pass1" placeholder="X8df!90EO">
							  </div>
							</div>
							<div class="form-group col-md-6">
							  <label for="repeat_password"><?=$lang['l_63']?></label>
							  <div class="input-group mb-2 mr-sm-2">
								<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key"></i></div></div>
								<input type="password" class="form-control" id="repeat_password" name="pass2" placeholder="X8df!90EO">
							  </div>
							</div>
						  </div>
						  <?php 
							if(!empty($config['recaptcha_pub'])){
								echo '<div class="g-recaptcha" data-sitekey="'.$config['recaptcha_pub'].'"></div>';
							}
						  ?>
						  <button type="submit" name="change" class="btn btn-primary"><?=$lang['l_60']?></button>
						<?php } else { ?>
						  <div class="form-row">
							<div class="form-group col-md-6">
							  <label for="email"><?=$lang['l_46']?></label>
							  <div class="input-group mb-2 mr-sm-2">
								<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-envelope"></i></div></div>
								<input type="email" class="form-control" id="email" name="email" placeholder="name@email.com">
							  </div>
							 </div>
						  </div>
						  <?php
							if(!empty($config['recaptcha_pub'])){
								echo '<div class="g-recaptcha" data-sitekey="'.$config['recaptcha_pub'].'"></div>';
							}
						  ?>
						  <button type="submit" name="send" class="btn btn-primary"><?=$lang['l_106']?></button>
						<?php } ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>