<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

if(isset($_GET['del']) && is_numeric($_GET['del'])){
	$del = $db->EscapeString($_GET['del']);
	if($db->QueryGetNumRows("SELECT * FROM `users` WHERE `id`='".$del."' LIMIT 1") > 0){
		$db->Query("DELETE FROM `users` WHERE `id`='".$del."'");
		$db->Query("UPDATE `users` SET `ref`='0' WHERE `ref`='".$del."'");
	}
}elseif(isset($_GET['confirm']) && is_numeric($_GET['confirm'])){
	$confirm = $db->EscapeString($_GET['confirm']);
	$db->Query("UPDATE `users` SET `activate`='0' WHERE `id`='".$confirm."'");
}

if(isset($_GET['edit']) && is_numeric($_GET['edit'])){
	$id = $db->EscapeString($_GET['edit']);
	$edit = $db->QueryFetchArray("SELECT a.*, b.membership AS mem_name, c.country FROM users a LEFT JOIN memberships b ON b.id = a.membership_id LEFT JOIN list_countries c ON c.id = a.country_id WHERE a.id = '".$id."' LIMIT 1");
}

$errMessage = '';
if(isset($_POST['user_add'])){
	$name = $db->EscapeString($_POST['user_username']);
	$email = $db->EscapeString($_POST['user_email']);
	$password = $db->EscapeString($_POST['user_password']);
	$admin = $db->EscapeString($_POST['user_admin']);
	$country = $db->EscapeString($_POST['user_country']);
	$sex = ($_POST['user_gender'] < 0 ? 0 : ($_POST['user_gender'] > 2 ? 2 : $db->EscapeString($_POST['user_gender'])));

	if(!isUserID($name)){
		$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Please enter an valid username!</div>';
	}elseif(!isEmail($email)){
		$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Please enter a valid email address!</div>';
	}elseif(!checkPwd($password,$_POST['user_password_2'])) {
		$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Passwords doesn\'t match!</div>';
	}elseif($email != $_POST['user_email_2']) {
		$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Email addresses doesn\'t match!</div>';
	}elseif($db->QueryGetNumRows("SELECT id FROM `users` WHERE `username`='".$name."' LIMIT 1") > 0){
		$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Username already registered!</div>';
	}elseif($db->QueryGetNumRows("SELECT id FROM `users` WHERE `email`='".$email."' LIMIT 1") > 0){
		$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Email address already registered!</div>';
	}else{
		$db->Query("INSERT INTO `users` (`username`,`email`,`password`,`country_id`,`gender`,`admin`,`reg_ip`,`reg_time`)VALUES('".$name."','".$email."','".securePassword($password)."','".$country."','".$sex."','".$admin."','".VisitorIP()."','".time()."')");
		$user_id = $db->GetLastInsertId();
		
		$errMessage = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> User account was successfuly created!</div>';
		
		redirect('index.php?x=users&edit='.$user_id);
	}
}

$order_value = 'id ASC';
$order_by = (isset($_GET['oby']) ? $_GET['oby'] : '');
$sorting = (isset($_GET['sort']) ? $_GET['sort'] : '');
if(!empty($sorting) && !empty($order_by)){
	$sort = ($sorting == 'asc' ? 'ASC' : 'DESC');
	if($order_by == 1){
		$order_value = 'id '.$sort;
	}elseif($order_by == 2){
		$order_value = 'account_balance '.$sort;
	}elseif($order_by == 3){
		$order_value = 'total_claims '.$sort;
	}
}

