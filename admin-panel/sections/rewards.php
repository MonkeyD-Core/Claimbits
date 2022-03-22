<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_GET['del']) && is_numeric($_GET['del'])){
		$del = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `activity_rewards` WHERE `id`='".$del."'");
	}elseif(isset($_GET['edit'])){
		$edit = $db->EscapeString($_GET['edit']);
		$pack = $db->QueryFetchArray("SELECT * FROM `activity_rewards` WHERE `id`='".$edit."'");
		if(isset($_POST['submit'])){
			$requirements = $db->EscapeString($_POST['requirements']);
			$reward = $db->EscapeString($_POST['reward']);
			$req_type = $db->EscapeString($_POST['req_type']);
			$type = $db->EscapeString($_POST['type']);
			$type = ($type < 0 ? 0 : $type > 2 ? 2 : $type);
			
			$db->Query("UPDATE `activity_rewards` SET `requirements`='".$requirements."', `req_type`='".$req_type."', `reward`='".$reward."', `type`='".$type."' WHERE `id`='".$edit."'");

			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Reward was successfully edited!</div>';
		}
	}

	if(isset($_POST['add_reward'])){
		$requirements = $db->EscapeString($_POST['requirements']);
		$reward = $db->EscapeString($_POST['reward']);
		$req_type = $db->EscapeString($_POST['req_type']);
		$type = $db->EscapeString($_POST['type']);
		$type = ($type < 0 ? 0 : $type > 2 ? 2 : $type);
	
		if(!empty($requirements) && is_numeric($reward)){
			$db->Query("INSERT INTO `activity_rewards` (requirements, req_type, reward, type, membership) VALUES('".$requirements."', '".$req_type."', '".$reward."', '".$type."', '2')");

			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Reward was successfuly added!</div>';
		}else{
			$message = '<div class="alert error"><span class="icon"></span><strong>Error!</strong> You have to complete all fields!</div>';
		}
	}

	if(isset($_GET['edit']) && $pack['id'] != ''){
?>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Achievement</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Requirement Type</strong></label>
					<div><select name="req_type"><option value="0">Faucet Claims</option><option value="1"<?=($pack['req_type'] == 1 ? ' selected' : '')?>>Shortlinks Visits</option><option value="2"<?=($pack['req_type'] == 2 ? ' selected' : '')?>>Offerwalls Leads</option><option value="3"<?=($pack['req_type'] == 3 ? ' selected' : '')?>>Referrals</option></select></div>
				</div>
				<div class="row">
					<label><strong>Requirements</strong></label>
					<div><input type="text" name="requirements" value="<?=(isset($_POST['requirements']) ? $_POST['requirements'] : $pack['requirements'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Reward Type</strong></label>
					<div><select name="type"><option value="0">Bits</option><option value="1"<?=($pack['type'] == 1 ? ' selected' : '')?>>Membership Days</option><option value="2"<?=($pack['type'] == 2 ? ' selected' : '')?>>Satoshi (Purchase Balance)</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward</strong></label>
					<div><input type="text" name="reward" value="<?=(isset($_POST['reward']) ? $_POST['reward'] : $pack['reward'])?>" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="submit" value="Submit" />
				</div>
			</div>
        </form>
	</div>
</section>
<?php
}elseif(isset($_GET['claims'])){
	$page = (isset($_GET['p']) ? $_GET['p'] : 0);
	$limit = 20;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$total_pages = $db->QueryGetNumRows("SELECT * FROM activity_rewards_claims");
	include('../system/libs/Paginator.php');

	$urlPattern = GetHref('p=(:num)');
	$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
	$paginator->setMaxPagesToShow(5);
?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Claims (<?=number_format($total_pages)?>)</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="25">#</th>
						<th>User ID</th>
						<th>Reward</th>
						<th>Requirements</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
					<?php
					  $j = $start;
					  $packs = $db->QueryFetchArrayAll("SELECT a.*, b.req_type, b.requirements, b.reward, b.type, c.username FROM activity_rewards_claims a LEFT JOIN activity_rewards b ON b.id = a.reward_id LEFT JOIN users c ON c.id = a.user_id ORDER BY a.date DESC LIMIT ".$start.",".$limit."");
					  foreach($packs as $pack)
					  {
						$j++;
						$achievement = getAchievement($pack['req_type'], $pack['requirements'], $pack['reward'], $pack['type']);
					?>	
					<tr>
						<td><?=$j?></td>
						<td><a href="index.php?x=users&edit=<?=$pack['user_id']?>"><?=$pack['username']?></a></td>
						<td>User received <b><?=$achievement['reward']?></b></td>
						<td><?=$achievement['requirement']?></td>
						<td><?=date('Y M d - H:i', $pack['date'])?></td>
					 </tr>
					<?php }?>
				</tbody>
			</table>
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
<?}else{?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Achievements</h1>
	<div class="grid_8">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th width="25">ID</th>
						<th>Requirements</th>
						<th>Reward</th>
						<th>Claims</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					  $packs = $db->QueryFetchArrayAll("SELECT * FROM `activity_rewards` ORDER BY `id` ASC");
					  foreach($packs as $pack)
					  {
						  $achievement = getAchievement($pack['req_type'], $pack['requirements'], $pack['reward'], $pack['type']);
					?>	
					<tr>
						<td><?=$pack['id']?></td>
						<td>User needs <b><?=$achievement['requirement']?></b></td>
						<td><?=$achievement['reward']?></td>
						<td><?=number_format($pack['claims'])?> claims</td>
						<td class="center">
							<a href="index.php?x=rewards&edit=<?=$pack['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=rewards&del=<?=$pack['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
						</td>
					 </tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="grid_4">
		<form method="post" class="box">
			<div class="header">
				<h2>Add Achievement</h2>
			</div>
			<div class="content"><?=$message?>
				<div class="row">
					<label><strong>Requirement Type</strong></label>
					<div><select name="req_type"><option value="0">Faucet Claims</option><option value="1">Shortlinks Visits</option><option value="2">Offerwalls Leads</option><option value="3">Referrals</option></select></div>
				</div>
				<div class="row">
					<label><strong>Requirements</strong></label>
					<div><input type="text" name="requirements" placeholder="100" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Reward Type</strong></label>
					<div><select name="type"><option value="0">Bits</option><option value="1">Membership Days</option><option value="2">Satoshi (Purchase Balance)</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reward</strong></label>
					<div><input type="text" name="reward" placeholder="0.00" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="add_reward" value="Submit" />
				</div>
			</div>
        </form>
	</div>
</section>
<?php }?>