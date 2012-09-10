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
 * @version    3.2.1
 */

// This initializes tools and authentication
require('.'.DIRECTORY_SEPARATOR.'tools_autoload.php');

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
	foreach($errorLogs as $error){
		if($error!='.empty' && is_file('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$error)){
			header('Location: debugger.php?error='.$error);
			die();
		}
	}
} elseif(isset($_GET['error'],$_GET['alldone'])){
	$errorLogs=scandir('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR);
	// If it found error messages of any kind
	foreach($errorLogs as $error){
		if(is_file('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$error)){
			unlink('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$error);
		}
	}
	// User will be redirected back to initial site
	header('Location: debugger.php');
	die();
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
		<meta content="noindex,nocache,nofollow,noarchive,noimageindex,nosnippet" name="robots"/>
		<meta http-equiv="cache-control" content="no-cache"/>
		<meta http-equiv="pragma" content="no-cache"/>
		<meta http-equiv="expires" content="0"/>
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
			echo '<h5 onclick="if(confirm(\'Are you sure? This deletes all error log entries!\')){ document.location.href=document.location.href+\'&alldone\'; }" class="red bold" style="cursor:pointer;float:right;">or delete all error logs</h5>';
			echo '<h3 onclick="if(confirm(\'Are you sure?\')){ document.location.href=document.location.href+\'&done\'; }" class="red bold" style="cursor:pointer;width:400px;">Click to delete this error log</h3>';
			
			// Getting error file size
			$filesize=filesize('..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$_GET['error']);
			
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
			} else {
				echo '<p class="bold">This error log is too large for PHP to handle. Are you using error tracing in your configuration file? If so, then it is recommended to turn that setting off as that will generate smaller error log files.</p>';
			}
		}
		
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>