<?php
/* IF YOU ADD DATABASE SETTINGS MANUALLY, DELETE CODE FROM BELOW */
if(!defined('INSTALL')){
	header("Location: /install");
}
/* -------------------------------------------------------------- */

$config['sql_host']       = 'localhost';
$config['sql_username']   = 'user';			// Database Username
$config['sql_password']   = '';				// Database Password
$config['sql_database']   = '';				// The database
$config['sql_extenstion'] = 'MySQL';		// MySQL or MySQLi
?>