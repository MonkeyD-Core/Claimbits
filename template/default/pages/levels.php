<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
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
						<?=$lang['l_339']?>
					</div>
					<div class="content">
						<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-dark">
								<tr>
									<th></th>
									<th><?=$lang['l_338']?></th>
									<th><?=$lang['l_207']?></th>
									<th><?=$lang['l_400']?></th>
								</tr>
							</thead>
							<tbody class="table-primary text-dark">
								<?php
									$levels = $db->QueryFetchArrayAll("SELECT * FROM `levels` ORDER BY level ASC");

									foreach($levels as $level){
										echo '<tr'.(userLevel($data['id'], 1, $data['total_claims']) == $level['level'] ? ' class="bg-danger"' : '').'><td><img src="'.$level['image'].'"></td><td>'.$lang['l_338'].' <b>'.$level['level'].'</b></td><td><b>'.number_format($level['requirements']).' '.$lang['l_84'].'</b></td><td class="text-success"><b>x'.$level['reward'].'</b></td></tr>';
									}
								?>
							</tbody>
							<tfoot class="thead-dark">
								<tr>
									<th></th>
									<th><?=$lang['l_338']?></th>
									<th><?=$lang['l_207']?></th>
									<th><?=$lang['l_400']?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>