$db_value = '';
$url_value = '';
if(isset($_GET['su'])){
	$search = $db->EscapeString($_GET['su']);
	if($_GET['s_type'] == 1){
		$db_value = ($search != '' ?  " WHERE `email` LIKE '%".$search."%'" : "");
	}elseif($_GET['s_type'] == 2){
		$db_value = ($search != '' ?  " WHERE `reg_ip` LIKE '%".$search."%' OR `log_ip` LIKE '%".$search."%'" : "");
	}elseif($_GET['s_type'] == 3){
		$db_value = ($search != '' ?  " WHERE `ref_source` LIKE '%".$search."%'" : "");
	}elseif($_GET['s_type'] == 4){
		$db_value = ($search != '' ?  " WHERE `account_balance`>='".$search."'" : "");
	}else{
		$db_value = ($search != '' ?  " WHERE `username` LIKE '%".$search."%'" : "");
	}
}elseif(isset($_GET['online'])){
	$db_value = ' WHERE ('.time().'-`last_activity`) < 900';
	$url_value = '&online';
}elseif(isset($_GET['today'])){
	$db_value = " WHERE `reg_time` >= '".strtotime(date('d M Y'))."'";
	$order_value = 'reg_time DESC';
	$url_value = '&today';
}elseif(isset($_GET['premium'])){
	$db_value = " WHERE membership > '0'";
	$url_value = '&premium';
}elseif(isset($_GET['country'])){
	$code = $db->EscapeString($_GET['country_id']);
	$db_value = " WHERE country_id = '".$code."'";
	$url_value = '&country='.$code;
}elseif(isset($_GET['refid'])){
	$refid = $db->EscapeString($_GET['refid']);
	$db_value = " WHERE ref = '".$refid."'";
	$url_value = '&online='.$refid;
}elseif(isset($_GET['banned'])){
	$db_value = " WHERE disabled > '0'";
	$url_value = '&banned';
}elseif(isset($_GET['unverified'])){
	$db_value = " WHERE activate != '0'";
	$url_value = '&unverified';
}

$page = (isset($_GET['p']) ? $_GET['p'] : 0);
$limit = 20;
$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

$total_pages = $db->QueryGetNumRows("SELECT id FROM users ".$db_value);
include('../system/libs/Paginator.php');

$urlPattern = GetHref('p=(:num)');
$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
$paginator->setMaxPagesToShow(5);

