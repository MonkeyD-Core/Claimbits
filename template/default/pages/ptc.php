<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
?>
<main role="main" class="container">
  <div class="row">
	<?php 
		require_once(BASE_PATH.'/template/'.$config['theme'].'/common/sidebar.php');
	?>
	<div class="col-xl-9 col-lg-8 col-md-7">
		<div class="my-3 ml-2 p-3 bg-white rounded box-shadow box-style">
		  <div id="grey-box">
			<div class="title">
				<?=$lang['l_95']?>
			</div>
			<div class="content">
			<div class="infobox"><?php echo $lang['l_101']; ?></div>
			<?php
				$sites = $db->QueryFetchArrayAll("SELECT a.id, a.website, a.title, b.reward, b.time FROM ptc_websites a LEFT JOIN ptc_packs b ON b.id = a.ptc_pack LEFT JOIN ptc_done c ON c.user_id = '".$data['id']."' AND c.site_id = a.id WHERE a.status = '1' AND (a.daily_limit > a.received_today OR a.daily_limit = '0') AND a.received < a.total_visits AND c.site_id IS NULL ORDER BY b.reward DESC LIMIT 20");
				$total_sites = count($sites);

				if($total_sites == 0)
				{
					echo '<div class="alert alert-info" role="alert">'.$lang['l_230'].'</div>';
				}
				else
				{
					foreach($sites as $site) {
			?>
				<div class="website_block" id="<?=$site['id']?>">
					<div class="website_title"><?=truncate($site['title'], 15)?></div>
					<i class="fa fa-external-link-square fa-4x" title="<?php echo lang_rep($lang['l_229'], array('-TIME-' => $site['time'])); ?>"></i>
					<div class="clear"></div>
					<div class="reward"><b><?php echo $lang['l_225']; ?></b>: <span><?=(number_format($site['reward'], 2).' '.$lang['l_337']); ?></span></div>
					<div class="time"><i class="fa fa-clock-o"></i> <span><?=$site['time']; ?></span></div>
					<button type="button" class="btn btn-success btn-sm w-100 mt-1" onclick="opensite('<?php echo $site['id']; ?>','<?php echo $site['title']; ?>');"><i class="fa fa-external-link fa-fw"></i> <?php echo $lang['l_224']; ?></button>
				</div>
			<?php 
					} 
				}
			?>
			<div class="clearfix"></div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</main>
<?php
	if($total_sites > 0) {
?>
	<script type="text/javascript">
		var base = '<?php echo $config['site_url']; ?>';
		var start_click = 1;
		var end_click = <?php echo $total_sites; ?>;
		var hash_key = '<?php echo $token; ?>';

		function click_refresh() {
			if (start_click < end_click) {
				start_click = start_click + 1
			} else {
				location.reload(true)
			}
		}

		function opensite(a, b) {
			childWindow = open(base + '/surf.php?sid=' + a + '&key=<?php echo MD5(GenerateKey(20));?>', b);
			remove(a)
		}

		function remove(a) {
			document.getElementById(a).style.display = "none";
			click_refresh()
		}
	</script>
<?php } ?>