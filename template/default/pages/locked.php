<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	$adblock_locked = true;
?>
 <main role="main" class="container">
      <div class="row">
		<?php 
			if($is_online) {
				require(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
			}
		?>
		<div class="<?=($is_online ? 'col-xl-9 col-lg-8 col-md-7' : 'col-12')?>">
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						AdBlock Detected
					</div>
					<div class="content">
						<h2 class="text-center text-warning"><i class="fa fa-exclamation-triangle fa-fw"></i> Please disable your AdBlock! <i class="fa fa-exclamation-triangle fa-fw"></i></h2>
						<p>We do understand that ads are annoying most of the time, but you should understand that those ads are paying your earnings. Without ads there is no revenue and without revenue, we won't be able to pay your earnings.</p>
						<p class="text-center">To be able to use our website, you must disable your AdBlock first. After you disable your AdBlock, click <b>Redirect Me</b> button from bellow. If you don't know how to disable your adblock, try to use a different browser.</p>
						<div class="text-center mt-2"><a class="btn btn-success w-50" href="<?php echo $config['secure_url']; ?>">Redirect Me</a></div>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>