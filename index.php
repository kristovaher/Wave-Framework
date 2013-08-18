<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Index Gateway
 *
 * Index Gateway is an index/bootstrap file of Wave Framework that will serve almost every HTTP 
 * request made to the system built on Wave Framework. It analyzes the HTTP request, loads Logger 
 * and configuration as well as HTTP request Limiter, overwrites error handler of PHP and then 
 * executes the command through one of the request handlers that are stored in 
 * /engine/handler.[handler-type].php files.
 *
 * @package    Index Gateway
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/gateway.htm
 * @since      1.0.0
 * @version    3.7.0
 */

// SOLVING THE HTTP REQUEST

	// For performance logging
	$microTime=microtime(true);

	// Custom error-handling and reporting is used
	// Make sure to check for errors with /tools/debugger.php script (especially when you encounter a critical error page)
	error_reporting(0);

	// Getting resource without GET string
	$tmp=explode('?',$_SERVER['REQUEST_URI']);
	$resourceAddress=array_shift($tmp);

	// Stopping all direct requests to Index Gateway
	if($resourceAddress==$_SERVER['SCRIPT_NAME']){
		header('HTTP/1.1 403 Forbidden');
		die();
	}

	// Currently known location of the file in filesystem
	// Double replacement occurs since some environments give document root with the slash in the end, some don't (like Windows)
	$resourceRequest=str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$_SERVER['DOCUMENT_ROOT'].$resourceAddress);
	
	// If requested URL does not point to a directory, then request is possibly made to a file
	if(!is_dir($resourceRequest)){
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
	}
	
