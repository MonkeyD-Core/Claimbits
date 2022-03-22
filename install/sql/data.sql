--
-- Dumping data for table `activity_rewards`
--

INSERT IGNORE INTO `activity_rewards` (`id`, `requirements`, `req_type`, `reward`, `type`, `membership`, `claims`) VALUES
(1, 120, 0, 25, 0, 2, 0),
(2, 600, 1, 50, 0, 0, 0),
(3, 750, 0, 3, 1, 2, 0),
(4, 1500, 1, 120, 0, 0, 0),
(5, 1200, 0, 200, 0, 0, 0),
(6, 2000, 0, 500, 0, 0, 0),
(7, 2800, 0, 5, 1, 2, 0),
(8, 3000, 1, 250, 0, 0, 0),
(9, 4000, 0, 10, 1, 0, 0),
(10, 5000, 0, 2000, 0, 0, 0),
(11, 5200, 1, 350, 0, 2, 0);

--
-- Dumping data for table `ad_packs`
--

INSERT IGNORE INTO `ad_packs` (`id`, `price`, `days`, `bought`, `type`) VALUES
(1, 0.00030000, 3, 0, 0),
(2, 0.00045000, 3, 0, 1),
(3, 0.00060000, 7, 0, 0),
(4, 0.00090000, 7, 0, 1),
(5, 0.00120000, 15, 0, 0),
(6, 0.00180000, 15, 0, 1),
(7, 0.00230000, 30, 0, 0),
(8, 0.00350000, 30, 0, 1);

--
-- Dumping data for table `faq`
--

INSERT IGNORE INTO `faq` (`id`, `question`, `answer`) VALUES
(1, 'What are Bits?', '[i]Bits[/i] are an internal currency rewarded to you after every completed activity which can be converted into Bitcoin.'),
(2, 'How can I get more Bits?', 'You can get Bits every hour from our free Faucet. Also, you can visit Shortlinks, complete PTC Visits, complete offerwalls and many more. You can also increase your Bits revenue by inviting your friends using your special affiliate URL.'),
(3, 'How many accounts can I make?', 'You are allowed to register and use just one account. If you register more than 1 account, we will disable all your accounts!'),
(4, 'How can I withdraw my Bits?', 'You can withdraw your Bits as BTC into your FaucetHub Account or Bitcoin Wallet.'),
(5, 'Can I use VPS?', 'No, you are not allowed to use VPS. We expect you to use this website from your personal device, if you use VPS, your payout request will be rejected and your account will be suspended.'),
(6, 'Why do I get logged out?', 'First of all, you can''t be connected in multiple places in the same time. If you login on device A when you will login on device B, you will be logged out from device A. Also, if your IP address changes, you will be logged out.'),
(7, 'Why there is only Bitcoin available?', 'Bitcoin is the most trusted, known and used crypto currency available and we''re not planing to add other crypto currencies.');

--
-- Dumping data for table `faucet`
--

INSERT IGNORE INTO `faucet` (`id`, `small`, `big`, `reward`) VALUES
(1, 1, 59999, 10),
(2, 60000, 79999, 15),
(3, 80000, 89999, 20),
(4, 90000, 96999, 40),
(5, 97000, 98999, 120),
(6, 99000, 99998, 250);

--
-- Dumping data for table `jobs`
--

