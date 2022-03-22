<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$page = (isset($_GET['p']) ? $_GET['p'] : 0);
	$limit = 20;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$total_pages = $db->QueryGetNumRows("SELECT id FROM `completed_offers`");
	include('../system/libs/Paginator.php');

	$urlPattern = GetHref('p=(:num)');
	$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
	$paginator->setMaxPagesToShow(5);
?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Completed Offers (<?=number_format($total_pages)?>)</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="25">#</th>
						<th>User</th>
						<th>Offer ID</th>
						<th>User Country</th>
						<th>User IP</th>
						<th>Revenue</th>
						<th>Reward</th>
						<th>Offer Wall</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$trans = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM completed_offers a LEFT JOIN users b ON b.id = a.user_id ORDER BY a.timestamp DESC LIMIT ".$start.",".$limit."");
					
					if(!count($trans))
					{
						echo '<tr><td colspan="8"><center>There is no completed offer yet!</center></td></tr>';
					}

					foreach($trans as $tra){
				?>	
					<tr>
						<td><?=$tra['id']?></td>
						<td><?=('<a href="index.php?x=users&edit='.$tra['user_id'].'">'.$tra['username'].'</a>')?></td>
						<td><?=(empty($tra['survey_id']) ? 'Unknown' : $tra['survey_id'])?></td>
						<td><?=(empty($tra['user_country']) ? 'Unknown' : $tra['user_country'])?></td>
						<td><?=(empty($tra['user_ip']) ? 'Unknown' : '<a href="index.php?x=users&s_type=2&su='.$tra['user_ip'].'">'.long2ip($tra['user_ip']).'</a>')?></td>
						<td><?=(empty($tra['revenue']) ? 'Unknown' : '$'.$tra['revenue'])?></td>
						<td><?=number_format($tra['reward'], 2)?> Credits</td>
						<td><?=ucfirst($tra['method'])?></td>
						<td><?=date('d M Y - H:i', $tra['timestamp'])?></td>
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