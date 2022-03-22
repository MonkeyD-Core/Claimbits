<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	$refs = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `ref`='".$data['id']."'");
	$cms = $db->QueryFetchArray("SELECT SUM(`commission`) AS `total` FROM `ref_commissions` WHERE `user`='".$data['id']."'");
	
	$secureFormID = array(
			'toggleCaptcha' => GenerateKey(rand(10,15)),
			'captcha' => GenerateKey(rand(10,15)),
			'rollFaucet' => GenerateKey(rand(10,15))
		);
?>
	<main role="main" class="container">
      <div class="row">
		<?php 
			require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		?>
		<div class="col-xl-9 col-lg-8 col-md-7">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
			  <?php
				// Warning message
				if(!empty($data['warn_message'])){
					if($data['warn_expire'] < time()){
						$db->Query("UPDATE `users` SET `warn_message`='', `warn_expire`='0' WHERE `id`='".$data['id']."'");
					}
					
					echo '<div class="alert alert-danger" role="alert">'.$data['warn_message'].'</div>';
				}
				
				// VPN / Proxy Warning
				if(!empty($UserIPData) && $UserIPData['status'] == 1){
					echo '<div class="alert alert-danger text-center" role="alert"><i class="fa fa-exclamation-triangle"></i> <b>'.$lang['l_484'].'</b> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><br />'.$lang['l_485'].'</div>';
				}

				// Announcement
				$announcement = $db->QueryFetchArray("SELECT * FROM `announcement` ORDER BY `time` DESC LIMIT 1");
				if(!empty($announcement)){
					$style = ($announcement['type'] == 1 ? 'success' : ($announcement['type'] == 2 ? 'danger' : 'info'));
					if(empty($announcement['url'])) {
						echo '<div class="alert alert-'.$style.' text-center" role="alert">'.$announcement['message'].'</div>';
					} else {
						echo '<a href="'.$announcement['url'].'" style="text-decoration:none"><div class="alert alert-'.$style.' text-center" role="alert">'.$announcement['message'].'</div></a>';
					}
				}
			  ?>
			  <div id="grey-box">
				<div class="content">
					<h1 class="text-warning"><i class="fa fa-arrow-down"></i> <?php echo lang_rep($lang['l_114'], array('-MIN-' => $config['faucet_time'])); ?> <i class="fa fa-arrow-down"></i></h1>
					<p class="infobox my-4"><?php echo lang_rep($lang['l_115'], array('-MIN-' => $config['faucet_time'])); ?></p>
					<div class="row">
						<div class="col-lg-6 col-sm-12 d-flex align-items-stretch my-2">
							<div class="card text-dark text-center w-100">
							  <div class="card-header">
								<b>Claim FREE Bits</b>
							  </div>
							  <div class="card-body py-3 px-1">
								<?php
									$faucetLocked = false;
									if($data['last_claim'] > (time()-($config['faucet_time']*60))) 
									{
										$last_claim = $db->QueryFetchArray("SELECT `number`,`reward` FROM `faucet_claims` WHERE `user_id`='".$data['id']."' ORDER BY `time` DESC LIMIT 1");
								?>
									<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-circle fa-fw"></i> <?php echo $lang['l_429']; ?> <span id="claimTime"><?php echo remainingTime(($data['last_claim']+($config['faucet_time']*60))-time()); ?></span></div>
									<small><i><?php echo lang_rep($lang['l_428'], array('-ROLL-' => number_format($last_claim['number']), '-SUM-' => number_format($last_claim['reward'], 2))); ?></i></small>
									<hr>
									<h5 class="mt-0 mb-1 text-info text-center" role="alert"><i class="fa fa-arrow-down"></i> Earn more bits while you wait! <i class="fa fa-arrow-down"></i></h5>
									<a class="btn btn-secondary btn-sm w-100 mt-1" href="<?=GenerateURL('ptc')?>"><i class="fa fa-external-link fa-fw"></i> <?php echo $lang['l_95']; ?></a>
									<a class="btn btn-secondary btn-sm w-100 mt-1" href="<?=GenerateURL('shortlinks')?>"><i class="fa fa-link fa-fw"></i> <?php echo $lang['l_425']; ?></a>
									<a class="btn btn-secondary btn-sm w-100 mt-1" href="<?=GenerateURL('offers')?>"><i class="fa fa-list-alt fa-fw"></i> <?php echo $lang['l_464']; ?></a>
									<a class="btn btn-secondary btn-sm w-100 mt-1" href="<?=GenerateURL('mining')?>"><i class="fa fa-calculator fa-fw"></i> <?php echo $lang['l_402']; ?></a>
								<?php
									}
									elseif($config['faucet_sl_required'] > 0 && $data['sl_today'] < $config['faucet_sl_required'] && $count_sl >= ($config['faucet_sl_required']-$data['sl_today']))
									{
										$faucetLocked = true;
										echo '<div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-triangle fa-fw"></i> <b>'.$lang['l_426'].'</b> <i class="fa fa-exclamation-triangle fa-fw"></i><br />'.lang_rep($lang['l_427'], array('-SUM-' => $config['faucet_sl_required'] - $data['sl_today'])).'</span></div>';
										echo '<a href="'.GenerateURL('shortlinks').'" class="btn btn-info btn-md w-100 mt-2"><i class="fa fa-link fa-fw"></i> '.$lang['l_425'].'</a>';
									} 
									else 
									{
								?>
									<div class="alert alert-info mt-2" role="alert" id="loadingFaucet"><i class="fa fa-cog fa-spin fa-fw"></i> Faucet loading, please wait...</div>
									<div id="claimFaucet" class="d-none">
										<div id="luckyNumber">99,999</div>
										<div id="faucetMessage"></div>
										<div id="<?php echo $secureFormID['rollFaucet']; ?>">
											<?php
												if($config['faucet_recaptcha'] == 1 || $config['faucet_solvemedia'] == 1 || $config['faucet_raincaptcha'] == 1) 
												{
													echo '<select class="form-control form-control-sm custom-select mb-1" id="'.$secureFormID['toggleCaptcha'].'">'.($config['faucet_solvemedia'] == 1 ? '<option value="0">SolveMedia</option>' : '').($config['faucet_recaptcha'] == 1 ? '<option value="1">reCaptcha</option>' : '').($config['faucet_raincaptcha'] == 1 ? '<option value="2">rainCaptcha</option>' : '').'</select>';
													
													echo '<div class="d-flex justify-content-center">';
													if($config['faucet_solvemedia'] == 1)
													{
														echo '<div id="'.$secureFormID['captcha'].'_0" class="load_captcha"></div>';
													}
													if($config['faucet_recaptcha'] == 1)
													{
														echo '<div id="'.$secureFormID['captcha'].'_1" class="load_captcha'.($config['faucet_solvemedia'] == 0 ? '' : ' d-none').'"><div class="g-recaptcha" data-sitekey="'.$config['recaptcha_pub'].'"></div></div>';
													}
													if($config['faucet_raincaptcha'] == 1)
													{
														echo '<div id="'.$secureFormID['captcha'].'_2" class="load_captcha'.($config['faucet_solvemedia'] == 0 && $config['faucet_recaptcha'] == 0 ? '' : ' d-none').'"><script src="https://raincaptcha.com/base.js" type="application/javascript" async></script><div id="rain-captcha" data-key="'.$config['raincaptcha_public'].'"></div></div>';
													}
													echo '</div>';
												} 
											?>
											<button type="button" class="btn btn-danger btn-md w-100 mt-2"><i class="fa fa-forward fa-fw"></i> Roll &amp; Win <i class="fa fa-backward fa-fw"></i></button>
										</div>
									</div>
								<?php } ?>
							  </div>
							</div>
						</div>
						<div class="col-lg-6 col-sm-12 d-flex align-items-stretch my-2">
							<div class="card text-center text-dark w-100">
								<div class="table-responsive">
									<table class="table table-striped table-hover table-light text-dark borderless text-center mb-0">
									  <thead>
										<tr>
										  <th scope="col">Lucky Number</th>
										  <th scope="col">Reward</th>
										</tr>
									  </thead>
									  <tbody>
										<?php
											$prizes = $db->QueryFetchArrayAll("SELECT * FROM `faucet` ORDER BY `id` ASC");
											
											$level_multiplier = userLevel($data['id'], 3, $data['total_claims']);
											$multiplier = (($level_multiplier + $data['multiplier'])-1);
											foreach($prizes as $prize) {
												echo '<tr><td>Roll '.number_format($prize['small']).' to '.number_format($prize['big']).'</td><th scope="row">'.number_format($prize['reward']*$multiplier, 2).' '.$lang['l_337'].'</th></tr>';
											}
										?>
									  </tbody>
									</table>
								</div>
								<p class="text-danger mt-4"><?php echo lang_rep($lang['l_218'], array('-BITS-' => number_format($config['jackpot_prize']*$multiplier, 2))); ?></p>
							</div>
						</div>
					</div>
				</div>
			  </div>
			  <div class="row">
				<div class="col-lg-6 col-sm-12 my-2 d-flex align-items-stretch">
				  <div id="dashboard-info">
					<table class="w-100">
						<tr>
							<td><i class="fa fa-check-circle fa-fw"></i> <?php echo $lang['l_38']; ?>:</td>
							<td class="text-right text-success"><?php echo number_format($data['account_balance'], 2).' '.$lang['l_337']; ?></td>
						</tr>
						<tr>
							<td><i class="fa fa-check-circle fa-fw"></i> <?php echo $lang['l_116']; ?>:</td>
							<td class="text-right text-success"><?php echo number_format($data['today_revenue'], 2).' '.$lang['l_337']; ?></td>
						</tr>
						<tr>
							<td><i class="fa fa-check-circle fa-fw"></i> <?php echo $lang['l_117']; ?>:</td>
							<td class="text-right text-success"><?php echo number_format($data['total_revenue'], 2).' '.$lang['l_337']; ?></td>
						</tr>
						<tr>
							<td><i class="fa fa-clock-o fa-fw"></i> <?php echo $lang['l_118']; ?>:</td>
							<td class="text-right text-warning"><?php echo number_format($data['today_claims']).' '.$lang['l_84']; ?></td>
						</tr>
						<tr>
							<td><i class="fa fa-clock-o fa-fw"></i> <?php echo $lang['l_119']; ?>:</td>
							<td class="text-right text-warning"><?php echo number_format($data['total_claims']).' '.$lang['l_84']; ?></td>
						</tr>
						<tr>
							<td><i class="fa fa-users fa-fw"></i> <?php echo $lang['l_104']; ?>:</td>
							<td class="text-right text-info"><?php echo number_format($refs['total']).' '.$lang['l_20']; ?></td>
						</tr>
						<tr>
							<td><i class="fa fa-users fa-fw"></i> <?php echo $lang['l_260']; ?>:</td>
							<td class="text-right text-info"><?php echo number_format($cms['total'], 2).' '.$lang['l_337']; ?></td>
						</tr>
					</table>
				  </div>
				</div>
				<div class="col-lg-6 col-sm-12 my-2 d-flex align-items-stretch">
				  <div id="dashboard-info">
					<h1 class="text-warning"><?php echo $lang['l_120']; ?></h1>
					<p><?php echo lang_rep($lang['l_44'], array('-COMMISSION-' => $data['ref_com'])); ?></p>
					<div class="affiliate-url d-flex justify-content-center">
						<div class="form-group">
							<i class="fa fa-external-link text-dark"></i>
							<input type="email" class="form-control text-center" value="<?php echo $config['secure_url']; ?>/?ref=<?php echo $data['id']; ?>" onclick="this.select()" readonly>
						</div>
					</div>
					<div class="btn-group btn-group-sm d-flex justify-content-center">
						<a class="btn btn-info active" href="javascript:void(0)"><i class="fa fa-share-alt-square"></i> <?php echo $lang['l_261']; ?>:</a>
						<a class="btn btn-info" href="javascript:void(0)" onclick="open_popup('http://www.facebook.com/sharer/sharer.php?u=<?=$config['secure_url']?>/?ref=<?=$data['id']?>','Facebook Share',600,300); return false;"><i class="fa fa-facebook"></i> Facebook</a>
						<a class="btn btn-info" href="javascript:void(0)" onclick="open_popup('http://twitter.com/intent/tweet?text=Earn+free+<?php echo getCurrency('name'); ?>:+<?=$config['secure_url']?>/?ref=<?=$data['id']?>','Twitter Share',520,280); return false;"><i class="fa fa-twitter"></i> Twitter</a>
						<a class="btn btn-info" href="javascript:void(0)" onclick="open_popup('https://plus.google.com/share?url=<?=$config['secure_url']?>/?ref=<?=$data['id']?>','Google Share',600,300); return false;"><i class="fa fa-google"></i> Google</a>
					</div>
				  </div>
				</div>
			  </div>
			</div>
		</div>
	  </div>
    </main>
	<script src="static/js/countUp.min.js" type="text/javascript"></script>
	<script>
