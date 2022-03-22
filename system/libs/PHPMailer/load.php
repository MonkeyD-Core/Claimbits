<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require('Exception.php');
	require('PHPMailer.php');
	require('SMTP.php');

	$mailer = new PHPMailer(TRUE);
?>