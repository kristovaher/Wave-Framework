<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Data Handler
 *
 * Data Handler is loaded in situations when other handlers were not used, which is most commonly for 
 * regular web page requests. Data Handler takes all the input from GET, POST, FILES; SESSION and 
 * COOKIE variables, loads Wave Framework API and sends all the input to the API. It first uses URL 
 * Controller to find out what page the user agent is looking for and then a View Controller to 
 * generate that requested page.
 *
 * @package    Index Gateway
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/handler_data.htm
 * @since      1.5.0
 * @version    3.6.0
 */

// INITIALIZATION

	// Stopping all requests that did not come from Index Gateway
	if(!isset($resourceAddress)){
		header('HTTP/1.1 403 Forbidden');
		die();
	}
	
	// If access control header is set in configuration
	if(isset($config['access-control'])){
		header('Access-Control-Allow-Origin: '.$config['access-control']);
	}
	
	// This stores request header based cache timeout
	$cacheLoad=-1;
	
	// Setting cache timeout for HTTP request, used by Data Handler
	if(isset($_SERVER['HTTP_CACHE_CONTROL'])){
		if(trim($_SERVER['HTTP_CACHE_CONTROL'])=='no-cache'){
			$cacheLoad=0;
		} else {
			// Finding the amount of allowed seconds
			$raw=trim(str_replace('max-age=','',$_SERVER['HTTP_CACHE_CONTROL']));
			if(is_numeric($raw)){
				$cacheLoad=$raw;
			}
		}
	}

	// If index URL cache is not configured, it is turned off by default
	if(!isset($config['index-url-cache-timeout'])){
		$config['index-url-cache-timeout']=0;
	}
	
	// State class is used by API and Factory created objects to keep track of request state
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-state.php');
	$state=new WWW_State($config);
	
// DATABASE AND SESSIONS

	// This holds link to database
	$databaseConnection=false;
	
	// Connecting to database, if configuration is set
	if(isset($config['database-name'],$config['database-type'],$config['database-host'],$config['database-username'],$config['database-password'])){
		// Including the required class and creating the object
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-database.php');
		$databaseConnection=new WWW_Database($config['database-type'],$config['database-host'],$config['database-name'],$config['database-username'],$config['database-password'],((isset($config['database-errors']))?$config['database-errors']:false),((isset($config['database-persistent']))?$config['database-persistent']:false));
		// Passing the database to State object
		$state->databaseConnection=$databaseConnection;
	}
	
	// Loading sessions class
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-sessions.php');
	// Loading sessions class with the session namespace
	$state->sessionHandler=new WWW_Sessions($state->data['session-name'],$state->data['session-lifetime'],$databaseConnection);
	// Assigning session data to State
	if(!empty($state->sessionHandler->sessionData)){
		$state->data['session-original-data']=$state->sessionHandler->sessionData;
		$state->data['session-data']=$state->sessionHandler->sessionData;
	}
	
// AUTOLOAD AND SESSIONS FUNCTIONALITY

	// This functions file is not required, but can be used for system wide functions
	// If you want to include additional libraries, do so here
	if(file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'script.php')){
		require(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'script.php');
	} elseif(file_exists(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'script.php')) {
		require(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'script.php');
	}
	
// LOADING API AND CALLING URL SOLVING/ROUTING CONTROLLER

	// API is used to process all requests and it handles caching and API validations
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-api.php');
	$api=new WWW_API($state);

	// This uses current request URI to find out which view should be loaded, by default it uses the request set by State
	// API check is turned off, since index.php is considered a public gateway
	$view=$api->command(array('url'=>$state->data['request-true'],'www-command'=>'url-solve','www-output'=>0,'www-return-type'=>'php','www-cache-timeout'=>$config['index-url-cache-timeout'],'www-cache-load-timeout'=>$cacheLoad),false,false,true);

// CALLING DEFAULT VIEW CONTROLLER IF URL DID NOT ORDER A REDIRECTION

	// If view data includes flags for redirection then the view itself will be ignored
	if(isset($view['view']) && !isset($view['www-temporary-redirect']) && !isset($view['www-permanent-redirect'])){
			
		// Notifying State of View data
		$state->setState(array(array('view'=>$view,'language'=>$view['language'])));
		
		// All the data sent by the user agent is stored in this variable
		$inputData=array();

		// All the data sent by user agent is added here and merged into one array
		if(!empty($_POST)){ 
			$inputData=$_POST; 
		}
		if(!empty($_GET)){ 
			$inputData+=$_GET; 
		}
		if(!empty($_FILES)){
			$inputData+=$_FILES;
		}
		if(!empty($_COOKIE)){
			$inputData+=$_COOKIE;
		}
		// Removing input stream related data
		unset($inputData['www-xml'],$inputData['www-json']);

		// If index view cache is not configured, it is turned of by default
		if(isset($view['cache-timeout'])){
			$config['index-view-cache-timeout']=$view['cache-timeout'];
		} elseif(!isset($config['index-view-cache-timeout'])){
			$config['index-view-cache-timeout']=0;
		}
		
		// API check is turned off, since index.php is considered a public gateway
		$api->command(array('www-command'=>$view['controller'].'-'.$view['controller-method'],'www-request'=>$state->data['request-true'],'www-return-type'=>'html','www-cache-tags'=>((isset($view['cache-tag']))?$view['cache-tag']:''),'www-cache-timeout'=>$config['index-view-cache-timeout'],'www-cache-load-timeout'=>$cacheLoad)+$inputData,false,false,true);

	}
	
// LOGGER

	// API gathers its own log data internally and it is given to Logger to be logged
	if(isset($logger)){
		$logger->setCustomLogData(array('category'=>'data','database-query-counter'=>(($databaseConnection)?$databaseConnection->queryCounter:0))+$api->apiLoggerData);
		$logger->writeLog();
	}

?>