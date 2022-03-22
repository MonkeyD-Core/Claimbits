--
-- Table structure for table `activity_rewards`
--

CREATE TABLE IF NOT EXISTS `activity_rewards` (
  `id` int(11) unsigned NOT NULL,
  `requirements` int(11) unsigned NOT NULL DEFAULT '0',
  `req_type` smallint(11) NOT NULL DEFAULT '0',
  `reward` int(11) unsigned NOT NULL DEFAULT '0',
  `type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `membership` smallint(4) unsigned NOT NULL DEFAULT '0',
  `claims` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_rewards_claims`
--

CREATE TABLE IF NOT EXISTS `activity_rewards_claims` (
  `reward_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` int(11) unsigned NOT NULL DEFAULT '0',
  `type` smallint(11) unsigned NOT NULL DEFAULT '0',
  `date` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_codes`
--

CREATE TABLE IF NOT EXISTS `ad_codes` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` text COLLATE utf8_unicode_ci,
  `size` smallint(11) unsigned NOT NULL DEFAULT '0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0',
  `loggedin` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0 = doesn''t matter, 1 = yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_packs`
--

CREATE TABLE IF NOT EXISTS `ad_packs` (
  `id` int(11) unsigned NOT NULL,
  `price` decimal(10,8) unsigned NOT NULL DEFAULT '0.00000000',
  `days` int(11) unsigned NOT NULL DEFAULT '0',
  `bought` int(11) unsigned NOT NULL DEFAULT '0',
  `type` smallint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `banner_url` varchar(255) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `views` int(11) unsigned NOT NULL DEFAULT '0',
  `clicks` int(11) unsigned NOT NULL DEFAULT '0',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0',
  `expiration` int(11) unsigned NOT NULL DEFAULT '0',
  `ad_pack` int(11) unsigned NOT NULL DEFAULT '0',
  `type` smallint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ban_reasons`
--

CREATE TABLE IF NOT EXISTS `ban_reasons` (
  `user` int(11) NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bitcoin_investments`
--

CREATE TABLE IF NOT EXISTS `bitcoin_investments` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `old_value` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `new_value` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `type` smallint(1) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bitcoin_price`
--

CREATE TABLE IF NOT EXISTS `bitcoin_price` (
  `id` int(11) unsigned NOT NULL,
  `value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `minute` varchar(11) DEFAULT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11) unsigned NOT NULL,
  `author` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `content` text,
  `views` int(11) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(11) unsigned NOT NULL,
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `author` int(11) unsigned NOT NULL DEFAULT '0',
  `comment` text,
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `completed_offers`
--

CREATE TABLE IF NOT EXISTS `completed_offers` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `survey_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `campaign_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_country` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `user_ip` bigint(20) unsigned NOT NULL,
  `revenue` decimal(12,8) unsigned NOT NULL DEFAULT '0.00000000',
  `reward` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `method` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `uses` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `used` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `claims` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons_used`
--

CREATE TABLE IF NOT EXISTS `coupons_used` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `coupon_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE IF NOT EXISTS `deposits` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_email` varchar(128) DEFAULT NULL,
  `amount` decimal(12,8) unsigned NOT NULL DEFAULT '0.00000000',
  `txn_id` varchar(128) DEFAULT NULL,
  `fh_user` varchar(32) DEFAULT NULL,
  `method` smallint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0 = bitcoin, 1 = faucethub',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0',
  `user_ip` varchar(64) DEFAULT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(11) unsigned NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faucet`
--

CREATE TABLE IF NOT EXISTS `faucet` (
  `id` int(11) unsigned NOT NULL,
  `small` int(11) unsigned NOT NULL DEFAULT '0',
  `big` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faucet_claims`
--

CREATE TABLE IF NOT EXISTS `faucet_claims` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `number` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faucet_history`
--

CREATE TABLE IF NOT EXISTS `faucet_history` (
  `id` int(11) unsigned NOT NULL,
  `total_claims` int(11) unsigned NOT NULL DEFAULT '0',
  `total_link` int(11) unsigned NOT NULL DEFAULT '0',
  `total_revenue` decimal(12,6) unsigned NOT NULL DEFAULT '0.000000',
  `total_users` int(11) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0001-01-01'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funds_transfers`
--

CREATE TABLE IF NOT EXISTS `funds_transfers` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bits` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `satoshi` int(11) unsigned NOT NULL DEFAULT '0',
  `bits_rate` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ip_checks`
--

CREATE TABLE IF NOT EXISTS `ip_checks` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(128) DEFAULT NULL,
  `country_code` varchar(16) DEFAULT NULL,
  `status` smallint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0 = clear, 1 = proxy',
  `checked` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0 = pending, 1 = checked',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `requirement` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_required` smallint(1) unsigned NOT NULL DEFAULT '0',
  `reward` decimal(8,2) NOT NULL DEFAULT '0.00',
  `type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `membership` smallint(11) unsigned NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs_done`
--

CREATE TABLE IF NOT EXISTS `jobs_done` (
  `id` int(11) unsigned NOT NULL,
  `job_id` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `requirement` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reward` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `membership` smallint(11) unsigned NOT NULL DEFAULT '0',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE IF NOT EXISTS `levels` (
  `id` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `requirements` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_countries`
--

CREATE TABLE IF NOT EXISTS `list_countries` (
  `id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lottery`
--

CREATE TABLE IF NOT EXISTS `lottery` (
  `id` int(11) unsigned NOT NULL,
  `prize` decimal(32,2) unsigned NOT NULL DEFAULT '0.00',
  `tickets_purchased` int(11) unsigned NOT NULL DEFAULT '0',
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0',
  `winner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `winner_tickets` int(11) unsigned NOT NULL DEFAULT '0',
  `winning_ticket` int(11) unsigned NOT NULL DEFAULT '0',
  `closed` smallint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lottery_tickets`
--

CREATE TABLE IF NOT EXISTS `lottery_tickets` (
  `id` int(11) unsigned NOT NULL,
  `lottery_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `date` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE IF NOT EXISTS `memberships` (
  `id` int(11) unsigned NOT NULL,
  `membership` varchar(32) DEFAULT NULL,
  `price` decimal(10,8) unsigned NOT NULL DEFAULT '0.00000000',
  `multiplier` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `ref_com` int(11) unsigned NOT NULL DEFAULT '0',
  `offer_com` int(11) unsigned NOT NULL DEFAULT '0',
  `short_com` int(11) unsigned NOT NULL DEFAULT '0',
  `fp_min_pay` decimal(10,8) unsigned NOT NULL DEFAULT '0.00000000',
  `btc_min_pay` decimal(10,8) NOT NULL DEFAULT '0.00000000',
  `ks_min_pay` decimal(10,8) unsigned NOT NULL DEFAULT '0.00000000',
  `hide_ads` smallint(1) unsigned NOT NULL DEFAULT '0',
  `fp_wait_time` int(11) NOT NULL DEFAULT '0',
  `btc_wait_time` int(11) unsigned NOT NULL DEFAULT '0',
  `ks_wait_time` int(11) unsigned NOT NULL DEFAULT '0',
  `hash_rate` int(11) unsigned NOT NULL DEFAULT '0',
  `lottery_price` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `notify_id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `read` smallint(2) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offerwall_config`
--

CREATE TABLE IF NOT EXISTS `offerwall_config` (
  `config_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `config_value` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ptc_done`
--

CREATE TABLE IF NOT EXISTS `ptc_done` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ptc_packs`
--

CREATE TABLE IF NOT EXISTS `ptc_packs` (
  `id` int(11) unsigned NOT NULL,
  `price` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ptc_sessions`
--

CREATE TABLE IF NOT EXISTS `ptc_sessions` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ses_key` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ptc_websites`
--

CREATE TABLE IF NOT EXISTS `ptc_websites` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `received_today` int(11) unsigned NOT NULL DEFAULT '0',
  `received` int(11) unsigned NOT NULL DEFAULT '0',
  `daily_limit` int(11) unsigned NOT NULL DEFAULT '0',
  `total_visits` int(11) unsigned NOT NULL DEFAULT '0',
  `ptc_pack` smallint(11) unsigned NOT NULL DEFAULT '0',
  `redirect` smallint(1) unsigned NOT NULL DEFAULT '0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0',
  `added_time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_contest`
--

CREATE TABLE IF NOT EXISTS `referral_contest` (
  `id` int(11) unsigned NOT NULL,
  `winners` varchar(128) DEFAULT NULL,
  `total_referrals` varchar(128) DEFAULT NULL,
  `prizes` varchar(128) DEFAULT NULL,
  `start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ref_commissions`
--

CREATE TABLE IF NOT EXISTS `ref_commissions` (
  `id` int(11) unsigned NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `referral` int(11) NOT NULL DEFAULT '0',
  `commission` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `date` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortlinks`
--

CREATE TABLE IF NOT EXISTS `shortlinks` (
  `short_id` int(11) unsigned NOT NULL DEFAULT '0',
  `shortlink` varchar(128) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortlinks_config`
--

CREATE TABLE IF NOT EXISTS `shortlinks_config` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `shortlink` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `daily_limit` int(11) NOT NULL DEFAULT '1',
  `reward` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `today_views` int(11) unsigned NOT NULL DEFAULT '0',
  `total_views` int(11) unsigned DEFAULT '0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortlinks_contest`
--

CREATE TABLE IF NOT EXISTS `shortlinks_contest` (
  `id` int(11) unsigned NOT NULL,
  `winners` varchar(128) DEFAULT NULL,
  `points` varchar(128) DEFAULT NULL,
  `prizes` varchar(128) DEFAULT NULL,
  `start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortlinks_done`
--

CREATE TABLE IF NOT EXISTS `shortlinks_done` (
  `short_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortlinks_session`
--

CREATE TABLE IF NOT EXISTS `shortlinks_session` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `short_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `site_config`
--

CREATE TABLE IF NOT EXISTS `site_config` (
  `config_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `config_value` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_contest`
--

CREATE TABLE IF NOT EXISTS `tasks_contest` (
  `id` int(11) unsigned NOT NULL,
  `winners` varchar(128) DEFAULT NULL,
  `points` varchar(128) DEFAULT NULL,
  `prizes` varchar(128) DEFAULT NULL,
  `start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `account_balance` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `purchase_balance` decimal(16,8) unsigned NOT NULL DEFAULT '0.00000000',
  `membership` int(11) unsigned NOT NULL DEFAULT '0',
  `membership_id` smallint(11) unsigned NOT NULL DEFAULT '1',
  `level` int(11) unsigned DEFAULT '1',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `country_id` int(11) unsigned NOT NULL DEFAULT '0',
  `gender` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fp_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fp_hash` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `btc_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ks_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ow_credits` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `today_claims` int(11) unsigned NOT NULL DEFAULT '0',
  `total_claims` int(11) unsigned NOT NULL DEFAULT '0',
  `today_revenue` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `total_revenue` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `last_claim` int(11) unsigned NOT NULL DEFAULT '0',
  `pending_ch` int(11) unsigned NOT NULL DEFAULT '0',
  `today_ch` int(11) unsigned NOT NULL DEFAULT '0',
  `total_ch` int(11) unsigned NOT NULL DEFAULT '0',
  `sl_earnings` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `sl_today_earnings` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `sl_total` int(11) unsigned NOT NULL DEFAULT '0',
  `sl_today` int(11) unsigned NOT NULL DEFAULT '0',
  `tasks_contest` int(11) unsigned NOT NULL DEFAULT '0',
  `shortlinks_contest` int(11) NOT NULL DEFAULT '0',
  `reg_time` int(11) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(11) unsigned NOT NULL DEFAULT '0',
  `ref` int(11) unsigned NOT NULL DEFAULT '0',
  `reg_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `log_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `activate` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `rec_hash` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `warn_message` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `warn_expire` int(11) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ref_source` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_deleted`
--

CREATE TABLE IF NOT EXISTS `users_deleted` (
  `id` int(11) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `login` varchar(32) NOT NULL,
  `pass` varchar(32) DEFAULT NULL,
  `sex` int(11) NOT NULL DEFAULT '0',
  `country_id` int(11) NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '0001-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_offers`
--

CREATE TABLE IF NOT EXISTS `users_offers` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `total_offers` int(11) unsigned NOT NULL DEFAULT '0',
  `total_revenue` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `last_offer` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

CREATE TABLE IF NOT EXISTS `users_sessions` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `browser` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_address` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE IF NOT EXISTS `user_logins` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(32) NOT NULL DEFAULT '0',
  `info` varchar(255) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '0001-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_transactions`
--

CREATE TABLE IF NOT EXISTS `user_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '1 = membership, 2 = ptc ads, 3 = banner ads, 4 = lottery',
  `value` int(11) NOT NULL DEFAULT '0',
  `price` decimal(12,8) NOT NULL DEFAULT '0.00000000',
  `date` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bits` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `btc` decimal(10,8) unsigned NOT NULL DEFAULT '0.00000000',
  `method` int(11) unsigned NOT NULL DEFAULT '0',
  `payment_info` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payout_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_address` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `status` int(2) unsigned NOT NULL DEFAULT '0',
  `reason` varchar(255) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wrong_logins`
--

CREATE TABLE IF NOT EXISTS `wrong_logins` (
  `ip_address` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(3) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `activity_rewards`
--
ALTER TABLE `activity_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_rewards_claims`
--
ALTER TABLE `activity_rewards_claims`
  ADD UNIQUE KEY `unique_id` (`reward_id`,`user_id`) USING BTREE;

--
-- Indexes for table `ad_codes`
--
ALTER TABLE `ad_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `size` (`size`),
  ADD KEY `loggedin` (`loggedin`);

--
-- Indexes for table `ad_packs`
--
ALTER TABLE `ad_packs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `expiration` (`expiration`);

--
-- Indexes for table `ban_reasons`
--
ALTER TABLE `ban_reasons`
  ADD UNIQUE KEY `user` (`user`);

--
-- Indexes for table `bitcoin_investments`
--
ALTER TABLE `bitcoin_investments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bitcoin_price`
--
ALTER TABLE `bitcoin_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bid` (`bid`);

--
-- Indexes for table `completed_offers`
--
ALTER TABLE `completed_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`) USING BTREE;

--
-- Indexes for table `coupons_used`
--
ALTER TABLE `coupons_used`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`coupon_id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faucet`
--
ALTER TABLE `faucet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `numbers` (`small`,`big`) USING BTREE;

--
-- Indexes for table `faucet_claims`
--
ALTER TABLE `faucet_claims`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faucet_history`
--
ALTER TABLE `faucet_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`) USING BTREE;

--
-- Indexes for table `funds_transfers`
--
ALTER TABLE `funds_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ip_checks`
--
ALTER TABLE `ip_checks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`ip_address`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs_done`
--
ALTER TABLE `jobs_done`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requirements` (`requirements`),
  ADD KEY `level` (`level`);

--
-- Indexes for table `list_countries`
--
ALTER TABLE `list_countries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `lottery`
--
ALTER TABLE `lottery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `closed` (`closed`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Indexes for table `lottery_tickets`
--
ALTER TABLE `lottery_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `read` (`read`);

--
-- Indexes for table `offerwall_config`
--
ALTER TABLE `offerwall_config`
  ADD UNIQUE KEY `config_name` (`config_name`);

--
-- Indexes for table `ptc_done`
--
ALTER TABLE `ptc_done`
  ADD UNIQUE KEY `user_id` (`user_id`,`site_id`);

--
-- Indexes for table `ptc_packs`
--
ALTER TABLE `ptc_packs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ptc_sessions`
--
ALTER TABLE `ptc_sessions`
  ADD UNIQUE KEY `unique_id` (`user_id`,`site_id`) USING BTREE;

--
-- Indexes for table `ptc_websites`
--
ALTER TABLE `ptc_websites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `referral_contest`
--
ALTER TABLE `referral_contest`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `start_date` (`start_date`);

--
-- Indexes for table `ref_commissions`
--
ALTER TABLE `ref_commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`user`,`referral`) USING BTREE;

--
-- Indexes for table `shortlinks`
--
ALTER TABLE `shortlinks`
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `short_id` (`short_id`);

--
-- Indexes for table `shortlinks_config`
--
ALTER TABLE `shortlinks_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shortlinks_contest`
--
ALTER TABLE `shortlinks_contest`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `start_date` (`start_date`);

--
-- Indexes for table `shortlinks_done`
--
ALTER TABLE `shortlinks_done`
  ADD UNIQUE KEY `short_id` (`short_id`,`user_id`);

--
-- Indexes for table `shortlinks_session`
--
ALTER TABLE `shortlinks_session`
  ADD UNIQUE KEY `user_id` (`user_id`,`short_id`);

--
-- Indexes for table `site_config`
--
ALTER TABLE `site_config`
  ADD UNIQUE KEY `config_name` (`config_name`);

--
-- Indexes for table `tasks_contest`
--
ALTER TABLE `tasks_contest`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `start_date` (`start_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disabled` (`disabled`),
  ADD KEY `reg_time` (`reg_time`),
  ADD KEY `membership_id` (`membership_id`);

--
-- Indexes for table `users_deleted`
--
ALTER TABLE `users_deleted`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `users_offers`
--
ALTER TABLE `users_offers`
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `users_sessions`
--
ALTER TABLE `users_sessions`
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `user_transactions`
--
ALTER TABLE `user_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `timestamp` (`time`);

--
-- Indexes for table `wrong_logins`
--
ALTER TABLE `wrong_logins`
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- AUTO_INCREMENT for table `activity_rewards`
--
ALTER TABLE `activity_rewards`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ad_codes`
--
ALTER TABLE `ad_codes`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ad_packs`
--
ALTER TABLE `ad_packs`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bitcoin_investments`
--
ALTER TABLE `bitcoin_investments`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bitcoin_price`
--
ALTER TABLE `bitcoin_price`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `completed_offers`
--
ALTER TABLE `completed_offers`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `coupons_used`
--
ALTER TABLE `coupons_used`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faucet`
--
ALTER TABLE `faucet`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faucet_claims`
--
ALTER TABLE `faucet_claims`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `faucet_history`
--
ALTER TABLE `faucet_history`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `funds_transfers`
--
ALTER TABLE `funds_transfers`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ip_checks`
--
ALTER TABLE `ip_checks`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs_done`
--
ALTER TABLE `jobs_done`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `list_countries`
--
ALTER TABLE `list_countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lottery`
--
ALTER TABLE `lottery`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lottery_tickets`
--
ALTER TABLE `lottery_tickets`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ptc_packs`
--
ALTER TABLE `ptc_packs`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ptc_websites`
--
ALTER TABLE `ptc_websites`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `referral_contest`
--
ALTER TABLE `referral_contest`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ref_commissions`
--
ALTER TABLE `ref_commissions`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shortlinks_config`
--
ALTER TABLE `shortlinks_config`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shortlinks_contest`
--
ALTER TABLE `shortlinks_contest`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tasks_contest`
--
ALTER TABLE `tasks_contest`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_transactions`
--
ALTER TABLE `user_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;