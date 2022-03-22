<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	$errMessage = '<div class="alert alert-info" role="alert">'.$lang['l_433'].'</div>';
	if(isset($_POST['submit']))
	{
		$code = $db->EscapeString($_POST['code']);
		
		$ext = $db->QueryFetchArray("SELECT `id`,`value`,`uses`,`type`,`claims` FROM `coupons` WHERE `code`='".$code."' AND (`uses`>'0' OR `uses`='u') LIMIT 1");
		$used = $db->QueryGetNumRows("SELECT `id` FROM `coupons_used` WHERE `user_id`='".$data['id']."' AND `coupon_id`='".$ext['id']."' LIMIT 1");
		if(empty($ext['id']) || $used > 0)
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> '.$lang['l_432'].'</div>';
		}
		elseif($ext['claims'] != 0 && $ext['claims'] > $data['total_claims'])
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> '.lang_rep($lang['l_436'], array('-NUM-' => number_format($ext['claims']))).'</div>';
		}
		else
		{
			if($ext['type'] == 1)
			{
				$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`+'".($ext['value']/100000000)."' WHERE `id`='".$data['id']."'");
			}
			else
			{
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$ext['value']."' WHERE `id`='".$data['id']."'");
			}

			$db->Query("UPDATE `coupons` SET ".($ext['uses'] != 'u' ? "`uses`=`uses`-'1', " : '')."`used`=`used`+'1' WHERE `id`='".$ext['id']."'");
			$db->Query("INSERT INTO `coupons_used` (`user_id`,`coupon_id`,`time`) VALUES('".$data['id']."','".$ext['id']."','".time()."')");

			$errMessage = '<div class="alert alert-success" role="alert"><i class="fa fa-check"></i> '.lang_rep($lang['l_431'], array('-NUM-' => $ext['value'].' '.($ext['type'] == 1 ? 'Satoshi' : $lang['l_337']))).'</div>';
		}
	}
?> 
<main role="main" class="container">
  <div class="row">
	<?php 
		require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
	?>
	<div class="col-xl-9 col-lg-8 col-md-7">
		<div class="my-3 p-3 bg-white rounded box-shadow box-style">
			<?=$errMessage?>
			<div id="grey-box">
				<div class="title">
					<?=$lang['l_434']?>
				</div>
				<div class="content">
					<form method="post">
					  <div class="form-row">
						<div class="form-group col-md-6">
						  <label for="cpde"><?=$lang['l_434']?></label>
						  <div class="input-group mb-2 mr-sm-2">
							<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-ticket"></i></div></div>
							<input type="text" class="form-control" id="cpde" name="code" placeholder="<?=$lang['l_430']?>">
							<input type="submit" class="btn btn-primary d-inline" name="submit" value="<?=$lang['l_07']?>" />
						  </div>
						 </div>
					  </div>
					</form>
				</div>
			</div>
			<div id="grey-box" class="mt-2">
				<div class="title">
					<?=$lang['l_435']?>
				</div>
				<div class="content">
					<table class="table table-striped table-sm table-responsive-sm text-center">
						<thead class="thead-dark">
							<tr>
								<th>#</th>
								<th><?=$lang['l_430']?></th>
								<th><?=$lang['l_03']?></th>
								<th><?=$lang['l_329']?></th>
							</tr>
						</thead>
						<tfoot  class="thead-dark">
							<tr><th colspan="4" class="text-center"><?=$lang['l_435']?></th></tr>
						</tfoot>
						<tbody class="table-primary text-dark">
						<?php
							$coupons = $db->QueryFetchArrayAll("SELECT a.id,a.time,b.code,b.value,b.type FROM coupons_used a LEFT JOIN coupons b ON b.id = a.coupon_id WHERE a.user_id='".$data['id']."' ORDER BY a.id DESC LIMIT 5");
							if(!$coupons){
								echo '<tr><td colspan="4"><center>'.$lang['l_121'].'</center></td></tr>';
							}

							foreach($coupons as $coupon){
						?>	
							<tr>
								<td><?=$coupon['id']?></td>
								<td><span class="badge badge-light"><?=$coupon['code']?></span></td>
								<td><?=$coupon['value'].' '.($coupon['type'] == 1 ? 'Satoshi' : $lang['l_337'])?></td>
								<td><?=date('d M Y - H:i', $coupon['time'])?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
  </div>
</main>