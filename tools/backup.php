<?php

/**
 * Wave Framework <http://github.com/kristovaher/Wave-Framework>
 * System Backup Archiver
 *
 * This script creates backup of entire system together with filesystem or just a backup of the main core 
 * files. The type of backup depends on GET variable. 'all' creates backup of everything, 'system' creates 
 * backup of just the system files. Files are stored in /filesystem/backups/ folder.
 *
 * @package    Tools
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/guide_tools.htm
 * @since      1.0.0
 * @version    3.6.0
 */

// This initializes tools and authentication
require('.'.DIRECTORY_SEPARATOR.'tools_autoload.php');

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
		<meta content="noindex,nocache,nofollow,noarchive,noimageindex,nosnippet" name="robots"/>
		<meta http-equiv="cache-control" content="no-cache"/>
		<meta http-equiv="pragma" content="no-cache"/>
		<meta http-equiv="expires" content="0"/>
	</head>
	<body>
		<?php
		
		// Pops up an alert about default password
		passwordNotification($config['http-authentication-password']);
		
		// Header
		echo '<h1>System Backup</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		// If backup has no GET variables sent, then error is thrown
		if(empty($_GET)){
		
			echo '<p>Backup mode missing, please define either \'all\' or \'system\' in request</p>';
			
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
		
		echo '<h2>Modes</h2>';
		echo '<p><a href="backup.php?system">System Backup</a> - Creates backup of system files</p>';
		echo '<p><a href="backup.php?all">Full Backup</a> - Creates backup of all files, including filesystem directory</p>';
	
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>