INSERT IGNORE INTO `jobs` (`id`, `title`, `description`, `requirement`, `url_required`, `reward`, `type`, `membership`, `time`) VALUES
(1, 'Make one video and publish it on YouTube', '&lt;p&gt;Make one video about our website fulfilling all requirements then publish it on YouTube to get &lt;b&gt;200 Bits&lt;/b&gt;&lt;/p&gt;\n                        &lt;b&gt;Requirements:&lt;/b&gt;\n                        &lt;ul&gt;&lt;li&gt;Video duration: +3 minutes&lt;/li&gt;&lt;li&gt;Video quality: +360p&lt;/li&gt;&lt;li&gt;Description: include your referral link&lt;br&gt;&lt;/li&gt;&lt;/ul&gt;', 'Youtube Video URL', 1, 200.00, 0, 0, 1529935373),
(2, 'Blog post about our website', '&lt;p&gt;Write a blog post about our website fulfilling all requirements to get the reward&lt;b&gt;&lt;br&gt;&lt;/b&gt;&lt;/p&gt;\r\n                        &lt;b&gt;Requirements:&lt;/b&gt;\r\n                        &lt;ul&gt;&lt;li&gt;At least 200 words&lt;br&gt;&lt;/li&gt;&lt;li&gt;Article must be original&lt;br&gt;&lt;/li&gt;&lt;li&gt;Blog must be at least 30 days old&lt;/li&gt;&lt;li&gt;Blog must have paid domain name (.com, net, .org - blogspot and other free platforms are not accepted)&lt;br&gt;&lt;/li&gt;&lt;li&gt;Include backlink to our website&lt;br&gt;&lt;/li&gt;&lt;/ul&gt;', 'Blog post URL', 1, 7.00, 1, 2, 1529962447),
(3, 'Promote our website on forums', '&lt;p&gt;Open a thread on a forum, promoting our website, to get &lt;b&gt;75 Bits&lt;br&gt;&lt;/b&gt;&lt;/p&gt;\r\n                        &lt;b&gt;Requirements:&lt;/b&gt;\r\n                        &lt;ul&gt;&lt;li&gt;At least 50 words&lt;br&gt;&lt;/li&gt;&lt;li&gt;Post must be original&lt;br&gt;&lt;/li&gt;&lt;li&gt;Forum must have at least 5.000 members&lt;br&gt;&lt;/li&gt;&lt;li&gt;Include backlink to our website&lt;/li&gt;&lt;li&gt;Forum post must be public (no account required to see the thread)&lt;br&gt;&lt;/li&gt;&lt;/ul&gt;', 'Forum thread URL', 1, 75.00, 0, 0, 1530003717),
(4, 'Make a promo post on Facebook', '&lt;div&gt;Share your Referral URL on Facebook among with a short description about this website and include the hashtag &lt;span style=&quot;font-weight: bold;&quot;&gt;#claimbits&lt;/span&gt; then provide us your post URL or a screenshot to get &lt;span style=&quot;font-weight: bold;&quot;&gt;25 Bits&lt;/span&gt;&lt;/div&gt;&lt;div&gt;&lt;span style=&quot;font-weight: bold;&quot;&gt;&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-weight: bold;&quot;&gt;&lt;b&gt;Requirements:&lt;/b&gt;\r\n                        &lt;/span&gt;&lt;ul&gt;&lt;li&gt;Description must have at least 15 words&lt;br&gt;&lt;/li&gt;&lt;li&gt;Post privacy must be set to public&lt;br&gt;&lt;/li&gt;&lt;li&gt;Include your Referral URL&lt;br&gt;&lt;/li&gt;&lt;li&gt;Include hashtag &lt;span style=&quot;font-weight: bold;&quot;&gt;#claimbits&lt;/span&gt;&lt;/li&gt;&lt;li&gt;Post must be recent (no more than 24 hours old)&lt;br&gt;&lt;/li&gt;&lt;/ul&gt;&lt;/div&gt;', 'Facebook post URL or Screenshot URL', 1, 25.00, 0, 2, 1539802076);

--
-- Dumping data for table `levels`
--

INSERT IGNORE INTO `levels` (`id`, `level`, `requirements`, `reward`, `image`) VALUES
(1, 1, 0, 1.00, 'files/levels/Level_1.png'),
(2, 2, 100, 1.10, 'files/levels/Level_2.png'),
(3, 3, 1000, 1.20, 'files/levels/Level_3.png'),
(4, 4, 5000, 1.30, 'files/levels/Level_4.png'),
(5, 5, 10000, 1.40, 'files/levels/Level_5.png'),
(6, 6, 20000, 1.50, 'files/levels/Level_6.png'),
(7, 7, 35000, 1.60, 'files/levels/Level_7.png'),
(8, 8, 60000, 1.70, 'files/levels/Level_8.png'),
(9, 9, 100000, 1.80, 'files/levels/Level_9.png');

--
-- Dumping data for table `list_countries`
--

