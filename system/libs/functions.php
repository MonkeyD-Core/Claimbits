<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load required files
	include(BASE_PATH.'/system/libs/geoip2/autoload.php');
	use MaxMind\Db\Reader;

	function executeSql($sqlFileToExecute){
		$templine = '';
		$lines    = file($sqlFileToExecute);
		$impError = 0;
		foreach($lines as $line) {
			if(substr($line, 0, 2) == '--' || $line == '')
				continue;
			$templine .= $line;
			if (substr(trim($line), -1, 1) == ';') {
				if (!mysql_query($templine)) {
					$impError = 1;
				}
				$templine = '';
			}
		}
		if ($impError == 0) {
			return 'Script is executed succesfully!';
		} else {
			return 'An error occured during SQL importing!';
		}
	}

	function redirect($location){
		$hs = headers_sent();
		if($hs === false){
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Location: '.$location);
		}elseif($hs == true){
			$location = strtr($location, array("'" => '', '"' => ''));
			echo "<script>document.location.href='".$location."'</script>";
		}
		exit(0);
	}

	function validatePassword($x){
		if(empty($x) || strlen($x) < 8) { return false; }
		if (!preg_match("#[0-9]+#",$x)) { return false; } 
		if (!preg_match("#[A-Z]+#",$x)) { return false; } 
		if (!preg_match("#[a-z]+#",$x)) { return false; } 

		return true;
	}
	
	function checkPwd($x,$y){
		if(empty($x) || empty($y) ) { return false; }
		if (strlen($x) < 8 || strlen($y) < 8) { return false; }
		if (strcmp($x,$y) != 0) { return false; } 
		if (!preg_match("#[0-9]+#",$x)) { return false; } 
		if (!preg_match("#[A-Z]+#",$x)) { return false; } 
		if (!preg_match("#[a-z]+#",$x)) { return false; } 

		return true;
	}

	function VisitorIP(){ 
		if(isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	function isEmail($email){
		return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? true : false;
	}

	function isUserID($username){
		return preg_match('/^[a-z\d_]{4,20}$/i', $username) ? true : false;
	}

	function GetHref($value){
		$qS = preg_replace(array('/p=[^&]*&?/', '/&$/'), array('', ''), $_SERVER['QUERY_STRING']);
		
		if (!empty($qS)){
			$qS.= '&';
		}
		
		return '?'.$qS.$value;
	}

	function truncate($str, $length, $trailing='...'){
		if(function_exists('mb_strlen') && function_exists('mb_substr')){
			$length-=mb_strlen($trailing);
			if(mb_strlen($str)> $length){
				return mb_substr($str,0,$length).$trailing;
			}else{
				return $str;
			}
		}else{
			return $str;
		}
	} 

	function get_data($url, $timeout = 15, $header = array(), $options = array()){
		if(!function_exists('curl_init')){
			return file_get_contents($url);
		}elseif(!function_exists('file_get_contents')){
			return '';
		}

		if(empty($options)){
			$options = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_TIMEOUT => $timeout
			);
		}
		
		if(empty($header)){
			$header = array(
				"User-Agent: Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31",
				"Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*\/*;q=0.5",
				"Accept-Language: en-us,en;q=0.5",
				"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
				"Cache-Control: must-revalidate, max-age=0",
				"Connection: keep-alive",
				"Keep-Alive: 300",
				"Pragma: public"
			);
		}

		if($header != 'NO_HEADER'){
			$options[CURLOPT_HTTPHEADER] = $header;
		}
				
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function BBCode($string){
		$search = array(
				'/(\[b\])(.*?)(\[\/b\])/',
				'/(\[i\])(.*?)(\[\/i\])/',
				'/(\[u\])(.*?)(\[\/u\])/',
				'/(\[ul\])(.*?)(\[\/ul\])/',
				'/(\[li\])(.*?)(\[\/li\])/',
				'/(\[center\])(.*?)(\[\/center\])/',
				'/(\[img\])(.*?)(\[\/img\])/',
				'/(\[url=)(.*?)(\])(.*?)(\[\/url\])/',
				'/(\[url\])(.*?)(\[\/url\])/'
		);
		$replace = array(
				'<b>$2</b>',
				'<em>$2</em>',
				'<u>$2</u>',
				'<ul>$2</ul>',
				'<li>$2</li>',
				'<center>$2</center>',
				'<img src="$2" alt="" />',
				'<a href="$2" target="_blank">$4</a>',
				'<a href="$2" target="_blank">$2</a>'
		);
		return preg_replace($search, $replace, $string);
	}
	 
	function percent($num_amount, $num_total){
		$count = ($num_amount/$num_total)*100;
		return number_format($count,0);
	}

	function get_country($id){
		global $db;
		$id = $db->EscapeString($id);
		$country = $db->QueryFetchArray("SELECT country FROM `list_countries` WHERE `id`='".$id."' LIMIT 1");
		return $country['country'];
	}

	function get_gender($id, $man='Man', $woman='Woman', $unknow='Unknown'){
		$gender = ($id == 1 ? $man : ($id == 2 ? $woman : $unknow));
		return $gender;
	}

	function round_up($value, $precision) { 
		$pow = pow(10, $precision); 
		return (ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value))) / $pow; 
	}
	
	// Detect visitor country
	function detectCountry($ip, $cloudflare = true)
	{
		if ($cloudflare && !empty($_SERVER["HTTP_CF_IPCOUNTRY"])) {
			if (!in_array($_SERVER["HTTP_CF_IPCOUNTRY"], ['XX', 'T1'])) {
				return $_SERVER["HTTP_CF_IPCOUNTRY"];
			}
		}

		try {
			$reader = new Reader(BASE_PATH.'/system/libs/geoip2/maxmind-db/GeoLite2-Country.mmdb');
			$record = $reader->get($ip);
			$reader->close();
			$countryCode = (trim($record['country']['iso_code'])) ? $record['country']['iso_code'] : 'N/A';
		} catch (\Exception $ex) {
			$countryCode = 'N/A';
		}

		return $countryCode;
	}
	
	// Get website achivements
	function getAchievement($achievement, $requirements, $reward, $reward_type, $membership = 'Membership Days'){
		
		$requirements = number_format($requirements);
		$reward_type = ($reward_type == 0 ? 'Bits' : ($reward_type == 1 ? $membership : 'Satoshi'));
		$achievements = array(
				0 => array('name' => 'Faucet claims', 'requirement' => $requirements.' faucet claims', 'reward' => $reward.' '.$reward_type),
				1 => array('name' => 'Shortlinks visits', 'requirement' => $requirements.' shortlink visits', 'reward' => $reward.' '.$reward_type),
				2 => array('name' => 'Offerwalls leads', 'requirement' => $requirements.' offerwalls leads', 'reward' => $reward.' '.$reward_type),
				3 => array('name' => 'Referrals', 'requirement' => $requirements.' referrals', 'reward' => $reward.' '.$reward_type)
			);

		return $achievements[$achievement];
	}
	
	// Get website currency
	function getCurrency($type = 'stock'){
		global $config;

		$currencies = array(
				'BTC' => array('name' => 'Bitcoin', 'stock' => 'BTC', 'symbol' => '&#8383;', 'icon' => '<i class="fa fa-btc"></i>', 'icon_class' => 'fa fa-btc'),
				'ETH' => array('name' => 'Ethereum', 'stock' => 'ETH', 'symbol' => 'ETH', 'icon' => '<i class="fab fa-ethereum"></i>', 'icon_class' => 'fab fa-ethereum'),
				'LTC' => array('name' => 'Litecoin', 'stock' => 'LTC', 'symbol' => 'Ł', 'icon' => 'Ł', 'icon_class' => 'fas fa-coins'),
				'BCH' => array('name' => 'Bitcoin Cash', 'stock' => 'BCH', 'symbol' => '&#8383;', 'icon' => '&#8383;', 'icon_class' => 'fab fa-bitcoin'),
				'Doge' => array('name' => 'Dogecoin', 'stock' => 'DOGE', 'symbol' => 'Ð', 'icon' => 'Ð', 'icon_class' => 'fas fa-coins'),
				'Dash' => array('name' => 'Dash', 'stock' => 'DASH', 'symbol' => 'Ð', 'icon' => 'Ð', 'icon_class' => 'fab fa-dyalog'),
				'DGB' => array('name' => 'DigiByte', 'stock' => 'DGB', 'symbol' => 'Ð', 'icon' => 'Ð', 'icon_class' => 'fab fa-coins'),
				'TRX' => array('name' => 'Tron', 'stock' => 'TRX', 'symbol' => 'TRX', 'icon' => 'TRX', 'icon_class' => 'fab fa-coins')
			);

		return $currencies[$config['currency_code']][$type];
	}

	// Get payment method info
	function paymentMethod($method, $type = 0) {
		// 0 = Name, 1 = URL, 2 = Transaction proof
		
		$array = array(
				0 => array('FaucetHub', 'https://faucethub.io/balance/', true),
				1 => array(getCurrency('name').' Wallet', 'https://www.blockchain.com/btc/address/', true),
				2 => array('KSWallet', 'https://kswallet.net/', false),
				3 => array('FaucetPay', 'https://faucetpay.io/?r=2233', false)
			);

		return (empty($array[$method][$type]) ? 'N/A' : $array[$method][$type]);
	}

	// Calculate remaining time
	function remainingTime($seconds) {
		$measures = array(
			'day'=>24*60*60,
			'hour'=>60*60,
			'minute'=>60,
			'second'=>1,
		);
		foreach ($measures as $label=>$amount) {
			if ($seconds >= $amount) {  
				$howMany = floor($seconds / $amount);
				return $howMany." ".$label.($howMany > 1 ? "s" : "");
			}
		} 
	}

	// Language
	function lang_rep($text, $inputs = array()){
		if (empty($inputs) || !is_array($inputs)) return $text;
				
		foreach ($inputs as $search => $replace){
			$text = str_replace($search, $replace, $text);
		}

		return $text;
	}

	// User Level System
	function userLevel($uid, $type = 1, $claims = 0){
		global $db;

		$level = $db->QueryFetchArray("SELECT * FROM `levels` WHERE `requirements`<='".$claims."' ORDER BY `requirements` DESC LIMIT 1");
		
		if($type == 1){
			return $level['level'];
		}elseif($type == 2){
			return $level['image'];
		}elseif($type == 3){
			return $level['reward'];
		}elseif($type == 4){
			$next_level = $db->QueryFetchArray("SELECT `requirements` FROM `levels` WHERE `level`>'".$level['level']."' ORDER BY `level` ASC LIMIT 1");
			
			$result = array();
			$result['level'] = $level['level'];
			$result['image'] = $level['image'];
			$result['reward'] = $level['reward'];
			$result['progress'] = percent($claims, $next_level['requirements']);
			$result['next_level'] = (int)($level['level']+1);
			$result['remaining_claims'] = (int)($next_level['requirements']-$claims);

			return $result;
		}else{
			return $level;
		}
	}

	// Notifications
	function add_notification($user_id, $notify, $value = 0) {
		global $db;
		
		if(!empty($user_id) && !empty($notify)){
			$db->Query("INSERT INTO `notifications` (`user_id`,`notify_id`,`value`,`time`) VALUES ('".$user_id."','".$notify."','".$value."','".time()."')");
		}
	}

	function get_notification($notify, $value, $time, $read = 1, $type = 'none') {
		$message = '';
		$time = date('d M Y - H:i', $time);
		switch($notify) {
			case 1:
				$message = '<tr><td class="bg-success notify-icon"><i class="fa fa-level-up fa-2x fa-fw notify-icon-fa"></i></td><td>Congratulations, you reached <strong>level '.$value.'</strong>.<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 2:
				$message = '<tr><td class="bg-info notify-icon"><i class="fa fa-user-plus fa-2x fa-fw notify-icon-fa"></i></td><td>Congratulations, you have a new referral! Referral ID: <strong>#'.$value.'</strong><br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 3:
				$message = '<tr><td class="bg-primary notify-icon"><i class="fa fa-trophy fa-2x fa-fw notify-icon-fa"></i></td><td>Congratulations, you have won <strong>'.$value.' Bits</strong> from <i>Referrals Contest</i>!<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 4:
				$message = '<tr><td class="bg-primary notify-icon"><i class="fa fa-users fa-2x fa-fw notify-icon-fa"></i></td><td>Congratulations, you have won <strong>'.$value.' Bits</strong> from <i>Offerwalls Contest</i>!<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 5:
				$message = '<tr><td class="bg-warning notify-icon"><i class="'.getCurrency('icon_class').' fa-2x fa-fw notify-icon-fa"></i></td><td>You successfully deposited <strong>'.$value.' '.getCurrency().'</strong> into <i>Purchase Balance</i>. Thank you!<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 6:
				$message = '<tr><td class="bg-primary notify-icon"><i class="fa fa-users fa-2x fa-fw notify-icon-fa"></i></td><td>Congratulations, you have won <strong>'.$value.' Bits</strong> from <i>Shortlinks Contest</i>!<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 7:
				$message = '<tr><td class="bg-success notify-icon"><i class="'.getCurrency('icon_class').' fa-2x fa-fw notify-icon-fa"></i></td><td>Your payment request <strong>#'.$value.'</strong> was completed. Funds are now available into your wallet!<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
			case 8:
				$message = '<tr><td class="bg-warning notify-icon"><i class="fa fa-star fa-2x fa-fw notify-icon-fa"></i></td><td><b>Congratulations!</b> You won <strong>'.number_format($value, 2).' Bits</strong> from our lottery!<br /><span class="small text-muted">'.$time.($read == 0 ? ' <span class="badge badge-info">New</span>' : '').'</span></td></tr>';

				break;
		}
		
		return $message;
	}
	
	// Referral Commissions
	function ref_commission($user, $referral, $commission, $date = 0) {
		global $db;
		
		$date = (empty($date) ? time() : $date);
		
		if(!empty($user) && !empty($referral) && !empty($commission)){
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$commission."', `total_revenue`=`total_revenue`+'".$commission."' WHERE `id`='".$user."'");
			$db->Query("INSERT INTO `ref_commissions` (`user`,`referral`,`commission`,`date`) VALUES ('".$user."','".$referral."','".$commission."','".$date."') ON DUPLICATE KEY UPDATE `commission`=`commission`+'".$commission."', `date`='".$date."'");
		}
	}

	// Unique Key
	function GenerateKey($n = 10, $specialChars = false, $numbers = true)
	{
		$key = '';
		$pattern = 'abcdefghijklmnopqrstuvwxyz';
		if($numbers)
		{
			$pattern .= '0123456789';
		}

		if($specialChars){
			$pattern .= '!@#$%^&*()=+';
		}

		$counter = strlen($pattern)-1;
		for($i=0; $i<$n; $i++)
		{
			$key .= $pattern{mt_rand(0,$counter)};
		}

		return $key;
	}
	
	// Detect Proxy
	function detectProxy($ip_address) 
	{
		global $config;
		
		// Define default result
		$ip_result = array();
		$ip_result['status'] = 99;
		$ip_result['country'] = 'N/A';
		
		// Check with proxycheck.io
		if($config['proxycheck_status'] == 1)
		{
			$result = get_data('http://proxycheck.io/v2/'.$ip_address.'?key='.$config['proxycheck'].'&vpn=1&asn=1&time=1&inf=1&risk=1&tag=UserCheck', 5);
			$result = json_decode($result, true);
			
			if(!empty($result['status']) && $result['status'] == 'ok')
			{
				$ip_result['status'] = ($result[$ip_address]['proxy'] == 'yes' ? 1 : 0);
				$ip_result['country'] = (empty($result[$ip_address]['isocode']) ? 'N/A' : $result[$ip_address]['isocode']);
			}
		}

		return $ip_result;
	}

	// Security Tokens
	function GenToken()
	{
		if (function_exists('mcrypt_create_iv')) {
			$token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		} else {
			$token = bin2hex(openssl_random_pseudo_bytes(32));
		}

		return $token;
	}

	function GenGlobalToken()
	{
		$_SESSION['token'] = GenToken();
		return $_SESSION['token'];
	}

	function GenPTCToken()
	{
		$_SESSION['ptc_token'] = GenToken();
		return $_SESSION['ptc_token'];
	}

	function GenLoginToken()
	{
		$_SESSION['authentication_key'] = GenToken();
		return $_SESSION['authentication_key'];
	}

	function GenRegisterToken()
	{
		$_SESSION['registration_key'] = GenToken();
		return $_SESSION['registration_key'];
	}

	// License
	function decodeLicense($license) {
		$license = base64_decode($license);
		$license = explode('(||)', $license);
		
		$result = array();
		$result['date'] = base64_decode($license[0]);
		$result['hash_key'] = base64_decode($license[1]);
		$result['order_id'] = base64_decode($license[2]);
		$result['domain'] = base64_decode($license[3]);
		
		return $result;
	}
	
	// Encrypt Password
	function securePassword($pass)
	{
		$hash = md5(md5(sha1($pass).sha1(md5($pass))));

		return $hash;
	}

	// Generate URL
	function GenerateURL($page, $full = false) {
		global $config;

		$url = '?page='.$page;
		if($config['mod_rewrite'] == 1)
		{
			parse_str($url, $url_array);
			
			$url = '';
			foreach($url_array as $var)
			{
				$url .= '/'.$var;
			}
			$url = $url.'.html';

			if($full == true) {
				$url = $config['secure_url'].$url;
			}
		}
		elseif($full == true)
		{
			$url = $config['secure_url'].'/'.$url;
		}
		
		return $url;
	}
?>