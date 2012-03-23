<?php

/*
WWW - PHP micro-framework
Server compatibility script

This script checks if installation is ready for WWW Framework. It checks for PHP version, 
whether Apache and mod_rewrite RewriteEngine is turned on and whether filesystem can be 
written to.

Author and support: Kristo Vaher - kristo@waher.net
*/

// Compatibility returns data in text format
header('Content-Type: text/plain;charset=utf-8');

// Errors are turned off for this script, as it displays custom errors
error_reporting(0);

// Messages are stored in this array
$log=array();

// Log headers
$log[]='SYSTEM INFORMATION:';
$log[]='';

// PHP VERSION
	$phpVersion=explode('.',phpversion());
	if($phpVersion[0]>=5 && $phpVersion[1]>=3){
		$log[]='SUCCESS: PHP is running version 5.3 or newer';
	} else {
		$log[]='FAILURE: PHP is running older version than 5.3, WWW Framework has not been tested on older versions of PHP';
	}
	
// SHORT OPEN TAG
	$shortOpenTag=ini_get('short_open_tag');
	if($shortOpenTag && $shortOpenTag==1){
		$log[]='SUCCESS: PHP setting short_open_tag is enabled';
	} else {
		$log[]='WARNING: PHP setting short_open_tag is turned off, default View controller requires this to work properly, if this is not possible then edit /controllers/class.view.php';
	}
	
// XML
	if(extension_loaded('SimpleXML')){
		$log[]='SUCCESS: SimpleXML is supported';
	} else {
		$log[]='WARNING: SimpleXML PHP extension is not supported, this is needed if API returns XML-formatted data, if XML is not used then this warning can be ignored';
	}
	
// COMPRESSIONS
	if(extension_loaded('Zlib')){
		$log[]='SUCCESS: Zlib is supported';
	} else {
		$log[]='WARNING: Zlib PHP extension is not supported, this is needed if output compression is used, but system turns it off automatically if extension is not present';
	}
	
// APC
	if(extension_loaded('apc')){
		$log[]='SUCCESS: APC is supported';
	} else {
		$log[]='WARNING: APC PHP extension is not supported, this is not required by WWW Framework, but can improve performance, if supported';
	}

// PDO

	// PDO
	if(extension_loaded('PDO')){
		$log[]='SUCCESS: PDO is supported';
	} else {
		$log[]='WARNING: PDO PHP extension is not supported, this extension is used by default database class, if database connections are not used then this warning can be ignored';
	}
	
	// PDO MYSQL
	if(extension_loaded('pdo_mysql')){
		$log[]='SUCCESS: PDO MySQL is supported';
	} else {
		$log[]='WARNING: PDO MySQL PHP extension is not supported, MySQL connections could not be used, if MySQL is not used then this warning can be ignored';
	}
	
	// PDO SQLITE
	if(extension_loaded('pdo_sqlite')){
		$log[]='SUCCESS: PDO SQLite is supported';
	} else {
		$log[]='WARNING: PDO SQLite PHP extension is not supported, SQLite connections could not be used, if SQLite is not used then this warning can be ignored';
	}
	
	//PDO POSTGRESQL
	if(extension_loaded('pdo_pgsql')){
		$log[]='SUCCESS: PDO PostgreSQL is supported';
	} else {
		$log[]='WARNING: PDO PostgreSQL PHP extension is not supported, PostgreSQL connections could not be used, if PostgreSQL is not used then this warning can be ignored';
	}
	
	//PDO ORACLE
	if(extension_loaded('pdo_oci')){
		$log[]='SUCCESS: PDO Oracle is supported';
	} else {
		$log[]='WARNING: PDO Oracle PHP extension is not supported, Oracle connections could not be used, if Oracle is not used then this warning can be ignored';
	}
	
	//PDO MSSQL
	if(extension_loaded('pdo_mssql')){
		$log[]='SUCCESS: PDO MSSQL is supported';
	} else {
		$log[]='WARNING: PDO MSSQL PHP extension is not supported, MSSQL connections could not be used, if MSSQL is not used then this warning can be ignored';
	}
	
// FILEINFO
	if(extension_loaded('fileinfo')){
		$log[]='SUCCESS: Fileinfo is supported';
	} else {
		$log[]='WARNING: Fileinfo PHP extension is not supported, this is used by File handler, if this is not available then system detects all downloadable files as application/octet-stream';
	}
	
// ZIP
	if(extension_loaded('Zip')){
		$log[]='SUCCESS: Zip is supported';
	} else {
		$log[]='WARNING: Zip PHP extension is not supported, this is required by automatic update script, this warning can be ignored if update script is not used';
	}
	
// FTP
	if(extension_loaded('ftp')){
		$log[]='SUCCESS: FTP is supported';
	} else {
		$log[]='WARNING: FTP PHP extension is not supported, this is required by automatic update script, this warning can be ignored if update script is not used';
	}
	
