<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_POST['submit'])){
		$id = $db->EscapeString($_POST['membership_id']);
		$membership = $db->EscapeString($_POST['membership']);
		$multiplier = $db->EscapeString($_POST['multiplier']);
		$ref_com = $db->EscapeString($_POST['ref_com']);
		$lottery_price = $db->EscapeString($_POST['lottery_price']);
		$offer_com = $db->EscapeString($_POST['offer_com']);
		$short_com = $db->EscapeString($_POST['short_com']);
		$fp_min_pay = $db->EscapeString($_POST['fp_min_pay']);
		$ks_min_pay = $db->EscapeString($_POST['ks_min_pay']);
		$btc_min_pay = $db->EscapeString($_POST['btc_min_pay']);
		$hash_rate = $db->EscapeString($_POST['hash_rate']);
		$hide_ads = $db->EscapeString($_POST['hide_ads']);
		$btc_wait_time = $db->EscapeString($_POST['btc_wait_time']);
		$fp_wait_time = $db->EscapeString($_POST['fp_wait_time']);
		$ks_wait_time = $db->EscapeString($_POST['ks_wait_time']);
		$price = $db->EscapeString(isset($_POST['price']) ? $_POST['price'] : 0);

		$db->Query("UPDATE `memberships` SET `membership`='".$membership."', `multiplier`='".$multiplier."', `fp_min_pay`='".$fp_min_pay."', `ks_min_pay`='".$ks_min_pay."', `ref_com`='".$ref_com."', `offer_com`='".$offer_com."', `short_com`='".$short_com."', `btc_min_pay`='".$btc_min_pay."', `btc_wait_time`='".$btc_wait_time."', `fp_wait_time`='".$fp_wait_time."', `ks_wait_time`='".$ks_wait_time."', `hash_rate`='".$hash_rate."', `hide_ads`='".$hide_ads."', `lottery_price`='".$lottery_price."', `price`='".$price."' WHERE `id`='".$id."'");

		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings successfully changed</div>';
	}

	$memberships = $db->QueryFetchArrayAll("SELECT * FROM `memberships` ORDER BY `id` ASC LIMIT 4");
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_3">
		<form action="" method="post" class="box">
			<input type="hidden" name="membership_id" value="1" />
			<div class="header">
				<h2>Basic Membership</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Membership</strong></label>
					<div><input type="text" name="membership" value="<?=$memberships[0]['membership']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Multiplier</strong></label>
					<div><input type="text" name="multiplier" value="<?=$memberships[0]['multiplier']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Faucet Ref Commission</strong></label>
					<div><input type="text" name="ref_com" value="<?=$memberships[0]['ref_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Offerwalls Ref Commision</strong></label>
					<div><input type="text" name="offer_com" value="<?=$memberships[0]['offer_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Shortlinks Ref Commision</strong></label>
					<div><input type="text" name="short_com" value="<?=$memberships[0]['short_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Minimum Payout</strong></label>
					<div><input type="text" name="fp_min_pay" value="<?=$memberships[0]['fp_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Minimum Payout</strong></label>
					<div><input type="text" name="ks_min_pay" value="<?=$memberships[0]['ks_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Minimum Payout</strong></label>
					<div><input type="text" name="btc_min_pay" value="<?=$memberships[0]['btc_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="fp_wait_time" value="<?=$memberships[0]['fp_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="ks_wait_time" value="<?=$memberships[0]['ks_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="btc_wait_time" value="<?=$memberships[0]['btc_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Lottery Ticket Price</strong><small>Bits</small></label>
					<div><input type="text" name="lottery_price" value="<?=$memberships[0]['lottery_price']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>CPU Mining Hash Rate</strong></label>
					<div><input type="text" name="hash_rate" value="<?=$memberships[0]['hash_rate']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Hide Popup Ads</strong></label>
					<div><select name="hide_ads"><option value="0">Disabled</option><option value="1"<?=($memberships[0]['hide_ads'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Monthly Price</strong></label>
					<div><input type="text" name="price" value="<?=$memberships[0]['price']?>" disabled /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_3">
		<form action="" method="post" class="box">
			<input type="hidden" name="membership_id" value="2" />
			<div class="header">
				<h2>Membership Pack 1</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Membership</strong></label>
					<div><input type="text" name="membership" value="<?=$memberships[1]['membership']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Multiplier</strong></label>
					<div><input type="text" name="multiplier" value="<?=$memberships[1]['multiplier']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Faucet Ref Commission</strong></label>
					<div><input type="text" name="ref_com" value="<?=$memberships[1]['ref_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Offerwalls Ref Commision</strong></label>
					<div><input type="text" name="offer_com" value="<?=$memberships[1]['offer_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Shortlinks Ref Commision</strong></label>
					<div><input type="text" name="short_com" value="<?=$memberships[1]['short_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Minimum Payout</strong></label>
					<div><input type="text" name="fp_min_pay" value="<?=$memberships[1]['fp_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Minimum Payout</strong></label>
					<div><input type="text" name="ks_min_pay" value="<?=$memberships[1]['ks_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Minimum Payout</strong></label>
					<div><input type="text" name="btc_min_pay" value="<?=$memberships[1]['btc_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="fp_wait_time" value="<?=$memberships[1]['fp_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="ks_wait_time" value="<?=$memberships[1]['ks_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="btc_wait_time" value="<?=$memberships[1]['btc_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Lottery Ticket Price</strong><small>Bits</small></label>
					<div><input type="text" name="lottery_price" value="<?=$memberships[1]['lottery_price']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>CPU Mining Hash Rate</strong></label>
					<div><input type="text" name="hash_rate" value="<?=$memberships[1]['hash_rate']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Hide Popup Ads</strong></label>
					<div><select name="hide_ads"><option value="0">Disabled</option><option value="1"<?=($memberships[1]['hide_ads'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Monthly Price</strong></label>
					<div><input type="text" name="price" value="<?=$memberships[1]['price']?>" required /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_3">
		<form action="" method="post" class="box">
			<input type="hidden" name="membership_id" value="3" />
			<div class="header">
				<h2>Membership Pack 2</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Membership</strong></label>
					<div><input type="text" name="membership" value="<?=$memberships[2]['membership']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Multiplier</strong></label>
					<div><input type="text" name="multiplier" value="<?=$memberships[2]['multiplier']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Faucet Ref Commission</strong></label>
					<div><input type="text" name="ref_com" value="<?=$memberships[2]['ref_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Offerwalls Ref Commision</strong></label>
					<div><input type="text" name="offer_com" value="<?=$memberships[2]['offer_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Shortlinks Ref Commision</strong></label>
					<div><input type="text" name="short_com" value="<?=$memberships[2]['short_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Minimum Payout</strong></label>
					<div><input type="text" name="fp_min_pay" value="<?=$memberships[2]['fp_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Minimum Payout</strong></label>
					<div><input type="text" name="ks_min_pay" value="<?=$memberships[2]['ks_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Minimum Payout</strong></label>
					<div><input type="text" name="btc_min_pay" value="<?=$memberships[2]['btc_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="fp_wait_time" value="<?=$memberships[2]['fp_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="ks_wait_time" value="<?=$memberships[2]['ks_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="btc_wait_time" value="<?=$memberships[2]['btc_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Lottery Ticket Price</strong><small>Bits</small></label>
					<div><input type="text" name="lottery_price" value="<?=$memberships[2]['lottery_price']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>CPU Mining Hash Rate</strong></label>
					<div><input type="text" name="hash_rate" value="<?=$memberships[2]['hash_rate']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Hide Popup Ads</strong></label>
					<div><select name="hide_ads"><option value="0">Disabled</option><option value="1"<?=($memberships[2]['hide_ads'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Monthly Price</strong></label>
					<div><input type="text" name="price" value="<?=$memberships[2]['price']?>" required /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_3">
		<form action="" method="post" class="box">
			<input type="hidden" name="membership_id" value="4" />
			<div class="header">
				<h2>Membership Pack 3</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Membership</strong></label>
					<div><input type="text" name="membership" value="<?=$memberships[3]['membership']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Multiplier</strong></label>
					<div><input type="text" name="multiplier" value="<?=$memberships[3]['multiplier']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Faucet Ref Commission</strong></label>
					<div><input type="text" name="ref_com" value="<?=$memberships[3]['ref_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Offerwalls Ref Commision</strong></label>
					<div><input type="text" name="offer_com" value="<?=$memberships[3]['offer_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Shortlinks Ref Commision</strong></label>
					<div><input type="text" name="short_com" value="<?=$memberships[3]['short_com']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Minimum Payout</strong></label>
					<div><input type="text" name="fp_min_pay" value="<?=$memberships[3]['fp_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Minimum Payout</strong></label>
					<div><input type="text" name="ks_min_pay" value="<?=$memberships[3]['ks_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Minimum Payout</strong></label>
					<div><input type="text" name="btc_min_pay" value="<?=$memberships[3]['btc_min_pay']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>FH Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="fp_wait_time" value="<?=$memberships[3]['fp_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>KS Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="ks_wait_time" value="<?=$memberships[3]['ks_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong><?=getCurrency()?> Withdraw Wait Time</strong><small>0 = Instant</small></label>
					<div><input type="text" name="btc_wait_time" value="<?=$memberships[3]['btc_wait_time']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Lottery Ticket Price</strong><small>Bits</small></label>
					<div><input type="text" name="lottery_price" value="<?=$memberships[3]['lottery_price']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>CPU Mining Hash Rate</strong></label>
					<div><input type="text" name="hash_rate" value="<?=$memberships[3]['hash_rate']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Hide Popup Ads</strong></label>
					<div><select name="hide_ads"><option value="0">Disabled</option><option value="1"<?=($memberships[3]['hide_ads'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Monthly Price</strong></label>
					<div><input type="text" name="price" value="<?=$memberships[3]['price']?>" required /></div>
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