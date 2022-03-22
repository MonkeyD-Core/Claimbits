<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_POST['edit'])){
		$posts = $db->EscapeString($_POST['set']);
		foreach ($posts as $key => $value){
			if($config[$key] != $value){
				$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
				$config[$key] = $value;
			}
		}
		
		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings successfully changed</div>';
	}
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_6">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Pollfish Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>API Key</strong></label>
					<div><input type="text" name="set[pollfish_key]" value="<?=$config['pollfish_key']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Secret Key</strong></label>
					<div><input type="text" name="set[pollfish_secret]" value="<?=$config['pollfish_secret']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Status</strong></label>
					<div><select name="set[pollfish_enabled]"><option value="0">Disabled</option><option value="1"<?=($config['pollfish_enabled'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="edit" value="Submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_6">
		<div class="box">
			<div class="header">
				<h2>Instructions</h2>
			</div>
			<div class="content">
				<p><b>1)</b> <a href="https://www.pollfish.com/publisher/44017" target="_blank"><b>Click here</b></a> and create an account (or login if you're already registered).</p>
				<p><b>2)</b> Click on <i>Add New App</i> and configure your new app, but make sure you select <i>Web</i> under <i>Choose Platform</i>.</p>
				<p><b>3)</b> After you new app is created, on app settings complete <i>Register for S2S Callbacks</i> field with following URL and make sure you check <i>Notify me when the user is not eligible</i>:<br />
					<input type="text" value="<?php echo $config['site_url']; ?>/system/gateways/pollfish.php?device_id=[[device_id]]&cpa=[[cpa]]&request_uuid=[[request_uuid]]&timestamp=[[timestamp]]&tx_id=[[tx_id]]&signature=[[signature]]&status=[[status]]&reason=[[term_reason]]&reward_name=[[reward_name]]&reward_value=[[reward_value]]" onclick="select()" style="width:100%" />
				</p>
				<p><b>4)</b> Under <i>Integration Approach</i> make sure you select <i>Offerwall</i>, complete with <i>Coins</i> into <i>Currency Name</i> field and set how many coins do you want to earn your users for each $1 you earn, into <i>Variable Amount</i> field.</p>
				<p><b>5)</b> After you apply all those settings, make sure you request your app / account to be reviewed. Until your account is fully reviewed and approved, your users won't receive any coins from completed surveys.</p>
				<p><b>6)</b> To get your <i>Secret Key</i>, go to <i>Account</i> -> <i>Account Information</i>.</p>
			</div>
		</div>
	</div>
</section>