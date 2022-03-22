<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	if(isset($_GET['ignore']) && is_numeric($_GET['ignore']))
	{
		$ignore = $db->EscapeString($_GET['ignore']);
		$db->Query("UPDATE `ip_checks` SET `checked`='1' WHERE `id`= '".$ignore."' AND `checked`= '0'");
	}

	if(isset($_GET['ignore_u']) && is_numeric($_GET['ignore_u']))
	{
		$user_id = $db->EscapeString($_GET['ignore_u']);
		$db->Query("UPDATE `ip_checks` SET `checked`='1' WHERE `user_id`= '".$user_id."' AND `checked`= '0'");
	}

	$page = (isset($_GET['p']) ? $_GET['p'] : 0);
	$limit = 20;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);
	include('../system/libs/Paginator.php');
	$urlPattern = GetHref('p=(:num)');

	if(isset($_GET['user']) && is_numeric($_GET['user']))
	{
		$user = $db->EscapeString($_GET['user']);
		$total_pages = $db->QueryGetNumRows("SELECT a.id FROM ip_checks a LEFT JOIN users b ON b.id = a.user_id WHERE a.status = '1' AND a.checked = '0' AND b.disabled = '0' AND a.user_id = '".$user."'");
		$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
		$paginator->setMaxPagesToShow(5);
?>
<section id="content" class="container_12 clearfix">
	<div class="grid_12">
		<h1>User #<?php echo $user; ?> - Detected Proxies (<?php echo $total_pages; ?>)</h1>
		<div class="box">
			<table class="styled" style="text-align:center">
				<thead>
					<tr>
						<th>User</th>
						<th>IP Address</th>
						<th>IP Country</th>
						<th>Flag</th>
						<th>Detection Time</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$flags = $db->QueryFetchArrayAll("SELECT a.*, b.username, c.country FROM ip_checks a LEFT JOIN users b ON b.id = a.user_id LEFT JOIN list_countries c ON c.code = a.country_code WHERE a.status = '1' AND a.checked = '0' AND b.disabled = '0' AND a.user_id = '".$user."' ORDER BY a.time DESC LIMIT ".$start.",".$limit);	
					
					if(empty($flags))
					{
						echo '<tr><td colspan="6">There is nothing here yet!</td></tr>';
					}
					
					$j = 0;
					foreach($flags as $flag){
						++$j;
				?>	
					<tr>
						<td><a href="index.php?x=users&edit=<?=$flag['user_id']?>"><?=$flag['username']?></a></td>
						<td><a href="https://whatismyipaddress.com/ip/<?=$flag['ip_address']?>" target="_blank"><?=$flag['ip_address']?></a></td>
						<td><?=(empty($flag['country']) ? 'Unknown' : $flag['country'])?></td>
						<td style="color:red">Proxy / VPN Detected</td>
						<td><?=date('d M Y - H:i', $flag['time'])?></td>
						<td><center><a href="index.php?x=users&edit=<?=$flag['user_id']?>" class="button small grey tooltip" >Edit User</a> <a href="index.php?x=flag&user=<?=$flag['user_id']?>&ignore=<?=$flag['id']?>" class="button small blue tooltip">Ignore</a></center></td>
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

					foreach ($paginator->getPages() as $p) {
						if ($p['url']) {
							if($p['isCurrent']) {
								echo '<a class="paginate_active">'.$p['num'].'</a>';
							} else {
								echo '<a class="paginate_button" href="'. $p['url'].'">'.$p['num'].'</a>';
							}
						} else {
							echo '<a class="paginate_active">'.$p['num'].'</a>';
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
</section>
<?php
	} else {
		$total_pages = $db->QueryGetNumRows("SELECT a.id FROM ip_checks a LEFT JOIN users b ON b.id = a.user_id WHERE a.status = '1' AND a.checked = '0' AND b.disabled = '0' GROUP BY a.user_id");
		$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
		$paginator->setMaxPagesToShow(5);
		
		$order_value = '';
		$order_by = (isset($_GET['oby']) ? $_GET['oby'] : 2);
		$sorting = (isset($_GET['sort']) ? $_GET['sort'] : 'desc');
		if(!empty($sorting) && !empty($order_by)){
			$sort = ($sorting == 'desc' ? 'DESC' : 'ASC');
			if($order_by == 1){
				$order_value = 'total '.$sort;
			}elseif($order_by == 2){
				$order_value = 'a.time '.$sort;
			}
		}
?>
<section id="content" class="container_12 clearfix">
	<div class="grid_12">
		<h1>Users found using VPN / Proxy servers (<?php echo $total_pages; ?>)</h1>
		<div class="box">
			<table class="styled" style="text-align:center">
				<thead>
					<tr>
						<th>User</th>
						<th><a href="index.php?x=flag&sort=<?=($sorting == 'desc' ? 'asc' : 'desc')?>&oby=1">Total Proxies <img src="img/elements/table/sorting<?=($sorting == 'asc' && $order_by == 1 ? '-asc' : ($sorting == 'desc' && $order_by == 1 ? '-desc' : ''))?>.png" border="0" /></a></th>
						<th>Flag</th>
						<th><a href="index.php?x=flag&sort=<?=($sorting == 'desc' ? 'asc' : 'desc')?>&oby=2">Last Detection Time <img src="img/elements/table/sorting<?=($sorting == 'asc' && $order_by == 2 ? '-asc' : ($sorting == 'desc' && $order_by == 2 ? '-desc' : ''))?>.png" border="0" /></a></th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$flags = $db->QueryFetchArrayAll("SELECT COUNT(a.id) AS total, a.user_id, a.ip_address, a.country_code, a.status, a.checked, MAX(a.time) maxtime, b.username, c.country FROM ip_checks a LEFT JOIN users b ON b.id = a.user_id LEFT JOIN list_countries c ON c.code = a.country_code WHERE a.status = '1' AND a.checked = '0' AND b.disabled = '0'".$db_value." GROUP BY a.user_id ORDER BY ".$order_value.", a.time DESC LIMIT ".$start.",".$limit);	
					
					if(empty($flags))
					{
						echo '<tr><td colspan="6">There is nothing here yet!</td></tr>';
					}
					
					$j = 0;
					foreach($flags as $flag){
						++$j;
				?>	
					<tr>
						<td><a href="index.php?x=users&edit=<?=$flag['user_id']?>"><?=$flag['username']?></a></td>
						<td><?=$flag['total']?> proxy IPs detected</td>
						<td style="color:red">Proxy / VPN Detected</td>
						<td><?=date('d M Y - H:i', $flag['maxtime'])?></td>
						<td><center><a href="index.php?x=flag&user=<?=$flag['user_id']?>" class="button small grey">Details</a> <a href="index.php?x=users&edit=<?=$flag['user_id']?>" class="button small blue">Edit User</a> <a href="index.php?x=flag&ignore_u=<?=$flag['user_id']?>" class="button small red">Ignore</a></center></td>
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

					foreach ($paginator->getPages() as $p) {
						if ($p['url']) {
							if($p['isCurrent']) {
								echo '<a class="paginate_active">'.$p['num'].'</a>';
							} else {
								echo '<a class="paginate_button" href="'. $p['url'].'">'.$p['num'].'</a>';
							}
						} else {
							echo '<a class="paginate_active">'.$p['num'].'</a>';
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
</section>
<?php } ?>