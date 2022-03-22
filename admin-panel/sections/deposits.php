<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

$page = (isset($_GET['p']) ? $_GET['p'] : 0);
$limit = 20;
$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

$total_pages = $db->QueryGetNumRows("SELECT id FROM `deposits`");
include('../system/libs/Paginator.php');

$urlPattern = GetHref('p=(:num)');
$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
$paginator->setMaxPagesToShow(5);
?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Deposits (<?=number_format($total_pages)?>)</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="25">#</th>
						<th>User</th>
						<th>Transaction ID</th>
						<th>Amount</th>
						<th>Method</th>
						<th>Status</th>
						<th>Date</th>
						<th>User IP</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$trans = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM deposits a LEFT JOIN users b ON b.id = a.user_id ORDER BY a.time DESC LIMIT ".$start.",".$limit."");
					
					if(!count($trans))
					{
						echo '<tr><td colspan="8"><center>There is no deposit yet!</center></td></tr>';
					}

					foreach($trans as $tra){
				?>	
					<tr>
						<td><?=$tra['id']?></td>
						<td><a href="index.php?x=users&edit=<?=$tra['user_id']?>"><?=$tra['username']?></a></td>
						<td><?=(empty($tra['txn_id']) ? 'N/A' : ($tra['method'] == 1 ? $tra['fh_user'].' - #' : '').$tra['txn_id'])?></td>
						<td><?=number_format($tra['amount'], 8).' '.getCurrency()?></td>
						<td><?=($tra['method'] == 1 ? 'FaucetPay' : getCurrency('name'))?></td>
						<td><?=($tra['status'] == 1 ? '<font color="green"><b>Complete</b></font>' : ($tra['status'] == 2 ? '<b>Waiting confirmations...</b>' : '<b>Waiting funds...</b>'))?></td>
						<td><?=date('d M Y - H:i', $tra['time'])?></td>
						<td><?=(empty($tra['user_ip']) ? 'N/A' : '<a href="index.php?x=users&s_type=2&su='.$tra['user_ip'].'">'.$tra['user_ip'].'</a>')?></td>
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
</section>