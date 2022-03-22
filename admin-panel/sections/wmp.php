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
				<h2>WebMinePool</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Site Key</strong></label>
					<div><input type="text" name="set[wmp_key]" value="<?=$config['wmp_key']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Private key</strong></label>
					<div><input type="text" name="set[wmp_secret]" value="<?=$config['wmp_secret']?>" required="required" /></div>
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
				<p>1) Login on your <a href="https://webminepool.com/" target="_blank">WebMinePool account</a> then go to <i>Dashboard</i> -> <i>API Keys</i></p>
				<p>2) Complete <i>Site Key</i> with <i>Sitekey</i></p>
				<p>3) Complete <i>Private Key</i> with <i>Private Key</i></p>
			</div>
		</div>
	</div>
</section>