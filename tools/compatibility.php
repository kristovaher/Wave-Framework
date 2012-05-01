<?php

/*
WWW Framework
Server compatibility script

This script checks if installation is ready for WWW Framework. It checks for PHP version, whether 
Apache mod_rewrite RewriteEngine or Nginx URL rewriting is turned on and whether filesystem can be 
written to.

* PHP Version
* Short Open Tag
* XML extension
* Zlib extension
* APC extension
* cURL extension or allow_url_fopen
* PDO and PDO drivers
* Fileinfo extension
* Mcrypt extension
* Zip extension
* FTP extension
* GD library extension
* Apache, URL rewrites and .htaccess presence
* Nginx and URL rewrites
* Filesystem folder writability

Author and support: Kristo Vaher - kristo@waher.net
*/

// Main configuration file is included
$config=parse_ini_file('..'.DIRECTORY_SEPARATOR.'config.ini');

// Error reporting is turned off in this script
error_reporting(0);

// Authentication is always required, all developer tools ignore the http-authentication flag in configuration file
if(!isset($config['http-authentication-username']) || !isset($config['http-authentication-password']) || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$config['http-authentication-username'] || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$config['http-authentication-password']){
	header('WWW-Authenticate: Basic realm="Login"');
	header('HTTP/1.1 401 Unauthorized');
	echo '<h1>HTTP/1.1 401 Unauthorized</h1>';
	echo '<h2>Username and password need to be provided by the user agent</h2>';
	die();
}

// Requiring some maintenance functions
require('.'.DIRECTORY_SEPARATOR.'functions.php');

// Default version numbers
$softwareVersions=array();
// Getting current version numbers
$versionsRaw=explode("\n",str_replace("\r",'',file_get_contents('..'.DIRECTORY_SEPARATOR.'.version')));
foreach($versionsRaw as $ver){
	// Versions are separated by colon in the version file
	$thisVersion=explode(':',$ver);
	$softwareVersions[$thisVersion[0]]=$thisVersion[1];
}

