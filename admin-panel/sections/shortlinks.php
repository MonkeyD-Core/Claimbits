<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_GET['del']) && is_numeric($_GET['del'])){
		$del = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `shortlinks_config` WHERE `id`='".$del."'");
	}elseif(isset($_GET['edit'])){
		$edit = $db->EscapeString($_GET['edit']);
		$shortlink = $db->QueryFetchArray("SELECT * FROM `shortlinks_config` WHERE `id`='".$edit."'");

		if(isset($_POST['submit'])){
			$name = $db->EscapeString($_POST['name']);
			$password = $db->EscapeString($_POST['password']);
			$reward = $db->EscapeString($_POST['reward']);
			$daily_limit = $db->EscapeString($_POST['daily_limit']);
			$status = $db->EscapeString($_POST['status']);
			
			$db->Query("UPDATE `shortlinks_config` SET `name`='".$name."', `password`='".$password."', `reward`='".$reward."', `daily_limit`='".$daily_limit."', `status`='".$status."' WHERE `id`='".$edit."'");
			$shortlink = $db->QueryFetchArray("SELECT * FROM `shortlinks_config` WHERE `id`='".$shortlink['id']."'");
			
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Reward was successfully edited!</div>';
		}
	}

	if(isset($_POST['add_link'])){
		$name = $db->EscapeString($_POST['name']);
		$shortlink = $db->EscapeString($_POST['shortlink']);
		$password = $db->EscapeString($_POST['password']);
		$reward = $db->EscapeString($_POST['reward']);
		$daily_limit = $db->EscapeString($_POST['daily_limit']);
	
		if(empty($name) || empty($shortlink) || empty($password) || !is_numeric($reward) || !is_numeric($daily_limit))
		{
			$message = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> You have to complete all fields!</div>';
		}
		elseif($db->QueryGetNumRows("SELECT `id` FROM `shortlinks_config` WHERE `shortlink`='".$shortlink."' LIMIT 1") > 0)
		{
			$message = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> This shortlink already exists!</div>';
		}
		else
		{
			$valid = true;
			$getLink = get_data('http://'.$shortlink.'/api?api='.$password.'&url='.urlencode($config['secure_url']).'&alias=CB'.GenerateKey(5));
			if(empty($getLink))
			{
				$valid = false;
			}
			else
			{
				$getLink = json_decode($getLink, true);
				if($getLink['status'] === 'error' || empty($getLink['shortenedUrl']))
				{
					$valid = false;
				}
			}
			
			if(!$valid) 
			{
				$message = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> This link is not valid or API Token is not correct. Please try again!</div>';
			}
			else
			{
				$db->Query("INSERT INTO `shortlinks_config` (`name`,`shortlink`,`password`,`reward`,`daily_limit`,`status`) VALUES('".$name."', '".$shortlink."', '".$password."', '".$reward."', '".$daily_limit."', '1')");
				$message = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Shortlink was successfuly added!</div>';
			}
		}
	}
	
	if(isset($_POST['settings'])){
		$shortlink_reset = $db->EscapeString($_POST['shortlink_reset']);
		
		$db->Query("UPDATE `site_config` SET `config_value`='".$shortlink_reset."' WHERE `config_name`='shortlink_reset'");
		$config['shortlink_reset'] = $shortlink_reset;

		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Faucet settings were successfully changed!</div>';
	}

	if(isset($_GET['edit']) && !empty($shortlink['id'])){
?>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit ShortLink</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Name</strong></label>
					<div><input type="text" name="name" value="<?=$shortlink['name']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Shortlink</strong></label>
					<div><input type="text" value="<?=$shortlink['shortlink']?>" disabled /></div>
				</div>
				<div class="row">
					<label><strong>API Token</strong></label>
					<div><input type="text" name="password" value="<?=$shortlink['password']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Reward</strong></label>
					<div><input type="text" name="reward" value="<?=$shortlink['reward']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Daily Limit</strong></label>
					<div><input type="text" name="daily_limit" value="<?=$shortlink['daily_limit']?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Status</strong></label>
					<div><select name="status"><option value="1">Enabled</option><option value="0"<?=($shortlink['status'] == 0 ? ' selected' : '')?>>Disabled</option></select></div>
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
<?php }else{ ?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Shortlinks</h1>
	<div class="grid_12"><?=$message?></div>
	<div class="grid_6">
		<form method="post" class="box">
			<div class="header">
				<h2>Add Shortlink</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Name</strong></label>
					<div><input type="text" name="name" placeholder="ShortLink" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Shortlink</strong></label>
					<div><input type="text" name="shortlink" placeholder="shortlink.com" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>API Token</strong></label>
					<div><input type="text" name="password" placeholder="bc2f6ffad155c12d6ae8206e29ec3a0e" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Reward</strong></label>
					<div><input type="text" name="reward" placeholder="5.00" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Daily Limit</strong></label>
					<div><input type="text" name="daily_limit" placeholder="1" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="add_link" value="Submit" />
				</div>
			</div>
        </form>
	</div>
	<div class="grid_6">
		<form method="post" class="box">
			<div class="header">
				<h2>Settings</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Views Reset</strong><small>Midnight = all shortlinks reset every day at 00:00<br />Every 24 hours = each shortlink reset after 24 hours from last visit</small></label>
					<div><select name="shortlink_reset"><option value="0">Midnight</option><option value="1"<?=($config['shortlink_reset'] == 1 ? ' selected' : '')?>>Every 24 hours</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="settings" value="Submit" />
				</div>
			</div>
        </form>
		<p><small><b>NOTE 1:</b> Please be aware that shortlinks server time my be different. If shortlinks views are reset every midnight, some views may not be counted by those shortlinks.<br />Eg.: if on this server views are reset at mignight, on shortlink server views may be reset 2 hours later, which means in those 2 hours you may not be rewarded for visits done by your users.</small></p>
		<p><small><b>NOTE 2:</b> If views reset every 24 hours you may notice a slight decrease of shortlinks activity.</small></p>
	</div>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Shortlink</th>
						<th>API Token</th>
						<th>Daily Limit</th>
						<th>Reward</th>
						<th>Today Views</th>
						<th>Total Views</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					  $shortlinks = $db->QueryFetchArrayAll("SELECT * FROM `shortlinks_config` ORDER BY `id` ASC");
					  foreach($shortlinks as $shortlink){
					?>	
					<tr>
						<td><?=$shortlink['id']?></td>
						<td><?=($shortlink['status'] == 1 ? '<font color="green">'.$shortlink['name'].'</font>' : '<font color="red">'.$shortlink['name'].'</font>')?></td>
						<td><a href="http://<?=$shortlink['shortlink']?>" target="_blank"><?=$shortlink['shortlink']?></a></td>
						<td><?=$shortlink['password']?></td>
						<td><?=$shortlink['daily_limit']?> visits</td>
						<td><?=number_format($shortlink['reward'], 2)?> Bits</td>
						<td><?=number_format($shortlink['today_views'])?> views</td>
						<td><?=number_format($shortlink['total_views'])?> views</td>
						<td class="center">
							<a href="index.php?x=shortlinks&edit=<?=$shortlink['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=shortlinks&del=<?=$shortlink['id']?>" onclick="return confirm('You sure you want to delete this shortlink?');" class="button small red tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
						</td>
					 </tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php }?>