// LOADING CONFIGURATION

	// Defining root directory, this is required by handlers in /engine/ subfolder
	define('__ROOT__',__DIR__.DIRECTORY_SEPARATOR);

	//Configuration is stored in this array, it has to be defined even if no configuration is loaded
	$config=array();

	// Including the configuration
	if(!file_exists(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp') || filemtime(__ROOT__.'config.ini')>filemtime(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'config.tmp')){
		
		// Configuration is parsed from INI file in the root of the system
		$config=parse_ini_file(__ROOT__.'config.ini',false,INI_SCANNER_RAW);
		
		// Loading version numbers
		$versionsRaw=explode("\n",str_replace("\r",'',file_get_contents(__ROOT__.'.version')));
		foreach($versionsRaw as $ver){
			// Versions are separated by colon in the version file
			$tmp=explode(':',$ver);
			$config['version-'.$tmp[0]]=$tmp[1];
		}
		
		// List of logger IP's
		if(isset($config['logger-ip'])){
			$config['logger-ip']=explode(',',$config['logger-ip']);
		}
		
		// List of languages
		if(isset($config['languages'])){
			$config['languages']=explode(',',$config['languages']);
		}
		
		// Internal logging flags
		if(isset($config['internal-logging'])){
			$config['internal-logging']=explode(',',$config['internal-logging']);
		}
		
		// API logging settings
		if(isset($config['api-logging'])){
			$config['api-logging']=explode(',',$config['api-logging']);
		}
		
		// API versions
		if(isset($config['api-versions'])){
			// This also makes sure that the most recent version number exists in API versions list
			$config['api-versions']=explode(',',$config['api-versions']);
			if(!in_array($config['version-api'],$config['api-versions'])){
				$config['api-versions'][]=$config['version-api'];
			}
		} else {
			$config['api-versions']=array($config['version-api']);
		}
		
		// File extensions and defaults
		if(isset($config['image-extensions'])){
			$config['image-extensions']=explode(',',$config['image-extensions']);
		} else {
			$config['image-extensions']=array('jpeg','jpg','png');
		}
		if(isset($config['resource-extensions'])){
			$config['resource-extensions']=explode(',',$config['resource-extensions']);
		} else {
			$config['resource-extensions']=array('css','js','txt','csv','xml','html','htm','rss','vcard','appcache');
		}
		if(isset($config['file-extensions'])){
			$config['file-extensions']=explode(',',$config['file-extensions']);
		} else {
			$config['file-extensions']=array('pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar');
		}
		if(isset($config['forbidden-extensions'])){
			$config['forbidden-extensions']=explode(',',$config['forbidden-extensions']);
		} else {
			$config['forbidden-extensions']=array('tmp','log','ht','htaccess','pem','crt','db','sql','version','conf','ini','empty');
		}
	
		// Required timezone setting
		if(!isset($config['timezone'])){
			// Setting GMT as the default timezone
			$config['timezone']='Europe/London';
		}
	
		// Trusted proxies and IP address
		if(isset($config['trusted-proxies'])){
			$config['trusted-proxies']=explode(',',$config['trusted-proxies']);
		} else {
			$config['trusted-proxies']=array('*');
		}
	
		// Trusted proxies and IP address
		if(isset($config['session-fingerprint'])){
			$config['session-fingerprint']=explode(',',$config['session-fingerprint']);
		}
		
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
	
	// Setting the timezone
	date_default_timezone_set($config['timezone']);
	
	// IP may be forwarded (such as when website is used through a proxy), this can check for such an occasion
	if(isset($_SERVER['HTTP_CLIENT_IP']) && (in_array('*',$config['trusted-proxies']) || in_array($_SERVER['REMOTE_ADDR'],$config['trusted-proxies']))){
		$tmp=explode(',',$_SERVER['HTTP_CLIENT_IP']);
		define('__IP__',trim(array_pop($tmp)));
	} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && (in_array('*',$config['trusted-proxies']) || in_array($_SERVER['REMOTE_ADDR'],$config['trusted-proxies']))){
		$tmp=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
		define('__IP__',trim(array_pop($tmp)));
	} else {
		define('__IP__',$_SERVER['REMOTE_ADDR']);
	}
	
// LOADING LOGGER

	// Logger file is used for performance logging for later review
	// Configuration file can set what type of logging is used
	if(isset($config['logger']) && $config['logger']!=false && (!isset($config['logger-ip']) || in_array('*',$config['logger-ip']) || in_array(__IP__,$config['logger-ip'])) && ($config['logger']=='*' || preg_match($config['logger'],$_SERVER['REQUEST_URI']))){
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-logger.php');
		$logger=new WWW_Logger(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR,$microTime);
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
		if(isset($config['limiter-load']) && $config['limiter-load']){
			$limiter->limitServerLoad($config['limiter-load']);
		}
		// Load limiter allows access for certain IP's or blocks access to specific blacklist of IP's
		if(isset($config['limiter-whitelist']) && $config['limiter-whitelist']){
			$limiter->limitWhitelisted($config['limiter-whitelist']);
		} elseif(isset($config['limiter-blacklist']) && $config['limiter-blacklist']){
			$limiter->limitBlacklisted($config['limiter-blacklist']);
		}
		// If HTTPS limiter is used, the ststem returns a 401 error if the user agent attempts to access the site without HTTPS
		if(isset($config['limiter-https']) && $config['limiter-https']){
			$limiter->limitNonSecureRequests(); // By default the user agent is redirected to HTTPS address of the same request
		}
		// If HTTP authentication is turned on, the system checks for credentials and returns 401 if failed
		if(isset($config['limiter-authentication']) && $config['limiter-authentication']){
			$limiter->limitUnauthorized($config['http-authentication-username'],$config['http-authentication-password'],((isset($config['http-authentication-ip']))?$config['http-authentication-ip']:'*'));
		}
		// Request limiter keeps track of how many requests per minute are allowed on IP.
		// If limit is exceeded, then IP is blocked for an hour.
		if(isset($config['limiter-request']) && $config['limiter-request']){
			$limiter->limitRequestCount($config['limiter-request']);
		}
		// Referrer limiter checks if the Referrer URL is allowed or not
		if(isset($config['limiter-referrer']) && $config['limiter-referrer']){
			$limiter->limitReferrer($config['limiter-referrer']);
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
		
		// If there is an error and it is not a deprecated Line 0 error (which sporadically is thrown in PHP 5.3.4)
		if($errorCheck && ($errorCheck['line']!=0 || $errorCheck['type']<E_DEPRECATED)){
		
			// Using the global to access configuration settings beyond the scope of this method
			global $config;
		
			// Detecting if error is fatal - thus if error message should be shown to the user
			$fatalError=false;
			if(in_array($errorCheck['type'],array(E_ERROR,E_USER_ERROR,E_CORE_ERROR,E_PARSE))){
				$fatalError=true;
			}
			
			// Testing the configuration option for error-reporting
			if(!isset($config['errors-reporting']) || $config['errors-reporting']=='full' || ($fatalerror && $config['errors-reporting']=='critical')){
					
				// Error report will be stored in this array
				$errorReport=array();
				// Setting GMT as the error-reporting timezone
				$errorReport[]=date('d.m.Y H:i:s');
				
				// Input data to error report
				$error=array();
				if(!empty($_GET)){
					$error['get']=$_GET;
				}
				if(!empty($_POST)){
					$error['post']=$_POST;
				}
				if(!empty($_FILES)){
					$error['files']=$_FILES;
				}
				if(!empty($_COOKIE)){
					$error['cookies']=$_COOKIE;
				}
				if(!empty($_SERVER)){
					$error['server']=$_SERVER;
				}
				// Add to error array
				$errorReport[]=$error;
				
				//Adding backtrace
				if(isset($config['errors-trace']) && $config['errors-trace']==1){
					$errorReport[]=debug_backtrace();
				}
				
				// Writing current error and file to the array as well
				$error=array();
				$error['url']=$_SERVER['REQUEST_URI'];
				$error['type']=$errorCheck['type'];
				$error['file']=$errorCheck['file'];
				$error['line']=$errorCheck['line'];
				$error['message']=$errorCheck['message'];
				$errorReport[]=$error;
				
				// This is the signature used for storing developer sessions
				$signatureFolder=md5($_SERVER['REMOTE_ADDR'].' '.$_SERVER['HTTP_USER_AGENT']);
				
				// If error folder does not yet exist
				if(!is_dir(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signatureFolder.DIRECTORY_SEPARATOR)){
					if(!mkdir(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signatureFolder.DIRECTORY_SEPARATOR,0755)){
						trigger_error('Cannot create error folder',E_USER_ERROR);
					}
					file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signatureFolder.DIRECTORY_SEPARATOR.'signature.tmp',$_SERVER['REMOTE_ADDR'].' '.$_SERVER['HTTP_USER_AGENT']);
				}
				
				// Logging the error, the error filename is calculated from current error message (this makes sure there are no duplicates, if the error message is the same).
				file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$signatureFolder.DIRECTORY_SEPARATOR.md5($error['file'].$error['message']).'.tmp',json_encode($errorReport)."\n",FILE_APPEND);

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
					if(isset($config['errors-verbose']) && $config['errors-verbose']==1){
						echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">CRITICAL ERROR ENCOUNTERED</div>';
						echo '<div style="font:12px Tahoma;width:500px;margin:auto;padding:5px 50px 5px 50px;"><b>TYPE</b>: '.htmlspecialchars($errorCheck['type']).'</div>';
						echo '<div style="font:12px Tahoma;width:500px;margin:auto;padding:5px 50px 5px 50px;"><b>FILE</b>: '.htmlspecialchars($errorCheck['file']).'</div>';
						echo '<div style="font:12px Tahoma;width:500px;margin:auto;padding:5px 50px 5px 50px;"><b>LINE</b>: '.htmlspecialchars($errorCheck['line']).'</div>';
						echo '<div style="font:12px Tahoma;width:500px;margin:auto;padding:5px 50px 5px 50px;"><b>MESSAGE</b>: '.htmlspecialchars($errorCheck['message']).'</div>';
						if(isset($config['errors-trace']) && $config['errors-trace']==1){
							echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">FULL STACK TRACE AVAILABLE FROM DEBUGGER SCRIPT</div>';
						} else {
							echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">FULL STACK TRACE IS NOT LOGGED BY DEBUGGER</div>';
						}
					} else {
						echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">WE ARE CURRENTLY EXPERIENCING A PROBLEM WITH YOUR REQUEST</div>';
						echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">ERROR HAS BEEN LOGGED FOR FURTHER INVESTIGATION</div>';
					}
					
					// Closing the entire request
					die();
					
				}
			
			}
			
		}
		
	}
	
	// Setting the error handler
	set_error_handler('WWW_exitHandler',E_ALL);
	register_shutdown_function('WWW_exitHandler'); 
	
