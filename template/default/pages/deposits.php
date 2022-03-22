<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
?> 
	<main role="main" class="container">
      <div class="row">
		<?php 
			require_once(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		?>
		<div class="col-xl-9 col-lg-8 col-md-7">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box" class="mt-2">
					<div class="title">
						<?=$lang['l_328']?>
					</div>
					<div class="content">
						<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-dark">
								<tr>
									<th>#</th>
									<th><?=$lang['l_159']?></th>
									<th><?=$lang['l_96']?></th>
									<th><?=$lang['l_67']?></th>
									<th><?=$lang['l_329']?></th>
								</tr>
							</thead>
							<tbody class="table-primary text-dark">
							<?php
								$trans = $db->QueryFetchArrayAll("SELECT * FROM `deposits` WHERE `user_id`='".$data['id']."' ORDER BY `time` DESC LIMIT 25");
								if(count($trans) == 0)
								{ 
									echo '<tr><td colspan="6" class="text-center"><b>'.$lang['l_121'].'</b></td><tr>';
								}
								else
								{
									foreach($trans as $tran){
							?>	
								<tr>
									<td><?php echo $tran['id']; ?></td>
									<td class="text-success"><?php echo $tran['amount']; ?><i class="<?php echo getCurrency('icon_class'); ?> fa-fw"></i></td>
									<td><span class="badge badge-light"><?php echo (empty($tran['txn_id']) ? 'N/A' : $tran['txn_id']); ?></span></td>
									<td><?=($tran['status'] == 1 ? '<span class="text-success">'.$lang['l_330'].'</span>' : ($tran['status'] == 2 ? '<span class="text-primary">'.$lang['l_71'].'</span>' : '<span class="text-danger">'.$lang['l_331'].'</span>'))?></td>
									<td><?=date('d M Y - H:i', $tran['time'])?></td>
								</tr>
							<?php 
									}
								}
								
								$total = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`amount`) AS `amount` FROM `deposits` WHERE `user_id`='".$data['id']."'");
							?>
							</tbody>
							<tfoot class="bg-info">
								<tr>
									<th colspan="3"><?php echo $lang['l_238'].': '.number_format($total['total'], 0); ?></th>
									<th colspan="2"><?php echo $lang['l_222'].': '.number_format($total['amount'], 8).' '.getCurrency(); ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>