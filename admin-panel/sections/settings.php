<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	$message = '';
	if(isset($_POST['submit'])){
		$posts = $db->EscapeString($_POST['set']);
		foreach ($posts as $key => $value){
			if($config[$key] != $value){
				$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
				$config[$key] = $value;
			}
		}
		
		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings were successfully changed</div>';
		if(isset($_FILES['logo_image']['tmp_name']) && !empty($_FILES['logo_image']['tmp_name']))
		{
			$MAX_SIZE = 250;
			function getExtension($str) {
				if($str == 'image/jpeg'){
					return 'jpg';
				}elseif($str == 'image/png'){
					return 'png';
				}elseif($str == 'image/gif'){
					return 'gif';
				}
			}
			
			$tmpFile = $_FILES['logo_image']['tmp_name'];
			$b_info = getimagesize($tmpFile);
			$extension = getExtension($b_info['mime']);
			
			if($b_info[0] < 150 || $b_info[0] > 155 || $b_info[1] != 36)
			{
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Your logo must have exactly 152x36 px!</div>';
			}
			elseif($b_info['mime'] != 'image/jpeg' && $b_info['mime'] != 'image/png' && $b_info['mime'] != 'image/gif')
			{
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Your image must be png, gif or jpg!</div>';
			}
			elseif(filesize($tmpFile) > $MAX_SIZE*1024)
			{
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Your logo must have under '.$MAX_SIZE.' KB!</div>';
			}
			else
			{
				$image_name = 'logo_'.time().'.'.$extension;
				$copied = copy($tmpFile, BASE_PATH.'/files/logo/'.$image_name);
				
				if(!$copied){
					$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Image wasn\'t uploaded, make sure that you set files permissions to 777 for "files/levels/"!</div>';
				}
				else
				{
					$db->Query("UPDATE `site_config` SET `config_value`='files/logo/".$image_name."' WHERE `config_name`='logo_image'");
					
					$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings were successfully changed</div>';
				}
			}
		}
	}
	
	if(isset($_POST['change_design'])){
		if(!file_exists(BASE_PATH.'/template/'.$config['theme'].'/static/theme.php'))
		{
			$message = '<div class="alert error"><span class="icon"></span><strong>ERROR!</strong> /template/'.$config['theme'].'/static/theme.php doesn\'t exists!</div>';
		}
		else
		{
			$posts = $db->EscapeString($_POST['set']);
			$modified = false;
			foreach ($posts as $key => $value)
			{
				if($config[$key] != $value)
				{
					if(($key == 'bg_color' && strlen($value) != 6 && ctype_alnum($value)) || ($key == 'title_color' && strlen($value) != 6 && ctype_alnum($value)))
					{
						$message = '<div class="alert error"><span class="icon"></span><strong>ERROR!</strong> Your color code must have 6 alphanumeric characters!</div>';
					}
					else
					{
						$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
						$config[$key] = $value;
						$modified = true;
					}
				}
			}
			
			if($modified == true)
			{
				include('inc/generate_css.php');
				$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Website colors were successfully changed, press CTRL + F5 on your website to see your changes!</div>';
			}
		}
	}
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_6">
		<form method="post" enctype="multipart/form-data" class="box">
			<div class="header">
				<h2>General Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Site Name</strong><small>Eg.: ClaimBits</small></label>
					<div><input type="text" name="set[site_logo]" value="<?=$config['site_logo']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Site Title</strong><small>Eg.: ClaimBits - Earn Bitcoin!</small></label>
					<div><input type="text" name="set[site_name]" value="<?=$config['site_name']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Site Logo</strong><small>152x36px - PNG, JPG or GIF</small></label>
					<div><input type="file" name="logo_image" /></div>
				</div>
				<div class="row">
					<label><strong>Site Description</strong><small>Website meta description</small></label>
					<div><textarea name="set[site_description]" required><?=$config['site_description']?></textarea></div>
				</div>
				<div class="row">
					<label><strong>Site Keywords</strong><small>Meta keywords separated by comma</small></label>
					<div><textarea name="set[site_keywords]" required><?=$config['site_keywords']?></textarea></div>
				</div>
				<div class="row">
					<label><strong>Non-Secure Site URL (HTTP)</strong><small>Without trailing slash</small></label>
					<div><input type="text" name="set[site_url]" value="<?=$config['site_url']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Secure Site URL (HTTPS)</strong><small>Without trailing slash (set HTTP if SSL not installed)</small></label>
					<div><input type="text" name="set[secure_url]" value="<?=$config['secure_url']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Redirect non-secure to secure</strong><small>Force using HTTPS connection</small></label>
					<div><select name="set[force_secure]"><option value="0">Disabled</option><option value="1"<?=($config['force_secure'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>SEO Friendly URLs</strong><small>mod_rewrite must be enabled</small></label>
					<div><select name="set[mod_rewrite]"><option value="0">Disabled</option><option value="1"<?=($config['mod_rewrite'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Site Currency</strong></label>
					<div><select name="set[currency]" disabled><option value="<?=getCurrency()?>"><?=getCurrency('name')?></option></select></div>
				</div>
				<div class="row">
					<label><strong>Default Language</strong></label>
					<div><select name="set[def_lang]"><?=$set_def_lang?></select></div>
				</div>
				<div class="row">
					<label><strong>Analytics ID</strong><small>Google Analytics tracking ID</small></label>
					<div><input type="text" name="set[analytics_id]" value="<?=$config['analytics_id']?>" placeholder="Leave blank to disable!" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Website Design Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Background Color</strong><small>Color will be changed on whole website</small></label>
					<div><input type="text" name="set[bg_color]" value="<?=$config['bg_color']?>" class="jscolor" /></div>
				</div>
				<div class="row">
					<label><strong>Title Background Color</strong><small>Color will be changed on whole website</small></label>
					<div><input type="text" name="set[title_color]" value="<?=$config['title_color']?>" class="jscolor" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="change_design" onclick="return confirm('Any customisation done to theme.css will be lost. Do you want to continue?');" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Database Optimisation</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Delete Inactive Users</strong><small>Delete users inactive for X days (0 = disabled)<br />WARNING: You can't restore removed users!</small></label>
					<div><input type="text" name="set[cron_users]" value="<?=$config['cron_users']?>" maxlength="3" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Login Attempts</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Max login attempts</strong><small>How many times user can try to login before getting blocked!</small></label>
					<div><input type="text" name="set[login_attempts]" value="<?=$config['login_attempts']?>" maxlength="3" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Login Cooldown</strong><small>How many minutes user must way before he can try again to login!</small></label>
					<div><input type="text" name="set[login_wait_time]" value="<?=$config['login_wait_time']?>" maxlength="3" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Referral System</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Max Inactivity</strong><small>Maximum inactivity hours for commissions</small></label>
					<div><input type="text" name="set[ref_activity]" value="<?=$config['ref_activity']?>" required /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_6">
		<form method="post" class="box">
			<div class="header">
				<h2>Registration Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>1 account per IP</strong><small>Disable multiple accounts?</small></label>
					<div><select name="set[more_per_ip]"><option value="0">Yes</option><option value="1"<?=($config['more_per_ip'] != 0 ? ' selected' : '')?>>No</option></select></div>
				</div>
				<div class="row">
					<label><strong>Email Confirmation</strong><small>Require email confirmation?</small></label>
					<div><select name="set[reg_reqmail]"><option value="1">Enabled</option><option value="0"<?=($config['reg_reqmail'] != 1 ? ' selected' : '')?>>Disabled</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Withdrawal Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Bits Value</strong><small>1 Bit = ? <?=getCurrency()?> Satoshi</small></label>
					<div><input type="text" name="set[bits_rate]" value="<?=$config['bits_rate']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Required Faucet Claims</strong><small>To be able to withdraw</small></label>
					<div><input type="text" name="set[withdraw_min_claims]" value="<?=$config['withdraw_min_claims']?>" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Deposit Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Minimum Amount to Deposit</strong><small><?=getCurrency('name')?></small></label>
					<div><input type="text" name="set[deposit_min]" value="<?=$config['deposit_min']?>" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Membership Discount</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Months to discount</strong><small>Minimum months purchased to get the discount</small></label>
						<div><input type="text" name="set[months_to_discount]" value="<?=$config['months_to_discount']?>" required="required" /></div>
					</div>
                </div>
				<div class="content">
					<div class="row">
						<label><strong>Discount (0% - 90%)</strong><small> 0 = disabled, maximum 90% discount</small></label>
						<div><input type="text" name="set[vip_discount]" value="<?=$config['vip_discount']?>" required="required" /></div>
					</div>
                </div>
				<div class="actions">
					<div class="right">
						<input type="submit" value="Submit" name="submit" />
					</div>
				</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Penalty System</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Inactivity Time</strong><small>After how many days of inactivity users will be affected</small></label>
					<div><input type="text" name="set[penalty_time]" value="<?=$config['penalty_time']?>" maxlength="3" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Penalty Amount</strong><small>Amount of money deducted every day</small></label>
					<div><input type="text" name="set[penalty_amount]" value="<?=$config['penalty_amount']?>" maxlength="5" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Funds Transfer</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Minimum</strong><small>in Bits</small></label>
					<div><input type="text" name="set[transfer_min]" value="<?=$config['transfer_min']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Status</strong></label>
					<div><select name="set[transfer_status]"><option value="0">Disabled</option><option value="1"<?=($config['transfer_status'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Investment Game</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Minimum Investment</strong><small>Bits</small></label>
					<div><input type="text" name="set[invest_min]" value="<?=$config['invest_min']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Maximum Investment</strong><small>Bits</small></label>
					<div><input type="text" name="set[invest_max]" value="<?=$config['invest_max']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Investment Win</strong><small>Win x Invested amount</small></label>
					<div><input type="text" name="set[invest_win]" value="<?=$config['invest_win']?>" required /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
</section>