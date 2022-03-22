<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_POST['gm_add'])) {
		$url = $db->EscapeString($_POST['gm_url']);
		$type = $db->EscapeString($_POST['gm_color']);
		$message = $db->EscapeString($_POST['gm_message']);
		
		if(empty($message)) {
			$message = '<div class="alert error"><span class="icon"></span><strong>error!</strong> Please complete required fields</div>';
		} else {
			$db->Query("INSERT INTO `announcement` (`message`,`url`,`type`,`time`) VALUES ('".$message."','".$url."','".$type."','".time()."')");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Announcement was successfully added</div>';
		}
	}

	if(isset($_GET['up'])) {
		$id = $db->EscapeString($_GET['up']);
		$db->Query("UPDATE `announcement` SET `time`='".time()."' WHERE `id`='".$id."'");
		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Announcement was moved on top</div>';
	}elseif(isset($_GET['del'])) {
		$id = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `announcement` WHERE `id`='".$id."'");
	}elseif(isset($_GET['edit'])) {
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `announcement` WHERE `id`='".$id."' LIMIT 1");
		
		if(isset($_POST['gm_edit'])) {
			$url = $db->EscapeString($_POST['gm_url']);
			$type = $db->EscapeString($_POST['gm_color']);
			$message = $db->EscapeString($_POST['gm_message']);
			
			$db->Query("UPDATE `announcement` SET `message`='".$message."', `url`='".$url."', `type`='".$type."' , `time`='".time()."' WHERE `id`='".$id."'");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Announcement was successfully edited</div>';
		}
	}
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_8">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>ID</th>
						<th>Message</th>
						<th>URL</th>
						<th>Color</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
				  $announcements = $db->QueryFetchArrayAll("SELECT * FROM announcement ORDER BY time DESC");

				  $j = 0;
				  foreach($announcements as $announcement){
					  $j++;
				?>	
					<tr>
						<td><?=$announcement['id']?></td>
						<td><?=truncate($announcement['message'], 60)?></td>
						<td><?=(empty($announcement['url']) ? 'N/A' : '<a href="'.$announcement['url'].'" target="_blank">'.truncate($announcement['url'], 50).'</a>')?></td>
						<td><?=($announcement['type'] == 1 ? '<font color="green">Green</font>' : ($announcement['type'] == 2 ? '<font color="red">Red</font>' : '<font color="blue">Blue</font>'))?></td>
						<td><?=($j == 1 ? '<font color="green"><b>Active</b></font>' : 'Hidden')?></td>
						<td class="center">
							<a href="index.php?x=announcement&up=<?=$announcement['id']?>" class="button small grey tooltip" data-gravity=s title="Make Visible"><i class="icon-arrow-up"></i></a>
							<a href="index.php?x=announcement&edit=<?=$announcement['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=announcement&del=<?=$announcement['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
						</td>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="grid_4">
		<form method="post" class="box validate">
			<input type="hidden" name="x" value="users" /> 
			<div class="header">
				<h2><?=(isset($_GET['edit']) && !empty($edit['id']) ? 'Edit Announcement' : 'Add Announcement')?></h2>
			</div>
			<div class="content">
				<div class="row">
					<label for="gm_message"><strong>Message</strong></label>
					<div>
						<textarea class="full-width" rows="4" name="gm_message" id="gm_message" placeholder="Message" required><?=(isset($_GET['edit']) && !empty($edit['message']) ? $edit['message'] : '')?></textarea>
					</div>
				</div>
				<div class="row">
					<label for="gm_url"><strong>URL</strong></label>
					<div>
						<input type="text" value="<?=(isset($_GET['edit']) && !empty($edit['url']) ? $edit['url'] : '')?>" placeholder="http://" name="gm_url" id="gm_url" />
					</div>
				</div>
				<div class="row">
					<label for="gm_color"><strong>Color</strong></label>
					<div>
						<select name="gm_color" id="gm_color">
							<option value="0">Blue</option>
							<option value="1"<?=(isset($_GET['edit']) && $edit['type'] == 1 ? ' selected' : '')?>>Green</option>
							<option value="2"<?=(isset($_GET['edit']) && $edit['type'] == 2 ? ' selected' : '')?>>Red</option>
						</select>
					</div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="<?=(isset($_GET['edit']) && !empty($edit['id']) ? 'gm_edit' : 'gm_add')?>" value="<?=(isset($_GET['edit']) && !empty($edit['id']) ? 'Edit' : 'Add')?>" />
				</div>
			</div>
		</form>
	</div>
</section>