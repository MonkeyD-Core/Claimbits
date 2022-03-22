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
	}
	
	$page = (isset($_GET['p']) ? $_GET['p'] : 0);
	$limit = 25;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$total_pages = $db->QueryGetNumRows("SELECT `id` FROM `purchased_referrals`");
	include('../system/libs/Paginator.php');

	$urlPattern = GetHref('p=(:num)');
	$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
	$paginator->setMaxPagesToShow(5);
?>
<section id="content" class="container_12 clearfix ui-sortable" data-sort=true>
	<h1 class="grid_12">Sold Referrals (<?php echo $total_pages; ?>)</h1>
	<div class="grid_8">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>#</th>
						<th>Referral</th>
						<th>Buyer</th>
						<th>Price</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$users = $db->QueryFetchArrayAll("SELECT a.*, b.username AS buyer, c.username AS referral FROM purchased_referrals a LEFT JOIN users b ON b.id = a.user_id LEFT JOIN users c ON c.id = a.ref_id ORDER BY a.time DESC LIMIT ".$start.",".$limit);

						$j = 0;
						foreach($users as $user){
							$j++;
					?>		
					<tr>
						<td><?=$j?></td>
						<td><a href="index.php?x=users&edit=<?=$user['ref_id']?>"><?=$user['referral']?></a></td>
						<td><a href="index.php?x=users&edit=<?=$user['user_id']?>"><?=$user['buyer']?></a></td>
						<td><?=number_format($user['price'], 8).' '.getCurrency()?></td>
						<td><?=date('d M Y - H:i', $user['time'])?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php if($total_pages > $limit){ ?>
			<div class="dataTables_wrapper">
			<div class="footer">
				<div class="dataTables_paginate paging_full_numbers">
				<?php 
					if ($paginator->getPrevUrl()) {
						echo '<a class="first paginate_button" href="'.$paginator->getPrevUrl().'">&laquo; Previous</a></li>';
					} else {
						echo '<a class="first paginate_button">&laquo; Previous</a>';
					}
					
					echo '<span>';

					foreach ($paginator->getPages() as $page) {
						if ($page['url']) {
							if($page['isCurrent']) {
								echo '<a class="paginate_active">'.$page['num'].'</a>';
							} else {
								echo '<a class="paginate_button" href="'. $page['url'].'">'.$page['num'].'</a>';
							}
						} else {
							echo '<a class="paginate_active">'.$page['num'].'</a>';
						}
					}
					
					echo '<span>';
					
					if ($paginator->getNextUrl()) {
						echo '<a class="last paginate_button" href="'.$paginator->getNextUrl().'">Next &raquo;</a></li>';
					}
				?>
				</div>
			</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="grid_4">
		<?php echo $message; ?>
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Settings</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Referral Price</strong><small>Price with up to 8 decimals</small></label>
						<div><input type="text" name="set[market_price]" value="<?=$config['market_price']?>" placeholder="0.00010000" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Required Activity</strong><small>Sell only referrals active in past X days</small></label>
						<div><input type="text" name="set[market_days]" value="<?=$config['market_days']?>" placeholder="7" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Required Claims</strong><small>Sell only referrals with at least X claims</small></label>
						<div><input type="text" name="set[market_claims]" value="<?=$config['market_claims']?>" placeholder="2" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Required Shortlink Visits</strong><small>Sell only referrals with at least X shortlink visits</small></label>
						<div><input type="text" name="set[market_sl]" value="<?=$config['market_sl']?>" placeholder="5" required="required" /></div>
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