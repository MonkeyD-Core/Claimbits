<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	// Timestamps
	$nextMonth = strtotime('first day of next month'); 
	$nextMonth = date('Y-m-d', $nextMonth);
	$nextMonth = strtotime($nextMonth);
	$nextWeek = strtotime("next Sunday")-60;
	$remainingTime = date('Y-m-d H:i:s', ($config['lottery_duration'] == 0 ? $nextWeek : $nextMonth));
	
	// Load Current Round
	$lottery = $db->QueryFetchArray("SELECT * FROM `lottery` WHERE `closed`='0' ORDER BY `id` DESC LIMIT 1");
	if (empty($lottery)) {
		$db->Query("INSERT INTO lottery (`prize`,`tickets_purchased`,`date`) VALUES ('".$config['lottery_default']."', '0', '".time()."')");
		$lottery = $db->QueryFetchArray("SELECT * FROM `lottery` WHERE `closed`='0' ORDER BY `id` DESC LIMIT 1");
	}

	$get_tickets = $db->QueryGetNumRows("SELECT * FROM `lottery_tickets` WHERE `user_id`='".$data['id']."'");

	$errMessage = '';
	if (isset($_POST['buy_ticket'])) {
		$source = ($_POST['source'] == 1 ? 1 : 0);
		$tickets = 1;
		if(isset($_POST['tickets_amount']) && is_numeric($_POST['tickets_amount'])) {
			$tickets = ($_POST['tickets_amount'] < 1 ? 1 : ($_POST['tickets_amount'] > 200 ? 200 : $_POST['tickets_amount']));
		}

		$lottery_price = ($data['lottery_price']*$tickets);
		$btc_value = number_format(($lottery_price*$config['bits_rate'])/100000000, 8);

		if ($data['account_balance'] < $lottery_price && $source == 0) 
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_164'].'</div>';
		}
		elseif ($data['purchase_balance'] < $btc_value && $source == 1) 
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_164'].'</div>';
		}
		else 
		{
			$db_query = "('".$lottery['id']."', '".$data['id']."', '".time()."')";

			if($tickets > 1) {
				for($i = 1; $i < $tickets; $i++) {
					$db_query .= ",('".$lottery['id']."', '".$data['id']."', '".time()."')";
				}
			}

			$winPrize = 0;
			if($config['lottery_type'] != 1)
			{
				$lottery_fee = ($config['lottery_fee']*$tickets);
				$winPrize = ($lottery_price < $lottery_fee ? 0 : ($lottery_price-$lottery_fee));
			}

			$db->Query("UPDATE `lottery` SET `prize`=`prize`+'".$winPrize."', `tickets_purchased`=`tickets_purchased`+'".$tickets."' WHERE `id`='".$lottery['id']."'");
			$db->Query("INSERT INTO lottery_tickets (`lottery_id`,`user_id`,`date`) VALUES ".$db_query);		

			if($source == 1)
			{
				$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`-'".$btc_value."' WHERE `id`='".$data['id']."'");
				$db->Query("INSERT INTO `user_transactions` (`user_id`,`type`,`value`,`price`,`date`) VALUES('".$data['id']."', '4', '".$tickets."', '".$btc_value."', '".time()."') ");
				$data['purchase_balance'] = ($data['purchase_balance']-$btc_value);
			}
			else
			{
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`-'".$lottery_price."' WHERE `id`='".$data['id']."'");
				$data['account_balance'] = ($data['account_balance']-$lottery_price);
			}

			$errMessage = '<div class="alert alert-success" role="alert">'.($tickets == 1 ? $lang['l_264'] : lang_rep($lang['l_267'], array('-NUM-' => $tickets))).'</div>';
		}
	}
