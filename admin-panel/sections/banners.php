<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	$mesaj = '';
	if(isset($_GET['del']) && is_numeric($_GET['del'])){
		$del = $db->EscapeString($_GET['del']);
		$db->Query("DELETE FROM `ad_packs` WHERE `id`='".$del."'");
	}elseif(isset($_GET['b_del']) && is_numeric($_GET['b_del'])){
		$del = $db->EscapeString($_GET['b_del']);
		$db->Query("DELETE FROM `banners` WHERE `id`='".$del."'");
	}elseif(isset($_GET['edit'])){
		$edit = $db->EscapeString($_GET['edit']);
		$pack = $db->QueryFetchArray("SELECT * FROM `ad_packs` WHERE `id`='".$edit."'");
		if(isset($_POST['submit'])){
			$days = $db->EscapeString($_POST['days']);
			$price = $db->EscapeString($_POST['price']);
			$type = $db->EscapeString($_POST['type']);
			$type = ($type < 0 ? 0 : ($type > 1 ? 1 : $type));
		
			$db->Query("UPDATE `ad_packs` SET `days`='".$days."', `price`='".$price."', `type`='".$type."' WHERE `id`='".$edit."'");
			$mesaj = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Banner Ads pack was successfully edited!</div>';
		}
	}elseif(isset($_GET['b_edit'])){
		$edit = $db->EscapeString($_GET['b_edit']);
		$banner = $db->QueryFetchArray("SELECT * FROM `banners` WHERE `id`='".$edit."'");
		if(isset($_POST['submit'])){
			$banner_url	= $db->EscapeString($_POST['b_url']);
			$expiration = strtotime($_POST['b_expiration']);
			$config_url = $db->EscapeString($_POST['s_url']);
			$status = ($_POST['b_status'] > 1 ? 1 : ($_POST['b_status'] < 0 ? 0 : $_POST['b_status']));
		
			if(empty($expiration)) {
				$mesaj = '<div class="alert success"><span class="icon"></span><strong>Error!</strong> Please provide an valid expiration time!</div>';
			} else {
				$db->Query("UPDATE `banners` SET `banner_url`='".$banner_url."', `site_url`='".$config_url."', `expiration`='".$expiration."', `status`='".$status."' WHERE `id`='".$edit."'");
				$mesaj = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Banner was successfully edited!</div>';
			}
		}
	}

	if(isset($_POST['add_pack'])){
		$days = $db->EscapeString($_POST['days']);
		$price = $db->EscapeString($_POST['price']);
		$type = $db->EscapeString($_POST['type']);
		$type = ($type < 0 ? 0 : ($type > 1 ? 1 : $type));

		if(is_numeric($days) && $days > 0 && is_numeric($price) && $price > 0){
			$db->Query("INSERT INTO `ad_packs` (days, price, type) VALUES('".$days."', '".$price."', '".$type."')");
			$mesaj = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Banner Ads pack was successfuly added!</div>';
		}else{
			$mesaj = '<div class="alert error"><span class="icon"></span><strong>Error!</strong> You have to complete all fields!</div>';
		}
	}

	if(isset($_GET['edit']) && $pack['id'] != ''){
?>
<section id="content" class="container_12 clearfix"><?=$mesaj?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Pack</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Duration</strong><small>Days</small></label>
						<div><input type="text" name="days" value="<?=(isset($_POST['days']) ? $_POST['days'] : $pack['days'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Price</strong><small><?=getCurrency('name')?></small></label>
						<div><input type="text" name="price" value="<?=(isset($_POST['price']) ? $_POST['price'] : $pack['price'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Banner Size</strong></label>
						<div><select name="type"><option value="0">Size 1 - 468x60</option><option value="1"<?=(isset($_POST['type']) && $_POST['type'] == 1 ? ' selected' : ($pack['type'] == 1 ? ' selected' : ''))?>>Size 2 - 728x90</option></select></div>
					</div>
				</div>
				<div class="actions">
					<div class="right">
						<input type="submit" value="Submit" name="submit" />
					</div>
				</div>
		</form>
	</div>
</section>							
<?php }elseif(isset($_GET['b_edit']) && $banner['id'] != ''){ ?>
<section id="content" class="container_12 clearfix"><?=$mesaj?>
	<div class="grid_12">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit Banner</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Expiration date</strong></label>
						<div><input type="text" name="b_expiration" value="<?=(isset($_POST['b_expiration']) ? $_POST['b_expiration'] : date('d-m-Y H:i', $banner['expiration']))?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Banner URL</strong></label>
						<div><input type="text" name="b_url" value="<?=(isset($_POST['b_url']) ? $_POST['b_url'] : $banner['banner_url'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Site URL</strong></label>
						<div><input type="text" name="s_url" value="<?=(isset($_POST['s_url']) ? $_POST['s_url'] : $banner['site_url'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Status</strong></label>
						<div><select name="b_status"><option value="0">Waiting</option><option value="1"<?=(isset($_POST['b_status']) && $_POST['b_status'] == 1 ? ' selected' : $banner['status'] == 1 ? ' selected' : '')?>>Active</option></select></div>
					</div>
				</div>
				<div class="actions">
					<div class="right">
						<input type="submit" value="Submit" name="submit" />
					</div>
				</div>
		</form>
	</div>
</section>
<?php }elseif(isset($_GET['packs'])){ ?>
<section id="content" class="container_12 clearfix">
		<h1 class="grid_12">Banner Packs</h1>
			<div class="grid_8">
				<div class="box">
                    <table class="styled">
                        <thead>
                            <tr>
                                <th width="25">#</th>
                                <th>Duration</th>
                                <th>Price</th>
								<th>Banner Size</th>
								<th>Bought</th>
								<th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
							<?php
								$packs = $db->QueryFetchArrayAll("SELECT * FROM `ad_packs` ORDER BY `id` ASC");

								if(count($packs) == 0) {
									echo '<tr><td colspan="6" style="text-align: center">Nothing here yet!</td></tr>';
								}
								
								foreach($packs as $pack){
							?>	
                            <tr>
                                <td><?=$pack['id']?></td>
                                <td><?=$pack['days']?> days</td>
								<td><?=$pack['price'].' '.getCurrency()?></td>
								<td><?=($pack['type'] == 1 ? 'Size 2 - 728x90' : 'Size 1 - 468x60')?></td>
								<td><?=$pack['bought']?> times</td>
								<td class="center">
									<a href="index.php?x=banners&edit=<?=$pack['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
									<a href="index.php?x=banners&del=<?=$pack['id']?>" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
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
						<h2>Add Pack</h2>
					</div>
						<div class="content"><?=$mesaj?>
							<div class="row">
								<label><strong>Duration</strong><small>Days</small></label>
								<div><input type="text" name="days" value="0" required="required" /></div>
							</div>
							<div class="row">
								<label><strong>Price</strong><small><?=getCurrency('name')?></small></label>
								<div><input type="text" name="price" value="0.0010000" required="required" /></div>
							</div>
							<div class="row">
								<label><strong>Banner Size</strong></label>
								<div><select name="type"><option value="0">Size 1 - 468x60</option><option value="1">Size 2 - 728x90</option></select></div>
							</div>
						</div>
						<div class="actions">
							<div class="right">
								<input type="submit" value="Submit" name="add_pack" />
							</div>
						</div>
				</form>
			</div>
		</section>
<?php }else{?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Banners</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>ID</th>
						<th>User ID</th>
						<th>Banner</th>
						<th>Impressions</th>
						<th>Clicks</th>
						<th>Size</th>
						<th>Active until</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$page = (isset($_GET['p']) ? $_GET['p'] : 0);
						$limit = 15;
						$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

						$total_pages = $db->QueryGetNumRows("SELECT * FROM `banners`");
						include('../system/libs/Paginator.php');

						$urlPattern = GetHref('p=(:num)');
						$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
						$paginator->setMaxPagesToShow(5);

						$banners = $db->QueryFetchArrayAll("SELECT * FROM `banners` ORDER BY `id` ASC LIMIT ".$start.",".$limit."");
						
						if(count($banners) == 0) {
							echo '<tr><td colspan="8" style="text-align: center">Nothing here yet!</td></tr>';
						}
						
						foreach($banners as $banner){
					?>	
					<tr>
						<td><?=$banner['id']?></td>
						<td><a href="index.php?x=users&edit=<?=$banner['user']?>"><?=$banner['user']?></a></td>
						<td><a href="<?=$banner['site_url']?>" title="<?=$banner['site_url']?>" target="_blank"><img src="<?=$banner['banner_url']?>" width="234" border="0" /></a></td>
						<td><?=number_format($banner['views'])?></td>
						<td><?=number_format($banner['clicks'])?></td>
						<td><?=($banner['type'] == 1 ? '728x90 px' : '468x60 px')?></td>
						<td><?=date('d M Y - H:i', $banner['expiration'])?></td>
						<td><?=($banner['status'] == 0 ? '<font color="blue">Waiting payment...</font>' : ($banner['expiration'] > time() ? '<font color="green">Running</font>' : '<font color="red">Finished</font>'))?></td>
						<td class="center">
							<a href="index.php?x=banners&b_edit=<?=$banner['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=banners&b_del=<?=$banner['id']?>" onclick="return confirm('You sure you want to delete this banner?');" class="button small grey tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
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
</section>
<?php }?>