<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
$message = '';
if(isset($_POST['edit_recaptcha'])){
	$posts = $db->EscapeString($_POST['set']);
	foreach ($posts as $key => $value){
		if($config[$key] != $value){
			$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
			$config[$key] = $value;
		}
	}
	
	$message = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Captcha settings successfully saved!</div>';
}

if(isset($_POST['edit_solvemedia'])){
	$posts = $db->EscapeString($_POST['set']);
	foreach ($posts as $key => $value){
		if($config[$key] != $value){
			$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
			$config[$key] = $value;
		}
	}
	
	$message = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Captcha settings successfully saved!</div>';
}

if(isset($_POST['edit_raincaptcha'])){
	$posts = $db->EscapeString($_POST['set']);
	foreach ($posts as $key => $value){
		if($config[$key] != $value){
			$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
			$config[$key] = $value;
		}
	}
	
	$message = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Captcha settings successfully saved!</div>';
}
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_6">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>reCaptcha Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Site Key</strong></label>
					<div><input type="text" name="set[recaptcha_pub]" value="<?=$config['recaptcha_pub']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Secret Key</strong></label>
					<div><input type="text" name="set[recaptcha_sec]" value="<?=$config['recaptcha_sec']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Use on Faucet?</strong></label>
					<div><select name="set[faucet_recaptcha]"><option value="1">Yes</option><option value="0"<?=($config['faucet_recaptcha'] != 1 ? ' selected' : '')?>>No</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="edit_recaptcha" value="Submit" />
				</div>
			</div>
		</form>
		<form action="" method="post" class="box">
			<div class="header">
				<h2>SolveMedia Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>SolveMedia C-Key</strong></label>
					<div><input type="text" name="set[solvemedia_c]" value="<?=$config['solvemedia_c']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>SolveMedia V-Key</strong></label>
					<div><input type="text" name="set[solvemedia_v]" value="<?=$config['solvemedia_v']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>SolveMedia H-Key</strong></label>
					<div><input type="text" name="set[solvemedia_h]" value="<?=$config['solvemedia_h']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Use on Faucet?</strong></label>
					<div><select name="set[faucet_solvemedia]"><option value="1">Yes</option><option value="0"<?=($config['faucet_solvemedia'] != 1 ? ' selected' : '')?>>No</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="edit_solvemedia" value="Submit" />
				</div>
			</div>
		</form>
		<form action="" method="post" class="box">
			<div class="header">
				<h2>rainCaptcha Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Public Key</strong></label>
					<div><input type="text" name="set[raincaptcha_public]" value="<?=$config['raincaptcha_public']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Secret Key</strong></label>
					<div><input type="text" name="set[raincaptcha_secret]" value="<?=$config['raincaptcha_secret']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Use on Faucet?</strong></label>
					<div><select name="set[faucet_raincaptcha]"><option value="1">Yes</option><option value="0"<?=($config['faucet_raincaptcha'] != 1 ? ' selected' : '')?>>No</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="edit_raincaptcha" value="Submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_6">
		<div class="box">
			<div class="header">
				<h2>reCaptcha Instrunctions</h2>
			</div>
			<div class="content">
				<div class="alert information"><span class="icon"></span>reCaptcha will be used to prevent bots, automated registrations and brute force attacks!</div>
				<p><b>1)</b> <a href="https://www.google.com/recaptcha/admin" target="_blank">Click Here</a>, select <i>reCAPTCHA v2 - Checkbox</i>, complete on <i>Domains</i> with your domain name and click on <i>Register</i></p>
				<p><b>2)</b> Copy generated "Site Key" and paste this key on "ReCaptcha Site Key"</p>
				<p><b>3)</b> Copy generated "Secret Key" and paste this key on "ReCaptcha Secret Key"</p>
				<p><b>4)</b> Press on "Submit" and you're done</p>
			</div>
		</div>
		<div class="box">
			<div class="header">
				<h2>SolveMedia Instrunctions</h2>
			</div>
			<div class="content">
				<p><b>1)</b> <a href="https://portal.solvemedia.com/portal/public/signup" target="_blank">Click Here</a>, complete required info and join on this website</p>
				<p><b>2)</b> After you joined on SolveMedia, login and go to <i>Sites</i> then click on <i>Add Site</i></p>
				<p><b>3)</b> Complete with required info and click on <i>Submit</i>. When your website was successfully submited, click on <i>Keys</i> and then configure your SolveMedia captcha system here</p>
			</div>
		</div>
	</div>
</section>