// LOADING HANDLERS

	// Index Gateway works differently based on what file is being requested
	// Handlers for all different modes are stored under /engine/ subfolder

	// request has a file extension, then system will attempt to use another handler
	if(isset($resourceExtension)){

		// Handler is detected based on requested file extension
		if(in_array($resourceExtension,$config['image-extensions'])){
		
			// Image Handler allows for things such as dynamic image loading
			require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.image.php');
			
		} elseif(in_array($resourceExtension,$config['resource-extensions'])){
		
			// Text-based resources are handled by Resource Handler, except for two special cases (robots.txt and sitemap.xml)
			if($resourceFile=='sitemap.xml'){
				// Sitemap is dynamically generated from sitemap files in /resource/ subfolder
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.sitemap.php');
			} elseif($resourceFile=='robots.txt'){
				// Robots file is dynamically generated based on 'robots' configuration in config.php file
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.robots.php');
			} elseif($resourceExtension=='appcache'){
				// Appcache settings can be dynamically generated
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.appcache.php');
			} else {
				// In every other case the system loads text based resources with additional options, such as compressions and minifying, with Resource Handler
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.resource.php');
			}
			
		} elseif(in_array($resourceExtension,$config['file-extensions'])){
		
			// File Handler is loaded for every other file request case
			require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.file.php');
			
		} elseif(in_array($resourceExtension,$config['forbidden-extensions'])){
		
			// These file extensions are not allowed, thus 403 error is returned
			// Log category is 'file' due to it being a file with an extension
			if(isset($logger)){
				$logger->setCustomLogData(array('response-code'=>403,'category'=>'file'));
				$logger->writeLog();
			}
			
			// Returning 403 header
			header('HTTP/1.1 403 Forbidden');
			die();
			
		} elseif($resourceExtension=='api'){
			
			// Replacing the extension in the request to find handler filename
			$apiHandler=str_replace('.api','',$resourceFile);
			
			// Replacing all potentially sensitive characters from API handler name
			$apiHandler=preg_replace('/[^0-9a-z\-\_]/i','',$apiHandler);
			
			// If the file exists then system loads the new API, otherwise 404 is returned
			if(file_exists(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.api-'.$apiHandler.'.php')){
									
				// Custom API files need to be placed in engine subfolder
				// To see how the default API is built, take a look at handler.api.php
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.api-'.$apiHandler.'.php');
				
			} else {
			
				// This allows API filename to define what type of data should be returned
				if($apiHandler=='www'){
					if(!isset($_GET['www-return-type'])){
						$_GET['www-return-type']='php';
					}
				} elseif($apiHandler!='json' && $apiHandler!='www'){
					$_GET['www-return-type']=$apiHandler;
				}
				require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.api.php');
			
			}
			
		} else {
		
			// Every other extension is handled by Data Handler, which loads URL and View controllers for website views
			require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.data.php');
			
		}
		
	} else {
		// Every other request is handled by Data Handler, which loads URL and View controllers for website views
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'handler.data.php');
	}

?>