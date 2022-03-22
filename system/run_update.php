<?php
/** Update database **/
$add_config = array();

// Update to 2.1.4
$db->Query("ALTER TABLE `memberships` CHANGE `price` `price` DECIMAL(20,8) UNSIGNED NOT NULL DEFAULT '0.00000000';");
$db->Query("ALTER TABLE `ad_packs` CHANGE `price` `price` DECIMAL(20,8) UNSIGNED NOT NULL DEFAULT '0.00000000';");
$db->Query("ALTER TABLE `deposits` CHANGE `amount` `amount` DECIMAL(22,8) UNSIGNED NOT NULL DEFAULT '0.00000000';");
$db->Query("ALTER TABLE `user_transactions` CHANGE `price` `price` DECIMAL(22,8) NOT NULL DEFAULT '0.00000000';");
$db->Query("ALTER TABLE `users` CHANGE `purchase_balance` `purchase_balance` DECIMAL(22,8) UNSIGNED NOT NULL DEFAULT '0.00000000';");
$db->Query("ALTER TABLE `completed_offers` CHANGE `survey_id` `survey_id` VARCHAR(128) NOT NULL DEFAULT '0';");

// Update to 2.1.3
$add_config[] = "('faucetpay_username', '')";

// Update to 2.1.2
$add_config[] = "('pollfish_key', ''),('pollfish_secret', ''),('pollfish_enabled', '0')";

// Update to 2.1.1
if(!$db->Query("SELECT auth_key FROM users")){
	$db->Query("ALTER TABLE `users` ADD `auth_key` VARCHAR(128) NULL DEFAULT NULL AFTER `activate`, ADD `auth_status` SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `auth_key`");
}

// Update to 2.1.0
$add_config[] = "('market_claims', '5'),('market_sl', '5'),('market_days', '7'),('market_price', '0.00010000'),('logo_image', '')";
$db->Query("CREATE TABLE `purchased_referrals` ( `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' , `ref_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' , `price` DECIMAL(12,8) UNSIGNED NOT NULL DEFAULT '0.00000000' , `time` INT(11) UNSIGNED NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB");

// Update to 2.0.1
if($db->QueryGetNumRows("SHOW KEYS FROM `shortlinks` WHERE KEY_NAME = 'hash'") > 0){
	$db->Query("ALTER TABLE `shortlinks` ADD UNIQUE(`hash`)");
	$db->Query("ALTER TABLE `shortlinks` DROP `id`");
}
if($db->QueryGetNumRows("SHOW KEYS FROM `ban_reasons` WHERE KEY_NAME = 'user' AND NON_UNIQUE = 1") > 0){
	$db->Query("ALTER TABLE `ban_reasons` ADD UNIQUE(`user`)");
	$db->Query("ALTER TABLE `ban_reasons` DROP `id`");
}

// Update to 2.0.0
$add_config[] = "('lottery_type', '0')";
if($db->QueryGetNumRows("SHOW KEYS FROM `levels` WHERE KEY_NAME = 'level'") > 0){
	$db->Query("ALTER TABLE `levels` ADD INDEX(`level`)");
}

// Update to 1.9.2
$add_config[] = "('fp_api_key', '')";
if($db->Query("SELECT fh_min_pay FROM memberships")){
	$db->Query("ALTER TABLE `memberships` CHANGE `fh_min_pay` `fp_min_pay` DECIMAL(10,8) UNSIGNED NOT NULL DEFAULT '0.00000000';");
}
if($db->Query("SELECT fh_wait_time FROM memberships")){
	$db->Query("ALTER TABLE `memberships` CHANGE `fh_wait_time` `fp_wait_time` INT(11) NOT NULL DEFAULT '0';");
}
if(!$db->Query("SELECT fp_id FROM users")){
	$db->Query("ALTER TABLE `users` ADD `fp_id` VARCHAR(128) NULL DEFAULT NULL AFTER `gender`, ADD `fp_hash` VARCHAR(128) NULL DEFAULT NULL AFTER `fp_id`;");
}
if($db->Query("SELECT fh_id FROM users")){
	$db->Query("ALTER TABLE `users` DROP `fh_id`;");
}
if($db->Query("SELECT fh_hash FROM users")){
	$db->Query("ALTER TABLE `users` DROP `fh_hash`;");
}
$db->Query("DELETE FROM `site_config` WHERE `config_name` = 'faucethub_username'");
$db->Query("DELETE FROM `site_config` WHERE `config_name` = 'faucethub_api'");

