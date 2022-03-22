<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$message = '<div class="alert information"><span class="icon"></span><strong>ATENTION!</strong> Your first level (Level 1) must have requirements set to 0!</div>';
	if(isset($_GET['del']) && is_numeric($_GET['del'])){
		$del = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `levels` WHERE `id`='".$del."'");
	}elseif(isset($_GET['edit'])){
		$edit = $db->EscapeString($_GET['edit']);
		$pack = $db->QueryFetchArray("SELECT * FROM `levels` WHERE `id`='".$edit."'");
		if(isset($_POST['submit'])){
			$requirements = $db->EscapeString($_POST['requirements']);
			$level = $db->EscapeString($_POST['level']);
			$free_bonus = $db->EscapeString($_POST['free_bonus']);
			$vip_bonus = $db->EscapeString($_POST['vip_bonus']);
			$image = $db->EscapeString($_POST['image']);
			
			$db->Query("UPDATE `levels` SET `requirements`='".$requirements."', `level`='".$level."', `free_bonus`='".$free_bonus."', `vip_bonus`='".$vip_bonus."', `image`='".$image."' WHERE `id`='".$edit."'");

			$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Level was successfully edited!</div>';
		}
	}elseif(isset($_GET['add'])){
		$MAX_SIZE = 500;
		function getExtension($str) {
			if($str == 'image/jpeg'){
				return 'jpg';
			}elseif($str == 'image/png'){
				return 'png';
			}elseif($str == 'image/gif'){
				return 'gif';
			}
		}

		if(isset($_POST['submit'])){
			$tmpFile = $_FILES['image']['tmp_name'];
			$b_info = getimagesize($tmpFile);
			$extension = getExtension($b_info['mime']);

			$requirements = $db->EscapeString($_POST['requirements']);
			$level = $db->EscapeString($_POST['level']);
			$reward = $db->EscapeString($_POST['reward']);
		
			if($db->QueryGetNumRows("SELECT * FROM `levels` WHERE `level`='".$level."' OR `requirements`='".$requirements."' LIMIT 1") > 0){
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> This level already exists!</div>';
			}elseif($b_info['mime'] != 'image/jpeg' && $b_info['mime'] != 'image/png' && $b_info['mime'] != 'image/gif'){
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Your image must be png, gif or jpg!</div>';
			}elseif($b_info[0] > 32 || $b_info[1] > 32){
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Your image cannot be bigger than 32x32 px!</div>';
			}elseif(filesize($tmpFile) > $MAX_SIZE*1024){
				$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Your image must have under '.$MAX_SIZE.' KB!</div>';
			}else{
				$image_name = 'Level_'.$level.'.'.$extension;
				$copied = copy($tmpFile, BASE_PATH.'/files/levels/'.$image_name);

				if(!$copied){
					$message = '<div class="alert error"><span class="icon"></span><b>ERROR:</b> Image wasn\'t uploaded, make sure that you set files permissions to 777 for "files/levels/"!</div>';
				}elseif(!is_numeric($requirements) || !is_numeric($level) || !is_numeric($reward)){
					$message = '<div class="alert error"><span class="icon"></span><strong>Error!</strong> You have to complete all fields!</div>';
				}else{
					$db->Query("INSERT INTO `levels` (level, requirements, reward, image) VALUES('".$level."', '".$requirements."', '".$reward."', 'files/levels/".$image_name."')");

					$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Level was successfully added!</div>';
				}
			}
		}
	}
	if(isset($_GET['edit']) && $pack['id'] != ''){
?>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Level</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Level</strong></label>
					<div><input type="text" name="level" value="<?=(isset($_POST['level']) ? $_POST['level'] : $pack['level'])?>" placeholder="1" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Image</strong></label>
					<div><input type="text" name="image" value="<?=(isset($_POST['image']) ? $_POST['image'] : $pack['image'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Required Exchanges</strong></label>
					<div><input type="text" name="requirements" value="<?=(isset($_POST['requirements']) ? $_POST['requirements'] : $pack['requirements'])?>" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Multiplier</strong><small>Daily bonus for free users</small></label>
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
<?php }elseif(isset($_GET['add'])){?>
<section id="content" class="container_12 clearfix"><?=$message?>
	<div class="grid_12">
		<form method="post" enctype="multipart/form-data" class="box">
			<div class="header">
				<h2>Add Level</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Level</strong></label>
					<div><input type="text" name="level" placeholder="1" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Image</strong></label>
					<div><input type="file" name="image" /></div>
				</div>
				<div class="row">
					<label><strong>Required Claims</strong></label>
					<div><input type="text" name="requirements" placeholder="100" required="required" /></div>
				</div>
				<div class="row">
					<label><strong>Multiplier</strong><small>Daily bonus for free users</small></label>
					<div><input type="text" name="reward" placeholder="1.5" required="required" /></div>
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
<?php }else{?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Levels</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th></th>
						<th>Level</th>
						<th>Requirements</th>
						<th>Multiplier</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					  $levels = $db->QueryFetchArrayAll("SELECT * FROM `levels` ORDER BY `id` ASC");
					  if(empty($levels)){
						echo '<td colspan="5"><center>Nothing found</center></td>';
					  }
					 
					  foreach($levels as $level){
					?>	
					<tr>
						<td width="20"><img src="<?=$config['site_url'].'/'.$level['image']?>" alt="" title="Level <?=$level['level']?>" width="16" /></td>
						<td>Level <b><?=$level['level']?></b></td>
						<td><?=number_format($level['requirements'])?> faucet claims</td>
						<td><b>x<?=number_format($level['reward'], 2)?></b></td>
						<td class="center">
							<a href="index.php?x=levels&edit=<?=$level['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=levels&del=<?=$level['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
						</td>
					 </tr>
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php }?>