if(isset($_GET['edit']) && $edit['username'] != ""){
	if(isset($_POST['submit'])){
		$value = ($_POST['pass'] != '' ? ", `password`='".securePassword($_POST['pass'])."'" : '');
		
		$name = $db->EscapeString($_POST['username']);
		$email = $db->EscapeString($_POST['email']);
		$admin = $db->EscapeString($_POST['admin']);
		$bits = $db->EscapeString($_POST['bits']);
		$purchase_balance = $db->EscapeString($_POST['purchase_balance']);
		$country = $db->EscapeString($_POST['country']);
		$sex = ($_POST['gender'] < 0 ? 0 : ($_POST['gender'] > 2 ? 2 : $db->EscapeString($_POST['gender'])));
		$membership = strtotime($_POST['membership']);
		$membership = ($membership < time() ? 0 : $membership);
		$membership_id = $db->EscapeString($_POST['membership_id']);
		$activate = (isset($_POST['activate']) ? ", `activate`='0'" : '');
		
		if(!isUserID($name)){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Please enter an valid username!</div>';
		}elseif(!isEmail($email)){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Please enter a valid email address!</div>';
		}elseif($name != $edit['username'] && $db->QueryGetNumRows("SELECT id FROM `users` WHERE `username`='".$name."' ") > 0){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Username already registered!</div>';
		}elseif($email != $edit['email'] && $db->QueryGetNumRows("SELECT id FROM `users` WHERE `email`='".$email."'") > 0){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Email address already registered!</div>';
		}else{
			$db->Query("UPDATE `users` SET `username`='".$name."', `email`='".$email."', `country_id`='".$country."', `gender`='".$sex."', `admin`='".$admin."'".$value.", `account_balance`='".$bits."', `purchase_balance`='".$purchase_balance."', `membership`='".$membership."', `membership_id`='".$membership_id."'".$activate." WHERE `id`='".$id."'");
			$errMessage = '<div class="alert success"><span class="icon"></span><strong>Success!</strong> User was successfuly edited!</div>';
		}
	}elseif(isset($_POST['warn'])){
		$warn_message = $db->EscapeString($_POST['warn_message']);
		$days = 0;
		if(!empty($warn_message)){
			$days = ($_POST['days'] < 1 ? 1 : $db->EscapeString($_POST['days']));
			$days = ((86400*$days)+time());
		}
		
		if($warn_message != '' && strlen($warn_message) < 10){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Warning message must have at least 10 characters!</div>';
		}elseif($warn_message != '' && strlen($warn_message) > 255){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Warning message can\'t have more than 255 characters!</div>';
		}else{
			$db->Query("UPDATE `users` SET `warn_message`='".$warn_message."',`warn_expire`='".$days."' WHERE `id`='".$edit['id']."'");
			$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS:</strong> Warning message was successfully added!</div>';
		}
	}elseif(isset($_POST['ban'])){
		$status = ($_POST['status'] < 0 ? 0 : ($_POST['status'] > 1 ? 1 : $_POST['status']));
		$reason = $db->EscapeString($_POST['ban_reason']);
		
		if($status == 1 && empty($reason)){
			$errMessage = '<div class="alert error"><span class="icon"></span><strong>ERROR:</strong> Please complete all fields!</div>';
		}else{
			$db->Query("INSERT INTO `ban_reasons` (`user`,`reason`,`date`) VALUES ('".$edit['id']."', '".$reason."', '".time()."') ON DUPLICATE KEY UPDATE `reason`='".$reason."', `date`='".time()."'");
			$db->Query("UPDATE `users` SET `disabled`='".$status."' WHERE `id`='".$edit['id']."'");
			$errMessage = '<div class="alert success"><span class="icon"></span><strong>SUCCESS!</strong> User was successfuly '.($status == 1 ? 'blocked' : 'unblocked').'!</div>';
		}
	}elseif(isset($_POST['username_as'])){
		$_SESSION['PT_User'] = $edit['id'];
		if(isset($_COOKIE['SesToken'])){
			setcookie('SesToken', '0', time()-604800);
		}
		redirect($config['secure_url']);
	}

	$ban = $db->QueryFetchArray("SELECT reason FROM `ban_reasons` WHERE `user`='".$edit['id']."'");
	$check_ip = $db->QueryGetNumRows("SELECT id FROM `users` WHERE `reg_ip`='".$edit['reg_ip']."'");

	$check_log_ip = 0;
	if($edit['log_ip'] != '0'){
		$check_log_ip = $db->QueryGetNumRows("SELECT id FROM `users` WHERE `log_ip`='".$edit['log_ip']."'");
		$log_ip_country = detectCountry($edit['log_ip'], false);
	}

	$u_refs = $db->QueryGetNumRows("SELECT id FROM `users` WHERE `ref`='".$edit['id']."'");
	$p_refs = $db->QueryGetNumRows("SELECT id FROM `purchased_referrals` WHERE `user_id`='".$edit['id']."'");
	$b_refs = $db->QueryGetNumRows("SELECT id FROM `users` WHERE `ref`='".$edit['id']."' AND `disabled`='1'");
	$invest_won = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`amount`) AS `amount` FROM `bitcoin_investments` WHERE `user_id`='".$edit['id']."' AND `status`='1'");
	$invest_lost = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`amount`) AS `amount` FROM `bitcoin_investments` WHERE `user_id`='".$edit['id']."' AND `status`='2'");
	$offerwalls = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`revenue`) AS `revenue` FROM `completed_offers` WHERE `user_id`='".$edit['id']."'");
	$commissions = $db->QueryFetchArray("SELECT SUM(`commission`) AS `commission`, COUNT(*) AS `total` FROM `ref_commissions` WHERE `user`='".$edit['id']."'");
	$payouts = $db->QueryFetchArray("SELECT SUM(`btc`) AS `cash`, COUNT(`id`) AS `total` FROM `withdrawals` WHERE `user_id`='".$edit['id']."' AND `status`='1'");
	$proxyChecks = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `ip_checks` WHERE `user_id`='".$edit['id']."' AND `status`= '1' AND `checked`= '0'");
