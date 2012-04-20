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
*/

// INITIALIZATION

	// Stopping all requests that did not come from Index gateway
	if(!isset($resourceAddress)){
		header('HTTP/1.1 403 Forbidden');
		die();
	}

	// This functions file is not required, but can be used for system wide functions
	// If you want to include additional libraries, do so here
	if(file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'functions.php')){
		require(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'functions.php');
	} else {
		require(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'functions.php');
	}

	// If index URL cache is not configured, it is turned off by default
	if(!isset($config['index-url-cache-timeout'])){
		$config['index-url-cache-timeout']=0;
	}
	
	// State class is used by API and Factory created objects to keep track of request state
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-state.php');
	$state=new WWW_State($config);
	
// DATABASE

	// If database name is set then database controller is loaded
	if(isset($config['database-name']) && $config['database-name']!='' && isset($config['database-type']) && isset($config['database-host']) && isset($config['database-username']) && isset($config['database-password'])){
		// Including the required class and creating the object
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-database.php');
		$databaseConnection=new WWW_Database();
		// Assigning database variables and creating the connection
		$databaseConnection->type=$config['database-type'];
		$databaseConnection->host=$config['database-host'];
		$databaseConnection->username=$config['database-username'];
		$databaseConnection->password=$config['database-password'];
		$databaseConnection->database=$config['database-name'];
		$databaseConnection->connect();
	}
	
// LOADING API AND CALLING URL SOLVING/ROUTING CONTROLLER

	// API is used to process all requests and it handles caching and API validations
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-api.php');
	$api=new WWW_API($state);

	// This uses current request URI to find out which view should be loaded, by default it uses the request set by State
	// API check is turned off, since index.php is considered a public gateway
	$viewData=$api->command(array('www-command'=>'url-solve','www-output'=>0,'www-return-type'=>'php','www-request'=>$state->data['true-request'],'www-cache-timeout'=>$config['index-url-cache-timeout']),false,false,true);

// CALLING DEFAULT VIEW CONTROLLER IF URL DID NOT ORDER A REDIRECTION

	// If view data includes flags for redirection then the view itself will be ignored
	if(!isset($viewData['www-temporary-redirect']) && !isset($viewData['www-permanent-redirect'])){
		
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
		if(isset($_COOKIES) && !empty($_COOKIES)){ 
			$inputData['www-cookies']=$_COOKIES;
			// Testing if namespace cookie has been set, if it has then checking for session variables
			if(isset($_COOKIES[$state->data['session-namespace']])){
				// Starting sessions
				$state->sessionStart();
				// Checking for session variables
				if(isset($_SESSION[$state->data['session-namespace']]) && !empty($_SESSION[$state->data['session-namespace']])){ 
					$inputData['www-session']=$_SESSION[$state->data['session-namespace']]; 
				}
			}
		}

		// Solved request is used to load the view through API
		$inputData['www-view-data']=$viewData;

		// If index view cache is not configured, it is turned of by default
		if(isset($viewData['cache-timeout'])){
			$config['index-view-cache-timeout']=$viewData['cache-timeout'];
		} elseif(!isset($config['index-view-cache-timeout'])){
			$config['index-view-cache-timeout']=0;
		}
		
		// Default view controller defined
		$viewController='view';
		// View controller can be overwritten from sitemap files
		if(isset($viewData['view-controller'])){
			$viewController=$viewData['view-controller'];
		}
		
		// API check is turned off, since index.php is considered a public gateway
		$api->command($inputData+array('www-profile'=>$state->data['api-public-profile'],'www-command'=>$viewController.'-load','www-return-type'=>'html','www-cache-timeout'=>$config['index-view-cache-timeout']),false,false,true);
	
	}
	
// LOGGER

	// API gathers its own log data internally and it is given to Logger to be logged
	if(isset($logger)){
		$logger->setCustomLogData($api->apiLoggerData+array('category'=>'data','database-query-counter'=>((isset($databaseConnection))?$databaseConnection->queryCounter:0)));
		$logger->writeLog();
	}

?>