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

	// Getting resource without GET string
	$resourceAddress=array_shift(explode('?',$_SERVER['REQUEST_URI']));

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
		if(!file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp',json_encode($config))){
			header('HTTP/1.1 500 Internal Server Error');
			echo '<h1>HTTP/1.1 500 Internal Server Error</h1>';
			echo '<p>Cannot write cache in filesystem, please make sure filesystem folders are writable.</p>';
		}
	} else {
		// Since INI file has not been changed, configuration is loaded from cache
		$config=json_decode(file_get_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp'),true);
	}

	// Error reporting is turned off by default
	if(isset($config['error-reporting'])){
		error_reporting($config['error-reporting']);
	} else {
		error_reporting(0);
	}
	
// LOADING LOGGER

	// Logger file is used for performance logging for later review
	// Configuration file can set what type of logging is used
	if(isset($config['logger']) && $config['logger']!='' && $config['logger']!=false){
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-logger.php');
		$logger=new WWW_Logger($config['logger'],__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR);
	}
	
// LOADING HTTP REQUEST LIMITER

	// If limiter is configured to be used
	if(isset($config['limiter']) && $config['limiter']==true){

		// Limiter is used to block requests under specific conditions, like DOS attacks or when server load is too high
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-limiter.php');
		$limiter=new WWW_Limiter(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR);
		
		// Assigning logger to Limiter
		// Logger is used to output log data in case Limiter stops the script pre-maturely
		if(isset($logger)){
			$limiter->logger=$logger;
		}
		// Load limiter blocks access if server load is detected to be too high at the moment of request
		if(isset($config['load-limiter']) && $config['load-limiter']!=0){
			$limiter->limitServerLoad($config['load-limiter']);
		}
		// Load limiter blocks access to specific blacklist of IP's
		if(isset($config['blacklist-limiter']) && $config['blacklist-limiter'] && $config['blacklist-limiter']!=''){
			$limiter->limitBlacklisted($config['blacklist-limiter']);
		}
		// If HTTPS limiter is used, the ststem returns a 401 error if the user agent attempts to access the site without HTTPS
		if(isset($config['https-limiter']) && $config['https-limiter']==true){
			$limiter->limitNonSecureRequests(); // By default the user agent is redirected to HTTPS address of the same request
		}
		// If HTTP authentication is turned on, the system checks for credentials and returns 401 if failed
		if(isset($config['http-authentication-limiter']) && $config['http-authentication-limiter']==true){
			$limiter->limitUnauthorized($config['http-authentication-username'],$config['http-authentication-password']);
		}
		// Request limiter keeps track of how many requests per minute are allowed on IP.
		// If limit is exceeded, then IP is blocked for an hour.
		if(isset($config['request-limiter']) && $config['request-limiter']!=0){
			$limiter->limitRequestCount($config['request-limiter']);
		}

	}
	
// LOADING HANDLERS

	// Index gateway works differently based on what file is being requested
	// Handlers for all different modes are stored under /engine/ subfolder

	// Errors that are encountered within handlers are all logged, so system starts catching for potential errors
	try {

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
		
	} catch (Exception $e){

		// There was an error in code
		// System returns 500 header even as a server error (possible bug in code)
		header('HTTP/1.1 500 Internal Server Error');
		
		// Verbose error shown to developer only
		if(isset($config['http-authentication-username']) && isset($config['http-authentication-password']) && isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']==$config['http-authentication-username'] && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_PW']==$config['http-authentication-password']){
			echo '<h1>HTTP/1.1 500 Internal Server Error</h1>';
			echo '<p><b>Exception trace:</b></p>';
			$trace=array_reverse($e->getTrace());
			foreach($trace as $key=>$t){
				$key++;
				echo '<p><b>'.$key.'</b> '.$t['file'].' (line: '.$t['line'].') '.((isset($t['class']))?$t['class'].'->':'').((isset($t['function']))?$t['function']:'').'</p>';
				if(isset($t['args'])){
					echo '<pre style="padding-left:50px;">';
					var_dump($t['args']);
					echo '</pre>';
				}
			}
			$key++;
			echo '<p><b>'.$key.'</b> '.$e->getFile().' (line: '.$e->getLine().') '.$e->getMessage().'</p>';

		}

		// Error-hitting request is logged and can be used for debugging later
		// This message will still be logged regardless whether the logger is used or not
		if(!$logger){
			// Log class
			require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-logger.php');
			// All information about the error-creating request is logged
			$logger=new WWW_Logger('*',__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR);
		}
		
		// 500 Error response code and error message are stored in log file
		$logger->setCustomLogData(array('response-code'=>500,'category'=>'error','error'=>$e->getMessage().' [FILE:'.$e->getFile().' LINE:'.$e->getLine().']'));
		// Log is written in 'error' category for easier filtering
		$logger->writeLog();

	}

?>