?>
<section id="content" class="container_12 clearfix">
	<div class="grid_12 profile">
		<div class="header">
			<div class="title">
				<h2><?=$edit['username']?></h2><h3>(<?=($edit['admin'] == 1 ? 'Administrator' : 'Member').' - Level '.userLevel($edit['id'], 1 , $edit['total_claims'])?>)</h3>
			</div>
			<div class="avatar">
				<img src="img/<?=((time()-$edit['last_activity']) < 3600 ? 'on' : 'off')?>.png" />
			</div>
			
			<ul class="info">
				<li>
					<a href="javascript:void(0);">
						<strong><?=number_format(isset($_POST['account_balance']) ? $_POST['account_balance'] : $edit['account_balance'], 2)?> Bits</strong>
						<small>Account Balance</small>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<strong><?=number_format($payouts['total']).' - '.number_format($payouts['cash'], 8).' '.getCurrency()?></strong>
						<small>Withdrawals</small>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<strong><?=number_format($edit['ow_credits'], 2)?></strong>
						<small>Offerwall Credits</small>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<strong><?=number_format($edit['today_revenue'], 2)?> Bits</strong>
						<small>Today Earnings</small>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<strong><?=number_format($edit['total_revenue'], 2)?> Bits</strong>
						<small>Total Earnings</small>
					</a>
				</li>
			</ul>
		</div>
		<?php
			if(!empty($errMessage))
			{
				echo '<div class="grid_12">'.$errMessage.'</div>';
			}
			elseif($proxyChecks['total'] > 0)
			{
				echo '<div class="grid_12"><div class="alert warning"><span class="icon"></span>This user has been found using at least <strong>'.$proxyChecks['total'].' different VPN / Proxy IP Addresses</strong>. <a href="index.php?x=flag&user='.$edit['id'].'" target="_blank">Click here to check...</a></div></div>';
			}
		?>
		<div class="details grid_8">
			<h2>Personal Details</h2>
			<section>
				<table>
					<tr>
						<th>Username:</th><td><i><?=$edit['username']?></i></td>
					</tr>
					<tr>
						<th>Email:</th><td><i><?=$edit['email']?></i></td>
					</tr>
					<tr>
						<th>Country:</th><td><i><?=$edit['country']?></i></td>
					</tr>
					<tr>
						<th>Gender:</th><td><i><?=get_gender($edit['gender'])?></i></td>
					</tr>
					<tr>
						<th>Membership:</th><td><b><?=$edit['mem_name']?></b></td>
					</tr>
					<tr>
						<th>Valid until:</th><td><i><?=($edit['membership'] > 0 ? date('d M Y - H:i', $edit['membership']) : 'N/A')?></i></td>
					</tr>
					<tr>
						<th>Invited By:</th><td><i><?=($edit['ref'] == 0 ? 'N/A' : '<a href="index.php?x=users&edit='.$edit['ref'].'">'.$edit['ref'].'</a>')?></i></td>
					</tr>
					<tr>
						<th>Source:</th><td><i><?=(empty($edit['ref_source']) ? 'N/A' : '<a href="'.$edit['ref_source'].'" target="_blank">'.truncate($edit['ref_source'], 60).'</a>')?></i></td>
					</tr>
				</table>
			</section>
		</div>
		<div class="details grid_4">
			<h2>Referrals</h2>
			<section>
				<table>
					<tr><th>Total Referrals:</th><td><i><a href="index.php?x=users&refid=<?=$edit['id']?>"><?=number_format($u_refs)?></a><small> - <?=number_format($b_refs)?> suspended</small></i></td></tr>
					<tr><th>Purchased Referrals:</th><td><i><a href="index.php?x=users&refid=<?=$edit['id']?>"><?=number_format($p_refs)?></a><small></i></td></tr>
					<tr><th>Revenue:</th><td><i><?=number_format($commissions['commission'], 2)?> Bits (<?=number_format($commissions['total'])?>)</i></td></tr>
				</table>
			</section>
			<h2>Deposits</h2>
			<section>
				<table>
					<?php
						$trans = $db->QueryFetchArray("SELECT SUM(`amount`) AS `amount`, COUNT(*) AS `total` FROM `deposits` WHERE `user_id`='".$edit['id']."'");
					?>
					<tr><th>Deposits:</th><td><i><?=number_format($trans['total'])?></i></td><td style="width:150px"></td></tr>
					<tr><th>Total:</th><td><font style="color:green"><?=number_format($trans['amount'], 8).' '.getCurrency()?></font></td></tr>
				</table>
			</section>
		</div>
		<div class="details grid_6">
			<h2>Other Info</h2>
			<section>
				<table>
					<tr>
						<th>Registration date:</th><td><i><?=date('d M Y - H:i', $edit['reg_time'])?></i></td>
					</tr>
					<tr>
						<th>Last activity:</th><td><i><?=($edit['last_activity'] == 0 ? 'Never' : date('d M Y - H:i', $edit['last_activity']))?></i></td>
					</tr>
					<tr>
						<th>Registered IP:</th><td><i><a href="index.php?x=users&s_type=2&su=<?=$edit['reg_ip']?>"><?=$edit['reg_ip']?></a> - <b><?=detectCountry($edit['reg_ip'], false)?></b> - <?=$check_ip?> account<?=($check_ip > 1 ? 's' : '')?></i></td>
					</tr>
					<tr>
						<th>Latest IP used:</th><td><i><?=($edit['log_ip'] == '0' ? 'N/A' : '<a href="index.php?x=users&s_type=2&su='.$edit['log_ip'].'">'.$edit['log_ip'].'</a>')?><?if($check_log_ip != 0){?> - <b><?=$log_ip_country?></b> - <?=$check_log_ip?> account<?=($check_log_ip > 1 ? 's' : '')?><?php } ?></i></td>
					</tr>
					<tr>
						<th>IP Status:</th><td><b id="checkIP"><img src="img/ajax-loader.gif" border="0" /></b></td>
					</tr>
				</table>
			</section>
		</div>
		<div class="details grid_6">
			<h2>Other Stats</h2>
			<section>
				<table>
					<tr>
						<th>Completed Offerwalls:</th><td><?=number_format($offerwalls['total'])?> offers = <font style="color:green">$<?=number_format($offerwalls['revenue'], 5)?></font></td>
					</tr>
					<tr>
						<th>Shortlinks Visits:</th><td><?=number_format($edit['sl_today'])?> today / <?=number_format($edit['sl_total'])?> total</td>
					</tr>
					<tr>
						<th>Today Faucet Claims:</th><td><?=number_format($edit['today_claims'])?> claims</td>
					</tr>
					<tr>
						<th>Total Faucet Claims:</th><td><?=number_format($edit['total_claims'])?> claims</td>
					</tr>
					<tr>
						<th>Investment Games:</th><td><?=number_format($invest_won['total'])?> won = <?=number_format($invest_won['amount'])?> bits | <?=number_format($invest_lost['total'])?> lost = <?=number_format($invest_lost['amount'])?> bits</td>
					</tr>
				</table>
			</section>
		</div>
		<div class="clearfix"></div>
		<div class="divider"></div>
	</div>
	<div class="grid_7">
		<form action="" method="post" class="box">
			<div class="header">
				<h2>Edit User</h2>
			</div>
				<div class="content">
					<div class="row">
						<label><strong>Username</strong></label>
						<div><input type="text" name="username" value="<?=(isset($_POST['username']) ? $_POST['username'] : $edit['username'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Email</strong></label>
						<div><input type="text" name="email" value="<?=(isset($_POST['email']) ? $_POST['email'] : $edit['email'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Password</strong></label>
						<div><input type="password" name="pass" placeholder="Leave blank if you don't want to change!" /></div>
					</div>
					<div class="row">
						<label><strong>Gender</strong></label>
						<div><select name="gender"><option value="0"></option><option value="1"<?=($edit['gender'] == 1 ? ' selected' : (isset($_POST['gender']) && $_POST['gender'] == 1 ? ' selected' : ''))?>>Male</option><option value="2"<?=($edit['gender'] == 2 ? ' selected' : (isset($_POST['gender']) && $_POST['gender'] == 2 ? ' selected' : ''))?>>Female</option></select></div>
					</div>
					<div class="row">
						<label><strong>Country</strong></label>
						<div><select name="country" class="search" data-placeholder="Select Country"><option value="0"></option>
						<?php
							$countries = $db->QueryFetchArrayAll("SELECT country,id FROM `list_countries` ORDER BY country ASC"); 
							foreach($countries as $country){echo '<option value="'.$country['id'].'"'.($edit['country_id'] == $country['id'] ? ' selected' : (isset($_POST['country']) && $_POST['country'] == $country['id'] ? ' selected' : '')).'>'.$country['country'].'</option>';}
						?>
						</select></div>
					</div>
					<div class="row">
						<label><strong>Membership</strong></label>
						<div><select name="membership_id"><option value="1">Basic</option><option value="2"<?=($edit['membership_id'] == 2 ? ' selected' : (isset($_POST['membership_id']) && $_POST['membership_id'] == 2 ? ' selected' : ''))?>>Silver</option><option value="3"<?=($edit['membership_id'] == 3 ? ' selected' : (isset($_POST['membership_id']) && $_POST['membership_id'] == 3 ? ' selected' : ''))?>>Gold</option><option value="4"<?=($edit['membership_id'] == 4 ? ' selected' : (isset($_POST['membership_id']) && $_POST['membership_id'] == 4 ? ' selected' : ''))?>>Diamond</option></select></div>
					</div>
					<div class="row">
						<label><strong>Valid until</strong><?=($edit['membership'] == 0 ? '<Font color="red"><small>Expired</small></font>' : '')?></label>
						<div><input type="text" name="membership" value="<?=(isset($_POST['membership']) ? $_POST['membership'] : ($edit['membership'] > 0 ? date('d-m-Y H:i', $edit['membership']) : date('d-m-Y H:i')))?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Account Balance</strong><small>Bits</small></label>
						<div><input type="text" name="bits" value="<?=(isset($_POST['bits']) ? $_POST['bits'] : $edit['account_balance'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Purchase Balance</strong><small><?=getCurrency()?></small></label>
						<div><input type="text" name="purchase_balance" value="<?=(isset($_POST['purchase_balance']) ? $_POST['purchase_balance'] : $edit['purchase_balance'])?>" required="required" /></div>
					</div>
					<div class="row">
						<label><strong>Type</strong></label>
						<div><select name="admin"><option value="0">User</option><option value="1"<?=($edit['admin'] != 0 ? ' selected' : (isset($_POST['admin']) && $_POST['admin'] == 1 ? ' selected' : ''))?>>Admin</option></select></div>
					</div>
					<?php if($edit['activate'] != '0' && !isset($_POST['activate'])){ ?>
					<div class="row">
						<label><strong>Email address unverified</strong></label>
						<div><div><input type="checkbox" name="activate" id="activate" />  <label for="activate">Confirm email address?</label></div></div>
					</div>
					<?php } ?>
                </div>
				<div class="actions">
					<div class="right">
						<input type="submit" value="Submit" name="submit" />
					</div>
				</div>
		</form>
	</div>
	<div class="grid_5">
		<form method="post" class="box">
			<div class="header">
				<h2>Warn user</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Message</strong><small>Will appear on homepage!</small></label>
					<div><textarea name="warn_message"><?=(isset($_POST['warn_message']) ? $_POST['warn_message'] : $edit['warn_message'])?></textarea></div>
				</div>
				<div class="row">
					<label><strong>Days</strong><small>After how many days will expire!</small></label>
					<div><input type="text" name="days" value="<?=(isset($_POST['warn_expire']) ? $_POST['warn_expire'] : ($edit['warn_expire'] > 0 ? round(($edit['warn_expire']-time())/86400, 0) : 1))?>" required="required" /></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="warn" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="header">
				<h2>Ban User</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Banned</strong></label>
					<div><select name="status"><option value="0">No</option><option value="1"<?=(!isset($_POST['status']) && $edit['disabled'] != 0 ? ' selected' : (isset($_POST['status']) && $_POST['status'] == 1 ? ' selected' : ''))?>>Yes</option></select></div>
				</div>
				<div class="row">
					<label><strong>Reason</strong><small>Why was this user banned?</small></label>
					<div><textarea name="ban_reason"><?=(isset($_POST['ban_reason']) ? $_POST['ban_reason'] : $ban['reason'])?></textarea></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Submit" name="ban" />
				</div>
			</div>
		</form>
		<form method="post" class="box">
			<div class="actions">
				<div align="center">
					<input type="submit" value="Login as this User" name="username_as" /><br />
					<small><i>You will be disconected from current account</i></small>
				</div>
			</div>
		</form>
	</div>