<?php
	if($faucetLocked === false) 
	{
		if($data['last_claim'] < (time()-($config['faucet_time']*60))) 
		{
			$script = "var RC_response = false;
			window.addEventListener('load', function(){if ('rainCaptcha' in window) {rainCaptcha.on('complete', function(data){RC_response = data;}); } }, false);

			$(document).ready(function() {
				$('#".$secureFormID['rollFaucet']." button').on('click',function(e){
					var captcha = $('#".$secureFormID['toggleCaptcha']."').find(':selected').val();
					var challenge = false;
					var response = false;
					var options = {
						useEasing: true,
						useGrouping: true,
						separator: ',',
						decimal: '.',
					};

					if ('rainCaptcha' in window) { 
						rainCaptcha.on('complete', function(data){
							response = data;
						}); 
					}

					if(captcha == 0) {
						challenge = $(\"[name='adcopy_challenge']\").val();
						response = $(\"[name='adcopy_response']\").val();
					} else if (captcha == 1) {
						response = grecaptcha.getResponse();
					} else if (captcha == 2) {
						response = RC_response;
					}

					$('#".$secureFormID['rollFaucet']."').hide();
					$('#captcha').hide();
					$('#faucetMessage').html('<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin fa-fw\"></i> Please wait...</div>');		

					$.post('system/ajax.php', {a: 'getFaucet', token: '".$token."', captcha: captcha, challenge: challenge, response: response},
					function(response) {
						if(response.status == 200){			
							window.setTimeout(function() {
								$('#faucetMessage').html(response.message);
							}, 2000);

							var roll = new CountUp('luckyNumber', 1, response.number, 0, 2, options);
							if (!roll.error) {
								roll.start();
							}
						} else if(response.status == 400){
							$('#faucetMessage').html(response.message);
						} else {
							$('#".$secureFormID['rollFaucet']."').show();
							$('#captcha').show();
							$('#faucetMessage').html(response.message);
						}
					},'json');
				});

				setTimeout(function(){
					".($config['faucet_solvemedia'] == 1 ? 'ACPuzzle.create("'.$config['solvemedia_c'].'", "'.$secureFormID['captcha'].'_0");' : '')."
					$('#loadingFaucet').addClass('d-none');
					$('#claimFaucet').removeClass('d-none');
				}, 2500);

				$('#".$secureFormID['toggleCaptcha']."').on('change', function() {
					var captcha = this.value;
					$('.load_captcha').each(function(i, o) {
						if ($(this).attr('id') == '".$secureFormID['captcha']."_'+captcha) {
							$(this).removeClass('d-none');
						} else {
							$(this).addClass('d-none');
						}
					});
				});
			});";

			$packer = new JavaScriptPacker($script, 'Normal', true, false);
			$packed = $packer->pack();

			echo $packed;
		} else { 
?>
		$(document).ready(function () {
            $("#claimTime").countdown(<?php echo (($data['last_claim']+($config['faucet_time']*60))*1000); ?>, {elapse: true}).on('update.countdown', function(event) {
                    if (event.elapsed) {
                        window.location.reload();
                    }
                    $(this).text(
                        event.strftime('<?php echo (($data['last_claim']+($config['faucet_time']*60))-time() > 3600 ? '%H hours, %M minutes and %S seconds' : '%M minutes and %S seconds'); ?>')
                    );
            });
		});
<?php
		}
	}	
?>
		function open_popup(a,b,c,d){var e=(screen.width-c)/2;var f=(screen.height-d)/2;var g='width='+c+', height='+d;g+=', top='+f+', left='+e;g+=', directories=no';g+=', location=no';g+=', menubar=no';g+=', resizable=no';g+=', scrollbars=no';g+=', status=no';g+=', toolbar=no';newwin=window.open(a,b,g);if(window.focus){newwin.focus()}return false}
	</script>