// Update to 1.9.1
$add_config[] = "('ks_api_key', '')";
if(!$db->Query("SELECT ks_min_pay FROM memberships")){
	$db->Query("ALTER TABLE `memberships` ADD `ks_min_pay` DECIMAL(10,8) UNSIGNED NOT NULL DEFAULT '0.00000000' AFTER `btc_min_pay`;");
}
if(!$db->Query("SELECT ks_wait_time FROM memberships")){
	$db->Query("ALTER TABLE `memberships` ADD `ks_wait_time` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `btc_wait_time`;");
}
if(!$db->Query("SELECT ks_id FROM users")){
	$db->Query("ALTER TABLE `users` ADD `ks_id` VARCHAR(128) NULL DEFAULT NULL AFTER `btc_id`;");
}

// Update to 1.9.0
$add_config[] = "('faucethub_username', 'hyuga')";
if(!$db->Query("SELECT method FROM deposits")){
	$db->Query("ALTER TABLE `deposits` ADD `method` SMALLINT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 = bitcoin, 1 = faucethub' AFTER `txn_id`;");
}
if(!$db->Query("SELECT fh_user FROM deposits")){
	$db->Query("ALTER TABLE `deposits` ADD `fh_user` VARCHAR(32) NULL DEFAULT NULL AFTER `txn_id`;");
}

// Update to 1.8.0
$add_config[] = "('withdraw_min_claims', '0'),('ptc_redirect_price', '20')";
if(!$db->Query("SELECT redirect FROM ptc_websites")){
	$db->Query("ALTER TABLE `ptc_websites` ADD `redirect` SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `ptc_pack`;");
}

// Update to 1.7.1
$db->Query("INSERT IGNORE INTO `offerwall_config` (`config_name`, `config_value`) VALUES ('mtt_hash',''),('mtt_reward','15000'),('mtt_url',''),('personaly_id',''),('personaly_secret',''),('personaly_hash',''),('kiwiwall_id',''),('kiwiwall_secret',''),('offerdaddy_token',''),('offerdaddy_secret',''),('bitswall_key',''),('bitswall_secret',''),('offertoro_app',''),('offertoro_pub',''),('offertoro_secret',''),('tr_key',''),('tr_secret',''),('adgem_app',''),('adgem_hash','')");

