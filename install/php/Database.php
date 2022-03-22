<?php 
$config['sql_host']       = '{HOSTNAME}';
$config['sql_username']   = '{USERNAME}';			// Database Username
$config['sql_password']   = '{PASSWORD}';			// Database Password
$config['sql_database']   = '{DATABASE}';				// The database
$config['sql_extenstion'] = (version_compare(phpversion(), '5.5', '<') ? 'MySQL' : 'MySQLi');			// MySQL or MySQLi
?>
