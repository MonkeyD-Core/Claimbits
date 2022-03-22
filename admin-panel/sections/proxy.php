<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_POST['submit'])){
		$posts = $db->EscapeString($_POST['set']);
		foreach ($posts as $key => $value){
			if($config[$key] != $value){
				$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
				$config[$key] = $db->EscapeString($value);
			}
		}
		
		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings successfully changed</div>';
	}
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_6">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>ProxyCheck.io</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>API Key</strong></label>
					<div><input type="text" name="set[proxycheck]" value="<?=$config['proxycheck']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Status</strong></label>
					<div><select name="set[proxycheck_status]"><option value="0">Disabled</option><option value="1"<?=($config['proxycheck_status'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="submit" value="Submit" />
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
				<p>1) Go to <a href="https://proxycheck.io/" target="_blank">ProxyCheck.io</a> and signup using your email address</p>
				<p>2) Complete <i>API Key</i> field with api key received by email from ProxyCheck.io</p>
				<p><small><a href="https://proxycheck.io/" target="_blank">ProxyCheck.io</a> provides 1000 free queries / day. If you have more than 1000 active users daily, we recommend to upgrade from free plan do another plan who fits your requirements.</small></p>
			</div>
		</div>
	</div>
</section>