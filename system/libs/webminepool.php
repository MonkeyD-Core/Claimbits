<?php
class WMP {
    private $private_key;
    private $base_url;

    public function __construct($private_key){
        $this->base_url="https://webminepool.com/api/";
        $this->private_key=$private_key;
    }

    public function set_token($hashes_amount, $username=''){
        return $this->run("/set_token/$hashes_amount/$username");
    }
    
    public function get_token($token_id, $unset=''){
        return $this->run("/get_token/$token_id/$unset");
    }
    
    public function users(){
        return $this->run("/users");
    }
    
    public function create_user($username, $referral = ''){
        return $this->run("/create_user/$username/$referral");
    }
    public function delete_user($username){
        return $this->run("/delete_user/$username");
    }
    
    public function user_hashes($username){
        return $this->run("/user_hashes/$username");
    }
    
    public function withdraw($username,$amount){
        return $this->run("/withdraw/$username/$amount");
    }
    
    public function reset_user_hashes($username){
        return $this->run("/reset_user_hashes/$username");
    }
    
    public function reset_all_user_hashes(){
        return $this->run("/reset_all_user_hashes");
    }
    
    public function balance(){
        return $this->run("/balance");
    }
    
    public function hashes(){
        return $this->run("/hashes");
    }
    
    public function wmc_rate($amount=1){
        return $this->run("/wmc_rate/$amount");
    }
    
    public function hash_rate($amount=1){
        return $this->run("/hash_rate/$amount");
    }
    
    private function run($url){
        return json_decode(file_get_contents($this->base_url.$this->private_key.$url));
    }
}
?>