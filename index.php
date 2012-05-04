<?php

/* 
WWW Framework
Index gateway

Index gateway is a file where majority of requests are forwarded by Apache and /.htaccess file. 
This file serves caches and compresses data, if supported, for both static files as well 
as regular views. It also displays errors for files not found or files that are forbidden to 
be accessed. Handlers for Index gateway are stored in /engine/ subfolder.

* Request limiter checks
* Loads state and loggers
* Loads API handler and data handler
* Loads files, resources and images through handlers
* Loads robots.txt and sitemap.xml through handlers

Author and support: Kristo Vaher - kristo@waher.net
*/

// SOLVING THE HTTP REQUEST

	// Custom error-handling and reporting is used
	// Make sure to check for errors with /tools/debugger.php script
	error_reporting(0);

	// Getting resource without GET string
	$tmp=explode('?',$_SERVER['REQUEST_URI']);
	$resourceAddress=array_shift($tmp);

	// Stopping all direct requests to Index gateway
	if($resourceAddress==$_SERVER['SCRIPT_NAME']){
		header('HTTP/1.1 403 Forbidden');
		die();
	}

	// Currently known location of the file in filesystem
	// Double replacement occurs since some environments give document root with the slash in the end, some don't (like Windows)
	$resourceRequest=str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$_SERVER['DOCUMENT_ROOT'].$resourceAddress);
	// Getting directory, filename and extension information about current resource address
	$resourceInfo=pathinfo($resourceRequest);
	// Solving the folder that user agent is loading resource from
	$resourceFolder=$resourceInfo['dirname'].DIRECTORY_SEPARATOR;
	// Assigning file information
	$resourceFile=$resourceInfo['basename'];
	// If extension was detected then this too is used
	if(isset($resourceInfo['extension'])){
		$resourceExtension=$resourceInfo['extension'];
	}
	
// LOADING CONFIGURATION

	// Defining root directory, this is required by handlers in /engine/ subfolder
	define('__ROOT__',__DIR__.DIRECTORY_SEPARATOR);

	//Configuration is stored in this array, it has to be defined even if no configuration is loaded
	$config=array();

	// Including the configuration
	if(!file_exists(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp') || filemtime(__ROOT__.'config.ini')>filemtime(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp')){
		// Configuration is parsed from INI file in the root of the system
		$config=parse_ini_file(__ROOT__.'config.ini');
		// Cache of parsed INI file is stored for later use
		if(!file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp',serialize($config))){
			header('HTTP/1.1 500 Internal Server Error');
			echo '<h1>HTTP/1.1 500 Internal Server Error</h1>';
			echo '<p>Cannot write cache in filesystem, please make sure filesystem folders are writable.</p>';
		}
	} else {
		// Since INI file has not been changed, configuration is loaded from cache
		$config=unserialize(file_get_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp'));
	}
	
	// Required timezone setting
	if(!isset($config['timezone'])){
		// Setting GMT as the default timezone
		$config['timezone']='Europe/London';
	}
	// Setting the timezone
	date_default_timezone_set($config['timezone']);
	
// LOADING LOGGER

	// Logger file is used for performance logging for later review
	// Configuration file can set what type of logging is used
	if(isset($config['logger']) && $config['logger']!=false && (!isset($config['logger-ip']) || $config['logger-ip']=='*' || in_array($_SERVER['REMOTE_ADDR'],explode(',',$config['logger-ip'])))){
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-logger.php');
		$logger=new WWW_Logger($config['logger'],__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR);
	}
	
// LOADING HTTP REQUEST LIMITER

	// If limiter is configured to be used
	if(isset($config['limiter']) && $config['limiter']){

		// Limiter is used to block requests under specific conditions, like DOS attacks or when server load is too high
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-limiter.php');
		$limiter=new WWW_Limiter(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR);
		
		// Assigning logger to Limiter
		// Logger is used to output log data in case Limiter stops the script pre-maturely
		if(isset($logger)){
			$limiter->logger=$logger;
		}
		// Load limiter blocks access if server load is detected to be too high at the moment of request
		if(isset($config['load-limiter']) && $config['load-limiter']){
			$limiter->limitServerLoad($config['load-limiter']);
		}
		// Load limiter allows access for certain IP's or blocks access to specific blacklist of IP's
		if(isset($config['whitelist-limiter']) && $config['whitelist-limiter']){
			$limiter->limitWhitelisted($config['whitelist-limiter']);
		} elseif(isset($config['blacklist-limiter']) && $config['blacklist-limiter']){
			$limiter->limitBlacklisted($config['blacklist-limiter']);
		}
		// If HTTPS limiter is used, the ststem returns a 401 error if the user agent attempts to access the site without HTTPS
		if(isset($config['https-limiter']) && $config['https-limiter']){
			$limiter->limitNonSecureRequests(); // By default the user agent is redirected to HTTPS address of the same request
		}
		// If HTTP authentication is turned on, the system checks for credentials and returns 401 if failed
		if(isset($config['http-authentication-limiter']) && $config['http-authentication-limiter']){
			$limiter->limitUnauthorized($config['http-authentication-username'],$config['http-authentication-password']);
		}
		// Request limiter keeps track of how many requests per minute are allowed on IP.
		// If limit is exceeded, then IP is blocked for an hour.
		if(isset($config['request-limiter']) && $config['request-limiter']){
			$limiter->limitRequestCount($config['request-limiter']);
		}

	}
	
// ERROR AND EXIT HANDLING

	// This error handler replaces default PHP error handler and is tied to Exception class
	function WWW_exitHandler($type=false,$message=false,$file=false,$line=false){
	
		// if this is called through error handler
		if($message){
			$errorCheck=array();
			$errorCheck['type']=$type;
			$errorCheck['message']=$message;
			$errorCheck['file']=$file;
			$errorCheck['line']=$line;
		} else {
			// Getting the last thrown error, if any
			$errorCheck=error_get_last();
		}
		
		// If there was an error
		if($errorCheck!=null){
		
			// Detecting if error is fatal - thus if error message should be shown to the user
			$fatalError=false;
			if(in_array($errorCheck['type'],array(E_ERROR,E_USER_ERROR,E_CORE_ERROR,E_PARSE))){
				$fatalError=true;
			}
					
			// Error report will be stored in this array
			$errorReport=array();
			$errorReport[]=date('d.m.Y H:i:s').' GMT';
			
			// Input data to error report
			$error=array();
			if(isset($_GET) && !empty($_GET)){
				$error['get']=$_GET;
			}
			if(isset($_POST) && !empty($_POST)){
				$error['post']=$_POST;
			}
			if(isset($_FILES) && !empty($_FILES)){
				$error['files']=$_FILES;
			}
			if(isset($_COOKIES) && !empty($_COOKIES)){
				$error['cookies']=$_COOKIES;
			}
			if(isset($_SESSION) && !empty($_SESSION)){
				$error['session']=$_SESSION;
			}
			if(isset($_SERVER) && !empty($_SERVER)){
				$error['server']=$_SERVER;
			}
			// Add to error array
			$errorReport[]=$error;
			
			//Adding backtrace
			$errorReport[]=debug_backtrace();
			
			// Writing current error and file to the array as well
			$error=array();
			$error['type']=$errorCheck['type'];
			$error['file']=$errorCheck['file'];
			$error['line']=$errorCheck['line'];
			$error['message']=$errorCheck['message'];
			$errorReport[]=$error;
			
			// Logging the error, the error filename is calculated from current error message (this makes sure there are no duplicates, if the error message is the same).
			file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.md5(serialize($error)).'.log',json_encode($errorReport)."\n",FILE_APPEND);
				
			// As long as the error level is set to display errors of this type				
			if($fatalError){
			
				// Cleaning output buffer, if it exists
				if(ob_get_level()>=1){
					ob_end_clean();
				}

				// There was an error in code
				// System returns 500 header even as a server error (possible bug in code)
				header('HTTP/1.1 500 Internal Server Error');
				
				// Regular users will be shown a friendly error message
				echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">WE ARE CURRENTLY EXPERIENCING A PROBLEM WITH YOUR REQUEST</div>';
				echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">ERROR HAS BEEN LOGGED FOR FURTHER INVESTIGATION</div>';
				
				// Closing the entire request
				die();
				
			}
			
		}
		
	}
	
	// Setting the error handler
	set_error_handler('WWW_exitHandler',E_ALL);
	register_shutdown_function('WWW_exitHandler'); 
	
