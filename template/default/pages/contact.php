<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$errMessage = '<div class="alert alert-info" role="alert">Please use English to send your message, otherwise we won\'t be able to answer!</div>';
	if(isset($_POST['send'])) {

		$captcha_valid = 1;
		if(!empty($config['recaptcha_sec'])){
			$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_sec']);
			$recaptcha = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		
			if($recaptcha->isSuccess()){
				$captcha_valid = 1;
			}else{
				$captcha_valid = 0;
			}
		}

		if(!$captcha_valid){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_107'].'</div>';
		}elseif(empty($_POST['name'])){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_108'].'</div>';
		}elseif(empty($_POST['email'])){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_109'].'</div>';
		}elseif(empty($_POST['message'])){
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_110'].'</div>';
		}else{
			$subject = 'New message from ClaimBits';
			$message = (!empty($data['username']) ? '<b>Sender Username:</b> '.$data['username'].'<br />' : '<b>Sender Name:</b> '.$_POST['name'].'<br />').'<b>Sender Email:</b> '.$_POST['email'].'<br /> <b>Sender IP:</b> '.$_SERVER['REMOTE_ADDR'].'<br />-------------------------------------<br /> <b>Website URL:</b> '.$config['site_url'].'<br /><br /> ---------------Message---------------<br /><br />'.nl2br($_POST['message']);
			$header = "From: ".$_POST['email']."\r\n".
					  "MIME-Version: 1.0\r\n".
					  "Content-Type: text/html;charset=utf-8";
			mail($config['site_email'],$subject,$message,$header);
			$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_111'].'</div>';
		}
	}
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
						<?=$lang['l_30']?>
					</div>
					<div class="content">
						<?=$errMessage?>
						<form method="post">
						  <div class="form-row">
							<div class="form-group col-md-6">
							  <label for="name"><?=$lang['l_112']?></label>
							  <div class="input-group mb-2 mr-sm-2">
								<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-user"></i></div></div>
								<input type="text" class="form-control" id="name" name="name" placeholder="John_Doe" required="required">
							  </div>
							 </div>
						  </div>
						  <div class="form-row">
							<div class="form-group col-md-6">
							  <label for="email"><?=$lang['l_46']?></label>
							  <div class="input-group mb-2 mr-sm-2">
								<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-envelope"></i></div></div>
								<input type="email" class="form-control" id="email" name="email" placeholder="name@domain.com" required="required">
							  </div>
							</div>
						  </div>
						  <div class="form-row">
							<div class="form-group col-md-12">
							  <label for="message"><?=$lang['l_113']?></label>
							  <textarea class="form-control" id="message" name="message" rows="3" required="required"></textarea>
							</div>
						  </div>
							<?php 
								if(!empty($config['recaptcha_pub'])){
									echo '<script src="https://www.google.com/recaptcha/api.js"></script><div class="g-recaptcha mb-2" data-sitekey="'.$config['recaptcha_pub'].'"></div>';
								}
							?>
						  <button type="submit" name="send" class="btn btn-primary"><?=$lang['l_106']?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>