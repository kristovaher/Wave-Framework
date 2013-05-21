<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Errors and Warnings Debugger
 *
 * This is a script that collects error messages that have been written to filesystem. It also provides method 
 * to easily delete the error log about a specific error message, once it is considered 'fixed'. This script 
 * should be checked every now and then to test and make sure that there are no outstanding problems in the system.
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

// Debugger actions are based on whether signature and error are set
if(isset($_GET['signature']) && isset($_GET['error'])){

	// Replacing potentially harmful characters
	$signature=preg_replace('[^a-zA-Z0-9]','',$_GET['signature']);
	$error=preg_replace('[^a-zA-Z0-9]','',$_GET['error']);
	$folder='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signature.DIRECTORY_SEPARATOR;
	
	// If the entry is marked for removal
	if(isset($_GET['done'])){
	
		// Making sure that the file exists
		if(file_exists($folder.$error.'.tmp')){
			unlink($folder.$error.'.tmp');
		}
		// Redirecting to signature itself, this finds the next error
		header('Location: debugger.php?signature='.$signature);
		die();
		
	} elseif(isset($_GET['alldone'])){
	
		// Making sure that the folder exists
		if(is_dir($folder)){
			// Cleaning all the files in the folder
			dirCleaner($folder);
			// Removing the directory itself
			rmdir($folder);
		}
	
		// Redirecting to Debugger main menu
		header('Location: debugger.php');
		die();
		
	}
	
} elseif(isset($_GET['signature'])){

	// Replacing potentially harmful characters
	$signature=preg_replace('[^a-zA-Z0-9]','',$_GET['signature']);
	// This is the folder that should store the error messages
	$folder='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signature.DIRECTORY_SEPARATOR;
	// Making sue that the file exists and that there is more than one file in the folder
	if(file_exists($folder.'signature.tmp') && fileCount($folder)>1){
		// Finding the first non signature file in the folder
		$filenames=fileIndex($folder,'filenames');
		foreach($filenames as $file){
			if($file!='signature.tmp'){
				header('Location: debugger.php?signature='.$signature.'&error='.str_replace('.tmp','',$filenames[0]));
				die();
			}
		}
	} else {
		// Otherwise redirect to main Debugger page
		header('Location: debugger.php');
		die();
	}
	
} else {

	// This array stores all the error developer signatures
	$signatures=array();

	// This is the list of all errors based on signatures
	$folders=fileIndex('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR,'folders');
	
	// Looping over each folder that includes errors
	foreach($folders as $f){
		// Making sure that this is a proper file with errors
		if(file_exists($f.'signature.tmp')){
			$fileCount=fileCount($f);
			if($fileCount==1){
				// Removing the error signature and folder, since it is empty
				unlink($f.'signature.tmp');
				rmdir($f);
			} else {
				$signatures[$f]=file_get_contents($f.'signature.tmp');
			}
		}
	}
	
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Debugger</title>
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
		echo '<h1>Debugger</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		if(isset($_GET['signature']) && isset($_GET['error'])){
		
			echo '<h2>Logged Error</h2>';
			
			echo '<h5 onclick="if(confirm(\'Are you sure? This deletes all error log entries!\')){ document.location.href=document.location.href+\'&alldone\'; }" class="red bold" style="cursor:pointer;float:right;">or remove all error entries with this signature</h5>';
			echo '<h3 onclick="if(confirm(\'Are you sure?\')){ document.location.href=document.location.href+\'&done\'; }" class="red bold" style="cursor:pointer;width:400px;">Click to remove this entry</h3>';
				
			
			// Getting error file size
			$filesize=filesize('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$error);
			
			// Attempting to get memory limit
			$memory=ini_get('memory_limit');
			if($memory){
				$memory=iniSettingToBytes($memory);
				// Getting the amount of bytes currently allocated
				$memory=$memory-memory_get_usage();
				// available memory is 'about half' of the actual memory, just in case the array creation takes a lot of memory
				$memory=intval($memory/2);
			}
			
			// Error is only shown if memory limit could not be gotten or is less than the file size
			if(!$memory || $filesize<$memory){
				$errorData=file_get_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signature.DIRECTORY_SEPARATOR.$error.'.tmp');
				$errorData=explode("\n",$errorData);
				foreach($errorData as $nr=>$d){
					if(trim($d)!=''){
						$d=json_decode($d,true);
						// Last element in error log is used as preview of the full error
						$time=array_shift($d);
						$summary=array_pop($d);
						echo '<div class="border block">';
							echo '<b>'.$time.'</b>';
							echo '<pre>';
							print_r($summary);
							echo '</pre>';
							echo '<span class="bold" style="cursor:pointer;" onclick="if(document.getElementById(\'error'.$nr.'\').style.display==\'\'){document.getElementById(\'error'.$nr.'\').style.display=\'none\';} else {document.getElementById(\'error'.$nr.'\').style.display=\'\';}">MORE DETAILS</span>';
							echo '<div id="error'.$nr.'" style="display:none;">';
								echo '<pre>';
								print_r($d);
								echo '</pre>';
							echo '</div>';
						echo '</div>';
					}
				}
				echo '<a href="debugger.php">Back to Debugger menu</a>';
			} else {
				echo '<p class="bold">This error log is too large for PHP to handle. Are you using \'trace-errors\' setting in your configuration file? If so, then it is recommended to turn that off. This will generate smaller error log files.</p>';
			}
		
		} else {
		
			echo '<h3>Available Signatures</h3>';
			echo '<h4>Signature is a pair of IP address and user agent string that generated the error. This helps you keep different log entries separate from one another based on client. Click on signature to see relevant error messages.</h4>';
			
			// If there are error signatures found
			if(!empty($signatures)){
				foreach($signatures as $folder=>$signature){
					if($signature==$_SERVER['REMOTE_ADDR'].' '.$_SERVER['HTTP_USER_AGENT']){
						echo '<a href="debugger.php?signature='.md5($signature).'"><span class="red bold">'.($fileCount-1).'</span> - '.$signature.' <span class="orange">(this matches your signature)</span></a><br/>';
					} else {
						echo '<a href="debugger.php?signature='.md5($signature).'"><span class="red bold">'.($fileCount-1).'</span> - '.$signature.'</a><br/>';
					}
				}
			} else {
				echo '<p class="bold">There are no logged errors</p>';
			}
		
		}
				
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>