// Log is printed out in plain text format
header('Content-Type: text/html;charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Compatibility</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width"/> 
		<link type="text/css" href="style.css" rel="stylesheet" media="all"/>
		<link rel="icon" href="../favicon.ico" type="image/x-icon"/>
		<link rel="icon" href="../favicon.ico" type="image/vnd.microsoft.icon"/>
	</head>
	<body>
		<?php
		
		// Header
		echo '<h1>System compatibility</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		echo '<h2>Reference</h2>';
		echo '<div class="border box">';
			echo '<span class="bold">SUCCESS</span> Everything is OK<br/>';
			echo '<span class="bold orange">WARNING</span> Some functionality might not work or is unavailable<br/>';
			echo '<span class="bold red">FAILURE</span> WWW Framework is not meant to work on this system';
		echo '</div>';
		
		echo '<h2>Details</h2>';

		// Messages are stored in this array
		$log=array();

		// PHP VERSION
			$phpVersion=phpversion();
			if($phpVersion){
				if(version_compare($phpVersion,'5.3.0')>=0){
					$log[]='<span class="bold">SUCCESS</span>: PHP is running version 5.3 or newer';
				} else {
					$log[]='<span class="bold red"><span class="bold red">FAILURE</span></span>: PHP is running older version than 5.3, WWW Framework has not been tested on older versions of PHP';
				}
			} else {
				$log[]='<span class="bold orange">WARNING</span>: Unable to detect PHP version number, WWW Framework requires PHP version 5.3 or above';
			}
			
		// SHORT OPEN TAG
			$shortOpenTag=ini_get('short_open_tag');
			if($shortOpenTag && $shortOpenTag==1){
				$log[]='<span class="bold">SUCCESS</span>: PHP setting short_open_tag is enabled';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PHP setting short_open_tag is turned off, default View controller requires this to work properly, if this is not possible then edit /controllers/controller.view.php';
			}
			
		// ZLIB
			if(extension_loaded('Zlib')){
				$log[]='<span class="bold">SUCCESS</span>: Zlib is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: Zlib PHP extension is not supported, this is needed if output compression is used, but system turns it off automatically if extension is not present';
			}
			
		// APC
			if(extension_loaded('apc')){
				$log[]='<span class="bold">SUCCESS</span>: APC is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: APC PHP extension is not supported, this is not required by WWW Framework, but can improve performance, if supported';
			}
			
		// CURL AND URL OPEN
			if(extension_loaded('curl')){
				$log[]='<span class="bold">SUCCESS</span>: cURL is supported';
			} else {
				if(ini_get('allow_url_fopen')==1){
					$log[]='<span class="bold orange">WARNING</span>: cURL PHP extension is not supported, this is not required by WWW Framework, but is useful when making API requests to other systems that include POST data';
				} else {
					$log[]='<span class="bold orange">WARNING</span>: cURL PHP extension is not supported and allow_url_fopen setting is also off, these are not required by WWW Framework, but without them you cannot make API requests to other networks';
				}
			}

		// PDO

			// PDO
			if(extension_loaded('PDO')){
				$log[]='<span class="bold">SUCCESS</span>: PDO is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PDO PHP extension is not supported, this extension is used by default database class, if database connections are not used then this warning can be ignored';
			}
			
			// PDO MYSQL
			if(extension_loaded('pdo_mysql')){
				$log[]='<span class="bold">SUCCESS</span>: PDO MySQL is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PDO MySQL PHP extension is not supported, MySQL connections could not be used, if MySQL is not used then this warning can be ignored';
			}
			
			// PDO SQLITE
			if(extension_loaded('pdo_sqlite')){
				$log[]='<span class="bold">SUCCESS</span>: PDO SQLite is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PDO SQLite PHP extension is not supported, SQLite connections could not be used, if SQLite is not used then this warning can be ignored';
			}
			
			//PDO POSTGRESQL
			if(extension_loaded('pdo_pgsql')){
				$log[]='<span class="bold">SUCCESS</span>: PDO PostgreSQL is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PDO PostgreSQL PHP extension is not supported, PostgreSQL connections could not be used, if PostgreSQL is not used then this warning can be ignored';
			}
			
			//PDO ORACLE
			if(extension_loaded('pdo_oci')){
				$log[]='<span class="bold">SUCCESS</span>: PDO Oracle is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PDO Oracle PHP extension is not supported, Oracle connections could not be used, if Oracle is not used then this warning can be ignored';
			}
			
			//PDO MSSQL
			if(extension_loaded('pdo_mssql')){
				$log[]='<span class="bold">SUCCESS</span>: PDO MSSQL is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: PDO MSSQL PHP extension is not supported, MSSQL connections could not be used, if MSSQL is not used then this warning can be ignored';
			}
			
		// FILEINFO
			if(extension_loaded('fileinfo')){
				$log[]='<span class="bold">SUCCESS</span>: Fileinfo is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: Fileinfo PHP extension is not supported, this is used by File handler, if this is not available then system detects all downloadable files as application/octet-stream';
			}
			
		// MCRYPT
			if(extension_loaded('mcrypt')){
				$log[]='<span class="bold">SUCCESS</span>: Mcrypt is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: Mcrypt PHP extension is not supported, this is optional and used only when API requests are made with www-crypt-input and www-crypt-output requests';
			}
			
		// ZIP
			if(extension_loaded('Zip')){
				$log[]='<span class="bold">SUCCESS</span>: Zip is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: Zip PHP extension is not supported, this is required by automatic update script, this warning can be ignored if update script is not used';
			}
			
		// FTP
			if(extension_loaded('ftp')){
				$log[]='<span class="bold">SUCCESS</span>: FTP is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: FTP PHP extension is not supported, this is required by automatic update script, this warning can be ignored if update script is not used';
			}
			
		// GD LIBRARY
			if(extension_loaded('gd')){
				$log[]='<span class="bold">SUCCESS</span>: GD Graphics Library is supported';
			} else {
				$log[]='<span class="bold orange">WARNING</span>: GD Graphics Library extension is not supported, this is required for dynamically loaded images, this warning can be ignored if dynamic loading is not used';
			}
			
		// APACHE AND NGINX
			if(strpos($_SERVER['SERVER_SOFTWARE'],'Apache')!==false){
				$log[]='<span class="bold">SUCCESS</span>: Apache server is used';
				
				// APACHE URL REWRITES
					if(file_exists('.htaccess')){
						// .htaccess in this directory attempts to rewrite compatibility.php into compatibility.php?mod_rewrite_enabled and if this is <span class="bold">SUCCESS</span>ful then mod_rewrite must work
						if(isset($_GET['rewrite_enabled'])){
							$log[]='<span class="bold">SUCCESS</span>: Apache mod_rewrite extension is supported';
						} else {
							$log[]='<span class="bold">SUCCESS</span>: Apache mod_rewrite extension is not supported, Index gateway and mod_rewrite functionality will not work, this warning can be ignored if Index gateway is not used';
						}
					} else {
						$log[]='<span class="bold orange">WARNING</span>: Cannot test if mod_rewrite and RewriteEngine are enabled, .htaccess file is missing from /tools/ folder, this warning can be ignored if Index gateway is not used';
					}
					
				// HTACCESS
					if(file_exists('..'.DIRECTORY_SEPARATOR.'.htaccess')){
						$log[]='<span class="bold">SUCCESS</span>: .htaccess file is present';
					} else {
						$log[]='<span class="bold orange">WARNING</span>: .htaccess file is missing from root folder, Index gateway and rewrite functionality will not work, this warning can be ignored if Index gateway is not used';
					}
					
			} else if(strpos($_SERVER['SERVER_SOFTWARE'],'nginx')!==false){
				$log[]='<span class="bold">SUCCESS</span>: Nginx server is used';

				// NGINX URL REWRITES
				// This only works if the /nginx.conf location setting for compatibility script is used in Nginx server configuration
				if(isset($_GET['rewrite_enabled'])){
					$log[]='<span class="bold">SUCCESS</span>: Nginx HttpRewriteModule is supported';
				} else {
					$log[]='<span class="bold">SUCCESS</span>: Apache HttpRewriteModule is not supported, Index gateway and rewrite functionality will not work, this warning can be ignored if Index gateway is not used';
				}
				
			} else {
				$log[]='<span class="bold orange">WARNING</span>: Your server is not Apache or Nginx, Index gateway will not work, this warning can be ignored if Index gateway is not used';
			}
			
			
		// FILESYSTEM

			// FILESYSTEM ROOT
			// No files should really be saved in this folder, but it might be necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/ is writable';
				unlink('../filesystem/test.tmp');
			} else {
				$log[]='<span class="bold red"><span class="bold red">FAILURE</span></span>: /filesystem/ is not writable';
			}
			
			// FILESYSTEM CACHE ROOT
			// No files should really be saved in this folder, but it might be necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/cache/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold red"><span class="bold red">FAILURE</span></span>: /filesystem/cache/ is not writable';
			}
			
			// FILESYSTEM IMAGE CACHE
			// All dynamically loaded image cache is stored here
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/cache/images/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold red"><span class="bold red">FAILURE</span></span>: /filesystem/cache/images/ is not writable';
			}
			
			// FILESYSTEM OUTPUT CACHE
			// All API response cache is stored here
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/cache/output/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold red"><span class="bold red">FAILURE</span></span>: /filesystem/cache/output/ is not writable';
			}
			
			// FILESYSTEM RESOURCE CACHE
			// All loaded resources (JavaScript, CSS and so on) in their compressed and/or minified format are cached here
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/cache/resources/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold red"><span class="bold red">FAILURE</span></span>: /filesystem/cache/resources/ is not writable';
			}
			
			// FILESYSTEM MESSENGER
			// All the certificates and encryption keys should be stored here
			// WWW Framework itself does not use this folder and this should be used by developer, if necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/messenger/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/messenger/ is not writable, this warning can be ignored if state messenger is not used';
			}
			
			// FILESYSTEM KEYS
			// All the certificates and encryption keys should be stored here
			// WWW Framework itself does not use this folder and this should be used by developer, if necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/keys/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/keys/ is not writable, this warning can be ignored if API keys and security features are not used';
			}
			
			// FILESYSTEM LIMITER
			// All the limiter data (requests per IP and so on) is stored here and is used by Limiter to check for possible denial of service attacks from IP's
			// Automatically blocked IP's are also stored here
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/limiter/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/limiter/ is not writable, this warning can be ignored if Limiter is not used by Index gateway';
			}
			
			// FILESYSTEM LOG
			// This stores all Logger generated log files
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/logs/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/logs/ is not writable, this warning can be ignored if performance logging is not used by Index gateway';
			}
			
			// FILESYSTEM SESSIONS
			// This stores all API sessions and tokens per API profile
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/sessions/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/sessions/ is not writable, this warning can be ignored if API keys and security features are not used';
			}
			
			// FILESYSTEM TEMPORARY FILES
			// Various temporary files should be stored here
			// WWW Framework itself does not use this folder and this should be used by developer, if necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/tmp/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/tmp/ is not writable, this warning can be ignored if your system does not write anything to that folder';
			}
			
			// FILESYSTEM USERDATA
			// Various user uploaded files should be stored here
			// WWW Framework itself does not use this folder and this should be used by developer, if necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/userdata/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/userdata/ is not writable, this warning can be ignored if your system does not write anything to that folder';
			}
			
			// FILESYSTEM DATA
			// Various databases (like SQLite) should be stored here
			// WWW Framework itself does not use this folder and this should be used by developer, if necessary
			if(file_put_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'test.tmp','1')){
				$log[]='<span class="bold">SUCCESS</span>: /filesystem/data/ is writable';
				unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'test.tmp');
			} else {
				$log[]='<span class="bold orange">WARNING</span>: /filesystem/data/ is not writable, this warning can be ignored if your system does not write anything to that folder';
			}

		// Printing out the log
		echo '<p>';
		echo implode('</p><p>',$log);
		echo '</p>';

		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>