// GD LIBRARY
	if(extension_loaded('gd')){
		$log[]='SUCCESS: GD Graphics Library is supported';
	} else {
		$log[]='WARNING: GD Graphics Library extension is not supported, this is required for dynamically loaded images, this warning can be ignored if dynamic loading is not used';
	}
	
// APACHE
	if(strpos($_SERVER['SERVER_SOFTWARE'],'Apache')!==false){
		$log[]='SUCCESS: Apache server is used';
	} else {
		$log[]='WARNING: Your server is not Apache, Index gateway and mod_rewrite functionality will not work, this warning can be ignored if Index gateway is not used';
	}
	
// MOD_REWRITE
	if(file_exists('.htaccess')){
		// .htaccess in this directory attempts to rewrite compatibility.php into compatibility.php?mod_rewrite_enabled and if this is successful then mod_rewrite must work
		if(isset($_GET['mod_rewrite_enabled'])){
			$log[]='SUCCESS: Apache mod_rewrite extension is supported';
		} else {
			$log[]='SUCCESS: Apache mod_rewrite extension is not supported, Index gateway and mod_rewrite functionality will not work, this warning can be ignored if Index gateway is not used';
		}
	} else {
		$log[]='WARNING: Cannot test if mod_rewrite and RewriteEngine are enabled, .htaccess file is missing from /tools/ folder, this warning can be ignored if Index gateway is not used';
	}
	
// HTACCESS
	if(file_exists('..'.DIRECTORY_SEPARATOR.'.htaccess')){
		$log[]='SUCCESS: .htaccess file is present';
	} else {
		$log[]='WARNING: .htaccess file is missing from root folder, Index gateway and mod_rewrite functionality will not work, this warning can be ignored if Index gateway is not used';
	}
	
	
// FILESYSTEM

	// FILESYSTEM ROOT
	// No files should really be saved in this folder, but it might be necessary
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/ is writable';
		unlink('../filesystem/test.tmp');
	} else {
		$log[]='FAILURE: /filesystem/ is not writable';
	}
	
	// FILESYSTEM CACHE ROOT
	// No files should really be saved in this folder, but it might be necessary
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/cache/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='FAILURE: /filesystem/cache/ is not writable';
	}
	
	// FILESYSTEM IMAGE CACHE
	// All dynamically loaded image cache is stored here
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/cache/images/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='FAILURE: /filesystem/cache/images/ is not writable';
	}
	
	// FILESYSTEM OUTPUT CACHE
	// All API response cache is stored here
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/cache/output/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='FAILURE: /filesystem/cache/output/ is not writable';
	}
	
	// FILESYSTEM RESOURCE CACHE
	// All loaded resources (JavaScript, CSS and so on) in their compressed and/or minified format are cached here
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/cache/resources/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='FAILURE: /filesystem/cache/resources/ is not writable';
	}
	
	// FILESYSTEM KEYS
	// All the certificates and encryption keys should be stored here
	// WWW Framework itself does not use this folder and this should be used by developer, if necessary
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/keys/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/keys/ is not writable, this warning can be ignored if API keys and security features are not used';
	}
	
	// FILESYSTEM LIMITER
	// All the limiter data (requests per IP and so on) is stored here and is used by Limiter to check for possible denial of service attacks from IP's
	// Automatically blocked IP's are also stored here
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/limiter/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/limiter/ is not writable, this warning can be ignored if Limiter is not used by Index gateway';
	}
	
	// FILESYSTEM LOG
	// This stores all Logger generated log files
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/log/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/log/ is not writable, this warning can be ignored if performance logging is not used by Index gateway';
	}
	
	// FILESYSTEM SESSIONS
	// This stores all API sessions and tokens per API profile
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/sessions/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/sessions/ is not writable, this warning can be ignored if API keys and security features are not used';
	}
	
	// FILESYSTEM TEMPORARY FILES
	// Various temporary files should be stored here
	// WWW Framework itself does not use this folder and this should be used by developer, if necessary
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/tmp/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/tmp/ is not writable, this warning can be ignored if your system does not write anything to that folder';
	}
	
	// FILESYSTEM USERDATA
	// Various user uploaded files should be stored here
	// WWW Framework itself does not use this folder and this should be used by developer, if necessary
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/userdata/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/userdata/ is not writable, this warning can be ignored if your system does not write anything to that folder';
	}
	
	// FILESYSTEM DATA
	// Various databases (like SQLite) should be stored here
	// WWW Framework itself does not use this folder and this should be used by developer, if necessary
	if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'test.tmp','1')){
		$log[]='SUCCESS: /filesystem/data/ is writable';
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'test.tmp');
	} else {
		$log[]='WARNING: /filesystem/data/ is not writable, this warning can be ignored if your system does not write anything to that folder';
	}
	
// Printing out glossary in the footer of the compatibility log
$log[]='';
$log[]='Glossary:';
$log[]='SUCCESS = Everything is OK';
$log[]='WARNING = Some functionality might have problems';
$log[]='FAILURE = WWW Framework might not work at all';

// Printing out the log
echo implode("\n",$log);

?>
