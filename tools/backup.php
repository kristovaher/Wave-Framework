<?php

/*
WWW Framework
System backup creator

This script creates backup of entire system together with filesystem or just a backup of the main core 
files. The type of backup depends on GET variable. 'all' creates backup of everything, 'system' creates 
backup of just the system files. Files are stored in /filesystem/backups/ folder.

* Uses Zip extension to archive the files
* Name of the archive is {Y-m-d-H-i-s}.zip.tmp (tmp forbids access over URL's, since WWW Framework does not allow requesting tmp files)
* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
*/

// Main configuration file is included
require('..'.DIRECTORY_SEPARATOR.'config.php');

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
		<title>Backup</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width"/> 
		<link type="text/css" href="style.css" rel="stylesheet" media="all"/>
		<link rel="icon" href="../favicon.ico" type="image/x-icon"/>
		<link rel="icon" href="../favicon.ico" type="image/vnd.microsoft.icon"/>
	</head>
	<body>
		<?php
		
		// Header
		echo '<h1>System backup</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		// If backup has no GET variables sent, then error is thrown
		if(!isset($_GET) || empty($_GET)){
		
			echo '<p class="bold red">Backup mode missing, please define either \'all\' or \'system\' in request</p>';
			
		} else {

			echo '<h2>Log</h2>';

			// Target archive of the backup
			$backupFilename='system-backup-'.date('Y-m-d-H-i-s').'.zip.tmp';

			// Creates backup based on backup mode
			if(isset($_GET['all'])){

				// This calls the backup function with filesystem flag set to 'true', so it creates archive with filesystem files
				if(systemBackup('../','..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$backupFilename,true)){
					echo '<p class="bold">Backup file /filesystem/backups/'.$backupFilename.' created</p>';
				} else {
					echo '<p class="bold red">Cannot create backup!</p>';
				}
				
			} elseif(isset($_GET['system'])){

				// This calls the backup function with the filesystem flag undefined (thus 'false')
				if(systemBackup('../','..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR.$backupFilename)){
					echo '<p class="bold">Backup file /filesystem/backups/'.$backupFilename.' created</p>';
				} else {
					echo '<p class="bold red">Cannot create backup!</p>';
				}
				
			} else {
				echo '<p class="bold red">Incorrect backup mode used</p>';
			}
		
		}
	
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>