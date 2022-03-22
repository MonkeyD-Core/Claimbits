<?php
/* 
	KSWallet PHP Library
	
	CHANGELOG:
	v1.0: - First version released

	This library was created by MN-Shop.com
*/
class KSWallet
{
    protected $api_key;
    protected $currency;
    protected $timeout;
    protected $api_base = 'https://www.kswallet.net/api/';

    public function __construct($api_key, $currency = 'BTC', $disable_curl = false, $verify_peer = true, $timeout = null) {
        $this->api_key = $api_key;
        $this->currency = $currency;
        $this->disable_curl = $disable_curl;
        $this->verify_peer = $verify_peer;
        $this->curl_warning = false;
        $this->setTimeout($timeout);
    }

    public function setTimeout($timeout) {
        if($timeout === null) {
            $socket_timeout = ini_get('default_socket_timeout'); 
            $script_timeout = ini_get('max_execution_time');
            $timeout = min($script_timeout / 2, $socket_timeout);
        }
        $this->timeout = $timeout;
     }

    public function __execPHP($method, $params = array(), $request = 'POST') {
        $params = array_merge($params, array("api_key" => $this->api_key, "c" => $this->currency));
        $opts = array(
            "http" => array(
                "method" => $request,
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "content" => http_build_query($params),
                "timeout" => $this->timeout,
            ),
            "ssl" => array(
                "verify_peer" => $this->verify_peer
            )
        );
        $ctx = stream_context_create($opts);
        $fp = fopen($this->api_base . $method, 'rb', null, $ctx);
        
        if (!$fp) {
            return json_encode(array(
                'status' => 503,
                'message' => 'Connection to KSWallet failed, please try again later',
            ), TRUE);
        }
        
        $response = stream_get_contents($fp);
        if($response && !$this->disable_curl) {
            $this->curl_warning = true;
        }
        fclose($fp);
        return $response;
    }

    public function __exec($method, $params = array(), $request = 'POST') {
        if($this->disable_curl) {
            $response = $this->__execPHP($method, $params, $request);
        } else {
            $response = $this->__execCURL($method, $params, $request);
        }
        $response = json_decode($response, true);
        if(!$response) {
            $response = array(
                'status' => 502,
                'message' => 'Invalid response',
            );
        }
        return $response;
    }

    public function __execCURL($method, $params = array(), $request = 'POST') {
        $params = array_merge($params, array("api_key" => $this->api_key, "c" => $this->currency));
		$exec_URL = $this->api_base . $method . ($request != 'POST' ? '?'.http_build_query($params) : '');

        $ch = curl_init($exec_URL);
		if($request == 'POST')
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_peer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int)$this->timeout);

        $response = curl_exec($ch);
        if(!$response) {
            return json_encode(array(
                'status' => 504,
                'message' => 'Connection error',
            ), TRUE);
        }
        curl_close($ch);

        return $response;
    }

    public function send($to, $amount) {
        $r = $this->__exec('send', array('to' => $to, 'amount' => $amount));
		return array(
			'success' => (array_key_exists('status', $r) && $r['status'] == 200 ? true : false),
			'status' => $r['status'],
			'amount' => $r['amount'],
			'hash' => $r['hash'],
			'response' => json_encode($r)
		);
    }

    public function getBalance() {
        $r = $this->__exec('getbalance', array(), 'GET');
        return $r;
    }
    
    public function checkAddress($address) {
        $r = $this->__exec('checkaddress', array('recieveaddress' => $address), 'GET');
        return $r;
    }
}
?>