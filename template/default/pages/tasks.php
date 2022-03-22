<?php
    if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$errMessage = '';
	if(isset($_POST['submit'])){
		$job_id = $db->EscapeString($_POST['job_id']);
		$requirement = $db->EscapeString($_POST['requirement']);

		$job = $db->QueryFetchArray("SELECT * FROM `jobs` WHERE `id`='".$job_id."' LIMIT 1");
		if(empty($job['id'])) {
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_10'].'</div>';
		} elseif(empty($requirement)) {
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_08'].'</div>';
		} elseif($job['url_required'] == 1 && !preg_match('/^(http|https):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i', $requirement)) {
			$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_184'].'</div>';
		} else {
			$check_job = $db->QueryFetchArray("SELECT * FROM `jobs_done` WHERE `uid`=".$data['id']." AND `job_id`='".$job_id."' ORDER BY `time` DESC LIMIT 1");

			if(empty($check_job['id']) || $check_job['status'] == 2) {
				$db->Query("INSERT INTO `jobs_done` (`job_id`, `uid`, `requirement`, `reward`,`type`,`membership`,`time`) VALUES ('".$job_id."','".$data['id']."','".$requirement."','".$job['reward']."','".$job['type']."','".$job['membership']."','".time()."')");
				$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_11'].'</div>';
			} elseif($check_job['status'] == 1) {
				$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_12'].'</div>';
			} else {
				$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_13'].'</div>';
			}
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
				<?=$errMessage?>
                <div id="grey-box">
                    <div class="title">
                        <?=$lang['l_04']?>
                    </div>
                    <div class="content">
					<?php
						$jobs = $db->QueryFetchArrayAll("SELECT a.*, b.membership AS mem_name FROM jobs a LEFT JOIN memberships b ON b.id = a.membership ORDER BY a.time DESC");

						foreach($jobs as $job) {
							$job_status = $db->QueryFetchArray("SELECT * FROM `jobs_done` WHERE `job_id`='".$job['id']."' AND `uid`='".$data['id']."' ORDER BY `time` DESC LIMIT 1");
							$description = htmlspecialchars_decode($job['description']);
					?>
						<div class="card mb-2">
						  <div class="card-header">
							<h1 class="text-success"><?=$job['title']?></h1>
							<p class="text-danger text-center mb-0"><?=$lang['l_02']?>: <?=$job['id']?> - <?=$lang['l_03']?>: <?=($job['type'] == 0 ? $job['reward'].' '.$lang['l_337'] : number_format($job['reward'], 0).' '.($data['membership_id'] == $job['membership'] ? $job['mem_name'] : ($data['membership_id'] > 1 ? $data['mem_name'] : $job['mem_name'])).' '.$lang['l_234'])?></p>
						  </div>
						  <div class="card-body text-dark">
							<?php 
								echo $description;

								if(empty($job_status['id']) || $job_status['status'] == 2) {
							?>
								<form method="post">
								  <input type="hidden" name="job_id" value="<?=$job['id']?>">
								  <div class="form-row">
									<div class="form-group col-md-12">
									  <div class="input-group mt-3 mb-0">
										<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-link"></i></div></div>
										<input type="text" class="form-control" name="requirement" placeholder="<?=$job['requirement']?>">
										<input type="submit" class="btn btn-primary d-inline" name="submit" value="<?=$lang['l_07']?>" />
									  </div>
									</div>
								  </div>
								</form>
							<?php
								} elseif($job_status['status'] == 0) {
									echo '<div class="alert alert-info text-center mb-0" role="alert">'.$lang['l_05'].'</div>';
								} else { 
									echo '<div class="alert alert-success text-center mb-0" role="alert">'.lang_rep($lang['l_06'], array('-REWARD-' => ($job_status['type'] == 0 ? $job_status['reward'].' '.$lang['l_337'] : number_format($job_status['reward'], 0).' '.$lang['l_01']))).'</div>';
								}
							?>
						  </div>
						</div>
					<?php } ?>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </main>