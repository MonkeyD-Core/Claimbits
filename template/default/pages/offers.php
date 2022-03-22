<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	/* Load offerwall settings */
	$ow_config = array();
	$ow_configs = $db->QueryFetchArrayAll("SELECT config_name,config_value FROM `offerwall_config`");
	foreach ($ow_configs as $con)
	{
		$ow_config[$con['config_name']] = $con['config_value'];
	}
	unset($ow_configs); 
	
	$method = (isset($_GET['x']) ? $_GET['x'] : 'none');
	$title = $lang['l_464'];
	$offer_wall = '<div class="alert alert-info mb-0 text-center" role="alert">'.$lang['l_465'].'</div>';
	switch($method) {
		case 'adworkmedia' :
			if(!empty($ow_config['adwork_id'])) {
				$title = 'AdWorkMedia';
				$offer_wall = '<iframe src="https://lockwall.xyz/wall/'.$ow_config['adwork_id'].'/'.$data['id'].'" width="100%" height="900px" style="border:0; padding:0; border-radius:5px; margin:0;" frameborder="0"></iframe>';
			}
			break;
		case 'cpalead' :
			if(!empty($ow_config['cpalead_link'])) {
				$title = 'CPALead';
				$offer_wall = '<iframe src="'.$ow_config['cpalead_link'].'&subid='.$data['id'].'" width="100%" height="900px" style="border:0; padding:0; border-radius:5px; margin:0;" frameborder="0"></iframe>';
			}
			break;
		case 'adscendmedia' :
			if(!empty($ow_config['adscend_publisher']) && !empty($ow_config['adscend_profile'])) {
				$title = 'AdscendMedia';
				$offer_wall = '<iframe src="https://asmwall.com/adwall/publisher/'.$ow_config['adscend_publisher'].'/profile/'.$ow_config['adscend_profile'].'?subid1=cb-'.$data['id'].'" width="100%" height="900px" frameborder="0" allowfullscreen></iframe>';
			}
			break;
		case 'wannads' :
			if(!empty($ow_config['wannads_key'])) {
				$title = 'Wannads';
				$offer_wall = '<iframe src="https://wall.wannads.com/wall?apiKey='.$ow_config['wannads_key'].'&userId='.$data['id'].'" style="width:100%;height:690px;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'personaly' :
			if(!empty($ow_config['personaly_id']) && !empty($ow_config['personaly_secret'])) {
				$title = 'Personaly';
				$offer_wall = '<iframe src="https://persona.ly/widget/?appid='.$ow_config['personaly_id'].'&userid='.$data['id'].'&gender='.($data['gender'] == 2 ? 'f' : 'm').'" style="width:100%;height:690px;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'bitswall' :
			if(!empty($ow_config['bitswall_key']) && !empty($ow_config['bitswall_secret'])) {
				$title = 'Bitswall';
				$offer_wall = '<iframe src="https://bitswall.net/offerwall/'.$ow_config['bitswall_key'].'/'.$data['id'].'" style="width:100%;height:690px;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'theoremreach' :
			if(!empty($ow_config['tr_key'])) {
				$title = 'TheoremReach';
				$offer_wall = '<iframe src="https://theoremreach.com/respondent_entry/direct?api_key='.$ow_config['tr_key'].'&user_id='.$data['id'].'" style="width:100%;height:690px;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'kiwiwall' :
			if(!empty($ow_config['kiwiwall_id'])) {
				$title = 'KiwiWall';
				$offer_wall = '<iframe src="https://www.kiwiwall.com/wall/'.$ow_config['kiwiwall_id'].'/'.$data['id'].'" style="width:100%;height:690px;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'jungle' :
			if(!empty($ow_config['mtt_reward']) && !empty($ow_config['mtt_url'])) {
				$title = 'Jungle Survey';
				$offer_wall = '<iframe src="'.$ow_config['mtt_url'].$data['id'].'" style="width:96%;height:800px;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'offerdaddy' :
			if(!empty($ow_config['offerdaddy_token'])) {
				$title = 'OfferDaddy';
				$offer_wall = '<iframe src="https://www.offerdaddy.com/wall/'.$ow_config['offerdaddy_token'].'/'.$data['id'].'/" style="height: 690px;width:100%;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'offertoro' :
			if(!empty($ow_config['offertoro_pub']) && !empty($ow_config['offertoro_app'])) {
				$title = 'OfferToro';
				$offer_wall = '<iframe src="https://www.offertoro.com/ifr/show/'.$ow_config['offertoro_pub'].'/'.$data['id'].'/'.$ow_config['offertoro_app'].'" style="height: 690px;width:100%;border:0;border-radius:5px;"></iframe>';
			}
			break;
		case 'adgem' :
			if(!empty($ow_config['adgem_app'])) {
				$title = 'AdGem';
				$offer_wall = '<iframe src="https://api.adgem.com/v1/wall?playerid='.$data['id'].'&appid='.$ow_config['adgem_app'].'" style="height: 690px;width:100%;border:0;border-radius:5px;"></iframe>';
			}
			break;
		default :
			$offer_wall = '<div class="alert alert-info mb-0 text-center" role="alert">Please select your desired offerwall from above menu!</div>';
			break;
	}

	$errMessage = '';
	if(isset($_POST['exchange']))
	{
		$credits = $db->EscapeString($_POST['credit']);
		
		if(!is_numeric($credits) || $credits < $config['credit_exchange_rate'])
		{
			$errMessage = '<div class="alert alert-danger mt-0 mb-2" role="alert"><i class="fa fa-exclamation-triangle"></i> <b>ERROR:</b> You can\'t exchange less than '.$config['credit_exchange_rate'].' credits!</div>';
		}
		elseif($credits > $data['ow_credits'])
		{
			$errMessage = '<div class="alert alert-danger mt-0 mb-2" role="alert"><i class="fa fa-exclamation-triangle"></i> <b>ERROR:</b> You don\'t have enough credits!</div>';
		}
		else
		{
			$bits = ($credits / $config['credit_exchange_rate']);
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$bits."', `today_revenue`=`today_revenue`+'".$bits."', `total_revenue`=`total_revenue`+'".$bits."', `ow_credits`=`ow_credits`-'".$credits."' WHERE `id`='".$data['id']."'");

			if($data['ref'] > 0) {
				$ref_data = $db->QueryFetchArray("SELECT a.last_activity, b.offer_com FROM users a LEFT JOIN memberships b ON b.id = a.membership_id WHERE a.id = '".$data['ref']."' LIMIT 1");
				
				if(!empty($ref_data['last_activity']) && $ref_data['last_activity'] > (time() - ($config['ref_activity']*3600))) {
					$commission = number_format(($ref_data['offer_com']/100)*$bits, 2, '.', '');
					ref_commission($data['ref'], $data['id'], $commission);
				}
			}
			
			$errMessage = '<div class="alert alert-success mt-0 mb-2" role="alert"><i class="fa fa-check"></i> <b>SUCCESS:</b> You exchanged '.number_format($credits, 2).' credits and you received '.number_format($bits, 2).' bits!</div>';
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
					<?php echo $title; ?>
				</div>
				<div class="content">
					<?php echo $errMessage; ?>
					<div class="card text-center text-dark w-100 mb-2">
						<table class="table table-striped text-center mb-0">
							<thead>
								<tr>
								  <th scope="col">Credits</th>
								  <th scope="col">Exchange Credits</th>
								  <th scope="col">Receive</th>
								  <th scope="col"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<form method="post">
									  <td class="text-info"><?php echo number_format($data['ow_credits'], 2); ?> credits</td>
									  <td> <input autocomplete="off" type="text" min="0" name="credit" id="credit" class="form-control form-control-sm" placeholder="0"> </td>
									  <td class="text-warning align-middle"><span id="receiveBits">0</span> Bits</td>
									  <td><button class="btn btn-warning btn-sm" type="submit" name="exchange"><i class="fa fa-exchange"></i></button></td>
									</form>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="infobox mb-2 text-center">
						<a href="<?php echo GenerateURL('offers&x=bitswall'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'bitswall' ? ' active' : ''); ?>">BitsWall</a>
						<a href="<?php echo GenerateURL('offers&x=wannads'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'wannads' ? ' active' : ''); ?>">Wannads</a>
						<a href="<?php echo GenerateURL('offers&x=adgem'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'adgem' ? ' active' : ''); ?>">AdGem</a>
						<a href="<?php echo GenerateURL('offers&x=offertoro'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'offertoro' ? ' active' : ''); ?>">OfferToro</a>
						<a href="<?php echo GenerateURL('offers&x=offerdaddy'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'offerdaddy' ? ' active' : ''); ?>">OfferDaddy</a>
						<a href="<?php echo GenerateURL('offers&x=jungle'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'jungle' ? ' active' : ''); ?>">JungleSurvey</a>
						<a href="<?php echo GenerateURL('offers&x=kiwiwall'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'kiwiwall' ? ' active' : ''); ?>">KiwiWall</a>
						<a href="<?php echo GenerateURL('offers&x=cpalead'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'cpalead' ? ' active' : ''); ?>">CPALead</a>
						<a href="<?php echo GenerateURL('offers&x=theoremreach'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'theoremreach' ? ' active' : ''); ?>">TheoremReach</a>
						<a href="<?php echo GenerateURL('offers&x=adworkmedia'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'adworkmedia' ? ' active' : ''); ?>">AdWorkMedia</a>
						<a href="<?php echo GenerateURL('offers&x=adscendmedia'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'adscendmedia' ? ' active' : ''); ?>">AdscendMedia</a>
						<a href="<?php echo GenerateURL('offers&x=personaly'); ?>" class="btn btn-secondary mb-1<?php echo ($method == 'personaly' ? ' active' : ''); ?>">Persona.ly</a>
					</div>
					<div class="card text-center text-dark w-100">
						<?php echo $offer_wall; ?>
					</div>
				</div>
			</div>
		  </div>
		</div>
	  </div>
    </main>
	<script type="text/javascript" >
	  $(document).ready(function(){
			function roundToTwo(num) {
				return +(Math.round(num + "e+2")  + "e-2");
			}
			$("#credit").on('keyup',function(){
				var totalcostm= $("#credit").val() / 5
				$("#receiveBits").html(roundToTwo(totalcostm));
			})
      });
  </script>