// Update to 1.7.0
$add_config[] = "('shortlink_reset', '0'),('blog_comments', '1'),('transfer_min', '1000'),('transfer_status', '1')";
$db->Query("CREATE TABLE IF NOT EXISTS `blog` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `author` int(11) unsigned NOT NULL DEFAULT '0', `title` varchar(255) DEFAULT NULL, `description` varchar(255) DEFAULT NULL, `content` text DEFAULT NULL, `views` int(11) unsigned NOT NULL DEFAULT '0', `timestamp` int(11) unsigned NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8 ;");
$db->Query("CREATE TABLE IF NOT EXISTS `blog_comments` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `bid` int(11) unsigned NOT NULL DEFAULT '0', `author` int(11) unsigned NOT NULL DEFAULT '0', `comment` text DEFAULT NULL, `timestamp` int(11) unsigned NOT NULL DEFAULT '0',  KEY `bid` (`bid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$db->Query("CREATE TABLE IF NOT EXISTS `funds_transfers` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `user_id` int(11) unsigned NOT NULL DEFAULT '0', `bits` decimal(12,2) unsigned NOT NULL DEFAULT '0.00', `satoshi` int(11) unsigned NOT NULL DEFAULT '0', `bits_rate` decimal(4,2) unsigned NOT NULL DEFAULT '0.00', `time` int(11) unsigned NOT NULL DEFAULT '0', KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
if(!$db->Query("SELECT today_views FROM shortlinks_config")){$db->Query("ALTER TABLE `shortlinks_config` ADD `today_views` int(11) unsigned NOT NULL DEFAULT '0';");}
if(!$db->Query("SELECT total_views FROM shortlinks_config")){$db->Query("ALTER TABLE `shortlinks_config` ADD `total_views` int(11) unsigned NOT NULL DEFAULT '0';");}

// Update to 1.6.0
$add_config[] = "('chatbro_id', ''),('chatbro_key', ''),('chatbro_status', '0'),('proxycheck_status', '0'),('proxycheck', '')";
$db->Query("CREATE TABLE IF NOT EXISTS `ip_checks` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `user_id` int(11) unsigned NOT NULL DEFAULT '0', `ip_address` varchar(128) DEFAULT NULL, `country_code` varchar(16) DEFAULT NULL, `status` smallint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0 = clear, 1 = proxy', `checked` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0 = pending, 1 = checked', `time` int(11) unsigned NOT NULL DEFAULT '0', UNIQUE KEY `user_id` (`user_id`,`ip_address`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Update to 1.5.0
$add_config[] = "('lottery_default', '200'),('lottery_duration', '0'),('lottery_fee', '0'),('lottery_status', '0')";
$db->Query("INSERT IGNORE INTO `offerwall_config` (`config_name`, `config_value`) VALUES ('cpalead_link',''),('cpalead_password','')");
$db->Query("CREATE TABLE IF NOT EXISTS `lottery` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `prize` decimal(32,2) unsigned NOT NULL DEFAULT '0.00', `tickets_purchased` int(11) unsigned NOT NULL DEFAULT '0', `date` int(11) unsigned NOT NULL DEFAULT '0', `end_date` int(11) unsigned NOT NULL DEFAULT '0', `winner_id` int(11) unsigned NOT NULL DEFAULT '0', `winner_tickets` int(11) unsigned NOT NULL DEFAULT '0', `winning_ticket` int(11) unsigned NOT NULL DEFAULT '0', `closed` smallint(1) unsigned NOT NULL DEFAULT '0', KEY `closed` (`closed`), KEY `winner_id` (`winner_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$db->Query("CREATE TABLE IF NOT EXISTS `lottery_tickets` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `lottery_id` int(11) unsigned NOT NULL DEFAULT '0', `user_id` int(11) unsigned NOT NULL DEFAULT '0', `date` int(11) unsigned NOT NULL DEFAULT '0', KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
if(!$db->Query("SELECT lottery_price FROM memberships")){
	$db->Query("ALTER TABLE `memberships` ADD `lottery_price` INT(11) unsigned NOT NULL DEFAULT '0';");
}

// Update to 1.4.1
$db->Query("CREATE TABLE IF NOT EXISTS `shortlinks_session` (`user_id` int(11) NOT NULL DEFAULT '0', `short_id` int(11) NOT NULL DEFAULT '0', `time` int(11) unsigned NOT NULL DEFAULT '0', UNIQUE KEY `user_id` (`user_id`,`short_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Update to 1.4.0
$add_config[] = "('sl_duration', '0'),('tc_duration', '0'),('contest_duration', '0')";
if(!$db->Query("SELECT short_com FROM memberships")){
	$db->Query("ALTER TABLE `memberships` ADD `short_com` INT(11) NOT NULL DEFAULT '0' AFTER `offer_com`;");
}

// Update to 1.3.0
$add_config[] = "('invest_win', '2')";

// Update to 1.2.1
$add_config[] = "('wmp_key', ''),('wmp_secret', '')";

// Update to 1.2.0
$db->Query("CREATE TABLE IF NOT EXISTS `coupons` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL UNIQUE KEY, `value` int(11) NOT NULL DEFAULT '0', `uses` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0', `used` int(11) NOT NULL DEFAULT '0', `type` int(11) NOT NULL DEFAULT '0', `claims` int(11) NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
$db->Query("CREATE TABLE IF NOT EXISTS `coupons_used` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `user_id` int(11) NOT NULL DEFAULT '0', `coupon_id` int(11) NOT NULL DEFAULT '0', `time` int(11) unsigned NOT NULL DEFAULT '0', UNIQUE KEY `user_id` (`user_id`,`coupon_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

// Update to 1.1.1
$add_config[] = "('faucet_sl_required', '3')";

// Update to 1.1.0
$add_config[] = "('faucet_time', '60'),('sl_prizes', '200,100,50'),('sl_points', '200'),('bg_color', '433c41'),('title_color', '2c3135')";
if(!$db->Query("SELECT shortlinks_contest FROM users")){
	$db->Query("ALTER TABLE `users` ADD `shortlinks_contest` INT(11) NOT NULL DEFAULT '0' AFTER `tasks_contest`;");
}

$db->Query("CREATE TABLE IF NOT EXISTS `shortlinks_contest` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,`winners` varchar(128) DEFAULT NULL,`points` varchar(128) DEFAULT NULL,`prizes` varchar(128) DEFAULT NULL,`start_date` int(11) unsigned NOT NULL DEFAULT '0' UNIQUE KEY,`end_date` int(11) unsigned NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8;");
if(!$db->Query("SELECT winner_tickets FROM lottery")){
	$db->Query("ALTER TABLE `lottery` ADD `winner_tickets` INT(11) NOT NULL DEFAULT '0';");
}

// Insert new configs
$config_values = implode(',', $add_config);
$db->Query("INSERT IGNORE INTO `site_config` (`config_name`, `config_value`) VALUES ".$config_values);

// Remove files
if($db->Connect()){
	eval(base64_decode('QHVubGluayhyZWFscGF0aChkaXJuYW1lKF9fRklMRV9fKSkuJy9ydW5fdXBkYXRlLnBocCcpOw=='));
}
?>