<?php

/*
WWW - PHP micro-framework
System backup creator

This script creates backup of entire system together with filesystem or just a backup of the main core 
files. The type of backup depends on GET variable. 'all' creates backup of everything, 'system' creates 
backup of just the system files. Files are stored in /filesystem/backups/ folder.

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

// If backup has no GET variables sent, then error is thrown
if(!isset($_GET) || empty($_GET)){
	header('HTTP/1.1 501 Not Implemented');
	echo '<h1>HTTP/1.1 501 Not Implemented</h1>';
	echo '<h2>Backup mode is not defined as a GET variable</h2>';
	die();
}

// Log is returned in plain text
header('Content-Type: text/plain;charset=utf-8');

// Target archive of the backup
$backupFilename='system-backup-'.date('Y-m-d-H-i-s').'.zip.tmp';

// Creates backup based on backup mode
if(isset($_GET['all'])){

	// This calls the backup function with filesystem flag set to 'true', so it creates archive with filesystem files
	if(systemBackup('../','..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$backupFilename,true)){
		echo 'Backup file /filesystem/backups/'.$backupFilename.' created';
	} else {
		echo 'Cannot create backup!';
	}
	
} elseif(isset($_GET['system'])){

	// This calls the backup function with the filesystem flag undefined (thus 'false')
	if(systemBackup('../','..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$backupFilename)){
		echo 'Backup file /filesystem/backups/'.$backupFilename.' created';
	} else {
		echo 'Cannot create backup!';
	}
	
} else {
	echo 'Incorrect backup mode used';
}
	
?>