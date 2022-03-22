<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_POST['edit'])){
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
				<h2>FaucetPay</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>API Key</strong><small>Your faucet API Key</small></label>
					<div><input type="text" name="set[fp_api_key]" value="<?=$config['fp_api_key']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Username</strong><small>Your FaucetPay Username</small></label>
					<div><input type="text" name="set[faucetpay_username]" value="<?=$config['faucetpay_username']?>" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="edit" value="Submit" />
				</div>
			</div>
		</form>

		<form action="" method="post" class="box">
			<div class="header">
				<h2>KSWallet</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>API Key</strong><small>Your faucet API Key</small></label>
					<div><input type="text" name="set[ks_api_key]" value="<?=$config['ks_api_key']?>" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="edit" value="Submit" />
				</div>
			</div>
		</form>

		<form action="" method="post" class="box">
			<div class="header">
				<h2>CoinPayments</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Merchant ID</strong></label>
					<div><input type="text" name="set[cp_id]" value="<?=$config['cp_id']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>IPN Secret</strong></label>
					<div><input type="text" name="set[cp_secret]" value="<?=$config['cp_secret']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>API Public Key</strong></label>
					<div><input type="text" name="set[cp_public_key]" value="<?=$config['cp_public_key']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>API Private Key</strong></label>
					<div><input type="text" name="set[cp_private_key]" value="<?=$config['cp_private_key']?>" required="required" /></div>
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
				<h2>FaucetPay Instructions</h2>
			</div>
			<div class="content">
				<p>1) Login on your <a href="http://faucetpay.io/" target="_blank">FaucetPay account</a>, go to <i>Faucet Darhboard</i> then add your Faucet.
				<p>2) After you create your Faucet add your <i>API Key</i> here.</p>
			</div>
		</div>
		
		<div class="box">
			<div class="header">
				<h2>KSWallet Instructions</h2>
			</div>
			<div class="content">
				<p>1) Login on your <a href="https://www.kswallet.net/register?r=Hyuga" target="_blank">KSWallet account</a>, go to <i>Owner</i> -> <i>Manage Sites</i> -> <i>Apply For Api Key</i> then follow the instructions</i>.
				<p>2) After your website is approved, add your <i>API Key</i> here.</p>
			</div>
		</div>

		<div class="box">
			<div class="header">
				<h2>CoinPayments Instructions</h2>
			</div>
			<div class="content">
				<p>1) Login on your <a href="https://www.coinpayments.net/index.php?ref=5fdb20fb2dc0efc7d9a302e517c73dc4" target="_blank">Coinayments account</a> and go to <i>Account</i> -> <i>Account Settings</i></p>
				<p>2) Copy <i>Your Merchant ID</i> and complete <i>Merchant ID</i> field.</p>
				<p>3) Go to <i>Merchant Settings</i>, copy <i>IPN Secret</i> and complete <i>IPN Secret</i> field.</p>
				<p>4) Go to <i>Account</i> -> <i>API Keys</i> and click on <i>Generate New Key</i>.</p>
				<p>5) Click on <i>Edit Permissions</i> of your new generate keys and allow access to those permissions: create_transaction, get_callback_address, balances, create_withdrawal and Allow auto_confirm</p>
				<p>6) Copy required fields here with previous generated keys.</p>
			</div>
		</div>
	</div>
</section>