INSERT IGNORE INTO `list_countries` (`id`, `country`, `code`) VALUES
(1, 'United States', 'US'),
(2, 'United Kingdom', 'GB'),
(3, 'Norway', 'NO'),
(4, 'Greece', 'GR'),
(5, 'Afghanistan', 'AF'),
(6, 'Albania', 'AL'),
(7, 'Algeria', 'DZ'),
(8, 'American Samoa', 'AS'),
(9, 'Andorra', 'AD'),
(10, 'Angola', 'AO'),
(11, 'Anguilla', 'AI'),
(12, 'Antigua & Barbuda', 'AG'),
(13, 'Antilles, Netherlands', 'AN'),
(15, 'Argentina', 'AR'),
(16, 'Armenia', 'AM'),
(17, 'Aruba', 'AW'),
(18, 'Australia', 'AU'),
(19, 'Austria', 'AT'),
(20, 'Azerbaijan', 'AZ'),
(21, 'Bahamas, The', 'BS'),
(22, 'Bahrain', 'BH'),
(23, 'Bangladesh', 'BD'),
(24, 'Barbados', 'BB'),
(25, 'Belarus', 'BY'),
(26, 'Belgium', 'BE'),
(27, 'Belize', 'BZ'),
(28, 'Benin', 'BJ'),
(29, 'Bermuda', 'BM'),
(30, 'Bhutan', 'BT'),
(31, 'Bolivia', 'BO'),
(32, 'Bosnia and Herzegovina', 'BA'),
(33, 'Botswana', 'BW'),
(34, 'Brazil', 'BR'),
(35, 'British Virgin Islands', 'VG'),
(36, 'Brunei Darussalam', 'BN'),
(37, 'Bulgaria', 'BG'),
(38, 'Burkina Faso', 'BF'),
(39, 'Burundi', 'BI'),
(40, 'Cambodia', 'KH'),
(41, 'Cameroon', 'CM'),
(42, 'Canada', 'CA'),
(43, 'Cape Verde', 'CV'),
(44, 'Cayman Islands', 'KY'),
(45, 'Central African Republic', 'CF'),
(46, 'Chad', 'TD'),
(47, 'Chile', 'CL'),
(48, 'China', 'CN'),
(49, 'Colombia', 'CO'),
(50, 'Comoros', 'KM'),
(51, 'Congo', 'CG'),
(52, 'Congo', 'CD'),
(53, 'Cook Islands', 'CK'),
(54, 'Costa Rica', 'CR'),
(55, 'Cote D''Ivoire', 'CI'),
(56, 'Croatia', 'HR'),
(57, 'Cuba', 'CU'),
(58, 'Cyprus', 'CY'),
(59, 'Czech Republic', 'CZ'),
(60, 'Denmark', 'DK'),
(61, 'Djibouti', 'DJ'),
(62, 'Dominica', 'DM'),
(63, 'Dominican Republic', 'DO'),
(64, 'East Timor (Timor-Leste)', 'TP'),
(65, 'Ecuador', 'EC'),
(66, 'Egypt', 'EG'),
(67, 'El Salvador', 'SV'),
(68, 'Equatorial Guinea', 'GQ'),
(69, 'Eritrea', 'ER'),
(70, 'Estonia', 'EE'),
(71, 'Ethiopia', 'ET'),
(72, 'Falkland Islands', 'FK'),
(73, 'Faroe Islands', 'FO'),
(74, 'Fiji', 'FJ'),
(75, 'Finland', 'FI'),
(76, 'France', 'FR'),
(77, 'French Guiana', 'GF'),
(78, 'French Polynesia', 'PF'),
(79, 'Gabon', 'GA'),
(80, 'Gambia, the', 'GM'),
(81, 'Georgia', 'GE'),
(82, 'Germany', 'DE'),
(83, 'Ghana', 'GH'),
(84, 'Gibraltar', 'GI'),
(86, 'Greenland', 'GL'),
(87, 'Grenada', 'GD'),
(88, 'Guadeloupe', 'GP'),
(89, 'Guam', 'GU'),
(90, 'Guatemala', 'GT'),
(91, 'Guernsey and Alderney', 'GG'),
(92, 'Guinea', 'GN'),
(93, 'Guinea-Bissau', 'GW'),
(94, 'Guinea, Equatorial', 'GP'),
(95, 'Guiana, French', 'GF'),
(96, 'Guyana', 'GY'),
(97, 'Haiti', 'HT'),
(99, 'Honduras', 'HN'),
(100, 'Hong Kong, (China)', 'HK'),
(101, 'Hungary', 'HU'),
(102, 'Iceland', 'IS'),
(103, 'India', 'IN'),
(104, 'Indonesia', 'ID'),
(105, 'Iran, Islamic Republic of', 'IR'),
(106, 'Iraq', 'IQ'),
(107, 'Ireland', 'IE'),
(108, 'Israel', 'IL'),
(109, 'Ivory Coast (Cote d''Ivoire)', 'CI'),
(110, 'Italy', 'IT'),
(111, 'Jamaica', 'JM'),
(112, 'Japan', 'JP'),
(113, 'Jersey', 'JE'),
(114, 'Jordan', 'JO'),
(115, 'Kazakhstan', 'KZ'),
(116, 'Kenya', 'KE'),
(117, 'Kiribati', 'KI'),
(118, 'Korea, (South) Rep. of', 'KR'),
(119, 'Kuwait', 'KW'),
(120, 'Kyrgyzstan', 'KG'),
(121, 'Lao People''s Dem. Rep.', 'LA'),
(122, 'Latvia', 'LV'),
(123, 'Lebanon', 'LB'),
(124, 'Lesotho', 'LS'),
(125, 'Libyan Arab Jamahiriya', 'LY'),
(126, 'Liechtenstein', 'LI'),
(127, 'Lithuania', 'LT'),
(128, 'Luxembourg', 'LU'),
(129, 'Macao, (China)', 'MO'),
(130, 'Macedonia, TFYR', 'MK'),
(131, 'Madagascar', 'MG'),
(132, 'Malawi', 'MW'),
(133, 'Malaysia', 'MY'),
(134, 'Maldives', 'MV'),
(135, 'Mali', 'ML'),
(136, 'Malta', 'MT'),
(137, 'Martinique', 'MQ'),
(138, 'Mauritania', 'MR'),
(139, 'Mauritius', 'MU'),
(140, 'Mexico', 'MX'),
(141, 'Micronesia', 'FM'),
(142, 'Moldova, Republic of', 'MD'),
(143, 'Monaco', 'MC'),
(144, 'Mongolia', 'MN'),
(145, 'Montenegro', 'CS'),
(146, 'Morocco', 'MA'),
(147, 'Mozambique', 'MZ'),
(148, 'Myanmar (ex-Burma)', 'MM'),
(149, 'Namibia', 'NA'),
(150, 'Nepal', 'NP'),
(151, 'Netherlands', 'NL'),
(152, 'New Caledonia', 'NC'),
(153, 'New Zealand', 'NZ'),
(154, 'Nicaragua', 'NI'),
(155, 'Niger', 'NE'),
(156, 'Nigeria', 'NG'),
(157, 'Northern Mariana Islands', 'MP'),
(159, 'Oman', 'OM'),
(160, 'Pakistan', 'PK'),
(161, 'Palestinian Territory', 'PS'),
(162, 'Panama', 'PA'),
(163, 'Papua New Guinea', 'PG'),
(164, 'Paraguay', 'PY'),
(165, 'Peru', 'PE'),
(166, 'Philippines', 'PH'),
(167, 'Poland', 'PL'),
(168, 'Portugal', 'PT'),
(170, 'Qatar', 'QA'),
(171, 'Reunion', 'RE'),
(172, 'Romania', 'RO'),
(173, 'Russian Federation', 'RU'),
(174, 'Rwanda', 'RW'),
(175, 'Saint Kitts and Nevis', 'KN'),
(176, 'Saint Lucia', 'LC'),
(177, 'St. Vincent & the Grenad.', 'VC'),
(178, 'Samoa', 'WS'),
(179, 'San Marino', 'SM'),
(180, 'Sao Tome and Principe', 'ST'),
(181, 'Saudi Arabia', 'SA'),
(182, 'Senegal', 'SN'),
(183, 'Serbia', 'RS'),
(184, 'Seychelles', 'SC'),
(185, 'Singapore', 'SG'),
(186, 'Slovakia', 'SK'),
(187, 'Slovenia', 'SI'),
(188, 'Solomon Islands', 'SB'),
(189, 'Somalia', 'SO'),
(190, 'Spain', 'ES'),
(191, 'Sri Lanka (ex-Ceilan)', 'LK'),
(192, 'Sudan', 'SD'),
(193, 'Suriname', 'SR'),
(194, 'Swaziland', 'SZ'),
(195, 'Sweden', 'SE'),
(196, 'Switzerland', 'CH'),
(197, 'Syrian Arab Republic', 'SY'),
(198, 'Taiwan', 'TW'),
(199, 'Tajikistan', 'TJ'),
(200, 'Tanzania, United Rep. of', 'TZ'),
(201, 'Thailand', 'TH'),
(202, 'Togo', 'TG'),
(203, 'Tonga', 'TO'),
(204, 'Trinidad & Tobago', 'TT'),
(205, 'Tunisia', 'TN'),
(206, 'Turkey', 'TR'),
(207, 'Turkmenistan', 'TM'),
(208, 'Uganda', 'UG'),
(209, 'Ukraine', 'UA'),
(210, 'United Arab Emirates', 'AE'),
(211, 'Uruguay', 'UY'),
(212, 'Uzbekistan', 'UZ'),
(213, 'Vanuatu', 'VU'),
(214, 'Venezuela', 'VE'),
(215, 'Viet Nam', 'VN'),
(216, 'Virgin Islands, U.S.', 'VI'),
(217, 'Yemen', 'YE'),
(218, 'Zambia', 'ZM'),
(219, 'Zimbabwe', 'ZW'),
(220, 'South Africa', 'ZA');

