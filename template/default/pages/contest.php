<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Timestamps
	$nextMonth = strtotime('first day of next month'); 
	$nextMonth = date('Y-m-d', $nextMonth);
	$nextMonth = strtotime($nextMonth);
	$nextWeek = strtotime("next Sunday")-60;

	$page_title = $lang['l_278'];
	$remainingTime = date('Y-m-d H:i:s', ($config['contest_duration'] == 0 ? $nextWeek : $nextMonth));
	if(isset($_GET['x']) && $_GET['x'] == 'tasks')
	{
		$page_title = $lang['l_319'];
		$remainingTime = date('Y-m-d H:i:s', ($config['tc_duration'] == 0 ? $nextWeek : $nextMonth));
	}
	elseif(isset($_GET['x']) && $_GET['x'] == 'shortlinks')
	{
		$page_title = $lang['l_424'];
		$remainingTime = date('Y-m-d H:i:s', ($config['sl_duration'] == 0 ? $nextWeek : $nextMonth));
	}
	
	$remainingTime = new DateTime($remainingTime);
	$remainingTime = $remainingTime->getTimestamp() * 1000;
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
				<?php echo $page_title; ?>
			</div>
			<div class="content">
				<?php
					if(isset($_GET['x']) && $_GET['x'] == 'tasks')
					{
						$currentRound = $db->QueryFetchArray("SELECT `id` FROM `tasks_contest` WHERE `end_date`='0' LIMIT 1");
						if(empty($currentRound['id']))
						{
							$db->Query("INSERT IGNORE INTO `tasks_contest` (`start_date`) VALUES ('".time()."')");
						}
				?>
				<div class="box">
					<div class="lotteryTitleBox">
						<?php echo $lang['l_266']; ?>
					</div>
					<div class="lotteryCountdownBox">
					  <div class="timer" id="remainingTime"></div>
					</div>
					<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-light">
								<tr>
									<th scope="col" colspan="5">Top 10 Users</th>
								</tr>
							</thead>
							<thead class="thead-dark">
								<tr>
									<th scope="col" width="20">#</th>
									<th scope="col"><?=$lang['l_153']?></th>
									<th scope="col"><?=$lang['l_320']?></th>
									<th scope="col"><?=$lang['l_67']?></th>
									<th scope="col"><?=$lang['l_276']?></th>
								</tr>
							</thead>
							<tfoot class="thead-dark">
								<tr>
									<th>#</th>
									<th><?=$lang['l_153']?></th>
									<th><?=$lang['l_320']?></th>
									<th><?=$lang['l_67']?></th>
									<th><?=$lang['l_276']?></th>
								</tr>
							</tfoot>
							<tbody class="table-primary text-dark">
							<?php
								$users = $db->QueryFetchArrayAll("SELECT `username`,`tasks_contest` FROM `users` WHERE `tasks_contest` > '0' ORDER BY `tasks_contest` DESC LIMIT 10");

								$prizes = explode(',', $config['tc_prizes']);
								$total_prizes = count($prizes);

								$prize = '-';
								for ($i = 0; $i < 10; $i++) {
									if($i < $total_prizes) {
										$prize = number_format($prizes[$i], 2).' '.$lang['l_337'];
									}
									
									switch($i) {
										case 0:
											$place = '<i class="fa fa-trophy fa-fw text-success" aria-hidden="true"></i>';
											break;
										case 1:
											$place = '<i class="fa fa-trophy fa-fw text-secondary" aria-hidden="true"></i>';
											break;
										case 2:
											$place = '<i class="fa fa-trophy fa-fw text-danger" aria-hidden="true"></i>';
											break;
										default:
											$place = ($i+1);
											break;
									}
							?>
								<tr>
									<td><?=$place?></td>
									<td><?=(empty($users[$i]['username']) ? '-' : $users[$i]['username'])?></td>
									<td><?=(empty($users[$i]['tasks_contest']) ? '-' : number_format($users[$i]['tasks_contest']))?></td>
									<td><?=(!empty($users[$i]['tasks_contest']) && $users[$i]['tasks_contest'] >= $config['tc_points'] ? '<font class="text-success">Qualified</font>' : '<font class="text-danger">Not Qualified</font>')?></td>
									<td class="text-success"><b><?=$prize?></b></td>
								</tr>
							<?php 
									$prize = '-';
								}
							?>
							</tbody>
						</table>
				</div>
				<?php
					$previousRound = $db->QueryFetchArray("SELECT * FROM `tasks_contest` WHERE `end_date`>'0' ORDER BY `end_date` DESC LIMIT 1");
					
					if(!empty($previousRound['winners'])) {
						echo '<h1 class="text-warning mt-1 mb-3"><i class="fa fa-gift"></i> '.$lang['l_438'].' <i class="fa fa-gift"></i></h1>';
						echo '<div class="row">';

						$winners = explode(',', $previousRound['winners']);
						$prizes = explode(',', $previousRound['prizes']);

						$i = 0;
						$total_winners = count($winners);
						foreach($winners as $winner) {
							$user = $db->QueryFetchArray("SELECT `username` FROM `users` WHERE `id`='".$winner."' LIMIT 1");
							
							$width = 'col-lg-4 col-md-6 col-12';
							if($total_winners >= 3)
							{
								if($i < 3) 
								{
									$width = 'col-lg-6 col-md-6 col-12';
								}
								elseif($i > 5) 
								{
									$width = 'col-lg-3 col-md-6 col-12';
								}
							}
				?>
					<div class="<?php echo $width; ?>">
						<div class="winner_block w-100">
							<div class="inside">
								<div class="ribbon-green"><?=($i+1)?></div>
								<p class="winner text-left"><?=$user['username']?></p>
								<h4 class="text-center pt-3"><?php echo number_format($prizes[$i], 2).' <small>'.$lang['l_337'].'</small>'; ?></h4>
								<p class="text-center"><small><?=date('d M Y', $previousRound['end_date'])?></small></p>
							</div>
						</div>
					</div>
				<?php 
							$i++;
							
							if($total_winners >= 3 && $i == 1)
							{
								echo '</div><div class="row justify-content-md-center">';
							}
						}
						
						echo '</div>';
					}
				?>
				<div class="clearfix"></div>
				<div class="box">
					<small><?php echo lang_rep(($config['tc_duration'] == 0 ? $lang['l_321'] : $lang['l_444']), array('-POINTS-' => number_format($config['tc_points']))); ?></small>
				</div>
				<?php
					}
					elseif(isset($_GET['x']) && $_GET['x'] == 'shortlinks')
					{
						$currentRound = $db->QueryFetchArray("SELECT `id` FROM `shortlinks_contest` WHERE `end_date`='0' LIMIT 1");
						if(empty($currentRound['id']))
						{
							$db->Query("INSERT IGNORE INTO `shortlinks_contest` (`start_date`) VALUES ('".time()."')");
						}
				?>
				<div class="box">
					<div class="lotteryTitleBox">
						<?php echo $lang['l_266']; ?>
					</div>
					<div class="lotteryCountdownBox">
					  <div class="timer" id="remainingTime"></div>
					</div>
					<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-light">
								<tr>
									<th scope="col" colspan="5">Top 10 Users</th>
								</tr>
							</thead>
							<thead class="thead-dark">
								<tr>
									<th scope="col" width="20">#</th>
									<th scope="col"><?=$lang['l_153']?></th>
									<th scope="col"><?=$lang['l_320']?></th>
									<th scope="col"><?=$lang['l_67']?></th>
									<th scope="col"><?=$lang['l_276']?></th>
								</tr>
							</thead>
							<tfoot class="thead-dark">
								<tr>
									<th>#</th>
									<th><?=$lang['l_153']?></th>
									<th><?=$lang['l_320']?></th>
									<th><?=$lang['l_67']?></th>
									<th><?=$lang['l_276']?></th>
								</tr>
							</tfoot>
							<tbody class="table-primary text-dark">
							<?php
								$users = $db->QueryFetchArrayAll("SELECT `username`,`shortlinks_contest` FROM `users` WHERE `shortlinks_contest` > '0' ORDER BY `shortlinks_contest` DESC LIMIT 10");

								$prizes = explode(',', $config['sl_prizes']);
								$total_prizes = count($prizes);

								$prize = '-';
								for ($i = 0; $i < 10; $i++) {
									if($i < $total_prizes) {
										$prize = number_format($prizes[$i], 2).' '.$lang['l_337'];
									}
									
									switch($i) {
										case 0:
											$place = '<i class="fa fa-trophy fa-fw text-success" aria-hidden="true"></i>';
											break;
										case 1:
											$place = '<i class="fa fa-trophy fa-fw text-secondary" aria-hidden="true"></i>';
											break;
										case 2:
											$place = '<i class="fa fa-trophy fa-fw text-danger" aria-hidden="true"></i>';
											break;
										default:
											$place = ($i+1);
											break;
									}
							?>
								<tr>
									<td><?=$place?></td>
									<td><?=(empty($users[$i]['username']) ? '-' : $users[$i]['username'])?></td>
									<td><?=(empty($users[$i]['shortlinks_contest']) ? '-' : number_format($users[$i]['shortlinks_contest']))?></td>
									<td><?=(!empty($users[$i]['shortlinks_contest']) && $users[$i]['shortlinks_contest'] >= $config['sl_points'] ? '<font class="text-success">Qualified</font>' : '<font class="text-danger">Not Qualified</font>')?></td>
									<td class="text-success"><b><?=$prize?></b></td>
								</tr>
							<?php 
									$prize = '-';
								}
							?>
							</tbody>
						</table>
				</div>
				<?php
					$previousRound = $db->QueryFetchArray("SELECT * FROM `shortlinks_contest` WHERE `end_date`>'0' ORDER BY `end_date` DESC LIMIT 1");
					
					if(!empty($previousRound['winners'])) {
						echo '<h1 class="text-warning mt-1 mb-3"><i class="fa fa-gift"></i> '.$lang['l_438'].' <i class="fa fa-gift"></i></h1>';
						echo '<div class="row justify-content-md-center">';

						$winners = explode(',', $previousRound['winners']);
						$prizes = explode(',', $previousRound['prizes']);

						$i = 0;
						$total_winners = count($winners);
						foreach($winners as $winner) {
							$user = $db->QueryFetchArray("SELECT `username` FROM `users` WHERE `id`='".$winner."' LIMIT 1");
							
							$width = 'col-lg-4 col-md-6 col-12';
							if($total_winners >= 3)
							{
								if($i < 3) 
								{
									$width = 'col-lg-6 col-md-6 col-12';
								}
								elseif($i > 5) 
								{
									$width = 'col-lg-3 col-md-6 col-12';
								}
							}
				?>
					<div class="<?php echo $width; ?>">
						<div class="winner_block w-100">
							<div class="inside">
								<div class="ribbon-green"><?=($i+1)?></div>
								<p class="winner text-left"><?=$user['username']?></p>
								<h4 class="text-center pt-3"><?php echo number_format($prizes[$i], 2).' <small>'.$lang['l_337'].'</small>'; ?></h4>
								<p class="text-center"><small><?=date('d M Y', $previousRound['end_date'])?></small></p>
							</div>
						</div>
					</div>
				<?php
							$i++;
							
							if($total_winners >= 3 && $i == 1)
							{
								echo '</div><div class="row justify-content-md-center">';
							}
						}
						
						echo '</div>';
					}
				?>
				<div class="clearfix"></div>
				<div class="box">
					<small><?php echo lang_rep(($config['sl_duration'] == 0 ? $lang['l_423'] : $lang['l_446']), array('-POINTS-' => number_format($config['sl_points']))); ?></small>
				</div>
				<?php
					}
					else 
					{
						$currentRound = $db->QueryFetchArray("SELECT `id`, `start_date` FROM `referral_contest` WHERE `end_date`='0' LIMIT 1");
						$contestStart = $currentRound['start_date'];
						if(empty($currentRound['id']))
						{
							$db->Query("INSERT IGNORE INTO `referral_contest` (`start_date`) VALUES ('".time()."')");
							$contestStart = time();
						}
				?>
				<div class="box">
					<div class="lotteryTitleBox">
						<?php echo $lang['l_266']; ?>
					</div>
					<div class="lotteryCountdownBox">
					  <div class="timer" id="remainingTime"></div>
					</div>
					<table class="table table-striped table-sm table-responsive-sm text-center">
							<thead class="thead-light">
								<tr>
									<th scope="col" colspan="5">Top 10 Users</th>
								</tr>
							</thead>
							<thead class="thead-dark">
								<tr>
									<th scope="col" width="20">#</th>
									<th scope="col"><?=$lang['l_153']?></th>
									<th scope="col"><?=$lang['l_20']?></th>
									<th scope="col"><?=$lang['l_67']?></th>
									<th scope="col"><?=$lang['l_276']?></th>
								</tr>
							</thead>
							<tfoot class="thead-dark">
								<tr>
									<th>#</th>
									<th><?=$lang['l_153']?></th>
									<th><?=$lang['l_20']?></th>
									<th><?=$lang['l_67']?></th>
									<th><?=$lang['l_276']?></th>
								</tr>
							</tfoot>
							<tbody class="table-primary text-dark">
							<?php
								$users = $db->QueryFetchArrayAll("SELECT a.ref, b.username, COUNT(a.id) AS total FROM users a INNER JOIN users b ON b.id = a.ref WHERE a.ref != '0' AND a.reg_time >= '".$contestStart."' AND a.total_claims >= '".$config['contest_claims']."' AND a.disabled = '0' GROUP BY a.ref ORDER BY total DESC LIMIT 10");

								$prizes = explode(',', $config['contest_prizes']);
								$total_prizes = count($prizes);

								$prize = '-';
								for ($i = 0; $i < 10; $i++) {
									if($i < $total_prizes) {
										$prize = number_format($prizes[$i], 2).' '.$lang['l_337'];
									}
									
									switch($i) {
										case 0:
											$place = '<i class="fa fa-trophy fa-fw text-success" aria-hidden="true"></i>';
											break;
										case 1:
											$place = '<i class="fa fa-trophy fa-fw text-secondary" aria-hidden="true"></i>';
											break;
										case 2:
											$place = '<i class="fa fa-trophy fa-fw text-danger" aria-hidden="true"></i>';
											break;
										default:
											$place = ($i+1);
											break;
									}
							?>
								<tr>
									<td><?=$place?></td>
									<td><?=(empty($users[$i]['username']) ? '-' : $users[$i]['username'])?></td>
									<td><?=(empty($users[$i]['total']) ? '-' : number_format($users[$i]['total']))?></td>
									<td><?=(!empty($users[$i]['total']) && $users[$i]['total'] >= $config['contest_referrals'] ? '<font class="text-success">Qualified</font>' : '<font class="text-danger">Not Qualified</font>')?></td>
									<td class="text-success"><b><?=$prize?></b></td>
								</tr>
							<?php
									$prize = '-';
								}
							?>
							</tbody>
						</table>
				</div>
				<?php
					$previousRound = $db->QueryFetchArray("SELECT * FROM `referral_contest` WHERE `end_date`>'0' ORDER BY `end_date` DESC LIMIT 1");
					
					if(!empty($previousRound['winners'])) {
						echo '<h1 class="text-warning mt-1 mb-3"><i class="fa fa-gift"></i> '.$lang['l_438'].' <i class="fa fa-gift"></i></h1>';
						echo '<div class="row">';

						$winners = explode(',', $previousRound['winners']);
						$prizes = explode(',', $previousRound['prizes']);

						$i = 0;
						$total_winners = count($winners);
						foreach($winners as $winner) {
							$user = $db->QueryFetchArray("SELECT `username` FROM `users` WHERE `id`='".$winner."' LIMIT 1");
							
							$width = 'col-lg-4 col-md-6 col-12';
							if($total_winners >= 3)
							{
								if($i < 3) 
								{
									$width = 'col-lg-6 col-md-6 col-12';
								}
								elseif($i > 5) 
								{
									$width = 'col-lg-3 col-md-6 col-12';
								}
							}
				?>
					<div class="<?php echo $width; ?>">
						<div class="winner_block w-100">
							<div class="inside">
								<div class="ribbon-green"><?=($i+1)?></div>
								<p class="winner text-left"><?=$user['username']?></p>
								<h4 class="text-center pt-3"><?php echo number_format($prizes[$i], 2).' <small>'.$lang['l_337'].'</small>'; ?></h4>
								<p class="text-center"><small><?=date('d M Y', $previousRound['end_date'])?></small></p>
							</div>
						</div>
					</div>
				<?php 
							$i++;
							
							if($total_winners >= 3 && $i == 1)
							{
								echo '</div><div class="row justify-content-md-center">';
							}
						}
						
						echo '</div>';
					}
				?>
				<div class="clearfix"></div>
				<div class="box">
					<small><?php echo lang_rep(($config['contest_duration'] == 0 ? $lang['l_277'] : $lang['l_445']), array('-MIN-' => number_format($config['contest_claims']), '-REFS-' => $config['contest_referrals'])); ?></small>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</main>
<script type="text/javascript">
	$(document).ready(function () {
		var myDate = new Date(<?php echo $remainingTime; ?>);
		myDate.setDate(myDate.getDate());
		$("#remainingTime").countdown(myDate, function (event) {
			$(this).html(
				event.strftime(
					'<div class="timer-wrapper"><div class="time">%D</div><span class="text">days</span></div><div class="timer-wrapper"><div class="time">%H</div><span class="text">hrs</span></div><div class="timer-wrapper"><div class="time">%M</div><span class="text">mins</span></div><div class="timer-wrapper"><div class="time">%S</div><span class="text">sec</span></div>'
				)
			);
		});
	});
</script>