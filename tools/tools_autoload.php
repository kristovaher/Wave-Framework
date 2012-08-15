<?php

/*
Wave Framework
Maintenance tools autoloader

This script includes authentication, timezone detection and software version detection and 
other functionality that is useful for developer tools scripts.

* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

// Main configuration file is included
$config=parse_ini_file('..'.DIRECTORY_SEPARATOR.'config.ini');

// Configuration is required
if($config){

	// Error reporting is turned off in this script
	error_reporting(0);

	// Required timezone setting
	if(!isset($config['timezone'])){
		// Setting GMT as the default timezone
		$config['timezone']='Europe/London';
	}
	// Setting the timezone
	date_default_timezone_set($config['timezone']);

	// Requiring some maintenance functions
	require('.'.DIRECTORY_SEPARATOR.'tools_functions.php');

	// Required HTTP authentication configuration settings
	if(!isset($config['trusted-proxies'])){
		$config['trusted-proxies']='*';
	}
	if(!isset($config['http-authentication-ip'])){
		$config['http-authentication-ip']='*';
	}
	
	// Exploding comma-separated lists
	$config['http-authentication-ip']=explode(',',$config['http-authentication-ip']);
	$config['trusted-proxies']=explode(',',$config['trusted-proxies']);
	
	if(!in_array('*',$config['http-authentication-ip']) && !in_array(getTrueIP($config['trusted-proxies']),$config['http-authentication-ip'])){
	
		header('HTTP/1.1 401 Unauthorized');
		// Response to be displayed in browser
		echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 401 Unauthorized</div>';
		echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">YOUR IP IS NOT ALLOWED TO USE THIS SERVICE</div>';
		die();
		
	} elseif(!isset($config['http-authentication-username']) || !isset($config['http-authentication-password']) || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$config['http-authentication-username'] || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$config['http-authentication-password']){
	
		header('WWW-Authenticate: Basic realm="'.$_SERVER['HTTP_HOST'].'"');
		header('HTTP/1.1 401 Unauthorized');
		echo '<h1>HTTP/1.1 401 Unauthorized</h1>';
		echo '<h2>Username and password need to be provided by the user agent</h2>';
		die();
		
	}

	// Default version numbers
	$softwareVersions=array();
	// Getting current version numbers
	$versionsRaw=explode("\n",str_replace("\r",'',file_get_contents('..'.DIRECTORY_SEPARATOR.'.version')));
	foreach($versionsRaw as $ver){
		// Versions are separated by colon in the version file
		$thisVersion=explode(':',$ver);
		$softwareVersions[$thisVersion[0]]=$thisVersion[1];
	}

} else {

	// Configuration file could not be loaded
	echo 'Configuration loading failed!';
	die();
	
}
	
?>