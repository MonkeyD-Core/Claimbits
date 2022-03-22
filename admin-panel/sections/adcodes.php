<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '';
	if(isset($_POST['ad_add'])) {
		$name = $db->EscapeString($_POST['ad_name']);
		$code = htmlentities(trim($_POST['ad_code']), ENT_QUOTES);
		$size = $db->EscapeString($_POST['ad_size']);
		$status = $db->EscapeString($_POST['ad_status']);
		
		if(empty($name) || empty($code)) {
			$message = '<div class="alert error"><span class="icon"></span><strong>error!</strong> Please complete required fields</div>';
		} else {
			$db->Query("INSERT INTO `ad_codes` (`name`,`code`,`size`,`status`) VALUES ('".$name."','".$code."','".$size."','".$status."')");

			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Advertisment was successfully added</div>';
		}
	}

	if(isset($_GET['del'])) {
		$id = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `ad_codes` WHERE `id`='".$id."'");
	}elseif(isset($_GET['edit'])) {
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `ad_codes` WHERE `id`='".$id."' LIMIT 1");
		$original_code = html_entity_decode($edit['code'], ENT_QUOTES);
		
		if(isset($_POST['ad_edit'])) {
			$name = $db->EscapeString($_POST['ad_name']);
			$code = htmlentities($_POST['ad_code'], ENT_QUOTES);
			$size = $db->EscapeString($_POST['ad_size']);
			$status = $db->EscapeString($_POST['ad_status']);
			
			$db->Query("UPDATE `ad_codes` SET `name`='".$name."', `code`='".$code."', `size`='".$size."' , `status`='".$status."' WHERE `id`='".$id."'");
			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Advertisment was successfully edited</div>';
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
						<th>Name</th>
						<th>Code</th>
						<th>Ad Unit</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
				  $advertisments = $db->QueryFetchArrayAll("SELECT * FROM ad_codes ORDER BY id DESC");

				  $ad_units = array(0 => 'Footer - 468x60px', 1 => 'Footer - 728x90px', 2 => 'Header - 468x60px', 3 => 'Header - 728x90px', 4 => 'Sidebar - 250x250px', 11 => 'Popup');
				  
				  $j = 0;
				  foreach($advertisments as $advertisment){
					  $j++;
				?>	
					<tr>
						<td><?=$advertisment['id']?></td>
						<td class="center"><?=$advertisment['name']?></td>
						<td style="max-width: 400px"><?=truncate($advertisment['code'], 80)?></td>
						<td class="center"><?= $ad_units[$advertisment['size']]?></td>
						<td><?=($advertisment['status'] == 1 ? '<font color="green"><b>Active</b></font>' : '<font color="red"><b>Disabled</b></font>')?></td>
						<td class="center">
							<a href="index.php?x=adcodes&edit=<?=$advertisment['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=adcodes&del=<?=$advertisment['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
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
				<h2><?=(isset($_GET['edit']) && !empty($edit['id']) ? 'Edit Advertisment' : 'Add Advertisment')?></h2>
			</div>
			<div class="content">
				<div class="row">
					<label for="ad_name"><strong>Name</strong></label>
					<div>
						<input type="text" value="<?=(isset($_GET['edit']) && !empty($edit['name']) ? $edit['name'] : '')?>" placeholder="Ad Code - 468x60" name="ad_name" id="ad_name" />
					</div>
				</div>
				<div class="row">
					<label for="ad_code"><strong>Code</strong></label>
					<div>
						<textarea class="full-width" rows="4" name="ad_code" id="ad_code" placeholder="Advertisment code" required><?=(isset($_GET['edit']) && !empty($edit['code']) ? $original_code : '')?></textarea>
					</div>
				</div>
				<div class="row">
					<label for="ad_size"><strong>Ad Unit</strong></label>
					<div>
						<select name="ad_size" id="ad_size">
							<option value="0">Footer - 468x60px</option>
							<option value="1"<?=(isset($_GET['edit']) && $edit['size'] == 1 ? ' selected' : '')?>>Footer - 728x90px</option>
							<option value="2"<?=(isset($_GET['edit']) && $edit['size'] == 2 ? ' selected' : '')?>>Header - 468x60px</option>
							<option value="3"<?=(isset($_GET['edit']) && $edit['size'] == 3 ? ' selected' : '')?>>Header - 728x90px</option>
							<option value="4"<?=(isset($_GET['edit']) && $edit['size'] == 4 ? ' selected' : '')?>>Sidebar - 250x250px</option>
							<option value="11"<?=(isset($_GET['edit']) && $edit['size'] == 11 ? ' selected' : '')?>>Popup</option>
						</select>
					</div>
				</div>
				<div class="row">
					<label for="ad_status"><strong>Status</strong></label>
					<div>
						<select name="ad_status" id="ad_status">
							<option value="1">Enabled</option>
							<option value="0"<?=(isset($_GET['edit']) && $edit['status'] == 0 ? ' selected' : '')?>>Disabled</option>
						</select>
					</div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" name="<?=(isset($_GET['edit']) && !empty($edit['id']) ? 'ad_edit' : 'ad_add')?>" value="<?=(isset($_GET['edit']) && !empty($edit['id']) ? 'Edit' : 'Add')?>" />
				</div>
			</div>
		</form>
	</div>
</section>