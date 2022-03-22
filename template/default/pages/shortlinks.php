<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Initialise captcha
	require('system/libs/captcha/session.class.php');
    require('system/libs/captcha/captcha.class.php');
    CBCaptcha::setIconsFolderPath('../../../static/img/captcha/');
	
	$errMessage = '';
	if(isset($_GET['x']) && $_GET['x'] == 'success')
	{
		if(isset($_SESSION['shortlink_reward']))
		{
			$errMessage = '<div class="alert alert-success mt-0" role="alert">'.lang_rep($lang['l_447'], array('-NUM-' => $_SESSION['shortlink_reward'])).'</div>';
			unset($_SESSION['shortlink_reward']);
		}
	}
	elseif(isset($_GET['x']) && $_GET['x'] == 'time')
	{
		if(isset($_SESSION['shortlink_time']))
		{
			$errMessage = '<div class="alert alert-danger mt-0" role="alert">'.$lang['l_448'].'</div>';
			unset($_SESSION['shortlink_time']);
		}
	}
?>
<link href="static/css/icon-captcha.min.css" rel="stylesheet" type="text/css">
<main role="main" class="container">
  <div class="row">
	<?php 
		require_once(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
	?>
	<div class="col-xl-9 col-lg-8 col-md-7">
		<div class="my-3 ml-2 p-3 bg-white rounded box-shadow box-style">
		  <div id="grey-box">
			<div class="title">
				<?=$lang['l_226']?>
			</div>
			<div class="content">
				<?php 
					echo $errMessage; 
					
					// VPN / Proxy Warning
					$proxy = false;
					if(!empty($UserIPData) && $UserIPData['status'] == 1){
						echo '<div class="alert alert-danger text-center" role="alert"><i class="fa fa-exclamation-triangle"></i> <b>'.$lang['l_484'].'</b> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><br />'.$lang['l_485'].'</div>';
						$proxy = true;
					}
				?>
				<div class="alert alert-warning">
					<i class="fa fa-info-circle"></i> <?php echo ($config['shortlink_reset'] == 1 ? $lang['l_456'] : $lang['l_227']); ?><br /><br />
					<small><i class="fa fa-exclamation-triangle"></i> <i><?php echo $lang['l_458']; ?></i></small>
				</div>
				<div class="alert alert-info text-center"><?php echo lang_rep($lang['l_457'], array('-SUM-' => $data['shortlinks_contest'])); ?></div>
				<div class="card text-dark bg-dark text-center w-100">
					<div class="card-header text-light">
						<b><?php echo $lang['l_425']; ?></b>
					</div>
					<div class="card-body p-1">
						<table class="table table-sm table-light text-dark table-striped">
						  <thead>
							<tr>
							  <th scope="col"></th>
							  <th scope="col" class="text-center"><?php echo $lang['l_225']; ?></th>
							  <th scope="col" class="text-center"><?php echo $lang['l_203']; ?></th>
							  <th scope="col"></th>
							</tr>
						  </thead>
						  <tbody>
						  <?php
							$remTime = (strtotime(date('j F Y'))+86460) - time();
							$shortlinks = $db->QueryFetchArrayAll("SELECT a.*, b.count, b.time FROM shortlinks_config a LEFT JOIN shortlinks_done b ON b.short_id = a.id AND b.user_id = '".$data['id']."' WHERE a.status = '1' ORDER BY a.reward DESC");

							if(empty($shortlinks))
							{
								echo '<tr><td colspan="4">'.$lang['l_121'].'</td></tr>';
							}
							else
							{
								$counters = array();
								$totalBits = 0;
								$totalVisits = 0;
								if($proxy === true)
								{
									echo '<tr><td class="text-center p-4" colspan="5"><div class="alert alert-danger text-center mb-0" role="alert"><i class="fa fa-exclamation-triangle"></i> <b>'.$lang['l_486'].'</b> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><br />'.$lang['l_487'].'</div></td></tr>';
								}

								foreach($shortlinks as $shortlink) {
									$remain = ($shortlink['daily_limit']-$shortlink['count']);
									$remain = ($remain < 0 ? 0 : $remain);
									$totalBits = $totalBits + ($shortlink['reward']*$shortlink['daily_limit']);
									$totalVisits = $totalVisits + $shortlink['daily_limit'];
									
									if($config['shortlink_reset'] == 1)
									{
										$remTime = ($shortlink['time']+86400) - time();
										$remTime = ($remTime <= 0 ? ($remTime+60) : $remTime);
									}

									if($remain <= 0)
									{
										$counters[] = $shortlink['id'];
									}
									
									if($proxy === false)
									{
										echo '<tr><td class="align-middle">'.$shortlink['name'].'</td><td class="align-middle text-center"><b class="badge badge-dark">'.$shortlink['reward'].' Bits</b></td><td class="align-middle text-center"><b class="badge badge-dark">'.$remain.'/'.$shortlink['daily_limit'].'</b></td><td class="text-right">'.($remain > 0 ? '<button onclick="goShortlink(\''.$shortlink['id'].'\');" class="btn btn-success btn-sm"  type="submit">'.$lang['l_224'].' <i class="fa fa-external-link fa-fw"></i></button>' : '<span id="short_'.$shortlink['id'].'" class="badge badge-light p-2" data-seconds-left="'.$remTime.'"></span><div class="badge badge-light p-2" id="staticTime_'.$shortlink['id'].'">'.gmdate('H:i:s', $remTime).'</div>').'</td></tr>';
									}
								}
							}
						  ?>
						  </tbody>
						  <tfoot>
							<tr>
							  <th colspan="4" class="text-center"><span class="badge badge-dark p-2"><?php echo lang_rep($lang['l_219'], array('-BITS-' => number_format($totalBits, 2), '-VISITS-' => number_format($totalVisits))); ?></span></th>
							</tr>
						  </tfoot>
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
<div class="modal fade" id="goShortlink" tabindex="-1" role="dialog" aria-labelledby="goShortlink" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
	  <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
	  <div class="modal-body text-center">
		<div id="status"><div class="alert alert-warning" role="alert"><i class="fa fa-exclamation-triangle"></i> <?php echo $lang['l_324']; ?> <i class="fa fa-exclamation-triangle"></i></div></div>
		<div class="d-flex justify-content-center" id="captchaBox"><div class="captcha-holder"></div></div>
	  </div>
	</div>
  </div>
</div>
<?php 
	if($proxy === false)
	{
		echo '<script src="static/js/simple.timer.js"></script><script async src="static/js/captcha.min.js"></script>';

		$script = "var waitMsg = \"".$lang['l_145']."\";
		var captchaMsg = \"".$lang['l_142']."\";
		function validateShortlink(sid){
			var captchaID = $('input[name=\"captcha-idhf\"]').val();
			var captchaIcon = $('input[name=\"captcha-hf\"]').val();
			$('#status').html('<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-cog fa-spin fa-fw\"></i> '+waitMsg+'</div>').fadeIn('fast');

			$.post('system/ajax.php',
			{
				a: 'getShortlink',
				data: sid,
				token: '".$token."',
				'captcha-idhf': captchaID,
				'captcha-hf': captchaIcon
			},
			function(response) {
				if(response.status == '600') {
					goShortlink(sid);
					$('#status').html(response.message).fadeIn('slow');
				} else if(response.status == '500') {
					$('#captchaBox').hide();
					$('#status').html(response.message).fadeIn('slow');
				} else {
					$('#status').html(response.message).fadeIn('slow');
					window.setTimeout(function(){window.location.replace(response.shortlink);}, 500);
				}
			},'json');
		}

		function goShortlink(sid) {
			$('#goShortlink').modal({backdrop: 'static', keyboard: false});
			$('.captcha-holder').CBCaptcha({
				clickDelay: 500,
				invalidResetDelay: 2500,
				requestIconsDelay: 1500,
				loadingAnimationDelay: 1500,
				hoverDetection: true,
				enableLoadingAnimation: true,
				validationPath: 'system/libs/captcha/request.php'
			}).bind('success.CBCaptcha', function(e, id) {
				validateShortlink(sid);
			});
		}";

		if(count($counters) > 0)
		{
			$script .= '$(document).ready(function(){';
			foreach($counters as $counter)
			{
				$script .= "$('#staticTime_".$counter."').hide(); $('#short_".$counter."').startTimer({onComplete: function(element){window.location.reload();}}); $('#short_".$counter." div').css('display','inline');";
			}
			$script .= '});';
		}

		$packer = new JavaScriptPacker($script, 'Normal', true, false);
		$packed = $packer->pack();
		
		echo '<script>'.$packed.'</script>';
	}
?>