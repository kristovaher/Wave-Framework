<?php

/*
WWW - PHP micro-framework
Filesystem cleaner

This script is used to clear WWW system generated cache. It should be used for debugging
purposes during development. This script can be configured by running it with a GET variable. 
Possible values are 'all' (clears everything), 'output' (clears /filesystem/cache/output/), 
'images' (clears images cache), 'resources' (clears cache of JavaScript and CSS), 
'limiter' (clears request data of client IP's), 'log' (clears system log), 'sessions' 
(clears API session tokens), 'tmp' (clears folder from everything that might be stored 
here). The only folders of /filesystem/ this script cannot touch are /userdata/ (meant 
for various user-uploaded files) /keys/ (meant for various keys and certificates for 
e-payment systems) and /data/ (meant for databases, like SQLite).

Author and support: Kristo Vaher - kristo@waher.net
*/

// Main configuration file is included
require('..'.DIRECTORY_SEPARATOR.'config.php');

// Authentication is always required, all developer tools ignore the http-authentication flag in configuration file
if(!isset($config['http-authentication-username']) || !isset($config['http-authentication-password']) || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$config['http-authentication-username'] || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$config['http-authentication-password']){
	header('WWW-Authenticate: Basic realm="Login"');
	header('HTTP/1.1 401 Unauthorized');
	echo '<h1>HTTP/1.1 401 Unauthorized</h1>';
	echo '<h2>Username and password need to be provided by the client</h2>';
	die();
}

// Error reporting is turned off in this script
error_reporting(0);

// Requiring some maintenance functions
require('.'.DIRECTORY_SEPARATOR.'functions.php');

// If cleaner has no GET variables sent, then error is thrown
if(!isset($_GET) || empty($_GET)){
	header('HTTP/1.1 501 Not Implemented');
	echo '<h1>HTTP/1.1 501 Not Implemented</h1>';
	echo '<h2>Cleaner mode is not defined as a GET variable</h2>';
	die();
}

// Log is returned in plain text
header('Content-Type: text/plain;charset=utf-8');

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

// Clears request data of client IP's
if(isset($_GET['all']) || isset($_GET['limiter'])){
	$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR;
	$log=array_merge($log,dirCleaner($directory));
}

// Clears system log
if(isset($_GET['all']) || isset($_GET['log'])){
	$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;
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

// Log is returned in plain text format, every log entry in a separate line
echo implode("\n",$log);
	
?>