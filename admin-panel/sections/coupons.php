<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_GET['edit'])){
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `coupons` WHERE `id`='".$id."'");

		if(isset($_POST['submit']) && !empty($_POST['code']) && !empty($_POST['uses']) && is_numeric($_POST['value']) && is_numeric($_POST['claims'])){
			$code = $db->EscapeString($_POST['code']);
			$uses = $db->EscapeString($_POST['uses']);
			$value = $db->EscapeString($_POST['value']);
			$type = $db->EscapeString($_POST['type']);
			$claims = $db->EscapeString($_POST['claims']);

			$db->Query("UPDATE `coupons` SET `code`='".$code."', `value`='".$value."', `uses`='".$uses."', `type`='".$type."', `claims`='".$claims."' WHERE `id`='".$id."'");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Coupon was successfuly updated!</div>';
		}
	}elseif(isset($_GET['del']) && is_numeric($_GET['del'])){
		$del = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `coupons` WHERE `id`='".$del."'");
		$db->Query("DELETE FROM `coupons_used` WHERE `coupon_id`='".$del."'");
	}

	if(isset($_POST['add_coupon']))
	{
		$code = $db->EscapeString($_POST['code']);
		$value = $db->EscapeString($_POST['value']);
		$uses = $db->EscapeString($_POST['uses']);
		$type = $db->EscapeString($_POST['type']);
		$claims = $db->EscapeString($_POST['claims']);
	
		if(is_numeric($value) && $value > 0 && !empty($uses) && !empty($code) && is_numeric($claims)){
			$db->Query("INSERT INTO `coupons`(`code`,`value`,`uses`,`type`,`claims`) values('".$code."', '".$value."', '".$uses."', '".$type."', '".$claims."')");
			$message = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Your coupon code is: '.$code.'</div>';
		}else{
			$message = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> You have to complete all fields!</div>';
		}
	}

	if(isset($_GET['edit']) && !empty($edit['id'])){
?>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Coupon</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Code</strong></label>
					<div><input type="text" name="code" value="<?=(isset($_POST['code']) ? $_POST['code'] : $edit['code'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Coupon Type</strong><small>Chose between Coins or VIP Days</small></label>
					<div><select name="type"><option value="0">Account Balance (Bits)</option><option value="1"<?=(!isset($_POST['type']) && $edit['type'] == 1 ? ' selected' : (isset($_POST['type']) && $_POST['type'] == 1 ? ' selected' : ''))?>>Purchase Balance (Satoshi)</option></select></div>
				</div>
				<div class="row">
					<label><strong>Coupon Value</strong><small>Bits or Satoshi</small></label>
					<div><input type="text" name="value" value="<?=(isset($_POST['value']) ? $_POST['value'] : $edit['value'])?>" /></div>
				</div>
				<div class="row">
					<label><strong>Requirements</strong><small>Claims required to use this coupon</small></label>
					<div><input type="text" name="claims" value="<?=(isset($_POST['claims']) ? $_POST['claims'] : $edit['claims'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Uses</strong><small>(u = unlimited)</small></label>
					<div><input type="text" name="uses" value="<?=(isset($_POST['uses']) ? $_POST['uses'] : $edit['uses'])?>" /></div>
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
<?php }else{ ?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Coupons</h1>
	<div class="grid_8">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>#</th>
						<th>Coupon Code</th>
						<th>Coupon Value</th>
						<th>Requirements</th>
						<th>Available Uses</th>
						<th>Used</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$coupons = $db->QueryFetchArrayAll("SELECT * FROM `coupons`");

						if(count($coupons) == 0) {
							echo '<tr><td colspan="7" style="text-align: center">Nothing here yet!</td></tr>';
						}

						foreach($coupons as $coupon){
					?>	
					<tr>
						<td><?=$coupon['id']?></td>
						<td><?=$coupon['code']?></td>
						<td><?=$coupon['value'].($coupon['type'] == 1 ? ' Satoshi' : ' Bits')?></td>
						<td><?=($coupon['claims'] > 0 ? $coupon['claims'].' faucet claims' : 'N/A')?></td>
						<td><?=($coupon['uses'] == 'u' ? 'Unlimited' : $coupon['uses'])?></td>
						<td><?=$coupon['used']?> times</td>
						<td class="center">
							<a href="index.php?x=coupons&edit=<?=$coupon['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=coupons&del=<?=$coupon['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="grid_4"><?=$message?>
	<form action="" method="post" class="box">
		<div class="header">
			<h2>Add Coupon</h2>
		</div>
		<div class="content">
			<div class="row">
				<label><strong>Coupon Code</strong></label>
				<div><input type="text" name="code" value="<?php echo (rand(1000,9999).'-'.rand(1000,9999).'-'.rand(1000,9999).'-'.rand(1000,9999)); ?>" required="required" /></div>
			</div>
			<div class="row">
				<label><strong>Coupon Type</strong></label>
				<div><select name="type"><option value="0">Account Balance (Bits)</option><option value="1">Purchase Balance (Satoshi)</option></select></div>
			</div>
			<div class="row">
				<label><strong>Coupon Value</strong><small>Bits or Satoshi</small></label>
				<div><input type="text" name="value" placeholder="10" required="required" /></div>
			</div>
			<div class="row">
				<label><strong>Requirements</strong><small>Claims required to use this coupon</small></label>
				<div><input type="text" name="claims" placeholder="0" required="required" /></div>
			</div>
			<div class="row">
				<label><strong>Uses</strong><small>(u = unlimited)</small></label>
				<div><input type="text" name="uses" placeholder="100" required="required" /></div>
			</div>
		</div>
		<div class="actions">
			<div class="right">
				<input type="submit" value="Add" name="add_coupon" />
			</div>
		</div>
	</form>
</div>
</section>
<?php }?>