<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Cleaner Script for the Server
 *
 * This is a maintenance script template. It works similarly to Cleaner tool, but is intended 
 * to be called by the server in order to automatically clean the filesystem folders. The only 
 * difference is that the log is shown only if the server itself makes the request and the 
 * 'cutoff' value cannot be sent with the request by default. Developer should change this 
 * script accordingly to make it suitable for use in whatever system it is implemented on.
 *
 * @package    Tools
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/guide_tools.htm
 * @since      1.0.0
 * @version    3.2.0
 */
 
 /**
  * CONFIGURATION
  *
  * You can write additional configuration and adjustments here to make the maintenance
  * script work the way you want. By default it accepts values exactly like Cleaner tool
  * does and the only value that is hardcoded is the timestamp value in order to protect
  * the server from potential misuse of the script.
  */
  
	// This is the value for how new files will the Cleaner script keep. If the last-modified
	// timestamp on the file is older than this value, then the file gets deleted. The default 
	// value for cutoff is 2592000 seconds (30 days)
	$cutoff=$_SERVER['REQUEST_TIME']-2592000;

	// This is the mode that is used, it is a comma-separated list of what '/filesystem/' folders
	// to clean. Here is a list of all possible modes: output, images, resources, custom, tags,
	// sessions, messenger, errors, logs, tmp, limiter, tokens, backups, updates, data, userdata,
	// keys, static, 
	$mode='output,images,resources,custom,tags,sessions,messenger,errors,logs,tmp';
  
 /**
  * EXECUTION
  *
  * The below code does not need tweaking unless you wish to add or modify the functionality
  * of the Cleaner script. This script will print out a log, but only if the request is made 
  * from the servers own IP.
  */
 
	// Main configuration file is included
	$config=parse_ini_file('..'.DIRECTORY_SEPARATOR.'config.ini');

	// Configuration is required because of trusted proxies setting
	if($config){
	 
		// Plain header
		header('Content-Type: text/plain;charset=utf-8');

		// Error reporting is turned off in this script
		error_reporting(0);

		// Setting the timezone
		date_default_timezone_set('Europe/London');

		// Requiring some maintenance functions
		require('.'.DIRECTORY_SEPARATOR.'tools_functions.php');
		
		if(!isset($config['trusted-proxies'])){
			$config['trusted-proxies']='*';
		}
		$config['trusted-proxies']=explode(',',$config['trusted-proxies']);

		// Log will be stored in this array
		$log=array();
		
		// Getting the mode keys
		$mode=explode(',',$mode);

		// Clears /filesystem/cache/output/
		if(in_array('output',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears images cache
		if(in_array('images',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(in_array('resources',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(in_array('custom',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'custom'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache tags
		if(in_array('tags',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears user sessions
		if(in_array('sessions',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(in_array('messenger',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears request data of user agent IP's
		if(in_array('errors',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears system log
		if(in_array('logs',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears folder from everything that might be stored here
		if(in_array('tmp',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears request data of user agent IP's
		if(in_array('limiter',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears API session tokens
		if(in_array('tokens',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tokens'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears backups
		if(in_array('backups',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears update archive
		if(in_array('updates',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'updates'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears database folder
		if(in_array('data',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears custom user data folder
		if(in_array('userdata',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears certificate and key folder
		if(in_array('keys',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears static files from filesystem
		if(in_array('static',$mode)){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Printing out log, if it is not empty
		if($_SERVER['SERVER_ADDR']==getTrueIP($config['trusted-proxies'])){
			if(!empty($log)){
				echo implode("\n",$log);
			} else {
				echo 'Did not find files to clean';
			}
		} else {
			echo 'Log is only shown if this script is run by the server';
		}

	}

?>