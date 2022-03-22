<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	$errMessage = '<div class="alert alert-warning"><b>The following advertisements are NOT allowed:</b><br><p>Adult (18+) content, illegal products and services (drugs, organs, weapons, prostitution), framebreakers, URL shorteners, copyright infringing material or websites that never load.</p>It is important to mention that if you try for any illegal websites, we will take them down and you won’t be refunded.</div>';
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
						<?=$lang['l_177']?>
					</div>
					<div class="content">
						<?php
							if(isset($_POST['submit'])){
								$url = $db->EscapeString($_POST['url']);
								$pack = $db->EscapeString($_POST['pack']);
								$pack = $db->QueryFetchArray("SELECT * FROM `ad_packs` WHERE `id`='".$pack."'");

								$MAX_SIZE = 500;	// Max banner size in kb
								function getExtension($str) {
									if($str == 'image/jpeg'){
										return 'jpg';
									}elseif($str == 'image/png'){
										return 'png';
									}elseif($str == 'image/gif'){
										return 'gif';
									}
								}
								function random_string($length) {
									$key = '';
									$keys = array_merge(range(0, 9), range('a', 'z'));
									for ($i = 0; $i < $length; $i++) {
										$key .= $keys[array_rand($keys)];
									}
									return $key;
								}

								if(!empty($url) && !empty($pack) && $_FILES['cons_image']['name']){
									$tmpFile = $_FILES['cons_image']['tmp_name'];
									$b_info = getimagesize($tmpFile);
									$extension = getExtension($b_info['mime']);

									if($pack['price'] > $data['purchase_balance']) {
										$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_164'].'</div>';
									}elseif(empty($pack['id'])){
										$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_173'].'</div>';
									}elseif(!preg_match("|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i", $url) || substr($url,-4) == '.exe'){
										$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_184'].'</div>';
									}elseif($b_info['mime'] != 'image/jpeg' && $b_info['mime'] != 'image/png' && $b_info['mime'] != 'image/gif'){
										$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_176'].'</div>';
									}elseif($pack['type'] == 0 && ($b_info[0] != '468' && $b_info[1] != '60')){
										$errMessage = '<div class="alert alert-danger" role="alert">'.lang_rep($lang['l_185'], array('-SIZE-' => '468x60')).'</div>';
									}elseif($pack['type'] == 1 && $b_info[0] != '728' && $b_info[1] != '90'){
										$errMessage = '<div class="alert alert-danger" role="alert">'.lang_rep($lang['l_185'], array('-SIZE-' => '728x90')).'</div>';
									}elseif(filesize($tmpFile) > $MAX_SIZE*1024){
										$errMessage = '<div class="alert alert-danger" role="alert">'.lang_rep($lang['l_195'], array('-SIZE-' => $MAX_SIZE)).'</div>';
									}else{	
										$image_name = 'b-'.$data['id'].'_'.($pack['type'] == 1 ? '728x90' : '468x60').'_'.random_string(rand(7,14)).'.'.$extension;
										$copied = copy($tmpFile, BASE_PATH.'/files/banners/'.$image_name);

										if(!$copied){
											$errMessage = '<div class="alert alert-danger" role="alert"><b>ERROR:</b> Banner wasn\'t uploaded, please contact us for more details!</div>';
										}else{
											$banner = '/files/banners/'.$image_name;
											$expiration = ($pack['days']*86400)+time();
											$db->Query("UPDATE `users` SET `purchase_balance`=`purchase_balance`-'".$pack['price']."' WHERE `id`='".$data['id']."'");
											$db->Query("UPDATE `ad_packs` SET `bought`=`bought`+'1' WHERE `id`='".$daily_pack['id']."'");
											$db->Query("INSERT INTO `user_transactions` (`user_id`,`type`,`value`,`price`,`date`) VALUES('".$data['id']."', '3', '".$pack['days']."', '".$pack['price']."', '".time()."') ");
											$db->Query("INSERT INTO `banners` (`user`,`banner_url`,`site_url`,`expiration`,`ad_pack`,`type`,`status`) VALUES('".$data['id']."', '".$banner."', '".$url."', '".$expiration."', '".$pack['id']."', '".$pack['type']."','1')");

											$errMessage = '<div class="alert alert-success" role="alert">'.$lang['l_175'].'</div>';

										}
									}
								}else{
									$errMessage = '<div class="alert alert-danger" role="alert">'.$lang['l_08'].'</div>';
								}
							}
						?>
						<script type="text/javascript"> function getOptions(){var b=$("#size").val();$.get('system/ajax.php?a=bannerPacks&type='+b,function(a){$('#bPacks').html(a);$('#load').hide();$('#bPacks').show()})} </script>
						<?=$errMessage?>
						<form method="post" enctype="multipart/form-data">
						  <div class="form-row">
							<div class="form-group col-md-6">
							  <label for="url"><?=$lang['l_178']?></label>
							  <div class="input-group mb-2 mr-sm-2">
								<div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-link"></i></div></div>
								<input type="text" class="form-control" id="url" name="url" placeholder="http://">
							  </div>
							</div>
							<div class="form-group col-md-6">
								<label for="banner"><?=$lang['l_179']?></label>
								<div class="custom-file" id="customFile" lang="es">
									<input type="file" class="custom-file-input" id="proof" name="cons_image" aria-describedby="banner">
									<label class="custom-file-label" for="banner"><?=$lang['l_179']?></label>
								</div>
							</div>
						  </div>
						  <div class="form-row">
							<div class="form-group col-md-6">
							  <label for="size"><?=$lang['l_196']?></label>
							  <select id="size" name="size" onchange="getOptions()" class="custom-select">
								<option value="0">468x60</option>
								<option value="1">728x90</option>
							  </select>
							  </div>
							<div class="form-group col-md-6">
							  <label for="bPacks"><?=$lang['l_181']?></label>
							  <select id="bPacks" name="pack" class="custom-select">
								<?php
									$packs = $db->QueryFetchArrayAll("SELECT * FROM `ad_packs` WHERE `type`='0' ORDER BY `price` ASC");
									foreach($packs as $pack){
										echo '<option value="'.$pack['id'].'"'.(isset($_POST['pack']) && $_POST['pack'] == $pack['id'] ? ' selected' : '').'>'.$pack['days'].' '.$lang['l_234'].' - '.$pack['price'].' '.getCurrency().'</option>';
									}
								?>
							  </select>
							</div>
						  </div>
						  <p><input type="submit" name="submit" class="btn btn-primary d" value="<?=$lang['l_07']?>" /></p>
						</form>
					</div>
				</div>
			</div>
			<div class="my-3 p-3 bg-white rounded box-shadow box-style">
				<div id="grey-box">
					<div class="title">
						<?=$lang['l_183']?>
					</div>
					<div class="content">
						<table class="table table-striped table-sm table-responsive-sm">
							<thead class="thead-dark text-center">
								<tr><th><?=$lang['l_187']?></th><th><?=$lang['l_188']?></th><th><?=$lang['l_189']?></th><th><?=$lang['l_196']?></th><th><?=$lang['l_67']?></th></tr>
							</thead>
							<tbody class="table-primary text-dark text-center">
								<?php
									$banners = $db->QueryFetchArrayAll("SELECT a.*, b.price FROM banners a LEFT JOIN ad_packs b ON b.id = a.ad_pack WHERE a.user = '".$data['id']."' AND a.status > '0'");

									if(empty($banners)) {
										echo '<td colspan="5">'.$lang['l_121'].'</td>';
									}
									
									foreach($banners as $banner){
										$status = ($banner['expiration'] > time() ? '<i class="fa fa-play text-success" title="'.$lang['l_68'].'"></i>' : ($banner['status'] == 2 ? '<i class="fa fa-times text-danger" title="'.$lang['l_201'].'"></i>' : '<i class="fa fa-stop text-danger" title="'.$lang['l_69'].'"></i>'));
								?>
									<tr><td><a href="<?=$banner['site_url']?>" title="<?=$banner['site_url']?>" class="img-fluid" target="_blank"><img style="max-width: 225px" src="<?=$config['secure_url'].$banner['banner_url']?>" width="280" border="0" /></a></td><td><?=number_format($banner['views'])?></td><td><?=number_format($banner['clicks'])?></td><td align="center"><?=($banner['type'] == 1 ? '728x90' : '468x60')?></td><td><?=$status?></td></tr>
								<?php } ?>				
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	  </div>
    </main>