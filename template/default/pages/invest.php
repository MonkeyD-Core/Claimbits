<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	$currentPrice = $db->QueryFetchArray("SELECT `value` FROM `bitcoin_price` ORDER BY `time` DESC LIMIT 1");
	$BTCPrice = $db->QueryFetchArrayAll("SELECT * FROM `bitcoin_price` ORDER BY `time` DESC LIMIT 10");
	$BTCPrice = array_reverse($BTCPrice);
	
	$prices = array();
	$times = array();
	foreach($BTCPrice as $price) {
		$prices[] = '"'.$price['value'].'"';
		$times[] = '"'.$price['minute'].'"';
	}
	
	$prices = implode(',', $prices);
	$times = implode(',', $times);
	
	// Check investments
	$investment = $db->QueryFetchArray("SELECT * FROM `bitcoin_investments` WHERE `user_id`='".$data['id']."' AND `status`='0' ORDER BY `time` DESC LIMIT 1");
?> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>
	<main role="main" class="container">
      <div class="row">
		<?php 
			require_once(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		?>
		<div class="col-xl-9 col-lg-8 col-md-7">
			<div class="my-3 ml-2 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?php echo $lang['l_409']; ?>
					</div>
					<div class="content">
						<div class="infobox">
							<?php echo lang_rep($lang['l_410'], array('-NUM-' => $config['invest_win'])); ?>
						</div>
						<div class="infobox text-center">
							<small>
								<b><?php echo $lang['l_411']; ?></b><br />
								<?php echo $lang['l_412']; ?>
							</small>
						</div>
						<div class="card w-100 "><canvas id="BTCPrice"></canvas></div>
						<?php
							if(empty($investment['id'])) {
						?>
							<div class="card w-100 text-dark p-2 mt-2 text-center">
								<div class="bitcoin-value"><span class="text-success">$</span><span id="liveValue"><?php echo $currentPrice['value']; ?></span></div>
								<small><?php echo $lang['l_413']; ?></small>
							</div>
							<div class="card text-dark text-center mt-2">
							  <div class="card-header">
								<b><?php echo $lang['l_414']; ?></b>
							  </div>
							  <div class="card-body bg-dark p-2">
								  <div id="investMsg"></div>
								  <div class="form-row align-items-center">
									<div class="col-md-6">
									  <input type="text" class="form-control" id="bits" placeholder="<?php echo $lang['l_415']; ?>">
									</div>
									<div class="col-md-6">
									 <button id="call" class="btn btn-success" onclick="proccessInvest('0');"><i class="fa fa-angle-double-up fa-fw"></i> <?php echo $lang['l_416']; ?></button>
									 <button id="putt" class="btn btn-danger" onclick="proccessInvest('1');"><i class="fa fa-angle-double-down fa-fw"></i> <?php echo $lang['l_417']; ?></button>
									</div>
								  </div>
								</div>
							</div>
						<?php 
							} else { 
								$remTime = ($investment['time']+300)-time();
						?>
							<div class="card w-100 text-dark p-2 mt-2 text-center">
								<div id="investMsg"></div>
								<div class="row">
									<div class="col-lg-3 col-md-6">
										<div class="bitcoin-value"><?php echo $investment['amount'].' '.$lang['l_337']; ?></div>
										<small><?php echo $lang['l_418']; ?></small>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="bitcoin-value"><span class="text-success">$</span><?php echo $investment['old_value']; ?> <i class="<?=($investment['type'] == 1 ? 'fa fa-angle-double-down text-danger' : 'fa fa-angle-double-up text-success')?>"></i></div>
										<small><?php echo $lang['l_419']; ?></small>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="bitcoin-value"><span class="text-success">$</span><span id="liveValue"><?php echo $currentPrice['value']; ?></span></div>
										<small><?php echo $lang['l_413']; ?></small>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="bitcoin-value"><span id="staticTime"><?php echo gmdate('i:s', $remTime); ?></span><span id="timer" data-seconds-left="<?php echo $remTime; ?>"></span></div>
										<small><?php echo $lang['l_420']; ?></small>
									</div>
								</div>
							</div>
							<script src="static/js/simple.timer.js"></script>
							<script>
							$(document).ready(function(){
								$('#staticTime').hide(); 
								$('#timer').startTimer({onComplete: function(element){
									$.ajax({
										type: "POST",
										url: "system/ajax.php",
										data: {a: 'finishInvest', token: token, sid: '<?php echo $investment['id']; ?>'},
										dataType: "json",
										success: function(data) {
											if(data.status == 200){
												window.setTimeout(function() {
													window.location.reload();
												}, 2000);
											}

											$("#investMsg").html(data.message);
										}
									});
								}}); 
								$('#timer div').css('display','inline');
							});
						</script>
						<?php } ?>
						
						<div class="card text-center text-dark mt-2 w-100">
						  <div class="card-header">
							<b>Last 10 Investments</b>
						  </div>
							<div class="table-responsive">
								<table class="table table-striped table-hover table-dark text-light borderless text-center mb-0">
								  <thead>
									<tr>
									  <th scope="col">#</th>
									  <th scope="col"><?php echo $lang['l_418']; ?></th>
									  <th scope="col"><?php echo $lang['l_419']; ?></th>
									  <th scope="col"><?php echo $lang['l_421']; ?></th>
									  <th scope="col"><?php echo $lang['l_67']; ?></th>
									</tr>
								  </thead>
								  <tbody>
									<?php
										$investments = $db->QueryFetchArrayAll("SELECT * FROM `bitcoin_investments` WHERE `user_id`='".$data['id']."' AND `status`!='0' ORDER BY `id` DESC LIMIT 10");

										foreach($investments as $invest) {
											echo '<tr><td>'.number_format($invest['id']).'</td><td>'.number_format($invest['amount'], 2).' '.$lang['l_337'].'</td><td>$'.$invest['old_value'].' <i class="'.($invest['type'] == 1 ? 'fa fa-angle-double-down text-danger' : 'fa fa-angle-double-up text-success').'"></i></td><td>$'.$invest['new_value'].'</td><td>'.($invest['status'] == 1 ? '<span class="badge badge-success">Won</span>' : ($invest == 3 ? '<span class="badge badge-light">Even</span>' : '<span class="badge badge-danger">Lost</span>')).'</td></tr>';
										}
									?>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>
<script>
	var ctx = document.getElementById('BTCPrice').getContext('2d');
	var gradient = ctx.createLinearGradient(0, 0, 0, 400);
	gradient.addColorStop(0, '#474346');    
	gradient.addColorStop(1, '#212121');

	var BTCPrice = new Chart(ctx, {
		type: 'line',
		data: {
			labels: [<?=$times?>],
			datasets: [{
				label: "BTC Value",
				data: [<?=$prices?>],
				fill: true,
				pointRadius: 5,
				lineTension: 0,
				fillColor: gradient,
				backgroundColor: gradient,
				borderColor: '#ee7d0c',
			}],
		},
		options: {
		  responsive: true,
			scales: {
				xAxes: [{
					ticks: {
						beginAtZero:true
					},
					gridLines: {
						borderDash: [3, 3],
						color: "#ffffff",
						display: false,
					}
				}],
				yAxes: [{
			ticks: {
			  beginAtZero:false
			},
			gridLines: {
					borderDash: [3, 3],
					color: "#ededed",
					display: true,
				}
		  }]
			}
		}
	});

	function addData(chart, label, data) {
		chart.data.labels.push(label);
		chart.data.datasets.forEach((dataset) => {
			dataset.data.push(data);
		});
		chart.update();
	}
<?php
	if(empty($investment['id'])) {
?>
	var token = '<?php echo $token; ?>';
	var waitMessage = '<div class="alert alert-info" role="alert"><i class="fa fa-cog fa-spin fa-fw"></i> Please wait...</div>';
	function proccessInvest(type) {
		var amount = $('#bits').val();
		$('#investMsg').html(waitMessage);
		
		if(amount < <?php echo $config['invest_min']; ?>) {
			$('#investMsg').html('<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> <?php echo lang_rep($lang['l_404'], array('-SUM-' => $config['invest_min'])); ?></div>');
		} else if(amount > <?php echo $config['invest_max']; ?>) {
			$('#investMsg').html('<div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> <?php echo lang_rep($lang['l_405'], array('-SUM-' => $config['invest_max'])); ?></div>');
		} else {
			$.ajax({
				type: "POST",
				url: "system/ajax.php",
				data: {a: 'proccessInvest', token: token, amount: amount, type: type},
				dataType: "json",
				success: function(data) {
					if(data.status == 200){
						window.setTimeout(function() {
							window.location.reload();
						}, 2000);
					}

					$("#investMsg").html(data.message);
				}
			});
		}
	}
<?php } ?>
	window.setInterval(function() {
		$.getJSON('system/ajax.php?a=getBTCPrice', function(data) {
			BTCPrice.data.labels.splice(0,1);
			BTCPrice.data.datasets[0].data.splice(0,1);
			addData(BTCPrice, data.time, data.price);
			$('#liveValue').fadeOut(280).html(data.price).fadeIn(280);
		});
	}, 60000);
</script>