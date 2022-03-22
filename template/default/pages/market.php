<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$errMessage = '';
	$total_refs = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `admin`='0' AND `ref`='0' AND `id`!='".$data['id']."' AND `total_claims`>='".$config['market_claims']."' AND `sl_total`>='".$config['market_sl']."' AND (".time()."-`last_activity`) < ".($config['market_days']*86400));
	if(isset($_POST['purchase']))
	{
		$refs = empty($_POST['referrals']) ? 1 : intval($_POST['referrals']);
		$price = $refs * $config['market_price'];
		
		if($refs > 10)
		{
			$errMessage = '<div class="alert alert-danger">'.$lang['l_497'].'</div>';
		}
		elseif($refs < 1)
		{
			$errMessage = '<div class="alert alert-danger">'.$lang['l_498'].'</div>';
		}
		elseif($price > $data['purchase_balance'])
		{
			$errMessage = '<div class="alert alert-danger">'.$lang['l_164'].'</div>';
		}
		else
		{
			$referrals = $db->QueryFetchArrayAll("SELECT `id` FROM `users` WHERE `admin`='0' AND `ref`='0' AND `id`!='".$data['id']."' AND `total_claims`>='".$config['market_claims']."' AND `sl_total`>='".$config['market_sl']."' AND (".time()."-`last_activity`) < ".($config['market_days']*86400)." ORDER BY rand() LIMIT ".$refs);
			$valid_refs = count($referrals);
			
			if($valid_refs < $refs)
			{
				$errMessage = '<div class="alert alert-danger">'.lang_rep($lang['l_499'], array('-REFS-' => $valid_refs)).'</div>';
			}
			else
			{
				$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`-'".$price."' WHERE `id`='".$data['id']."'");
				$db->Query("INSERT INTO `user_transactions` (`user_id`,`type`,`value`,`price`,`date`) VALUES('".$data['id']."', '5', '".$refs."', '".$price."', '".time()."') ");

				$db_query = array();
				foreach($referrals as $referral)
				{
					$db_query[] = "('".$data['id']."','".$referral['id']."','".$config['market_price']."','".time()."')";
					$db->Query("UPDATE `users` SET `ref`='".$data['id']."' WHERE `id`='".$referral['id']."'");
				}
				
				$db_query = implode(',', $db_query);
				$db->Query("INSERT INTO `purchased_referrals` (`user_id`,`ref_id`,`price`,`time`) VALUES ".$db_query);

				$errMessage = '<div class="alert alert-success">'.lang_rep($lang['l_500'], array('-REFS-' => $refs, '-PRICE-' => number_format($price, 8))).'</div>';
			}
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
				<div id="grey-box">
					<div class="title">
						<?=$lang['l_490']?>
					</div>
					<div class="content">
						<?php echo $errMessage; ?>
						<div class="alert alert-info">
							<h5 class="text-center"><b><?php echo $lang['l_493']; ?></b></h5>
							<p><?php echo lang_rep($lang['l_494'], array('-DAYS-' => $config['market_days'], '-CLAIMS-' => $config['market_claims'], '-VISITS-' => $config['market_sl'], '-AVAILABLE-' => $total_refs['total'])); ?></p>
							<small><?php echo $lang['l_496']; ?></small>
						</div>
						<?php 
							if($total_refs['total'] < 1)
							{
								echo '<div class="alert alert-warning">'.$lang['l_495'].'</div>';
							}
							else
							{
						?>
						<form method="post">
							<div class="form-row">
								<div class="form-group col-md-6">
								  <label for="referrals"><?php echo $lang['l_20']; ?></label>
								  <input type="number" class="form-control" name="referrals" id="referrals" min="1" max="10" maxlenght="2" placeholder="1" oninput="get_price()" />
								</div>
							</div>
							<div class="price_block">
								<span class="text" id="priceBlock"><?php echo lang_rep($lang['l_491'], array('-REFS-' => 1, '-PRICE-' => $config['market_price'].' '.getCurrency())); ?></span>
								<span class="pay_block">
									<input type="submit" name="purchase" class="btn btn-success btn-sm" value="<?php echo $lang['l_87']; ?>" />
								</span>
								<div class="clearfix"></div>
							</div>
						</form>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
				$bpp = 20;
				$page = (isset($_GET['x']) ? intval($_GET['x']) : 0);
				$begin = ($page >= 0 ? ($page*$bpp) : 0);
				$purchased_refs = $db->QueryFetchArrayAll("SELECT a.*, b.username, b.reg_time, c.commission FROM purchased_referrals a LEFT JOIN users b ON b.id = a.ref_id LEFT OUTER JOIN ref_commissions c ON c.referral = a.ref_id WHERE a.user_id = '".$data['id']."' ORDER BY a.time DESC LIMIT ".$begin.",".$bpp);
				$total_purchased = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `purchased_referrals` WHERE `user_id`='".$data['id']."'");
				$total_purchased = empty($total_purchased['total']) ? 0 : $total_purchased['total'];
			?>
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?php echo $lang['l_492']; ?> (<?php echo $total_purchased; ?>)
					</div>
					<div class="content">
						<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-dark">
								<tr>
									<th scope="col"><?=$lang['l_153']?></th>
									<th scope="col"><?=$lang['l_241']?></th>
									<th scope="col"><?=$lang['l_501']?></th>
									<th scope="col"><?=$lang['l_242']?></th>
								</tr>
							</thead>
							<tfoot class="thead-dark">
								<tr>
									<th><?=$lang['l_153']?></th>
									<th><?=$lang['l_241']?></th>
									<th><?=$lang['l_501']?></th>
									<th><?=$lang['l_242']?></th>
								</tr>
							</tfoot>
							<tbody class="table-light text-dark">
							<?php
								if(empty($purchased_refs)){
									echo '<tr><td colspan="4" class="text-center">'.$lang['l_121'].'</td></tr>';
								}

								foreach($purchased_refs as $user)
								{
							?>	
								<tr>
									<td class="py-2"><?=$user['username']?></td>
									<td class="py-2"><?=date('d M Y', $user['reg_time'])?></td>
									<td class="py-2"><?=date('d M Y', $user['time'])?></td>
									<td class="py-2 text-success"><b><?=number_format($user['commission'], 0).' '.$lang['l_337']?></b></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						<?php
							if(ceil($total_purchased/$bpp) > 1) {
								if($page == 0) {
									$left = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0)">Previous</a></li>';
								}else{
									$left = '<li class="page-item"><a class="page-link" href="'.GenerateURL('market&x='.($page-1)).'">Previous</a></li>';
								}
								
								$total_pages = (number_format(($total_purchased/$bpp), 0)-1);
								$middle = '<li class="page-item active"><a class="page-link" href="javascript:void(0)">'.($page+1).' - '.($total_pages+1).'</a></li>';

								if($page >= $total_pages) {
									$right = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0)">Next</a></li>';
								}else{
									$right = '<li class="page-item"><a class="page-link" href="'.GenerateURL('market&x='.($page+1)).'">Next</a></li>';
								}
								
								echo '<nav aria-label="navigation"><ul class="pagination justify-content-center">'.$left.$middle.$right.'</ul></nav>';
							}
						?>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>
	<script type="text/javascript">
		function get_price() {
			var refs = $('#referrals').val();
			if(refs >= 1) {
				$.get("system/ajax.php?a=calculateRefs", {
					refs: refs
				}, function(a) {
					$('#refs').html(refs);
					$('#price').html(a);
				})
			}
		}
	</script>