?>
<main role="main" class="container">
  <div class="row">
	<?php 
		require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		$lottery = $db->QueryFetchArray("SELECT * FROM `lottery` WHERE `closed`='0' ORDER BY `id` DESC LIMIT 1");
	?>
	<div class="col-xl-9 col-lg-8 col-md-7">
		<div class="my-3 p-3 bg-white rounded box-shadow box-style">
		  <div id="grey-box">
			<div class="title">
				<?=$lang['l_265']?>
			</div>
			<div class="content">
				<?php echo $errMessage; ?>
				<div class="box">
					<div class="row d-flex justify-content-center">
						<div class="col-md-8 col-sm-12 my-2">
							<div id="loterry_stats_header"><i class="fa fa-star"></i> <b><?php echo $lang['l_268']; ?></b> <i class="fa fa-star"></i></div>
							<div id="loterry_stats" class="text-center"><i class="fa fa-check-circle fa-fw"></i> <?php echo number_format($lottery['prize'], 2).' '.$lang['l_337']; ?></div>
						</div>
						<div class="col-md-6 col-sm-12 my-2">
						  <div id="loterry_stats_header"><?php echo $lang['l_269']; ?></div>
						  <div id="loterry_stats" class="text-center"><i class="fa fa-ticket fa-fw"></i> <?php echo number_format($lottery['tickets_purchased']).' '.$lang['l_270']; ?></div>
						</div>
						<div class="col-md-6 col-sm-12 my-2">
						  <div id="loterry_stats_header">Your Tickets</div>
						  <div id="loterry_stats" class="text-center"><i class="fa fa-ticket fa-fw"></i> <?php echo number_format($get_tickets).' '.$lang['l_270']; ?></div>
						</div>
					</div>

					<div class="lotteryCountdownBox">
						<div class="timer" id="remainingTime"></div>
					</div>

					<div class="clear"></div>

					<div class="d-flex justify-content-center">
						<div class="card mt-2">
							<div class="card-header text-center"><b><?php echo $lang['l_271']; ?></b></div>
							<div class="card-body">
								<form method="POST" onsubmit="return confirm('<?php echo $lang['l_275']; ?>');">
									<div class="form-row">
										<div class="form-group col-md-6">
										  <label for="tickets_amount">How many tickets?</label>
										  <input type="text" class="form-control" id="tickets_amount" name="tickets_amount" placeholder="100">
										</div>
										<div class="form-group col-md-6">
										  <label for="source">Pay from</label>
										  <select name="source" id="source" class="form-control">
											<option value="0"><?php echo $lang['l_38']; ?></option>
											<option value="1"><?php echo $lang['l_332']; ?></option>
										  </select>
										</div>
										<div class="mx-auto">
											<button type="submit" name="buy_ticket" class="btn btn-secondary"><i class="fa fa-shopping-cart"></i> <?php echo $lang['l_271']; ?></button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="box">
					<small><?php echo lang_rep(($config['lottery_duration'] == 0 ? $lang['l_273'] : $lang['l_449']), array('-PRICE-' => $data['lottery_price'], '-SAT-' => number_format($data['lottery_price']*$config['bits_rate'], 0))); ?></small>
				</div>
				<div class="card text-center text-dark my-2 w-100">
				  <div class="card-header">
					<b><?php echo $lang['l_452']; ?></b>
				  </div>
					<div class="table-responsive">
						<table class="table table-striped table-hover table-dark text-light borderless text-center mb-0">
						  <thead>
							<tr>
							  <th scope="col">#</th>
							  <th scope="col"><?php echo $lang['l_454']; ?></th>
							  <th scope="col"><?php echo $lang['l_455']; ?></th>
							  <th scope="col"><?php echo $lang['l_269']; ?></th>
							  <th scope="col"><?php echo $lang['l_276']; ?></th>
							  <th scope="col"><?php echo $lang['l_329']; ?></th>
							</tr>
						  </thead>
						  <tbody>
							<?php
								$winners = $db->QueryFetchArrayAll("SELECT a.id, a.prize, a.winning_ticket, a.winner_tickets, a.end_date, b.username FROM lottery a LEFT JOIN users b ON b.id = a.winner_id WHERE a.closed = '1' AND a.winner_id != '0' ORDER BY a.id DESC LIMIT 10");
					
								if(empty($winners))
								{
									echo '<tr><td colspan="6">There is no winner yet!</td></tr>';
								}
					
								foreach($winners as $winner) {
									echo '<tr><td><span class="badge badge-light py-1 px-2">'.number_format($winner['id']).'</span></td><td><span class="badge badge-success py-1 px-2">'.$winner['username'].'</span></td><td><span class="badge badge-info py-1 px-2">#'.$winner['winning_ticket'].'</span></td><td><span class="badge badge-light py-1 px-2">'.number_format($winner['winner_tickets']).'</span></td><td><span class="badge badge-warning py-1 px-2">'.number_format($winner['prize'], 2).' '.$lang['l_337'].'</span></td><td><span class="badge badge-light py-1 px-2">'.date('d M Y', $winner['end_date']).'</span></td></tr>';
								}
							?>
						  </tbody>
						</table>
					</div>
				</div>
			<div class="clearfix"></div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</main>
<script type="text/javascript">
	$(document).ready(function () {
		var myDate = new Date('<?php echo $remainingTime; ?>');
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