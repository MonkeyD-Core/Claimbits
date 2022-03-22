<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$page = (isset($_GET['p']) ? $_GET['p'] : '');
	$limit = 20;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$message = '';
	if(isset($_POST['edit_set']))
	{
		$config['ptc_redirect_price'] = ($_POST['ptc_redirect_price'] < 1 ? 1 : ($_POST['ptc_redirect_price'] > 100 ? 100 : (int)$_POST['ptc_redirect_price']));

		$db->Query("UPDATE `site_config` SET `config_value`='".$config['ptc_redirect_price']."' WHERE `config_name`='ptc_redirect_price'");
		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings successfully changed!</div>';
	}
	
	if(isset($_GET['edit']))
	{
		$edit = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `ptc_websites` WHERE `id`='".$edit."'");

		if(isset($_POST['submit']))
		{
			$user_id = $db->EscapeString($_POST['user']);
			$url = $db->EscapeString($_POST['url']);
			$title = $db->EscapeString($_POST['title']);
			$clicks = $db->EscapeString($_POST['clicks']);
			$limit = $db->EscapeString($_POST['limit']);
			$status = $db->EscapeString($_POST['status']);
			$redirect = $db->EscapeString($_POST['redirect']);
			$ptc_pack = $db->EscapeString($_POST['ptc_pack']);

			$db->Query("UPDATE `ptc_websites` SET `user_id`='".$user_id."', `website`='".$url."', `title`='".$title."', `total_visits`='".$clicks."', `daily_limit`='".$limit."', `redirect`='".$redirect."', `status`='".$status."', `ptc_pack`='".$ptc_pack."' WHERE `id`='".$edit['id']."'");
			$edit = $db->QueryFetchArray("SELECT * FROM `ptc_websites` WHERE `id`='".$edit['id']."'");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Page successfully edited!</div>';
		}
	}
	elseif(isset($_GET['add']))
	{
		if(isset($_POST['submit']))
		{
			$url = $db->EscapeString($_POST['url']);
			$title = $db->EscapeString($_POST['title']);
			$clicks = $db->EscapeString($_POST['clicks']);
			$limit = $db->EscapeString($_POST['limit']);
			$redirect = $db->EscapeString($_POST['redirect']);
			$ptc_pack = $db->EscapeString($_POST['ptc_pack']);

			$db->Query("INSERT INTO `ptc_websites` (`user_id`,`website`,`title`,`total_visits`,`daily_limit`,`ptc_pack`,`redirect`,`status`,`added_time`)VALUE('".$data['id']."', '".$url."', '".$title."', '".$clicks."', '".$limit."', '".$ptc_pack."', '".$redirect."', '1','".time()."')");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Page successfully added!</div>';
		}
	}
	elseif(isset($_GET['del']) && is_numeric($_GET['del']))
	{
		$del = $db->EscapeString($_GET['del']); 
		$db->Query("DELETE FROM `ptc_websites` WHERE `id`='".$del."'");
	}
	elseif(isset($_GET['pack_del']))
	{
		$id = $db->EscapeString($_GET['pack_del']);
		$db->Query("DELETE FROM `ptc_packs` WHERE `id`='".$id."'");
	}
	elseif(isset($_GET['pack_edit']))
	{
		$id = $db->EscapeString($_GET['pack_edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `ptc_packs` WHERE `id`='".$id."' LIMIT 1");
		
		if(isset($_POST['edit_pack'])) {
			$price = $db->EscapeString($_POST['price']);
			$time = $db->EscapeString($_POST['time']);
			$reward = $db->EscapeString($_POST['reward']);
			
			$db->Query("UPDATE `ptc_packs` SET `price`='".$price."', `reward`='".$reward."', `time`='".$time."' WHERE `id`='".$id."'");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> PTC pack was successfully edited</div>';
		}
	}
	
	if(isset($_POST['add_pack'])) {
			$price = $db->EscapeString($_POST['price']);
			$time = $db->EscapeString($_POST['time']);
			$reward = $db->EscapeString($_POST['reward']);
		
		if(empty($price) || empty($time) || empty($reward)) {
			$message = '<div class="alert error"><span class="icon"></span><strong>error!</strong> Please complete required fields</div>';
		} else {
			$db->Query("INSERT INTO `ptc_packs` (`price`,`reward`,`time`) VALUES ('".$price."','".$reward."','".$time."')");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Ad pack was successfully added</div>';
		}
	}
?>
<section id="content" class="container_12 clearfix ui-sortable">
	<?php
	if(isset($_GET['edit'])){
		echo $message;
	?>
		<h1 class="grid_12">Websites</h1>
		<div class="grid_12">
		  <form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Website</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>User ID</strong></label>
					<div><input type="text" name="user" value="<?=(isset($_POST['user']) ? $db->EscapeString($_POST['user']) : $edit['user_id'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Website URL</strong></label>
					<div><input type="text" name="url" value="<?=(isset($_POST['url']) ? $db->EscapeString($_POST['url']) : $edit['website'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Website Title</strong></label>
					<div><input type="text" name="title" value="<?=(isset($_POST['title']) ? $db->EscapeString($_POST['title']) : $edit['title'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Total Visits</strong></label>
					<div><input type="text" name="clicks" value="<?=(isset($_POST['clicks']) ? $db->EscapeString($_POST['clicks']) : $edit['total_visits'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Daily Limit</strong><small>Set 0 to disable</small></label>
					<div><input type="text" name="limit" value="<?=(isset($_POST['limit']) ? $db->EscapeString($_POST['limit']) : $edit['daily_limit'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>PTC Pack</strong></label>
					<div>
						<select name="ptc_pack">
							<?php
								$ptcPacks = $db->QueryFetchArrayAll("SELECT * FROM `ptc_packs` ORDER BY `id` ASC");
								
								foreach($ptcPacks as $pack)
								{
									echo '<option value="'.$pack['id'].'"'.($edit['ptc_pack'] == $pack['id'] ? ' selected' : '').'>'.$pack['time'].' seconds - '.$pack['price'].' Satoshi</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="row">
					<label><strong>Status</strong></label>
					<div><select name="status"><option value="1">Enabled</option><option value="0"<?=($edit['status'] == 0 ? ' selected' : '')?>>Waiting payment</option><option value="2"<?=($edit['status'] == 2 ? ' selected' : '')?>>Disabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Redirect after completion</strong></label>
					<div><select name="redirect"><option value="0">Disabled</option><option value="1"<?=($edit['redirect'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		   </form>
		</div>
	<?php
		}
		elseif(isset($_GET['add']))
		{
			echo $message;
	?>
		<h1 class="grid_12">Websites</h1>
		<div class="grid_12">
		  <form action="" method="post" class="box">
			<div class="header">
				<h2>Add Website</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Website URL</strong></label>
					<div><input type="text" name="url" value="<?=(isset($_POST['url']) ? $db->EscapeString($_POST['url']) : '')?>" placeholder="http://" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Website Title</strong></label>
					<div><input type="text" name="title" value="<?=(isset($_POST['title']) ? $db->EscapeString($_POST['title']) : '')?>" placeholder="Website Name" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Total Visits</strong></label>
					<div><input type="text" name="clicks" value="<?=(isset($_POST['clicks']) ? $db->EscapeString($_POST['clicks']) : '')?>" placeholder="1000" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Daily Limit</strong><small>Set 0 to disable</small></label>
					<div><input type="text" name="limit" value="<?=(isset($_POST['limit']) ? $db->EscapeString($_POST['limit']) : '')?>" placeholder="0" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>PTC Pack</strong></label>
					<div>
						<select name="ptc_pack">
							<?php
								$ptcPacks = $db->QueryFetchArrayAll("SELECT * FROM `ptc_packs` ORDER BY `id` ASC");
								
								foreach($ptcPacks as $pack)
								{
									echo '<option value="'.$pack['id'].'">'.$pack['time'].' seconds - '.$pack['price'].' Satoshi</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="row">
					<label><strong>Redirect after completion</strong></label>
					<div><select name="redirect"><option value="0">Disabled</option><option value="1">Enabled</option></select></div>
				</div>	
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		  </form>
		</div>
	<?php }elseif(isset($_GET['packs'])){ ?>
		<h1 class="grid_12">PTC Packs</h1>
		<div class="grid_8">
			<?=$message?>
			<div class="box">
				<table class="styled">
					<thead>
						<tr>
							<th width="25">#</th>
							<th>Duration</th>
							<th>Price</th>
							<th>Reward</th>
							<th width="120">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$packs = $db->QueryFetchArrayAll("SELECT * FROM `ptc_packs` ORDER BY `id` ASC");

							if(count($packs) == 0) {
								echo '<tr><td colspan="6" style="text-align: center">Nothing here yet!</td></tr>';
							}
							
							foreach($packs as $pack){
						?>	
						<tr>
							<td><?=$pack['id']?></td>
							<td><?=$pack['time']?> seconds</td>
							<td><?=$pack['price']?> Satoshi</td>
							<td><?=$pack['reward']?> Bits</td>
							<td class="center">
								<a href="index.php?x=sites&packs&pack_edit=<?=$pack['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
								<a href="index.php?x=sites&packs&pack_del=<?=$pack['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
							</td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="grid_4">
			<form action="" method="post" class="box">
				<div class="header">
					<h2><?=(isset($_GET['pack_edit']) && !empty($edit['id']) ? 'Edit Pack' : 'Add Pack')?></h2>
				</div>
					<div class="content">
						<div class="row">
							<label><strong>Duration</strong><small>Seconds</small></label>
							<div><input type="text" name="time" value="<?=(isset($_GET['pack_edit']) && !empty($edit['time']) ? $edit['time'] : '')?>" placeholder="10" required="required" /></div>
						</div>
						<div class="row">
							<label><strong>Price</strong><small>Satoshi</small></label>
							<div><input type="text" name="price" value="<?=(isset($_GET['pack_edit']) && !empty($edit['price']) ? $edit['price'] : '')?>" placeholder="20" required="required" /></div>
						</div>
						<div class="row">
							<label><strong>Reward</strong><small>Bits</small></label>
							<div><input type="text" name="reward" value="<?=(isset($_GET['pack_edit']) && !empty($edit['reward']) ? $edit['reward'] : '')?>" placeholder="5" required="required" /></div>
						</div>
					</div>
					<div class="actions">
						<div class="right">
							<input type="submit" value="Submit" name="<?=(isset($_GET['pack_edit']) && !empty($edit['id']) ? 'edit_pack' : 'add_pack')?>" />
						</div>
					</div>
			</form>
			<form action="" method="post" class="box">
				<div class="header">
					<h2>Settings</h2>
				</div>
					<div class="content">
						<div class="row">
							<label><strong>Extra price for redirection</strong><small>Percentage between 1 and 100</small></label>
							<div><input type="text" name="ptc_redirect_price" value="<?=$config['ptc_redirect_price']?>" placeholder="10" required="required" /></div>
						</div>
					</div>
					<div class="actions">
						<div class="right">
							<input type="submit" value="Submit" name="edit_set" />
						</div>
					</div>
			</form>
		</div>
	<?php
		}else{
	?>
		<h1 class="grid_12">Websites</h1>
		<div class="grid_12">
			<div class="box">
				<table class="styled" style="text-align: center">
					<thead>
						<tr>
							<th>#</th>
							<th>Added By</th>
							<th>Website</th>
							<th>Avertising</th>
							<th>Remaining</th>
							<th>Received (today - total)</th>
							<th>Daily Limit</th>
							<th>Price</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$sql = $db->Query("SELECT id FROM `ptc_websites`");
						$total_pages = $db->GetNumRows($sql);
						include('../system/libs/Paginator.php');

						$urlPattern = GetHref('p=(:num)');
						$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
						$paginator->setMaxPagesToShow(5);

						$websites = $db->QueryFetchArrayAll("SELECT a.*, b.username, c.price, c.time FROM ptc_websites a LEFT JOIN users b ON b.id = a.user_id LEFT JOIN ptc_packs c ON c.id = a.ptc_pack ORDER BY a.id ASC LIMIT ".$start.",".$limit."");

						foreach($websites as $website){
					?>	
						<tr>
							<td><?=$website['id']?></td>
							<td><a href="index.php?x=users&edit=<?=$website['user_id']?>"><?=$website['username']?></a></td>
							<td><a href="<?=$website['website']?>" target="_blank"><?=truncate($website['title'], 60)?></a></td>
							<td><?=(number_format($website['total_visits']).' visits x '.$website['time'].' seconds')?></td>
							<td><?=(number_format($website['total_visits']-$website['received']).' visits')?></td>
							<td><?=number_format($website['received_today']).' - '.number_format($website['received'])?></td>
							<td><?=($website['daily_limit'] > 0 ? number_format($website['daily_limit']).' visits' : 'Disabled')?></td>
							<td><?=(number_format($website['price']).' Satoshi per visits')?></td>
							<td><?=($website['status'] == 0 ? '<font color="blue">Waiting...</font>' : ($website['status'] == 1 ? ($website['received'] < $website['total_visits'] ? '<font color="green">Running</font>' : '<font color="orange">Finished</font>') : '<font color="red">Disabled</font>' ))?></td>
							<td class="center">
								<a href="index.php?x=sites&edit=<?=$website['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
								<a href="index.php?x=sites&del=<?=$website['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
							</td>
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
	<?php }?>
</section>