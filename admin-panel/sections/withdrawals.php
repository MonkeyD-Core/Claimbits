<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

$msg = '';
$currency = getCurrency();
if(isset($_GET['pay']) && is_numeric($_GET['pay'])){
	$pid = $db->EscapeString($_GET['pay']);
	$req = $db->QueryFetchArray("SELECT id,user_id,btc,status,method,payment_info FROM `withdrawals` WHERE `id`='".$pid."'");
	if(!empty($req['id'])){
		if($req['status'] == 0){
			if($req['method'] == 3) {
				$satoshi = ($req['btc']*100000000);
				$faucetpay = new FaucetPay($config['fp_api_key'], $currency);
				$fp_result = $faucetpay->send($req['payment_info'], $satoshi);

				if($fp_result['success'] == true)
				{
					$db->Query("UPDATE `withdrawals` SET `status`='1', `payout_id`='".$fp_result['payout_id']."' WHERE `id`='".$req['id']."'");
					$msg = '<div class="alert success">'.$req['btc'].' BTC was transfered and request was marked as paid!</a></div>';
					
					add_notification($req['user_id'], 7, $req['id']);
				}
				else
				{
					$msg = '<div class="alert error">System wasn\'t able to send requested amount, check your FaucetPay account!</div>';
				}
			}
			else if($req['method'] == 2)
			{
				$satoshi = ($req['btc']*100000000);
				$kswallet = new KSWallet($config['ks_api_key'], $currency);
				$ks_result = $kswallet->send($req['payment_info'], $satoshi);

				if($ks_result['status'] == 200)
				{
					$db->Query("UPDATE `withdrawals` SET `status`='1', `payout_id`='".$ks_result['hash']."' WHERE `id`='".$req['id']."'");
					$msg = '<div class="alert success">'.$req['btc'].' BTC was transfered and request was marked as paid!</a></div>';
					
					add_notification($req['user_id'], 7, $req['id']);
				}
				else
				{
					$msg = '<div class="alert error">System wasn\'t able to send requested amount, check your KSWallet account!</div>';
				}
			}
			else
			{
				// Initiate CoinPayments API
				$cps_api = new CoinpaymentsAPI($config['cp_private_key'], $config['cp_public_key'], 'json');	
				$withdraw = array('amount' => $req['btc'], 'address' => $req['payment_info'], 'currency' => $currency, 'auto_confirm' => 1, 'note' => 'Withdrawal #'.$req['id']);
				$transaction = $cps_api->CreateWithdrawal($withdraw);
				
				if($transaction['error'] == 'ok')
				{
					$db->Query("UPDATE `withdrawals` SET `status`='1', `payout_id`='".$transaction['result']['id']."' WHERE `id`='".$req['id']."'");
					$msg = '<div class="alert success">'.$req['btc'].' '.$currency.' was transfered and request was marked as paid!</div>';
					
					add_notification($req['user_id'], 7, $req['id']);
				}
				else
				{
					$msg = '<div class="alert error">CoinPayments Error: '.$transaction['error'].'</div>';
				}

			}
		}
	}else{
		$msg = '<div class="alert error">This request doesn\'t exist!</div>';
	}
}
if(isset($_GET['mark']) && is_numeric($_GET['mark'])){
	$pid = $db->EscapeString($_GET['mark']);
	$req = $db->QueryFetchArray("SELECT id,user_id FROM `withdrawals` WHERE `id`='".$pid."'");

	if(!empty($req['id'])){
		$db->Query("UPDATE `withdrawals` SET `status`='1' WHERE `id`='".$req['id']."'");
		$msg = '<div class="alert success">Request <b>#'.$req['id'].'</b> was marked as paid!</div>';
		
		add_notification($req['user_id'], 7, $req['id']);
	}else{
		$msg = '<div class="alert error">This request doesn\'t exist!</div>';
	}
}
if(isset($_GET['refund']) && is_numeric($_GET['refund'])){
	$pid = $db->EscapeString($_GET['refund']);
	$req = $db->QueryFetchArray("SELECT id,user_id,bits,status FROM `withdrawals` WHERE `id`='".$pid."'");
	if(!empty($req['id'])){
		if($req['status'] == 0){
			$db->Query("UPDATE `withdrawals` SET `status`='3' WHERE `id`='".$pid."'");
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$req['bits']."' WHERE `id`='".$req['user_id']."'");
		}
		$msg = '<div class="alert success">Request was successfully refunded!</div>';
	}else{
		$msg = '<div class="alert error">This request doesn\'t exist!</div>';
	}
}
if(isset($_GET['reject']) && is_numeric($_GET['reject'])){
	$pid = $db->EscapeString($_GET['reject']);
	$rej = $db->QueryFetchArray("SELECT id,reason FROM `withdrawals` WHERE `id`='".$pid."'");
	if(empty($rej['id'])){
		redirect("index.php?x=withdrawals");
	}

	if(isset($_POST['reject'])){
		if($_POST['reason'] != ''){
			$reason = $db->EscapeString($_POST['reason']);
			$db->Query("UPDATE `withdrawals` SET `status`='2', `reason`='".$reason."' WHERE `id`='".$pid."'");
			$msg = '<div class="alert success">Request marked as rejected!</div>';
		}else{
			$msg = '<div class="alert error">Please write the reason!</div>';
		}
	}
?>
<section id="content" class="container_12 clearfix"><?=$msg?>
	<div class="grid_12">
		<form method="post" class="box">
			<div class="header">
				<h2>Reject Request</h2>
			</div>
			<div class="content">
				<div class="row">
					<label><strong>Reason</strong><small>Why do you want to reject that request?</small></label>
					<div><textarea name="reason"><?=(isset($_POST['reason']) ? $_POST['reason'] : $rej['reason'])?></textarea></div>
				</div>
			</div>
			<div class="actions">
				<div class="right">
					<input type="submit" value="Reject" name="reject" />
				</div>
			</div>
		</form>
	</div>
</section>
<?php
}elseif(isset($_GET['info']) && is_numeric($_GET['info'])){
	$pid = $db->EscapeString($_GET['info']);
	$info = $db->QueryFetchArray("SELECT * FROM `withdrawals` WHERE `id`='".$pid."'");
	if(empty($info['id'])){
		redirect("index.php?x=withdrawals");
	}

	$user = $db->QueryFetchArray("SELECT * FROM `users` WHERE `id`='".$info['user_id']."' LIMIT 1");
	$refs = $db->QueryGetNumRows("SELECT id FROM `users` WHERE `ref`='".$user['id']."'");
?>
<section id="content" class="container_12 clearfix">
	<div class="grid_6">
		<div class="box">
			<div class="header">
				<h2>Payout Request</h2>
			</div>
            <div class="content">
				<p><strong>User ID:</strong> <a href="index.php?x=users&edit=<?=$info['user_id']?>"><?=$info['user_id']?></a></p>
				<p><strong>IP Status:</strong> <b id="checkIP"><img src="img/ajax-loader.gif" border="0" /></b></p>
				<p><strong>Bits Amount:</strong> <font color="blue"><b><?=number_format($info['bits'], 2)?></b></font></p>
				<p><strong>Withdraw Method:</strong> <?=($info['method'] == 0 ? 'FaucetHub' : ($info['method'] == 2 ? 'KSWallet' : ($info['method'] == 3 ? 'FaucetPay' : getCurrency('name').' Wallet')))?></p>
				<p><strong>Bitcoin Address:</strong> <?=$info['payment_info']?></p>
				<p><strong>Bitcoin Amount:</strong> <font color="green"><b><?=number_format($info['btc'], 8)?></b></font></p>
				<hr>
				<p align="center">
					<a href="index.php?x=withdrawals&pay=<?=$info['id']?>" class="button small grey tooltip" title="Accept"><i class="icon-ok"></i></a>
					<a href="index.php?x=withdrawals&reject=<?=$info['id']?>" class="button small grey tooltip" title="Reject"><i class="icon-remove"></i></a>
				</p>
			</div>
		</div>
	</div>
	<div class="grid_6">
		<div class="box">
			<div class="header">
				<h2>User Info</h2>
			</div>
            <div class="content">
				<p><strong>Username:</strong> <a href="index.php?x=users&edit=<?=$user['id']?>"><?=$user['username']?></a></p>
				<p><strong>User Referrals:</strong> <a href="index.php?x=users&refid=<?=$user['id']?>"><?=number_format($refs)?></a></p>
				<p><strong>Paid withdrawals:</strong> <?=$db->QueryGetNumRows("SELECT id FROM `withdrawals` WHERE `user_id`='".$user['id']."' AND `status`='1'")?></p>
			</div>
		</div>
	</div>
</section>
<script>
	$(document).ready(function() {
		 function checkIP() {
			 $.ajax({
				 url: "../system/ajax.php?checkIP=<?=(empty($user['log_ip']) ? $user['reg_ip'] : $user['log_ip'])?>&user_id=<?=$user['id']?>",
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
<?php }else{ ?>
<section id="content" class="container_12 clearfix ui-sortable"><?=$msg?>
	<h1 class="grid_12">Withdrawals</h1>
	<div class="grid_12">
		<div class="box">
			<table class="styled" style="text-align:center">
				<thead>
					<tr>
						<th width="10">#</th>
						<th>User</th>
						<th>Membership</th>
						<th>Converted Bits</th>
						<th><?=getCurrency()?> Amount</th>
						<th>Payment Info</th>
						<th>Date</th>
						<th>Gateway</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$page = (isset($_GET['p']) ? $_GET['p'] : 0);
					$limit = 20;
					$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

					$total_pages = $db->QueryGetNumRows("SELECT id FROM `withdrawals`");
					include('../system/libs/Paginator.php');

					$urlPattern = GetHref('p=(:num)');
					$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
					$paginator->setMaxPagesToShow(5);

					$reqs = $db->QueryFetchArrayAll("SELECT a.*, b.username, b.membership_id, b.last_activity, b.disabled, c.membership AS mem_name FROM withdrawals a LEFT JOIN users b ON b.id = a.user_id LEFT JOIN memberships c ON c.id = b.membership_id ORDER BY a.time DESC LIMIT ".$start.",".$limit."");
					
					if(!count($reqs))
					{
						echo '<tr><td colspan="9"><center>There is no withdrawal request yet!</center></td></tr>';
					}
					
					foreach($reqs as $req){
				?>	
					<tr>
						<td><a href="index.php?x=withdrawals&info=<?=$req['id']?>"><?=$req['id']?></a></td>
						<td><a href="index.php?x=users&edit=<?=$req['user_id']?>"><?=($req['disabled'] > 0 ? '<del title="Account Banned">'.$req['username'].'</del>' : ($req['last_activity'] >= (time()-900) ? '<font color="green">'.$req['username'].'</font>' : $req['username']))?></a></td>
						<td><?=($req['membership_id'] > 1 ? '<b>'.$req['mem_name'].'</b>' : $req['mem_name'])?></td>
						<td><?=number_format($req['bits'], 2)?> Bits</td>
						<td><?=number_format($req['btc'], 8).' '.getCurrency()?></td>
						<td><?=(!empty($req['payment_info']) ? $req['payment_info'] : 'N/A')?></td>
						<td><?=date('d M Y - H:i', $req['time'])?></td>
						<td><font color="green"><?=paymentMethod($req['method'])?></td>
						<td><?if($req['status'] == 0){?><font color="orange">Waiting<?}elseif($req['status'] == 2){?><font color="red">Rejected<?}elseif($req['status'] == 3){?><font color="green">Refunded<?}else{?><font color="green">Paid<?}?></font></td>
						<td class="center">
						<?php if($req['status'] == 0) { ?>
							<a href="index.php?x=withdrawals&p=<?=$page?>&pay=<?=$req['id']?>" onclick="return confirm('You sure you want to proccess this request?');" class="button small blue tooltip" title="Send Payment"><i class="icon-ok-sign"></i></a>
							<a href="index.php?x=withdrawals&p=<?=$page?>&refund=<?=$req['id']?>" onclick="return confirm('You sure you want to refund this request?');" class="button small grey tooltip" title="Refund"><i class="icon-refresh"></i></a>
							<a href="index.php?x=withdrawals&p=<?=$page?>&mark=<?=$req['id']?>" onclick="return confirm('You sure you want to mark this request as Paid? Payment must be sent manually.');" class="button small grey tooltip" title="Mark As Complete"><i class="icon-ok"></i></a>
							<a href="index.php?x=withdrawals&p=<?=$page?>&reject=<?=$req['id']?>" class="button small red tooltip" title="Reject"><i class="icon-remove"></i></a>
						<?php 
							} else { 
								echo 'N/A';
							}
						?>
						</td>
					</tr>
				<?php } ?>
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
		<div class="alert information">Make sure you have enough funds into FaucetPay, KSWallet or CoinPayments accounts. You won't be able to proccess pending requests if you don't have enough funds!</div>
	</div>
</section>
<?php } ?>