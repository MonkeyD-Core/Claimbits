<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$rewards = $db->QueryFetchArrayAll("SELECT a.*, b.membership AS mem_name FROM activity_rewards a LEFT JOIN memberships b ON b.id = a.membership ORDER BY a.req_type ASC, a.requirements ASC");
	$leads = $db->QueryFetchArray("SELECT `total_offers` FROM `users_offers` WHERE `uid`='".$data['id']."' LIMIT 1");
	$refs = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `ref`='".$data['id']."'");

	if(empty($rewards)){
		redirect($config['site_url']);
	}
?> 
	<main role="main" class="container">
      <div class="row">
		<?php 
			require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		?>
	  <div class="col-xl-9 col-lg-8 col-md-7">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<script type="text/javascript">
					function getReward(rID) {
						$("#claim_"+rID).html('<center><i class="fa fa-refresh fa-spin fa-2x fa-fw"></i></center>');
						$("#rewardMSG").html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center><br />');
						$.getJSON('system/ajax.php?a=getReward&rID='+rID, function (c) {
							if(c['type'] == 'success'){
								$("#claim_"+rID).html('<span class="btn btn-primary btn-sm disabled"><?=$lang['l_210']?></span>');
								$("#rewardMSG").html('<div class="alert alert-success" role="alert">' + c['message'] + '</div>')
							} else {
								$("#claim_"+rID).html('<a href="javascript:void(0)" onclick="getReward('+rID+')" class="btn btn-primary btn-sm"><?=$lang['l_208']?></a>');
								$("#rewardMSG").html('<div class="alert alert-danger" role="alert">' + c['message'] + '</div>')
							}
						});
					}
				</script>
				<div id="grey-box">
					<div class="title">
						<?=$lang['l_205']?>
					</div>
					<div class="content">
						<div class="row">
							<div class="col-lg-3 col-md-6 col-12 mb-3">
								<div class="card text-white bg-secondary ">
								  <div class="card-header"><?=$lang['l_235']?></div>
								  <div class="card-body">
									<p class="card-title text-center"><b><?=number_format($data['total_claims'])?></b><br /><?=$lang['l_84']?></p>
								  </div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-12 mb-3">
								<div class="card text-white bg-secondary ">
								  <div class="card-header"><?=$lang['l_226']?></div>
								  <div class="card-body">
									<p class="card-title text-center"><b><?=number_format($data['sl_total'])?></b><br /><?=$lang['l_295']?></p>
								  </div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-12 mb-3">
								<div class="card text-white bg-secondary ">
								  <div class="card-header">Offerwalls</div>
								  <div class="card-body">
									<p class="card-title text-center"><b><?=number_format($leads['total_offers'])?></b><br />leads</p>
								  </div>
								</div>
							</div>
							<div class="col-lg-3 col-md-6 col-12 mb-3">
								<div class="card text-white bg-secondary ">
								  <div class="card-header"><?=$lang['l_20']?></div>
								  <div class="card-body">
									<p class="card-title text-center"><b><?=number_format($refs['total'])?></b><br />referrals</p>
								  </div>
								</div>
							</div>
						</div>
						<div id="rewardMSG"></div>
						<table class="table table-striped table-sm table-responsive-sm">
							<thead class="thead-dark">
								<tr>
									<th class="pl-2"><?=$lang['l_207']?></th>
									<th class="text-center"><?=$lang['l_206']?></th>
									<th class="text-center"></th>
								</tr>
							</thead>
							<tbody class="table-light text-dark">
							<?php
								foreach($rewards as $reward)
								{
									$membership = ($data['membership_id'] > 1 ? $data['mem_name'] : $reward['mem_name']).' days';
									$achievement = getAchievement($reward['req_type'], $reward['requirements'], $reward['reward'], $reward['type'], $membership);
									$claimed = $db->QueryGetNumRows("SELECT * FROM `activity_rewards_claims` WHERE `reward_id`='".$reward['id']."' AND `user_id`='".$data['id']."' LIMIT 1");

									$claimButton = '<span class="btn btn-primary btn-sm disabled">'.$lang['l_211'].'</span>';
									if ($claimed > 0)
									{
										$claimButton = '<span class="btn btn-warning btn-sm disabled">'.$lang['l_210'].'</span>';
									}
									else
									{
										$validClaim = false;
										switch($reward['req_type'])
										{
											case 0:
												if($data['total_claims'] >= $reward['requirements'])
												{
													$validClaim = true;
												}
												break;
											case 1:
												if($data['sl_total'] >= $reward['requirements'])
												{
													$validClaim = true;
												}
												break;
											case 2:
												if($leads['total_offers'] >= $reward['requirements'])
												{
													$validClaim = true;
												}
												break;
											case 3:
												if($refs['total'] >= $reward['requirements'])
												{
													$validClaim = true;
												}
												break;
										}

										if($validClaim)
										{
											$claimButton = '<a href="javascript:void(0)" onclick="getReward('.$reward['id'].')" class="btn btn-success btn-sm">'.$lang['l_208'].'</a>';
										}
									}
							?>
								<tr><td class="text-left align-middle pl-3"><b><?=$achievement['requirement']?></b></td><td class="align-middle text-center"><span class="badge badge-warning py-2"><b><?=$achievement['reward']?></b></span></td><td id="claim_<?=$reward['id']?>" class="text-center"><?=$claimButton?></td></tr>
							<?php } ?>
							</tbody>
							<tfoot class="thead-dark">
								<tr>
									<th class="pl-3"><?=$lang['l_207']?></th>
									<th class="text-center"><?=$lang['l_206']?></th>
									<th class="text-center"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>