--
-- Dumping data for table `memberships`
--

INSERT IGNORE INTO `memberships` (`id`, `membership`, `price`, `multiplier`, `ref_com`, `offer_com`, `short_com`, `fp_min_pay`, `btc_min_pay`, `ks_min_pay`, `hide_ads`, `fp_wait_time`, `btc_wait_time`, `ks_wait_time`, `hash_rate`, `lottery_price`) VALUES
(1, 'Basic', 0.00000000, 1.00, 3, 2, 4, 0.00004000, 0.00200000, 0.00004000, 0, 7, 7, 7, 60000, 40),
(2, 'Silver', 0.00050000, 1.50, 8, 5, 6, 0.00002000, 0.00150000, 0.00002000, 1, 0, 0, 0, 55000, 35),
(3, 'Gold', 0.00100000, 2.00, 16, 10, 10, 0.00001000, 0.00100000, 0.00001000, 1, 0, 0, 0, 50000, 30),
(4, 'Diamond', 0.00150000, 2.50, 15, 15, 15, 0.00000500, 0.00080000, 0.00000500, 1, 0, 0, 0, 45000, 25);

--
-- Dumping data for table `offerwall_config`
--

INSERT IGNORE INTO `offerwall_config` (`config_name`, `config_value`) VALUES
('adscend_profile', ''),
('adscend_publisher', ''),
('adscend_secret', ''),
('adwork_id', ''),
('cpalead_link', ''),
('cpalead_password', ''),
('kiwiwall_id', ''),
('kiwiwall_secret', ''),
('mtt_hash', ''),
('mtt_reward', '12000'),
('mtt_url', ''),
('offerdaddy_secret', ''),
('offerdaddy_token', ''),
('personaly_hash', ''),
('personaly_id', ''),
('personaly_secret', ''),
('ptcwall_id', ''),
('ptcwall_password', ''),
('ptcwall_reward', '25000'),
('wannads_key', ''),
('wannads_secret', '');


