<?php

/* 
WWW Framework
Index gateway data handler

Index gateway data handler is in functionality similar to api handler, except it uses API calls that 
cannot be controlled with direct request. It calls WWW_controller_url to solve a URL request and it 
calls WWW_controller_view to display data to the user agent relevant to the request. This handler is 
loaded when no other handlers can be used to solve the user agent request.

* Used for web pages
* Requires /resources/{language-code}.sitemap.php
* Requires /resources/{language-code}.translations.php
* Loads State and establishes database connection (if used)

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

// INITIALIZATION

	// Stopping all requests that did not come from Index gateway
	if(!isset($resourceAddress)){
		header('HTTP/1.1 403 Forbidden');
		die();
	}

	// If index URL cache is not configured, it is turned off by default
	if(!isset($config['index-url-cache-timeout'])){
		$config['index-url-cache-timeout']=0;
	}
	
	// State class is used by API and Factory created objects to keep track of request state
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-state.php');
	$state=new WWW_State($config);
	
// DATABASE

	// Connecting to database, if configuration is set
	if(isset($config['database-name'],$config['database-type'],$config['database-host'],$config['database-username'],$config['database-password'])){
		// Including the required class and creating the object
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-database.php');
		$databaseConnection=new WWW_Database($config['database-type'],$config['database-host'],$config['database-name'],$config['database-username'],$config['database-password'],((isset($config['database-errors']))?$config['database-errors']:false),((isset($config['database-persistent']))?$config['database-persistent']:false));
		// Passing the database to State object
		$state->databaseConnection=$databaseConnection;
	}
	
// AUTOLOAD FUNCTIONALITY

	// This functions file is not required, but can be used for system wide functions
	// If you want to include additional libraries, do so here
	if(file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'autoload.php')){
		require(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'autoload.php');
	} else {
		require(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'autoload.php');
	}
	
// LOADING API AND CALLING URL SOLVING/ROUTING CONTROLLER

	// API is used to process all requests and it handles caching and API validations
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-api.php');
	$api=new WWW_API($state);

	// This uses current request URI to find out which view should be loaded, by default it uses the request set by State
	// API check is turned off, since index.php is considered a public gateway
	$view=$api->command(array('www-command'=>'url-solve','www-output'=>0,'www-return-type'=>'php','www-request'=>$state->data['true-request'],'www-cache-timeout'=>$config['index-url-cache-timeout']),false,false,true);

// CALLING DEFAULT VIEW CONTROLLER IF URL DID NOT ORDER A REDIRECTION

	// If view data includes flags for redirection then the view itself will be ignored
	if(!isset($view['www-temporary-redirect']) && !isset($view['www-permanent-redirect'])){
		
		// All the data sent by the user agent is stored in this variable
		$inputData=array();

		// All the data sent by user agent is added here and merged into one array
		if(isset($_POST) && !empty($_POST)){ 
			$inputData+=$_POST; 
		}
		if(isset($_GET) && !empty($_GET)){ 
			$inputData+=$_GET; 
		}
		if(isset($_FILES) && !empty($_FILES)){ 
			$inputData['www-files']=$_FILES;
		}
		if(isset($_COOKIE) && !empty($_COOKIE)){ 
			$inputData['www-cookie']=$_COOKIE;
			// Testing if namespace cookie has been set, if it has then checking for session variables
			if(isset($_COOKIE[$state->data['session-namespace']])){
				// Starting sessions
				$state->startSession();
				// Checking for session variables
				if(isset($_SESSION[$state->data['session-namespace']]) && !empty($_SESSION[$state->data['session-namespace']])){ 
					$inputData['www-session']=$_SESSION[$state->data['session-namespace']];
				}
			}
		}

		// If index view cache is not configured, it is turned of by default
		if(isset($view['cache-timeout'])){
			$config['index-view-cache-timeout']=$view['cache-timeout'];
		} elseif(!isset($config['index-view-cache-timeout'])){
			$config['index-view-cache-timeout']=0;
		}
		
		// API check is turned off, since index.php is considered a public gateway
		$api->command($inputData+array('www-command'=>$view['controller'].'-load','www-request'=>$state->data['true-request'],'www-return-type'=>'html','www-cache-timeout'=>$config['index-view-cache-timeout']),false,false,true);
	
	}
	
// LOGGER

	// API gathers its own log data internally and it is given to Logger to be logged
	if(isset($logger)){
		$logger->setCustomLogData($api->apiLoggerData+array('category'=>'data','database-query-counter'=>((isset($databaseConnection))?$databaseConnection->queryCounter:0)));
		$logger->writeLog();
	}

?>