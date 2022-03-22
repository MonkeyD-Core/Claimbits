<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	$errMessage = '';
	if(isset($_POST['subscribe']))
	{
		$pid = $db->EscapeString($_POST['packID']);
		$membership = $db->QueryFetchArray("SELECT * FROM `memberships` WHERE `id`='".$pid."'");
		
		if(empty($membership['id']) || $membership['id'] == 1)
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><b>ERROR:</b> Please select a valid membership!</div>';
		}
		else
		{
			$duration = $db->EscapeString($_POST['duration']);
			$duration = ($duration < 1 ? 1 : ($duration > 12 ? 12 : $duration));
			$months = (2592000*$duration);
			$price = ($duration * $membership['price']);

			if($config['months_to_discount'] <= $duration && !empty($config['vip_discount'])) {
				$discount = (($config['vip_discount']/100)*$price);
				$price = ($price - $discount);
			}

			if($price > $data['purchase_balance'])
			{
				$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_164'].'</div>';
			}
			else
			{
				$premium = ($data['membership'] == 0 || $data['membership_id'] != $membership['id'] ? (time()+$months) : ($months+$data['membership']));
				$db->Query("INSERT INTO `user_transactions` (`user_id`,`type`,`value`,`price`,`date`) VALUES('".$data['id']."', '1', '".$duration."', '".$price."', '".time()."') ");
				$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`-'".$price."', `membership`='".$premium."', `membership_id`='".$membership['id']."' WHERE `id`='".$data['id']."'");
			
				$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_336'].'</div>';
			}
		}
	}
?>
	<link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="template/<?=$config['theme']?>/static/membership.css?v=<?php echo $config['version']; ?>" />
	<main role="main" class="container">
      <div class="row">
		<?php 
			require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		?>
	  <div class="col-xl-9 col-lg-8 col-md-7">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?=$lang['l_39']?>
					</div>
					<div class="content">
						<?php echo $errMessage; ?>
						<div class="membership-block text-center w-100"><?=$lang['l_39']?>: <?=('<b class="text-warning">'.$data['mem_name'].'</b>'.($data['membership'] > 0 ? ' <small>('.date('d M Y - H:i', $data['membership']).')</small>' : ''))?></div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-lg-4 col-md-12 padding-0" id="membership">
									<div class="title_first_colum"><?php echo $lang['l_124']; ?></div>
									<div class="field first"><?php echo $lang['l_216']; ?></div>
									<div class="field first"><?php echo $lang['l_125']; ?></div>
									<div class="field first"><?php echo $lang['l_291']; ?></div>
									<div class="field first"><?php echo $lang['l_85']; ?></div>
									<div class="field first"><?php echo $lang['l_217']; ?></div>
									<?php echo (empty($pack['ks_api_key']) ? '' : '<div class="field first">'.$lang['l_467'].'</div>'); ?>
									<div class="field first"><?php echo $lang['l_326']; ?></div>
									<div class="field first"><?php echo $lang['l_325']; ?></div>
									<?php echo (empty($pack['ks_api_key']) ? '' : '<div class="field first">'.$lang['l_468'].'</div>'); ?>
									<div class="field first"><?php echo $lang['l_287']; ?></div>
									<?php echo ($config['lottery_status'] == 1 ? '<div class="field first">'.$lang['l_450'].'</div>' : ''); ?>
									<div class="field first"><?php echo $lang['l_315']; ?></div>
									<div class="field first"><?php echo $lang['l_126']; ?></div>
							</div>
							<?php
								$memPacks = $db->QueryFetchArrayAll("SELECT * FROM `memberships` ORDER BY `price` ASC");
								
								foreach($memPacks as $pack) {
							?>
							<div class="col-lg-2 col-md-6 padding-0">
								<div id="membership">
									<div class="first_child">
										<div class="sub_pack"><?php echo $pack['membership']; ?></div>
										<div class="price"><?php echo ($pack['price'] == 0 ? '<span class="subprice_big">'.$lang['l_129'].'</span>' : number_format($pack['price'], 5).' <span class="subprice">'.getCurrency().'</span><br /><span class="small">'.$lang['l_131'].'</span>'); ?></div>
									</div>
									<div class="field text-center text-success"><b>x<?php echo $pack['multiplier']; ?></b></div>
									<div class="field text-center"><b><?php echo $pack['ref_com']; ?>%</b></div>
									<div class="field text-center"><b><?php echo $pack['offer_com']; ?>%</b></div>
									<div class="field text-center"><b><?php echo $pack['short_com']; ?>%</b></div>
									<div class="field text-center"><b><?php echo number_format($pack['fp_min_pay'], 8).' '.getCurrency(); ?></b></div>
									<?php echo (empty($pack['ks_api_key']) ? '' : '<div class="field text-center"><b>'.number_format($pack['ks_min_pay'], 8).' '.getCurrency().'</b></div>'); ?>
									<div class="field text-center"><b><?php echo number_format($pack['btc_min_pay'], 8).' '.getCurrency(); ?></b></div>
									<div class="field text-center"><b><?php echo ($pack['fp_wait_time'] > 0 ? 'Up to '.$pack['fp_wait_time'].' days' : 'Instant'); ?></b></div>
									<?php echo (empty($pack['ks_api_key']) ? '' : '<div class="field text-center"><b>'.($pack['ks_wait_time'] > 0 ? 'Up to '.$pack['ks_wait_time'].' days' : 'Instant').'</b></div>'); ?>
									<div class="field text-center"><b><?php echo ($pack['btc_wait_time'] > 0 ? 'Up to '.$pack['btc_wait_time'].' days' : 'Instant'); ?></b></div>
									<?php echo ($config['lottery_status'] == 1 ? '<div class="field text-center"><b>'.number_format($pack['lottery_price']).' '.$lang['l_337'].'</b></div>' : ''); ?>
									<div class="field text-center"><b><?php echo number_format($pack['hash_rate']); ?></b></div>
									<div class="field text-center">&nbsp;<i class="fa fa-<?php echo ($pack['hide_ads'] == 1 ? 'check' : 'times'); ?> fa-lg fa-fw" aria-hidden="true"></i>&nbsp;</div>
									<div class="last_child">
										<div class="price_box">
											<br /><span class="bottomprice"><?php echo ($pack['price'] == 0 ? $lang['l_129'] : '<button class="btn btn-success btn-sm" onclick="payUpgrade(\''.$pack['id'].'\',\''.$pack['price'].'\',\''.$pack['membership'].'\')" /><i class="fa fa-shopping-cart" aria-hidden="true"></i> '.$lang['l_132'].'</button>'); ?></span>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						<div class="clearfix"></div>
						<div id="payBlock"></div>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>
	<div class="modal fade" id="payUp" role="dialog">
	 <div class="modal-dialog modal-dialog-centered">
	 <div class="modal-content">
      <div class="price-box">
        <form class="form-pricing" role="form" method="post">
          <input type="hidden" id="packID" name="packID" class="form-control">
          <input type="hidden" id="packPrice" name="packPrice" class="form-control">
          <div class="price-slider">
            <h4 class="great"><?php echo $lang['l_307']; ?></h4>
            <span><?php echo $lang['l_310']; ?></span>
            <div class="col-sm-12">
              <div id="timeSlide"></div>
            </div>
          </div>
		  <div id="errMsg"></div>
          <div class="price-form">
            <div class="row form-group">
              <label for="duration" class="col-sm-6 col-form-label text-right"><?php echo $lang['l_307']; ?>: </label>
              <span class="help-text"><?php echo $lang['l_311']; ?></span>
              <div class="col-sm-6">
                <input type="hidden" id="duration" name="duration" class="form-control">
                <p class="result lead" id="duration-label"></p>
                <span class="result"><?php echo $lang['l_312']; ?></span>
              </div>
            </div>
            <div class="row form-group">
              <label for="duration" class="col-sm-6 col-form-label text-right"><?php echo $lang['l_308']; ?>: </label>
              <span class="help-text" id="discountLabel"></span>
              <div class="col-sm-6">
                <p class="result lead" id="discount-label">0.00 <?php echo getCurrency(); ?></p>
                <span class="result"></span>
              </div>
            </div>
            <hr class="style">
            <div class="row form-group">
              <label for="total" class="col-sm-6 col-form-label text-right"><strong><?php echo $lang['l_309']; ?>: </strong></label>
              <span class="help-text"><?php echo $lang['l_313']; ?></span>
              <div class="col-sm-6">
                <p class="result lead" id="total-label"></p>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12 d-flex justify-content-center">
				<button type="submit" class="btn btn-success form-control" name="subscribe"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?=$lang['l_87']?></button>
            </div>
          </div>
        </form>
      </div>
	  </div>
	 </div>
	</div>
	<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js" async></script>
	<script src="static/js/jquery.ui.touch-punch.min.js"></script>
	<script type="text/javascript">
	function payUpgrade(id, price, name) {
		if(id < '<?php echo $data['membership_id']; ?>') {
			$("#errMsg").html('<div class="alert alert-danger" role="alert"><?php echo $lang['l_314']; ?></div>');
		} else {
			$("#errMsg").html('');
		}

		$("#timeSlide").slider({
			  animate: true,
			  value:1,
			  min: 1,
			  max: 12,
			  step: 1,
			  slide: function(event, ui) {
				  update(2,ui.value);
			  }
		  });

		 $("#packID").val(id);
		 $("#packPrice").val(price);
		 $("#duration").val(1);
		 $("#duration-label").text(1);

		update();
		
		$('#payUp').modal('show');
	}

	function update(slider,val) {
		var $amount = $("#packPrice").val();
		var $duration = slider == 2 ? val : $("#duration").val();
		var $total = $amount * $duration;
		var $discount = (<?php echo $config['vip_discount']; ?>/100*$total);

		$("#duration").val($duration);
		$("#duration-label").text($duration);
		
		if($duration >= <?php echo $config['months_to_discount']; ?>) {
			$( "#discount-label").text($discount.toFixed(8) + ' <?php echo getCurrency(); ?>');
			$( "#total-label").text(($total - $discount.toFixed(8)).toFixed(8) + ' <?php echo getCurrency(); ?>');
			$("#discountLabel").text('<?php echo $config['vip_discount']; ?>% discount applied');
		} else {
			$( "#discount-label").text('0.00000000 <?php echo getCurrency(); ?>');
			$( "#total-label").text($total.toFixed(8) + ' <?php echo getCurrency(); ?>');
			$("#discountLabel").text('No discount');
		}

		 $('#timeSlide a').html('<label><i class="fa fa-chevron-left"></i> '+$duration+' <i class="fa fa-chevron-right"></i></label>');
	}
	</script>