</section>
<script>
	$(document).ready(function() {
		 function checkIP() {
			 $.ajax({
				 url: "../system/ajax.php?checkIP=<?=(empty($edit['log_ip']) ? $edit['reg_ip'] : $edit['log_ip'])?>&user_id=<?=$edit['id']?>",
				 timeout: 7500,
				 success: function(b) {
					 if(b == 1) {
						 $("#checkIP").html('<span style="color:#dc3545">Proxy / VPN Detected</span>')
					 } else if (b == 0) {
						 $("#checkIP").html('<span style="color:#28a745">Safe</span>')
					 } else {
						 $("#checkIP").html('<span style="color:#17a2b8">Unknown</span>')
					 }
				 },
				 error: function(b) {
					 $("#checkIP").html('<span style="color:#17a2b8">Error Fetching IP Details</span>')
				 }
			 })
		 }

		setTimeout(checkIP(), 1000);
	}); 
</script>
<?php
	}elseif(isset($_GET['countries'])){
?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Countries Overview</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>#</th>
						<th>Country</th>
						<th>Code</th>
						<th>Users</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$total_users = $db->QueryFetchArray("SELECT COUNT(`id`) AS `total` FROM users");	
					$countries = $db->QueryFetchArrayAll("SELECT a.country_id AS uc, COUNT(a.id) AS total, b.country, b.code FROM users a LEFT JOIN list_countries b ON b.id = a.country_id GROUP BY uc ORDER BY total DESC");	
					$j = 0;
					foreach($countries as $country){
						$j++;
				?>	
					<tr>
						<td><?=$j?></td>
						<td><?=(empty($country['country']) ? 'Unknown' : $country['country'])?></td>
						<td><?=(empty($country['code']) ? 'Unknown' : $country['code'])?></td>
						<td><?=number_format($country['total']).' ('.percent($country['total'], $total_users['total']).'%)'?></td>
						<td><a href="index.php?x=users&country=<?=$country['uc']?>">View Users</a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php
	}elseif(isset($_GET['multi_accounts'])){
?>
<section id="content" class="container_12 clearfix">
	<h1 class="grid_12">Multiple accounts on the same IP's</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled">
				<thead>
					<tr>
						<th>#</th>
						<th>IP Address</th>
						<th>Total Accounts</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$accounts = $db->QueryFetchArrayAll("SELECT log_ip, COUNT(*) AS total_accounts FROM users WHERE log_ip != '' AND log_ip != 0 AND disabled = '0' GROUP BY log_ip HAVING total_accounts > '1' ORDER BY total_accounts DESC");	
					$j = 0;
					foreach($accounts as $account){
						++$j;
				?>	
					<tr>
						<td><?=$j?></td>
						<td><a href="https://whatismyipaddress.com/ip/<?=$account['log_ip']?>" target="_blank"><?=$account['log_ip']?></a></td>
						<td><b><?=number_format($account['total_accounts'])?> different users</b> were logged in from this IP Address</td>
						<td><center><a href="index.php?x=users&s_type=2&su=<?=$account['log_ip']?>">View Users</a></center></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php
	}else{
?>
<section id="content" class="container_12 clearfix" data-sort=true>
	<h1 class="grid_12">Users (<?=$total_pages?>)</h1>
	<div class="grid_9">
	<?php echo $errMessage; ?>
		<div class="box">
			<table class="styled" style="text-align:center">
				<thead>
					<tr>
						<th><a href="index.php?x=users&sort=<?=($sorting == 'desc' ? 'asc' : 'desc')?>&oby=1">ID <img src="img/elements/table/sorting<?=($sorting == 'asc' && $order_by == 1 ? '-asc' : ($sorting == 'desc' && $order_by == 1 ? '-desc' : ''))?>.png" border="0" /></a></th>
						<th>Username</th>
						<th>Email</th>
						<th>Country</th>
						<th><a href="index.php?x=users&sort=<?=($sorting == 'desc' ? 'asc' : 'desc')?>&oby=2">Account Balance <img src="img/elements/table/sorting<?=($sorting == 'asc' && $order_by == 2 ? '-asc' : ($sorting == 'desc' && $order_by == 2 ? '-desc' : ''))?>.png" border="0" /></a></th>
						<th><a href="index.php?x=users&sort=<?=($sorting == 'desc' ? 'asc' : 'desc')?>&oby=3">Faucet Claims <img src="img/elements/table/sorting<?=($sorting == 'asc' && $order_by == 3 ? '-asc' : ($sorting == 'desc' && $order_by == 3 ? '-desc' : ''))?>.png" border="0" /></a></th>
						<th>Type</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$users = $db->QueryFetchArrayAll("SELECT id, username, email, account_balance, membership, country_id, admin, total_claims, disabled FROM users".$db_value." ORDER BY ".$order_value." LIMIT ".$start.",".$limit."");
					foreach($users as $user){
				?>	
					<tr>
						<td><?=$user['id']?></td>
						<td><?=($user['disabled'] > 0 ? '<del title="Account Banned">'.$user['username'].'</del>' : ($user['membership'] > 0 ? '<font color="green">'.$user['username'].'</font>' : $user['username']))?></td>
						<td><?=$user['email']?></td>
						<td><?=($user['country_id'] == '0' ? 'N/A' : get_country($user['country_id']))?></td>
						<td><?=number_format($user['account_balance'], 2)?> Bits</td>
						<td><?=number_format($user['total_claims'])?> claims</td>
						<td><?=($user['admin'] == 0 ? 'User' : '<b>Admin</b>')?></td>
						<td class="center">
							<a href="index.php?x=users<?=$url_value?>&edit=<?=$user['id']?>" class="button small grey tooltip" data-gravity=s title="Edit"><i class="icon-pencil"></i></a>
							<a href="index.php?x=users<?=$url_value?>&del=<?=$user['id']?>" onclick="return confirm('You sure you want to delete this user?');" class="button small red tooltip" data-gravity=s title="Remove"><i class="icon-remove"></i></a>
							<?php if(isset($_GET['unverified'])){ ?>
								<a href="index.php?x=users<?=$url_value?>&confirm=<?=$user['id']?>" onclick="return confirm('You sure you want to confirm this user email address?');" class="button small grey tooltip" data-gravity=s title="Confirm Email"><i class="icon-ok"></i></a>
							<?php } ?>
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
	<div class="grid_3">
		<form method="get" class="box validate">
			<input type="hidden" name="x" value="users" /> 
			<div class="header">
				<h2>Search Users</h2>
			</div>
			<div class="content">
				<div style="height: 10px;" class="clear"></div>
				<p class="_100 small">
					<label for="su">Search</label>
					<input type="text" class="required" name="su" id="su" />
				</p>
				<p class="_100 small">
					<label for="s_type" >By</label>
					<select name="s_type" id="s_type">
						<option value="0">Username</option>
						<option value="1">Email</option>
						<option value="2">IP Address</option>
						<option value="3">Source</option>
						<option value="4">Account balance (>=)</option>
					</select>
				</p>
				<div style="height: 10px;" class="clear"></div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Search" />
				</div>
			</div>
		</form>
	</div>
</section>
<?php } ?>