// LOADING HANDLERS

	// Index gateway works differently based on what file is being requested
	// Handlers for all different modes are stored under /engine/ subfolder

	// request has a file extension, then system will attempt to use another handler
	if(isset($resourceExtension)){

		// Handler is detected based on requested file extension
		if(in_array($resourceExtension,array('jpeg','jpg','png'))){
			// Image handler allows for things such as dynamic image loading
			require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.image.php');
		} elseif(in_array($resourceExtension,array('css','js','txt','csv','xml','html','htm','rss','vcard'))){
		
			// Text-based resources are handled by Resource handler, except for two special cases (robots.txt and sitemap.xml)
			if($resourceFile=='sitemap.xml'){
				// Sitemap is dynamically generated from sitemap files in /resource/ subfolder
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.sitemap.php');
			} elseif($resourceFile=='robots.txt'){
				// Robots file is dynamically generated based on 'robots' configuration in config.php file
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.robots.php');
			} else {
				// In every other case the system loads text based resources with additional options, such as compressions and minifying, with Resource handler
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.resource.php');
			}
			
		} elseif(in_array($resourceExtension,array('tmp','log','ht','htaccess','pem','crt','db','sql','version','conf','ini'))){
		
			// These file extensions are not allowed, thus 403 error is returned
			// Log category is 'file' due to it being a file with an extension
			if($logger){
				$logger->setCustomLogData(array('response-code'=>403,'category'=>'file'));
				$logger->writeLog();
			}
			
			// Returning 403 header
			header('HTTP/1.1 403 Forbidden');
			die();
			
		} elseif($resourceExtension=='api'){
			
			// Replacing the extension in the request to find handler filename
			$apiHandler=str_replace('.api','',$resourceFile);
			
			// If the file exists then system loads the new API, otherwise 404 is returned
			if(file_exists(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.api-'.$apiHandler.'.php')){
									
				// Custom API files need to be placed in engine subfolder
				// To see how the default API is built, take a look at handler.api.php
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.api-'.$apiHandler.'.php');
				
			} else {
			
				// This allows API filename to define what type of data should be returned
				if($apiHandler!='json' && $apiHandler!='www'){
					$_GET['www-return-type']=$apiHandler;
				}
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.api.php');
			
			}
			
		} else {
			// File handler is loaded for every other file request case
			require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.file.php');
		}
		
	} else {
		// Every other request is handled by Data handler, which loads URL and View controllers for website views
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.data.php');
	}

?>