--
-- Dumping data for table `ptc_packs`
--

INSERT IGNORE INTO `ptc_packs` (`id`, `price`, `reward`, `time`) VALUES
(1, 15, 3.00, 10),
(2, 25, 5.00, 20),
(3, 40, 7.50, 30),
(4, 55, 10.00, 45),
(5, 75, 15.00, 60),
(6, 100, 25.00, 90),
(7, 130, 30.00, 120);

--
-- Dumping data for table `ptc_websites`
--

INSERT IGNORE INTO `ptc_websites` (`id`, `user_id`, `website`, `title`, `received_today`, `received`, `daily_limit`, `total_visits`, `ptc_pack`, `status`, `added_time`) VALUES
(1, 1, 'https://mn-shop.com/', 'MN Shop', 0, 0, 0, 10000, 4, 1, 1548880410),
(2, 1, 'https://claimbits.net/', 'ClaimBits', 0, 0, 0, 10000, 4, 1, 1548880969);

--
-- Dumping data for table `referral_contest`
--

INSERT IGNORE INTO `referral_contest` (`id`, `winners`, `total_referrals`, `prizes`, `start_date`, `end_date`) VALUES (1, NULL, NULL, NULL, 1549749540, 0);

--
-- Dumping data for table `shortlinks_config`
--

