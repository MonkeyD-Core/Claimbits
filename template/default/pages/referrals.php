<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$refs = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `ref`='".$data['id']."'");
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
						Referrals (<?=$refs['total']?>)
					</div>
					<div class="content">
						<?php
							$bpp = 20;
							$page = (isset($_GET['x']) ? intval($_GET['x']) : 0);
							$begin = ($page >= 0 ? ($page*$bpp) : 0);
							$users = $db->QueryFetchArrayAll("SELECT a.id,a.username,a.reg_time,b.commission FROM users a LEFT JOIN ref_commissions b ON b.referral = a.id WHERE a.ref = '".$data['id']."' ORDER BY a.reg_time DESC LIMIT ".$begin.",".$bpp);
						?>
						<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-dark">
								<tr>
									<th scope="col"><?=$lang['l_323']?></th>
									<th scope="col"><?=$lang['l_241']?></th>
									<th scope="col"><?=$lang['l_242']?></th>
								</tr>
							</thead>
							<tfoot class="thead-dark">
								<tr>
									<th><?=$lang['l_323']?></th>
									<th><?=$lang['l_241']?></th>
									<th><?=$lang['l_242']?></th>
								</tr>
							</tfoot>
							<tbody class="table-primary text-dark">
							<?php
								if(empty($users)){
									echo '<tr><td colspan="3" class="text-center">'.$lang['l_121'].'</td></tr>';
								}

								foreach($users as $user){
							?>	
								<tr>
									<td>#<?=$user['id']?></td>
									<td><?=date('d M Y - H:i', $user['reg_time'])?></td>
									<td class="text-success"><b><?=number_format($user['commission'], 2).' '.$lang['l_337']?></b></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						<?php
							if(ceil($refs['total']/$bpp) > 1) {
								if($page == 0) {
									$left = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0)">Previous</a></li>';
								}else{
									$left = '<li class="page-item"><a class="page-link" href="'.GenerateURL('referrals&x='.($page-1)).'">Previous</a></li>';
								}
								
								$total_pages = (number_format(($refs['total']/$bpp), 0)-1);
								$middle = '<li class="page-item active"><a class="page-link" href="javascript:void(0)">'.($page+1).' - '.($total_pages+1).'</a></li>';

								if($page >= $total_pages) {
									$right = '<li class="page-item disabled"><a class="page-link" href="javascript:void(0)">Next</a></li>';
								}else{
									$right = '<li class="page-item"><a class="page-link" href="'.GenerateURL('referrals&x='.($page+1)).'">Next</a></li>';
								}
								
								echo '<nav aria-label="navigation"><ul class="pagination justify-content-center">'.$left.$middle.$right.'</ul></nav>';
							}
						?>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>