<?php
if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

class MySQLConnection {
	private $sqlHost;
	private $sqlUser;
	private $sqlPassword;
	private $sqlDatabase;
	private $MySQL = FALSE;
	private $numQueries = 0;
	public $UsedTime = 0;

	public function __construct($sqlHost, $sqlUser, $sqlPassword, $sqlDatabase = FALSE) {
		$this->sqlHost = $sqlHost;
		$this->sqlUser = $sqlUser;
		$this->sqlPassword = $sqlPassword;
		$this->sqlDatabase = $sqlDatabase;
	}
	
	public function __destruct() {
		$this->Close();
	}
	
	public function Connect() {
		if($this->MySQL !== FALSE) {
			return $this->MySQL;
		}
		
		$this->MySQL = mysql_connect($this->sqlHost, $this->sqlUser, $this->sqlPassword);
		if($this->MySQL === FALSE) {
			return FALSE;
		}

		if($this->sqlDatabase !== FALSE) {
			mysql_select_db($this->sqlDatabase, $this->MySQL);
		}
		
		return $this->MySQL;
	}

	public function Close() {
		if($this->MySQL !== FALSE) {
			mysql_close($this->MySQL);
			$this->MySQL = FALSE;
		}
	}

	public function GetLinkIdentifier() {
		return $this->MySQL;
	}		

	public function Query($query, $unbuffered = 0, $show_error = false) {
		$start = microtime(true);

		if($unbuffered == 1){
			$query = mysql_unbuffered_query($query, $this->GetLinkIdentifier());
		}else{
			$query = mysql_query($query, $this->GetLinkIdentifier());
		}
		
		$this->UsedTime += microtime(true) - $start;
		$this->numQueries++;
		
		if($show_error && $query === false){
			die($this->GetErrorMessage());
		}
		
		return $query;
	}

	public function FreeResult($result) {
		mysql_free_result($result);
	}

	public function FetchArray($result) {
		$result = mysql_fetch_array($result, MYSQL_ASSOC);
		return $result;
	}

	public function FetchArrayAll($result){
		$retval = array();
		if($this->GetNumRows($result)) {
			while($row = $this->FetchArray($result)) {
				$retval[] = $row;
			}			
		}
		return $retval;
	}	

	public function GetNumRows($result) {
		return mysql_num_rows($result);
	}

	public function QueryGetNumRows($result) {
		$result = $this->Query($result);
		$result = $this->GetNumRows($result);

		return $result;
	}

	public function GetNumAffectedRows() {
		return mysql_affected_rows($this->MySQL);
	}
	
	public function QueryFetchArray($result)
	{
		$result = $this->Query($result, 1);
		$result = $this->FetchArray($result);

		return $result;
	}

	public function QueryFetchArrayAll($result) {
		$result = $this->Query($result, 1);
		if($result === FALSE) {
			return FALSE;
		}
		
		$retval = array();
		while($row = $this->FetchArray($result)) {
			$retval[] = $row;
		}			

		return $retval;
	}
	
	public function QueryFirstRow($result) {
		$result = $this->Query($result);
		if($result === FALSE) {
			return FALSE;
		}
		
		$retval = FALSE;
		
		$row = $this->FetchArray($result);
		if($row !== FALSE) {
			$retval = $row;
		}

		return $retval;		
	}

	public function QueryFirstValue($query) {
		$row = $this->QueryFirstRow($query);
		if($row === FALSE) {
			return FALSE;
		}
		
		return $row[0];			
	}

	public function GetErrorMessage() {
		return "SQL Error: ".mysql_error().": ";
	}

	public function EscapeString($string, $no_html = 1) {
		if (is_array($string))
		{
			$str = array();
			foreach ($string as $key => $value)
			{
				$str[$key] = $this->EscapeString($value);
			}
			
			return $str;
		}
		elseif($no_html == 1)
		{
			$string = htmlspecialchars($string);
		}
		return get_magic_quotes_gpc() ? mysql_real_escape_string(stripslashes($string), $this->MySQL) : mysql_real_escape_string($string, $this->MySQL);
	}
	
	function GetNumberOfQueries() {
		return $this->numQueries;
	}

	public function BeginTransaction() {
		$this->Query("SET AUTOCOMMIT=0");
		$this->Query("BEGIN");
	}

	public function CommitTransaction() {
		$this->Query("COMMIT");
		$this->Query("SET AUTOCOMMIT=1");
	}

	public function RollbackTransaction() {
		$this->Query("ROLLBACK");
		$this->Query("SET AUTOCOMMIT=1");
	}
	
	public function GetFoundRows() {
		return $this->QueryFirstValue("SELECT FOUND_ROWS()");
	}
	
	public function GetLastInsertId() {
		return mysql_insert_id($this->GetLinkIdentifier());	
	}
	
	public function SetNames($type = 'utf8') {
		return $this->Query("SET NAMES '".$type."'");			
	}
}
?>