<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$errMessage = '';
	if(isset($_GET['edit'])){
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `jobs` WHERE `id`='".$id."'");

		if(isset($_POST['submit']) && !empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['requirement']) && is_numeric($_POST['type']) && is_numeric($_POST['reward'])){
			$title = $db->EscapeString($_POST['title']);
			$description = htmlentities($_POST['description'], ENT_QUOTES);
			$requirement = $db->EscapeString($_POST['requirement']);
			$url_required = $db->EscapeString($_POST['url_required']);
			$type = $db->EscapeString($_POST['type']);
			$reward = $db->EscapeString($_POST['reward']);

			$db->Query("UPDATE `jobs` SET `title`='".$title."', `description`='".$description."', `requirement`='".$requirement."', `url_required`='".$url_required."', `type`='".$type."', `reward`='".$reward."' WHERE `id`='".$id."'");
			$errMessage = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Job was successfully edited!</div>';
		}
	}elseif(isset($_GET['del']) && is_numeric($_GET['del'])){
		$del = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `jobs` WHERE `id`='".$del."'");
		$db->Query("DELETE FROM `jobs_done` WHERE `job_id`='".$del."'");
	}elseif(isset($_GET['pending']) && isset($_GET['approve'])  && is_numeric($_GET['approve'])){
		$job_id = $db->EscapeString($_GET['approve']);
		$job = $db->QueryFetchArray("SELECT * FROM `jobs_done` WHERE `id`='".$job_id."' AND `status`= '0' LIMIT 1");
		
		if(!empty($job['uid'])){
			if($job['type'] == 1){
				$user = $db->QueryFetchArray("SELECT `membership`, `membership_id` FROM `users` WHERE `id`='".$job['uid']."' LIMIT 1");

				if($user['membership'] == 0) {
					$membership = time()+(86400*$job['reward']);
					$db->Query("UPDATE `users` SET `membership`='".$membership."', `membership_id`='".$job['membership']."' WHERE `id`='".$job['uid']."'");
				} else {
					$membership = ((86400*$job['reward'])+$user['membership']);
					$db->Query("UPDATE `users` SET `membership`='".$membership."' WHERE `id`='".$job['uid']."'");
				}
			} else {
				$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$job['reward']."', `today_revenue`=`today_revenue`+'".$job['reward']."', `total_revenue`=`total_revenue`+'".$job['reward']."' WHERE `id`='".$job['uid']."'");
			}

			$db->Query("UPDATE `jobs_done` SET `status`='1' WHERE `id`='".$job_id."'");
			$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Job was successfully approved!</div>';
		}
	}elseif(isset($_GET['pending']) && isset($_GET['reject'])  && is_numeric($_GET['reject'])){
		$job_id = $db->EscapeString($_GET['reject']);
		$db->Query("UPDATE `jobs_done` SET `status`='2' WHERE `id`='".$job_id."'");
		$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Job was successfully rejected!</div>';
	}

	if(isset($_POST['add']))
	{
		$title = $db->EscapeString($_POST['title']);
		$description = htmlentities($_POST['description'], ENT_QUOTES);
		$requirement = $db->EscapeString($_POST['requirement']);
		$membership = $db->EscapeString($_POST['membership']);
		$url_required = $db->EscapeString($_POST['url_required']);
		$type = $db->EscapeString($_POST['type']);
		$reward = $db->EscapeString($_POST['reward']);
	
		if(!empty($title ) && !empty($description) && !empty($requirement) && is_numeric($type) && !empty($reward)){
			$db->Query("INSERT INTO `jobs`(`title`,`description`,`requirement`,`url_required`,`reward`,`type`,`membership`,`time`) VALUES ('".$title."', '".$description."', '".$requirement."', '".$url_required."', '".$reward."', '".$type."','".$membership."','".time()."')");
			$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Job was successfully added!</div>';
		}else{
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> You have to complete all fields!</div>';
		}
	}
	elseif(isset($_POST['reject_jobs']))
	{
		$selected = $db->EscapeString($_POST['job']);
	
		foreach($selected as $key => $value) {
			$db->Query("UPDATE `jobs_done` SET `status`='2' WHERE `id`='".$key."'");
		}

		$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Jobs were successfully rejected!</div>';
	}
	elseif(isset($_POST['approve_jobs']))
	{
		$selected = $db->EscapeString($_POST['job']);

		foreach($selected as $key => $value) {
			$job = $db->QueryFetchArray("SELECT * FROM `jobs_done` WHERE `id`='".$key."' LIMIT 1");

			if(!empty($job['uid'])){
				if($job['type'] == 1){
					$user = $db->QueryFetchArray("SELECT `membership`, `membership_id` FROM `users` WHERE `id`='".$job['uid']."' LIMIT 1");

					if($user['membership'] == 0) {
						$membership = time()+(86400*$job['reward']);
						$db->Query("UPDATE `users` SET `membership`='".$membership."', `membership_id`='".$job['membership']."' WHERE `id`='".$job['uid']."'");
					} else {
						$membership = ((86400*$job['reward'])+$user['membership']);
						$db->Query("UPDATE `users` SET `membership`='".$membership."' WHERE `id`='".$job['uid']."'");
					}
				} else {
					$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$job['reward']."' WHERE `id`='".$job['uid']."'");
				}
			}

			$db->Query("UPDATE `jobs_done` SET `status`='1' WHERE `id`='".$key."'");
		}
		
		$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Jobs were successfully approved!</div>';
	}

	if(isset($_GET['edit']) && !empty($edit['id'])){
?>
<section id="content" class="container_12 clearfix"><?=$errMessage?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Job</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Title</strong></label>
					<div><input type="text" name="title" value="<?=(isset($_POST['title']) ? $_POST['title'] : $edit['title'])?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Description &amp; Requirements</strong></label>
					<div><textarea class="editor" name="description" id="description" required /><?=(isset($_POST['description']) ? $_POST['description'] : html_entity_decode(htmlspecialchars_decode($edit['description'])))?></textarea></div>
				</div>
				<div class="row">
					<label><strong>Requirement</strong></label>
					<div><input type="text" name="requirement" value="<?=(isset($_POST['requirement']) ? $_POST['requirement'] : $edit['requirement'])?>" required /></div>
				</div>
				<div class="row">
					<label><strong>URL Required</strong></label>
					<div><select name="url_required"><option value="0">No</option><option value="1"<?=(!isset($_POST['url_required']) && $edit['url_required'] == 1 ? ' selected' : (isset($_POST['url_required']) && $_POST['url_required'] == 1 ? ' selected' : ''))?>>Yes</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward Type</strong></label>
					<div><select name="type"><option value="0">Bits</option><option value="1"<?=(!isset($_POST['type']) && $edit['type'] == 1 ? ' selected' : (isset($_POST['type']) && $_POST['type'] == 1 ? ' selected' : ''))?>>Membership Days</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward Membership</strong><small>Only for "Membership Days" reward</small></label>
					<div><select name="membership"><option value="2">Silver</option><option value="3"<?=(!isset($_POST['membership']) && $edit['membership'] == 3 ? ' selected' : (isset($_POST['membership']) && $_POST['membership'] == 3 ? ' selected' : ''))?>>Gold</option><option value="4"<?=(!isset($_POST['membership']) && $edit['membership'] == 4 ? ' selected' : (isset($_POST['membership']) && $_POST['membership'] == 4 ? ' selected' : ''))?>>Platinum</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward</strong></label>
					<div><input type="text" name="reward" value="<?=(isset($_POST['reward']) ? $_POST['reward'] : $edit['reward'])?>" required /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Edit Job" name="submit" />
				</div>
			</div>
		</form>
	</div>
</section>
<?php }elseif(isset($_GET['add'])){?>
<section id="content" class="container_12 clearfix"><?=$errMessage?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Add Job</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Title</strong></label>
					<div><input type="text" name="title" placeholder="Create youtube video / blog post / banner ads" required /></div>
				</div>
				<div class="row">
					<label><strong>Description &amp; Requirements</strong></label>
					<div><textarea class="editor" name="description" id="description" required /></textarea></div>
				</div>
				<div class="row">
					<label><strong>Requirement</strong></label>
					<div><input type="text" name="requirement" placeholder="URL of the video / blog post / website ads" required /></div>
				</div>
				<div class="row">
					<label><strong>URL Required</strong></label>
					<div><select name="url_required"><option value="0">No</option><option value="1">Yes</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward Type</strong></label>
					<div><select name="type"><option value="0">Bits</option><option value="1">Membership Days</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward Membership</strong><small>Only for "Membership Days" reward</small></label>
					<div><select name="membership"><option value="2">Silver</option><option value="3">Gold</option><option value="4">Platinum</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward</strong></label>
					<div><input type="text" name="reward" placeholder="100" required /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Add Job" name="add" />
				</div>
			</div>
		</form>
	</div>
</section>
<?php }elseif(isset($_GET['pending'])){?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Completed Jobs</h1>
	<div class="grid_12">
		<div class="box">
			<form method="POST">
			<table class="styled">
				<thead>
					<tr>
						<th></th>
						<th width="10">#</th>
						<th>User</th>
						<th>Job ID</th>
						<th>Job Requirement</th>
						<th>Reward</th>
						<th>Status</th>
						<th>Time</th>
						<th width="90">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$page = (isset($_GET['p']) ? $_GET['p'] : 0);
						$limit = 20;
						$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

						$total_query = "";
						$select_query = "";
						if($_GET['pending'] == 'only')
						{
							$total_query = " WHERE `status`='0'";
							$select_query = " WHERE a.status = '0'";
						}

						$total_pages = $db->QueryGetNumRows("SELECT `id` FROM `jobs_done`".$total_query);
						include('../system/libs/Paginator.php');

						$urlPattern = GetHref('p=(:num)');
						$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
						$paginator->setMaxPagesToShow(5);
					
						$jobs = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM jobs_done a LEFT JOIN users b ON b.id = a.uid".$select_query." ORDER BY a.time DESC LIMIT ".$start.",".$limit);
						if(count($jobs) == 0) {
							echo '<tr><td colspan="9" style="text-align: center">Nothing here yet!</td></tr>';
						}

						foreach($jobs as $job){
					?>	
					<tr>
						<td width="10"><input type="checkbox" name="job[<?=$job['id']?>]" /></td>
						<td><?=$job['id']?></td>
						<td><a href="index.php?x=users&edit=<?=$job['uid']?>"><?=$job['username']?></a></td>
						<td><a href="index.php?x=jobs&edit=<?=$job['job_id']?>"><?=$job['job_id']?></a></td>
						<td><?=$job['requirement']?></td>
						<td><?=($job['type'] == 1 ? 'Membership<br />'.number_format($job['reward'], 0).' days' : $job['reward'].' Bits')?></td>
						<td class="center"><?=($job['status'] == 2 ? '<font color="red">Rejected</font>' : ($job['status'] == 1 ? '<font color="green">Complete</font>' : '<font color="blue">Pending</font>'))?></td>
						<td><?=date('d M Y - H:i', $job['time'])?></td>
						<td class="center">
							<?php if($job['status'] == 0) { ?>
								<a href="index.php?x=jobs&pending&approve=<?=$job['id']?>" class="button small grey tooltip" data-gravity=s title="Approve"><i class="icon-ok"></i></a>
								<a href="index.php?x=jobs&pending&reject=<?=$job['id']?>" class="button small grey tooltip" data-gravity=s title="Reject"><i class="icon-remove"></i></a>
							<?php 
								} else {
									echo 'N/A';
								}
							?>
						</td>
					</tr>
					<?php }?>
					<tr>
						<td colspan="7"></td>
						<td colspan="2" class="center">
							<input type="submit" name="reject_jobs" value="Reject" onclick="return confirm('You sure you want to reject these jobs?');" class="button small grey tooltip" data-gravity=s title="Reject Jobs" />
							<input type="submit" name="approve_jobs" value="Approve" onclick="return confirm('You sure you want to approve these jobs?');" class="button small grey tooltip" data-gravity=s title="Approve Jobs" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			<?php if($total_pages > $limit){ ?>
			<div class="dataTables_wrapper">
			<div class="footer">
				<div class="dataTables_paginate paging_full_numbers">
				<?php 
					if ($paginator->getPrevUrl()) {
						echo '<a class="first paginate_button" href="'.$paginator->getPrevUrl().'">&laquo; Previous</a></li>';
					} else {
						echo '<a class="first paginate_button">&laquo; Previous</a>';
					}
					
					echo '<span>';

					foreach ($paginator->getPages() as $page) {
						if ($page['url']) {
							if($page['isCurrent']) {
								echo '<a class="paginate_active">'.$page['num'].'</a>';
							} else {
								echo '<a class="paginate_button" href="'. $page['url'].'">'.$page['num'].'</a>';
							}
						} else {
							echo '<a class="paginate_active">'.$page['num'].'</a>';
						}
					}
					
					echo '<span>';
					
					if ($paginator->getNextUrl()) {
						echo '<a class="last paginate_button" href="'.$paginator->getNextUrl().'">Next &raquo;</a></li>';
					}
				?>
				</div>
			</div>
			</div>
			<?php } ?>
		</div>
	</div>
</section>
<?php }else{?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Jobs</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="10">ID</th>
						<th>Title</th>
						<th>Reward</th>
						<th>Time</th>
						<th width="90">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$jobs = $db->QueryFetchArrayAll("SELECT * FROM `jobs`");

						if(count($jobs) == 0) {
							echo '<tr><td colspan="5" style="text-align: center">Nothing here yet!</td></tr>';
						}

						foreach($jobs as $job){
					?>	
					<tr>
						<td><?=$job['id']?></td>
						<td><?=$job['title']?></td>
						<td><?=($job['type'] == 1 ? number_format($job['reward'], 0).' VIP days' : $job['reward'].' Bits')?></td>
						<td><?=date('d M Y - H:i', $job['time'])?></td>
						<td class="center">
							<a href="index.php?x=jobs&edit=<?=$job['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=jobs&del=<?=$job['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php }?>