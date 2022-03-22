<?php
define('BASEPATH', true);
include('../../../system/init.php');
if($is_online && $data['admin'] == 0 || !$is_online){exit;}

if(isset($_POST['action']) && $_POST['action'] == 'get'){
	if($_POST['receivers'] == 6){
		$result = 1;
	}else{
		if($_POST['receivers'] == 1){
			$receivers = ' AND ('.time().'-`last_activity`) < 604800';
		}elseif($_POST['receivers'] == 2){
			$receivers = ' AND ('.time().'-`last_activity`) > 604800';
		}elseif($_POST['receivers'] == 5){
			$receivers = " AND `membership`>'0'";
		}elseif($_POST['receivers'] == 7){
			$country = $db->EscapeString($_POST['country']);
			$receivers = " AND `country_id` LIKE '".$country."'";
		}else{
			$receivers = '';
		}
		
		$result = $db->QueryFetchArray("SELECT COUNT(*) AS total FROM `users` WHERE (`activate`='0' AND `disabled`!='1')".$receivers);
		$result = $result['total'];
	}
	
	echo $result;
}elseif(isset($_POST['action']) && $_POST['action'] == 'send'){
	if($_POST['start'] == '' || $_POST['limit']  == '' || $_POST['title'] == '' || $_POST['message'] == '' || $_POST['captcha'] == ''){
		echo 'FIELDS_ERROR';
	}else{
		$subject = $_POST['title'];
		$message = $_POST['message'];
		$limit = $_POST['limit'];
		$start = $_POST['start'];
		
		if($_POST['secode'] == $_POST['captcha']){
			if($_POST['receivers'] == 6){
				$message = str_replace('-USER-', $data['username'], $message);
				$message = nl2br($message); 
				if(get_magic_quotes_gpc()){ $message = stripslashes($message); }
				$message = BBCode($message); 
				$subject = str_replace('-USER-', $data['username'], $subject);

				$mailer->CharSet = 'UTF-8';
				if($config['mail_delivery_method'] == 1){
					$mailer->isSMTP();
					$mailer->Host = $config['smtp_host'];
					$mailer->Port = $config['smtp_port'];

					if(!empty($config['smtp_auth'])){
						$mailer->SMTPSecure = $config['smtp_auth'];
					}
					$mailer->SMTPAuth = (empty($config['smtp_username']) || empty($config['smtp_password']) ? false : true);
					if($mailer->SMTPAuth){
						$mailer->Username = $config['smtp_username'];
						$mailer->Password = $config['smtp_password'];
					}
				}

				$mailer->AddAddress($config['site_email'], $config['site_name']);
				$mailer->SetFrom((!empty($config['noreply_email']) ? $config['noreply_email'] : $config['site_email']), $config['site_name']);
				$mailer->Subject = $subject;
				$mailer->MsgHTML('<html>
									<body style="font-family: Verdana; color: #333333; font-size: 12px;">
										<table style="width: 480px; margin: 0px auto;">
											<tr style="text-align: center;">
												<td style="border-bottom: solid 1px #cccccc;"><h1 style="margin: 0; font-size: 20px;"><a href="'.$config['site_url'].'" style="text-decoration:none;color:#333333"><b>'.$config['site_name'].'</b></a></h1><h2 style="text-align: right; font-size: 14px; margin: 7px 0 10px 0;">'.$subject.'</h2></td>
											</tr>
											<tr style="text-align: justify;">
												<td style="padding-top: 15px; padding-bottom: 15px;">
													<p>'.$message.'</p>
												</td>
											</tr>
											<tr style="text-align: right; font-size: 10px; color: #777777;">
												<td style="padding-top: 10px; border-top: solid 1px #cccccc;">
													<i>This email was sent by <a href="'.$config['secure_url'].'">'.$config['site_logo'].'</a> administrator.</i>
												</td>
											</tr>
										</table>
									</body>
								</html>');
				$mailer->Send();

				echo 'DONE';
			}else{
				if($_POST['receivers'] == 1){
					$receivers = ' AND ('.time().'-`last_activity`) < 604800';
				}elseif($_POST['receivers'] == 2){
					$receivers = ' AND ('.time().'-`last_activity`) > 604800';
				}elseif($_POST['receivers'] == 5){
					$receivers = " AND `membership`>'0'";
				}elseif($_POST['receivers'] == 7){
					$country = $db->EscapeString($_POST['country']);
					$receivers = " AND `country_id` LIKE '".$country."'";
				}else{
					$receivers = '';
				}

				$total = $db->QueryFetchArray("SELECT COUNT(*) AS total FROM `users` WHERE (`activate`='0' AND `disabled`!='1')".$receivers);
				$total = $total['total'];
				$batch = (($start+$limit) > $total ? $total : ($start+$limit));

				$sql = $db->Query("SELECT id,username,email FROM `users` WHERE (`activate`='0' AND `disabled`!='1')".$receivers." LIMIT ".$start.",".$limit);

				$mailer->CharSet = 'UTF-8';
				if($config['mail_delivery_method'] == 1){
					$mailer->isSMTP();
					$mailer->Host = $config['smtp_host'];
					$mailer->Port = $config['smtp_port'];

					if(!empty($config['smtp_auth'])){
						$mailer->SMTPSecure = $config['smtp_auth'];
					}
					$mailer->SMTPAuth = (empty($config['smtp_username']) || empty($config['smtp_password']) ? false : true);
					if($mailer->SMTPAuth){
						$mailer->Username = $config['smtp_username'];
						$mailer->Password = $config['smtp_password'];
					}
				}
				
				$mailer->SetFrom((!empty($config['noreply_email']) ? $config['noreply_email'] : $config['site_email']), $config['site_name']);
				$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!';
				
				$j = $start;
				while(($row = $db->FetchArray($sql)) and ($j <= $batch)){
					$message = str_replace('-USER-', $row['username'], $message);
					$message = nl2br($message); 
					if(get_magic_quotes_gpc()){ $message = stripslashes($message); }
					$message = BBCode($message); 
					$subject = str_replace('-USER-', $row['username'], $subject);

					$mailer->AddAddress($row['email'], $row['username']);
					$mailer->Subject = $subject;
					$mailer->MsgHTML('<html>
										<body style="font-family: Verdana; color: #333333; font-size: 12px;">
											<table style="width: 480px; margin: 0px auto;">
												<tr style="text-align: center;">
													<td style="border-bottom: solid 1px #cccccc;"><h1 style="margin: 0; font-size: 20px;"><a href="'.$config['site_url'].'" style="text-decoration:none;color:#333333"><b>'.$config['site_name'].'</b></a></h1><h2 style="text-align: right; font-size: 14px; margin: 7px 0 10px 0;">'.$subject.'</h2></td>
												</tr>
												<tr style="text-align: justify;">
													<td style="padding-top: 15px; padding-bottom: 15px;">
														<p>'.$message.'</p>
													</td>
												</tr>
												<tr style="text-align: right; font-size: 10px; color: #777777;">
													<td style="padding-top: 10px; border-top: solid 1px #cccccc;">
														<i>This email was sent by <a href="'.$config['secure_url'].'">'.$config['site_logo'].'</a> administrator.</i>
													</td>
												</tr>
											</table>
										</body>
									</html>');
					$mailer->Send();
					
					// Clear all addresses for next loop
					$mailer->clearAddresses();

					$subject = $_POST['title'];
					$message = $_POST['message'];
					$j++;

					if($total == $j){
						echo 'DONE';
					}elseif($j >= $batch && $j != $total){
						echo $j;
					}
				}
			}
		}else{
			echo 'CAPTCHA_ERROR';
		}
	}
}
?>