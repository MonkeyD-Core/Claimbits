<?php
    if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	if(empty($config['wmp_key']) || empty($config['wmp_secret'])) {
		redirect($config['secure_url']);
	}
	
	$progress = number_format((100/$data['hash_rate'])*$data['pending_ch'], 0);
	$progress = $progress > 100 ? 100 : $progress;
	
	$chtMessage = '';
	if(isset($_POST['transfer_ch'])){
		if($data['pending_ch'] < $data['hash_rate']) {
			$chtMessage = '<div class="alert alert-danger mt-3" role="alert">'.lang_rep($lang['l_243'], array('-MIN-' => $data['hash_rate'])).'</div>';
		} else {
			$reward = number_format($data['pending_ch']/$data['hash_rate'], 2);
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$reward."', `today_revenue`=`today_revenue`+'".$reward."', `total_revenue`=`total_revenue`+'".$reward."', `pending_ch`='0' WHERE `id`='".$data['id']."'");
			$chtMessage = '<div class="alert alert-success mt-3" role="alert">'.lang_rep($lang['l_244'], array('-TOTAL-' => $reward)).'</div>';
		}
	}
?>
	<main role="main" class="container">
      <div class="row">
		<?php 
			require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
		?>
		<div class="col-lg-9 col-md-8">
            <div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="content">
						<h4 class="text-warning text-center"><?php echo $lang['l_245']; ?></h4>
						<p class="infobox my-3"><?php echo lang_rep($lang['l_246'], array('-HASH-' => number_format($data['hash_rate']), '-TOTAL-' => number_format((20*86400)/$data['hash_rate'], 2))); ?></p>
						<script src="https://webminepool.com/lib/simple-ui.js" async></script>
						<div id="wmp-container" wmp-site-key="<?php echo $config['wmp_key']; ?>" wmp-username="<?php echo $data['id']; ?>" wmp-threads="8" wmp-throttle="0.1" wmp-autostart="false" style="background:#cccccc;color:#dc3545;width:100%;margin: 0 auto;"></div>
						<p class="text-right"><small><i><?php echo $lang['l_247']; ?></i></small></p>
					</div>
				</div>
				<?php echo $chtMessage; ?>
				<div class="row">
					<div class="col-lg-6 col-sm-12 mt-2 d-flex align-items-stretch">
					  <div id="dashboard-info">
						<table class="w-100">
							<tr>
								<td><i class="fa fa-money fa-fw"></i> <?php echo $lang['l_248']; ?>:</td>
								<td class="text-right text-success"><?php echo number_format($data['pending_ch']); ?> hashes</td>
							</tr>
							<tr>
								<td><i class="fa fa-money fa-fw"></i> <?php echo $lang['l_249']; ?>:</td>
								<td class="text-right text-success"><?php echo number_format($data['today_ch']); ?> hashes</td>
							</tr>
							<tr>
								<td><i class="fa fa-money fa-fw"></i> <?php echo $lang['l_250']; ?>:</td>
								<td class="text-right text-success"><?php echo number_format($data['total_ch']); ?> hashes</td>
							</tr>
						</table>
						<div class="progress mt-3">
						  <div class="progress-bar bg-<?php echo ($progress == 100 ? 'success' : 'info'); ?>" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $progress; ?>%</div>
						</div>
					  </div>
					</div>
					<div class="col-lg-6 col-sm-12 mt-2 d-flex align-items-stretch">
					  <div id="dashboard-info">
						<p class="text-warning text-center"><?php echo lang_rep($lang['l_252'], array('-MIN-' => number_format($data['hash_rate']))); ?></p>
						<form method="post">
							<button type="submit" name="transfer_ch" class="btn btn-primary btn-sm w-100"><i class="fa fa-exchange fa-fw"></i> <?php echo $lang['l_253']; ?></button>
						</form>
					  </div>
					</div>
				</div>
            </div>
        </div>
      </div>
    </main>