INSERT IGNORE INTO `shortlinks_config` (`id`, `name`, `shortlink`, `password`, `daily_limit`, `reward`, `status`, `today_views`, `total_views`) VALUES
(1, 'PowClick', 'clik.pw', 'd84a873db893043b2748fce2fbf3f4dca4dc8dff', 3, 3.50, 1, 0, 0),
(2, 'Cut URLs', 'cut-urls.com', 'de1b698714543d1a4c6963b58a9324ab86b327ea', 1, 7.50, 1, 0, 0),
(3, 'MozDream', 'mozdream.live', '546e8f9f521f98b726af1ce546da6954b2477d31', 3, 2.50, 1, 0, 0),
(4, 'ccURL', 'ccurl.net', 'b63618bf897844f90cb04b6f094617fc7ef92bc0', 7, 3.00, 1, 0, 0),
(5, 'DutchyCorp', 'dutchycorp.ovh/sh', '2938f686bf99a357d501dd1e8b8045b0ca17fcf1', 10, 1.60, 1, 0, 0),
(6, 'PeShort', 'short.pe', 'a46cd3559f02645615a5cfecd95ef2acb8578e61', 3, 3.00, 1, 0, 0),
(7, 'MM1', 'mm1.ink', '1c82f3e64a284ed9b226935b538251123aa6808f', 10, 1.25, 1, 0, 0),
(8, 'Clksh', 'clk.sh', 'c199bed3b7cd6dcea7ce0a03ec2cba14c7bea7a1', 1, 4.50, 1, 0, 0),
(9, 'ShortBit', 'shortbit.co', '898c4e7d9d5ea4410fe44143de2d744932d59372', 5, 4.60, 1, 0, 0),
(10, 'Voxc', 'voxc.org', '6f2a8ebec416225efab03088ca5b44ab514f7c32', 4, 3.00, 1, 0, 0),
(11, 'FcLc', 'fc.lc', '335b0b36b6cfdb2486ba8eb240a87d11e55aa021', 2, 4.00, 1, 0, 0),
(12, 'Xz2', 'xz2.xyz', '612671d88178cca8735ebd4fe024fa32e01d63b1', 1, 2.25, 1, 0, 0),
(13, 'EarnCoin', 'earncoin.site', '382d36ff8de542d1c8634e76e70e886f90cb4b0f', 1, 3.50, 1, 0, 0),
(14, 'AdShort', 'adshort.co', 'f5e9ee8372a837561c1aa5f1976bd6e6d2548c5b', 1, 4.50, 1, 0, 0),
(15, 'BoxLink', 'boxlink.us', '395eea574cf335a2af44341249b0f49ddb3bf990', 1, 3.00, 1, 0, 0),
(16, 'ShrinkThisURL', 'sturl.pw', 'd3cf409e8817406eed4bb070189aa788a7919e7e', 1, 2.75, 1, 0, 0),
(17, 'AdShort1a', 'ashort1a.xyz', '3976a811b2bc7087db2395e5cb57f451c4f29601', 10, 1.50, 1, 0, 0),
(18, 'Shortlink360', 'shortlink360.com', '4690e20e2e4dfc6e9c4555721b5c62b2e7eb5b68', 1, 2.75, 1, 0, 0),
(19, 'URLe', 'urle.io', '88981cd370de5f190c12a7e76e7fa57af34382d8', 1, 1.50, 1, 0, 0),
(20, 'OneURLs', 'oneurls.org', 'f319aa9589e1b1e77f434d9dde4e7ad8b10aa26e', 1, 2.50, 1, 0, 0),
(21, '100Count', '100count.net/clickit/sh', 'cf906da9b9bb05572123f802c5ef341340924b07', 1, 2.00, 1, 0, 0),
(22, 'CutLinks', 'cutlinks.pro', 'bd1173b92dc3e5d18562c3b794a3a87302fb5e9c', 5, 3.00, 1, 0, 0),
(23, 'GPLinks', 'gplinks.in', 'ea75b6ad370d039c62797e1cba094497fb6d6aae', 1, 3.00, 1, 0, 0),
(24, 'EarnLoad', 'earnload.com', '5f2418cf63405e783a874ab1bd43409ec55eff42', 1, 3.00, 1, 0, 0),
(25, 'ShrinkEarn', 'shrinkearn.com', '6d95ade07bb832e47699c5464b14309ba34a9af4', 1, 3.00, 1, 0, 0),
(26, 'Bitcoinly', 'bitcoinly.in', '153103d3ef4306166de3830da98e31eca0bc81dd', 1, 3.20, 1, 0, 0),
(27, 'Shorterall', 'shorterall.com', '0951d880c6c1613f54765def0cbaa64255ee9a97', 3, 2.50, 1, 0, 0),
(28, 'CashURL', 'cashurl.in', '0ebcd261fdafb192121d6fb9d428a8f128fa4f0b', 1, 3.50, 1, 0, 0),
(29, 'ShortLink180', 'shortlink180.com', '182de2a440e6b50e3cc1f112f9bc0089a6601599', 1, 2.75, 1, 0, 0);


--
-- Dumping data for table `tasks_contest`
--

INSERT IGNORE INTO `tasks_contest` (`id`, `winners`, `points`, `prizes`, `start_date`, `end_date`) VALUES (1, NULL, NULL, NULL, 1549749540, 0);