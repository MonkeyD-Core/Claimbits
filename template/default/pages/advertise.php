<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$errMessage = '<div class="alert alert-warning"><b>The following advertisements are NOT allowed:</b><br><p>Adult (18+) content, illegal products and services (drugs, organs, weapons, prostitution), framebreakers, URL shorteners, copyright infringing material or websites that never load.</p>It is important to mention that if you try for any illegal websites, we will take them down and you won’t be refunded.</div>';
	if(isset($_POST['submit'])){
		$url = $db->EscapeString($_POST['url']);
		$title = $db->EscapeString($_POST['title'], 1);
		$title = truncate($title, 60);
		$ad_pack = $db->EscapeString($_POST['ad_pack']);
		$ad_visits = $db->EscapeString($_POST['ad_visits']);
		$redirect = (isset($_POST['redirect']) ? 1 : 0);

		$daily_clicks = 0;
		if($ad_type == 0 && $_POST['daily_clicks_switch'] == 1)
		{
			$daily_clicks = $db->EscapeString($_POST['daily_clicks']);
			$daily_clicks = (is_numeric($daily_clicks) && $daily_clicks > 0 ? number_format($daily_clicks) : 0);
		}

		if(empty($title) || strlen($title) < 4 || empty($url))
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_08'].'</div>';
		}
		elseif($daily_clicks > 0 && $daily_clicks < 250)
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_82'].'</div>';
		}
		elseif(!is_numeric($ad_visits) || $ad_visits < $config['advertise_min'])
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.lang_rep($lang['l_98'], array('-MIN-' => $config['advertise_min'])).'</div>';
		}
		elseif(!preg_match('/^(http|https):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,6}'.'((:[0-9]{1,5})?\/.*)?$/i', $url))
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_72'].'</div>';
		}
		elseif(substr($url,-4) == '.exe')
		{
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_73'].'</div>';
		}
		elseif($db->QueryFetchArray("SELECT `id` FROM `ptc_websites` WHERE `user_id`='".$data['id']."' AND `added_time`>'".(time()-60)."'"))
		{
			$errMessage = '<div class="alert alert-danger" role="alert">Please wait at least 60 seconds before creating another campaign!</div>';
		}
		else
		{
			$ptc_pack = $db->QueryFetchArray("SELECT * FROM `ptc_packs` WHERE `id`='".$ad_pack."' LIMIT 1");

			if(empty($ptc_pack['id']))
			{
				$errMessage = '<div class="alert alert-danger" role="alert"><b>ERROR:</b> This ad pack doesn\'t exists!</div>';
			}
			else
			{
				$price = ($ptc_pack['price'] / 100000000) * $ad_visits;
				if($redirect)
				{
					$price = $price + ($price/100*$config['ptc_redirect_price']);
				}
				
				if($price > $data['purchase_balance'])
				{
					$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_164'].'</div>';
				}
				else
				{
					$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`-'".$price."' WHERE `id`='".$data['id']."'");
					$db->Query("INSERT INTO `user_transactions` (`user_id`,`type`,`value`,`price`,`date`) VALUES ('".$data['id']."', '2', '".$ad_visits."', '".$price."', '".time()."')");
					$db->Query("INSERT INTO `ptc_websites` (`user_id`,`website`,`title`,`daily_limit`,`total_visits`,`ptc_pack`,`redirect`,`status`,`added_time`) VALUES('".$data['id']."', '".$url."', '".$title."', '".$daily_clicks."', '".$ad_visits."', '".$ptc_pack['id']."', '".$redirect."', '1', '".time()."')");

					$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_70'].'</div>';
				}
			}
		}
	}
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
					<?=$lang['l_21']?>
				</div>
				<div class="content">
				<?php echo $errMessage; ?>
					<form method="post">
						<div class="form-row">
							<div class="form-group col-md-6">
							  <label for="url"><?=$lang['l_90']?></label>
							  <input type="text" class="form-control" name="url" id="url" value="<?php echo (isset($_POST['url']) ? $db->EscapeString($_POST['url']) : ''); ?>" placeholder="http://" required />
							</div>
							<div class="form-group col-md-6">
							  <label for="title"><?=$lang['l_91']?></label>
							  <input type="text" class="form-control" name="title" id="title" value="<?php echo (isset($_POST['title']) ? $db->EscapeString($_POST['title']) : ''); ?>" maxlength="50" placeholder="<?php echo $lang['l_91']; ?>" required />
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6">
							  <label for="ad_visits"><?=$lang['l_92']?></label>
							  <input type="number" class="form-control" name="ad_visits" id="ad_visits" min="<?php echo $config['advertise_min']; ?>" max="10000000" maxlenght="9" placeholder="1000" oninput="get_price()" />
							</div>
							<div class="form-group col-md-6">
							  <label for="ad_pack"><?php echo $lang['l_256']; ?></label>
							  <select name="ad_pack" id="ad_pack" class="custom-select" onchange="get_price()" >
								<?php
									$daily_packs = $db->QueryFetchArrayAll("SELECT * FROM `ptc_packs` ORDER BY `price` ASC");
									
									foreach($daily_packs as $pack) {
										echo '<option value="'.$pack['id'].'">'.$pack['time'].' '.$lang['l_182'].' - '.$pack['price'].' Satoshi</option>';
									}
								?>
							  </select>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-lg-3 col-md-4" id="ad_ppm_clicks">
							  <label for="daily_clicks"><?=$lang['l_93']?></label>
							  <input type="text" name="daily_clicks" id="daily_clicks" class="form-control" disabled />
							</div>
							<div class="form-group col-lg-3 col-md-4" id="ad_ppm_limit">
							  <label for="daily_clicks_switch">&nbsp;</label>
							  <select name="daily_clicks_switch" id="dailyLimitSelect" class="custom-select">
								<option value="0"><?php echo $lang['l_89']; ?></option>
								<option value="1"><?php echo $lang['l_94']; ?></option>
							  </select>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6" id="ad_ppm_clicks">
								<div class="custom-control custom-checkbox">
								  <input type="checkbox" class="custom-control-input" id="redirect" name="redirect"  onclick="get_price()">
								  <label class="custom-control-label" for="redirect"><?php echo $lang['l_461']; ?> (+<?php echo $config['ptc_redirect_price']; ?>%) <i class="fa fa-exclamation-circle fa-fw text-info" data-toggle="tooltip" data-placement="top" title="<?php echo lang_rep($lang['l_462'], array('-PERC-' => $config['ptc_redirect_price'])); ?>"></i></label>
								</div>
							</div>
						</div>
						<div class="price_block">
							<span class="text" id="priceBlock"><?php echo lang_rep($lang['l_97'], array('-PRICE-' => '0.00000000 '.getCurrency())); ?></span>
							<span class="pay_block">
								<input type="submit" name="submit" class="btn btn-success btn-sm" value="<?php echo $lang['l_87']; ?>" />
							</span>
							<div class="clearfix"></div>
						</div>
					</form>
				</div>
			</div>
			<div id="grey-box" class="mt-2">
				<div class="content">
					<table class="table table-striped table-sm table-responsive-sm text-center">
						<thead class="thead-dark">
							<tr>
								<th class="text-center" colspan="5"><?php echo $lang['l_66']; ?></th>
							</tr>
						</thead>
						<thead class="thead-dark">
							<tr>
								<th>#</th>
								<th><?php echo $lang['l_74']; ?></th>
								<th><?php echo $lang['l_256']; ?></th>
								<th><?php echo $lang['l_76']; ?></th>
								<th><?php echo $lang['l_75']; ?></th>
							</tr>
						</thead>
						<tbody class="table-primary text-dark">
							<?php
								$websites = $db->QueryFetchArrayAll("SELECT a.*, b.time FROM ptc_websites a LEFT JOIN ptc_packs b ON b.id = a.ptc_pack WHERE a.user_id = '".$data['id']."' AND a.status = '1' ORDER BY a.id DESC");
								
								if(count($websites) == 0)
								{
									echo '<td colspan="5"><center>'.$lang['l_99'].'</center></td>';
								}
								else
								{
									foreach($websites as $website) 
									{
										echo '<tr><td>'.$website['id'].'</td><td><a href="'.$website['website'].'" target="_blank">'.$website['title'].'</a></td><td>'.number_format($website['total_visits']).' '.$lang['l_295'].' x '.$website['time'].' '.$lang['l_182'].'</td><td>'.number_format($website['received_today']).' / '.($website['daily_limit'] == 0 ? '&#8734;' : number_format($website['daily_limit'])).'</td><td>'.number_format($website['total_visits'] - $website['received']).' '.$lang['l_295'].'</td></tr>';
									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		  </div>
		</div>
	  </div>
    </main>
	<script type="text/javascript">
		function get_price() {
			var pack = $('#ad_pack').val();
			var visits = $('#ad_visits').val();
			var redirect = 0;
			if($('#redirect').is(":checked"))
			{
				redirect = 1;
			}

			if(visits >= 1) {
				$.get("system/ajax.php?a=calculatePTC", {
					visits: visits,
					pack: pack,
					redirect: redirect
				}, function(a) {
					$('#adprice').html(a);
				})
			}
		}
		
		$('#dailyLimitSelect').on('change', function(e) {
			if ($(this).val() == '0') {
				$("#daily_clicks").prop('disabled', true).val('')
			} else {
				$("#daily_clicks").prop('disabled', false).val('250')
			}
		});
	
		$(function () { $('[data-toggle="tooltip"]').tooltip() }); 
	</script>