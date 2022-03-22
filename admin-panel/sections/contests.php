<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	$message = '';
	if(isset($_POST['submit'])){
		$posts = $db->EscapeString($_POST['set']);
		foreach ($posts as $key => $value){
			if($config[$key] != $value){
				$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
				$config[$key] = $value;
			}
		}
		
		$message = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> Settings were successfully changed</div>';
	}
?>
<section id="content" class="container_12"><?=$message?>
	<div class="grid_6">
		<form method="post" class="box">
			<div class="header">
				<h2>Referral Contest</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Required Claims</strong><small>Faucet claims required for referral to be valid</small></label>
					<div><input type="text" name="set[contest_claims]" value="<?=$config['contest_claims']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Required Referrals</strong><small>Referrals required to qualify in contest</small></label>
					<div><input type="text" name="set[contest_referrals]" value="<?=$config['contest_referrals']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Prizes</strong><small>1 - 10 prizes separated by comma (,)</small></label>
					<div><input type="text" name="set[contest_prizes]" value="<?=$config['contest_prizes']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Contest Period</strong></label>
					<div><select name="set[contest_duration]"><option value="0">Weekly</option><option value="1"<?=($config['contest_duration'] == 1 ? ' selected' : '')?>>Monthly</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Shortlinks Contest</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Required Points</strong><small>Points required to qualify in the contest</small></label>
					<div><input type="text" name="set[sl_points]" value="<?=$config['sl_points']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Prizes</strong><small>1 - 10 prizes separated by comma (,)</small></label>
					<div><input type="text" name="set[sl_prizes]" value="<?=$config['sl_prizes']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Contest Period</strong></label>
					<div><select name="set[sl_duration]"><option value="0">Weekly</option><option value="1"<?=($config['sl_duration'] == 1 ? ' selected' : '')?>>Monthly</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Tasks Contest</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Required Points</strong><small>Points required to qualify in the contest</small></label>
					<div><input type="text" name="set[tc_points]" value="<?=$config['tc_points']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Prizes</strong><small>1 - 10 prizes separated by comma (,)</small></label>
					<div><input type="text" name="set[tc_prizes]" value="<?=$config['tc_prizes']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Contest Period</strong></label>
					<div><select name="set[tc_duration]"><option value="0">Weekly</option><option value="1"<?=($config['tc_duration'] == 1 ? ' selected' : '')?>>Monthly</option></select></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
	<div class="grid_6">
		<form method="post" class="box">
			<div class="header">
				<h2>Lottery</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Lottery Status</strong><small>Enable or Disable lottery</small></label>
					<div><select name="set[lottery_status]"><option value="0">Disabled</option><option value="1"<?=($config['lottery_status'] == 1 ? ' selected' : '')?>>Enabled</option></select></div>
				</div>
				<div class="row">
					<label><strong>Lottery Type</strong><small>Dynamic Reward = prize increase with each ticket<br />Fixed Reward = prize remains the same, as set bellow</small></label>
					<div><select name="set[lottery_type]"><option value="0">Dynamic Reward</option><option value="1"<?=($config['lottery_type'] == 1 ? ' selected' : '')?>>Fixed Reward</option></select></div>
				</div>
				<div class="row">
					<label><strong>Lottery Starting Prize</strong><small>Prize set at the begining of each new round</small></label>
					<div><input type="text" name="set[lottery_default]" value="<?=$config['lottery_default']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Ticket Fee</strong><small>Bits deducted from each purchased ticket.<br />Difference goes to lottery prize, if set to <i>Dynamic Reward</i></small></label>
					<div><input type="text" name="set[lottery_fee]" value="<?=$config['lottery_fee']?>" required /></div>
				</div>
				<div class="row">
					<label><strong>Round Period</strong><small>When does lottery round reset?</small></label>
					<div><select name="set[lottery_duration]"><option value="0">Weekly</option><option value="1"<?=($config['lottery_duration'] == 1 ? ' selected' : '')?>>Monthly</option></select></div>
				</div>
				<div class="alert information"><span class="icon"></span>Ticket prices are set based on membership, go to Membership Settings to change them.</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="submit" />
				</div>
			</div>
		</form>
	</div>
</section>