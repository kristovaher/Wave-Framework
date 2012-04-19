<?php

/*
WWW Framework
Log reader for reading performance logs

This file uses /config.php settings for HTTP authentication. This is a simple script that is 
used to read log files stored by WWW_Logger class. It loads a log file defined by specific 
timestamp formatted as Y-m-d-H. By default it displays information about all the requests 
that have happened in the current hour, but if GET variable 'log' is supplied in same 
timestamp format, then that log file data is returned instead.

* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
*/

// Main configuration file is included
require('..'.DIRECTORY_SEPARATOR.'config.php');

// Authentication is always required, all developer tools ignore the http-authentication flag in configuration file
if(!isset($config['http-authentication-username']) || !isset($config['http-authentication-password']) || !isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$config['http-authentication-username'] || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$config['http-authentication-password']){
	header('WWW-Authenticate: Basic realm="Login"');
	header('HTTP/1.1 401 Unauthorized');
	echo '<h1>HTTP/1.1 401 Unauthorized</h1>';
	echo '<h2>Username and password need to be provided by the user agent</h2>';
	die();
}

// Error reporting is turned off in this script
error_reporting(0);

// Checking if logger attempts to read internal log
if(isset($_GET['internal'])){

	// Actual log address
	$logAddress='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'api.log.tmp';

} else {

	// Log reader can access any log file created by the system
	if(isset($_GET['log'])){
		// User agent requested input URL is validated against hostile characters
		$logFileName=preg_replace('/[^A-Za-z\-\_0-9\/]/i','',$_GET['log']);
	} else {
		// By default the results are returned from current hour
		$logFileName=date('Y-m-d-H');
	}
		
	// This stores the array types to print out
	$types=array();

	// You can print out only some log information
	if(isset($_GET['types'])){
		$rawTypes=explode(',',$_GET['types']);
		foreach($rawTypes as $t){
			$types[$t]=true;
		}
	} else {
		$types['all']=true;
	}

	// Every day the logs are stored under different log subfolder
	$logSubfolder=substr($logFileName,0,10);
	
	// Actual log address
	$logAddress='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.$logSubfolder.DIRECTORY_SEPARATOR.$logFileName.'.tmp';

}



// All logs are stored in /log/ folder, if a folder does not exist
if(file_exists($logAddress)){

	// Log is printed out in plain text format
	header('Content-Type: text/html;charset=utf-8');
	// Some basic styles
	echo '<div style="font:12px Verdana;">';
	
		// Log files are stored as JSON serialized arrays, separated with line-breaks
		$log=explode("\n",file_get_contents($logAddress));
		// Printing out every line from the log file
		foreach($log as $l){
			// Output buffer allows to increase peformance due to multiple echo's
			ob_start();
			// Log data is deencoded from JSON string
			$l=json_decode($l,true);
			// Log entry should be an array once decoded
			if(is_array($l)){
				// Printing out log data
				foreach($l as $key=>$entry){
					if(isset($_GET['internal']) || isset($types['all']) || isset($types[$key])){
						if(!is_array($entry)){
							echo '<b>'.$key.':</b> '.$entry.'<br/>';
						} else {
							echo '<b>'.$key.':</b>';
							echo '<pre>';
							print_r($entry);
							echo '</pre>';
						}
					}
				}
				echo '<hr/>';
			} else {
				// If by chance the log line was not an array, then it is simply printed out here
				// echo $l.'<hr/>';
			}
			// Output buffer flushed to user agent
			ob_end_flush(); 
		}
	
	// Closing the div
	echo '</div>';
	
} else {
	// 404 is returned if no log file was found for that date
	header('HTTP/1.1 404 Not Found');
	echo '<h1>HTTP/1.1 404 Not Found</h1>';
	echo '<h2>No log exists for this hour</h2>';
}
	
?>