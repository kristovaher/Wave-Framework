<?php

/*
WWW Framework
Filesystem cleaner

This script is used to clear WWW system generated cache. It should be used for debugging purposes 
during development. This script can be configured by running it with a GET variable. Possible 
values are 'all' (clears everything), 'output' (clears /filesystem/cache/output/), 'images' 
(clears images cache), 'resources' (clears cache of JavaScript and CSS), 'limiter' (clears 
request data of user agent IP's), 'logs' (clears system logs), 'sessions' (clears API session 
tokens), 'tmp' (clears folder from everything that might be stored here). The only folders of 
/filesystem/ this script cannot touch are /userdata/ (meant for various user-uploaded files) 
/keys/ (meant for various keys and certificates for e-payment systems) and /data/ (meant for 
databases, like SQLite).

* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

// Main configuration file is included
$config=parse_ini_file('..'.DIRECTORY_SEPARATOR.'config.ini');

// Error reporting is turned off in this script
error_reporting(0);

// Authentication is always required, all developer tools ignore the http-authentication flag in configuration file
if(!isset($config['http-authentication-username']) || !isset($config['http-authentication-password']) || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$config['http-authentication-username'] || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$config['http-authentication-password']){
	header('WWW-Authenticate: Basic realm="'.$_SERVER['HTTP_HOST'].'"');
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
		<title>Cleaner</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width"/> 
		<link type="text/css" href="style.css" rel="stylesheet" media="all"/>
		<link rel="icon" href="../favicon.ico" type="image/x-icon"/>
		<link rel="icon" href="../favicon.ico" type="image/vnd.microsoft.icon"/>
	</head>
	<body>
		<?php

		// If cleaner has no GET variables sent, then error is thrown
		if(!isset($_GET) || empty($_GET)){
			echo '<p class="bold">Cleaner mode is not defined as a GET variable</p>';
		} else {

			// Header
			echo '<h1>Filesystem cleaner</h1>';
			echo '<h4 class="highlight">';
			foreach($softwareVersions as $software=>$version){
				// Adding version numbers
				echo '<b>'.$software.'</b> ('.$version.') ';
			}
			echo '</h4>';
			
			echo '<h2>Log</h2>';

			// Log will be stored in this array
			$log=array();

			// Clears /filesystem/cache/output/
			if(isset($_GET['all']) || isset($_GET['output'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears images cache
			if(isset($_GET['all']) || isset($_GET['images'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears cache of JavaScript and CSS
			if(isset($_GET['all']) || isset($_GET['resources'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears cache of JavaScript and CSS
			if(isset($_GET['all']) || isset($_GET['messenger'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears request data of user agent IP's
			if(isset($_GET['all']) || isset($_GET['limiter'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears request data of user agent IP's
			if(isset($_GET['all']) || isset($_GET['errors'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears system log
			if(isset($_GET['all']) || isset($_GET['logs'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears API session tokens
			if(isset($_GET['all']) || isset($_GET['sessions'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Clears folder from everything that might be stored here
			if(isset($_GET['all']) || isset($_GET['tmp'])){
				$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
				$log=array_merge($log,dirCleaner($directory));
			}

			// Printing out log, if it is not empty
			if(!empty($log)){
				// Log is returned in plain text format, every log entry in a separate line
				echo '<p>';
				echo implode('</p><p>',$log);
				echo '</p>';
			} else {
				echo '<p class="box bold">Nothing to clean</p>';
			}
		
		}
		
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>