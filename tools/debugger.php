<?php

/*
WWW Framework
Error message debugger

This is a script that collects error messages that have been written to filesystem. It also provides method 
to easily delete the error log about a specific error message, once it is considered 'fixed'. This script 
should be checked every now and then to test and make sure that there are no outstanding problems in the system.

* Reads files from /filesystem/errors folder

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

// Required timezone setting
if(!isset($config['timezone'])){
	// Setting GMT as the default timezone
	$config['timezone']='Europe/London';
}
// Setting the timezone
date_default_timezone_set($config['timezone']);

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

// Action is based on GET values
if(empty($_GET)){
	// Making sure that the default .empty file is not listed in errors directory
	if(file_exists('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.'.empty')){
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.'.empty');
	}
	$errorLogs=scandir('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR);
	// If it found error messages of any kind
	if(count($errorLogs)>2){
		header('Location: debugger.php?error='.$errorLogs[2]);
		die();
	}
} elseif(isset($_GET['error'],$_GET['done'])){
	// If error is considered to be 'fixed' then it will be removed, if it exists
	if(file_exists('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$_GET['error'])){
		unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$_GET['error']);
	}
	// User will be redirected back to initial site
	header('Location: debugger.php');
	die();
} elseif(isset($_GET['error'])){
	// If error is found
	if(!file_exists('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$_GET['error'])){
		// User will be redirected back to initial site
		header('Location: debugger.php');
		die();
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
	</head>
	<body>
		<?php
		
		// Header
		echo '<h1>Debugger</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		// Subheader
		echo '<h2>Logged errors</h2>';
		
		// Action is based on GET values
		if(empty($_GET)){
			echo '<p class="bold">There are no outstanding errors</p>';
		} elseif(isset($_GET['error'])){
			echo '<h3 onclick="if(confirm(\'Are you sure?\')){ document.location.href=document.location.href+\'&done\'; }" class="red bold" style="cursor:pointer;">Click to delete this error log</h3>';
			// Getting error data
			$errorData=file_get_contents('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$_GET['error']);
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
		}
		
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>