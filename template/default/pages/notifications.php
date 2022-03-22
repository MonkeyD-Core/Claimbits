<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
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
						<?=$lang['l_401']?>
					</div>
					<div class="content">
						<div class="card text-dark p-1 w-100" style="max-height:800px">
							<div class="table-responsive">
								<table class="table table-sm table-condensed table-striped table-hover mb-0">
									<tbody>
										<?php
											$notifications = $db->QueryFetchArrayAll("SELECT * FROM `notifications` WHERE `user_id`='".$data['id']."' ORDER BY `time` DESC LIMIT 50");

											if(empty($notifications)) {
												echo '<div class="alert alert-info" role="alert">You don\'t have any notifications yet!</div>';
											} else {
												foreach($notifications as $notification) {
													echo get_notification($notification['notify_id'], $notification['value'], $notification['time'], $notification['read']);
												}
											}
											
											$db->Query("UPDATE `notifications` SET `read`='1' WHERE `user_id`='".$data['id']."' AND `read`='0'");
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