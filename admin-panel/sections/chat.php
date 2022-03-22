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
				<h2>ChatBro Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Chat ID</strong></label>
					<div><input type="text" name="set[chatbro_id]" value="<?=$config['chatbro_id']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Secret Key</strong></label>
					<div><input type="text" name="set[chatbro_key]" value="<?=$config['chatbro_key']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Status</strong></label>
					<div><select name="set[chatbro_status]"><option value="0">Disabled</option><option value="1"<?=($config['chatbro_status'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
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
				<p>1) <a href="https://www.chatbro.com/?a=2430529" target="_blank"><b>Click here</b></a> and signup</p>
				<p>2) Start creating a new chat, complete <i>Title</i> field with <i>Chat</i> then search for <i>Users authentication</i> and enable <i>Spoofing protection</i></p>
				<p>3) From <i>Spoofing protection</i> section copy your secret key and add it on <i>Secret Key</i> field.</p>
				<p>4) Search for <i>Chat menu</i> section and disable <i>Show menu</i> then save your configuration.</p>
				<p>5) Go to <a href="https://www.chatbro.com/en/account/#myChats" target="_blank">List of Chats</a>, copy your chat id (eg. 65FRv) and paste it into <i>Chat ID</i> field.</p>
				<p><small>If you followed the steps, your configuration is done and <a href="https://www.chatbro.com/?a=2430529" target="_blank">ChatBro</a> is now integrated into your website.</small></p>
			</div>
		</div>
	</div>
</section>