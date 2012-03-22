<?php

/* 
WWW - PHP micro-framework
Index gateway

Index gateway is a file where majority of requests are forwarded by Apache and /.htaccess file. 
This file serves caches and compresses data, if supported, for both static files as well 
as regular views. It also displays errors for files not found or files that are forbidden to 
be accessed. Handlers for Index gateway are stored in /engine/ subfolder.

Author and support: Kristo Vaher - kristo@waher.net
*/

//Configuration is stored in this array, it has to be defined even if no configuration is loaded
$config=array();

// Defining root directory, this is required by handlers in /engine/ subfolder
define('__ROOT__',__DIR__);

// Including the configuration
require(__ROOT__.DIRECTORY_SEPARATOR.'config.php');

// Error reporting is turned off by default
if(isset($config['error-reporting'])){
	error_reporting($config['error-reporting']);
} else {
	error_reporting(0);
}

// Logger file is used for performance logging for later review
// Configuration file can set what type of logging is used
if(isset($config['logger']) && $config['logger']!='' && $config['logger']!=false){
	require(__ROOT__.DIRECTORY_SEPARATOR.'engine'.DIRECTORY_SEPARATOR.'class.www-logger.php');
	$logger=new WWW_Logger($config['logger'],__ROOT__.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR);
}

// If limiter is configured to be used
if(isset($config['limiter']) && $config['limiter']==true){

	// Limiter is used to block requests under specific conditions, like DOS attacks or when server load is too high
	require(__ROOT__.DIRECTORY_SEPARATOR.'engine'.DIRECTORY_SEPARATOR.'class.www-limiter.php');
	$limiter=new WWW_Limiter(__ROOT__.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR);

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
	if(isset($config['blacklist-limiter']) && $config['blacklist-limiter']!=''){
		$limiter->limitBlacklisted($config['blacklist-limiter']);
	}

	// If HTTPS limiter is used, the ststem returns a 401 error if the client attempts to access the site without HTTPS
	if(isset($config['https-limiter']) && $config['https-limiter']==true){
		$limiter->limitNonSecureRequests(); // By default the client is redirected to HTTPS address of the same request
	}
	
	// If HTTP authentication is turned on, the system checks for credentials and returns 401 if failed
	if(isset($config['http-authentication-limiter']) && $config['http-authentication-limiter']==true){
		$limiter->limitUnauthorized($config['http-authentication-username'],$config['http-authentication-password']);
	}

	// Request limiter keeps track of how many requests per minute are allowed on IP.
	// If limit is exceeded, then IP is blocked for an hour.
	if(isset($config['request-limiter']) && $config['request-limiter']!=0){
		$limiter->limitRequestCount($config['ip-requests-per-minute']);
	}

}

// This gateway works differently based on what file is being requested
// $_SERVER variable QUERY_STRING defines what type of file was detected by .htaccess and what type of behavior is required
// Handlers for all different modes are stored under /engine/ subfolder

require(__ROOT__.DIRECTORY_SEPARATOR.'engine'.DIRECTORY_SEPARATOR.'handler.'.$_SERVER['QUERY_STRING'].'.php');

// If Logger is defined then request is logged and can be used for performance review later
if(isset($logger)){
	$logger->writeLog($_SERVER['QUERY_STRING']);
}

?>
