<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * API Handler
 *
 * API Handler is loaded whenever a HTTP request is made to *.api extension. API Handler takes all 
 * the input from GET, POST, FILES; SESSION and COOKIE variables, loads Wave Framework API and sends 
 * all the input to the API and then returns the result to the user agent. By default the API Handler 
 * returns data in JSON format. It also loads Database class for additional functionality.
 *
 * @package    Index Gateway
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/handler_api.htm
 * @since      1.5.0
 * @version    3.1.4
 */

//INITIALIZATION

	// Stopping all requests that did not come from Index Gateway
	if(!isset($resourceAddress)){
		header('HTTP/1.1 403 Forbidden');
		die();
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
	
// LOADING API AND GATHERING INPUT DATA

	// API is used to process all requests and it handles caching and API validations
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-api.php');
	$api=new WWW_API($state);

	// All the data sent by the user agent is stored in this variable
	$inputData=array();

	// If data was sent through other means, such as a JSON or XML string
	if(is_array($state->data['http-input']) && !empty($state->data['http-input'])){
	
		// http-input is data string, converted to array, that is sent as a stream (as XML or JSON)
		$inputData=$state->data['http-input'];
		
	} else {
	
		// All the data sent by user agent is added here and merged into one array
		if(!empty($_POST)){ 
			$inputData+=$_POST; 
		}
		if(!empty($_GET)){ 
			$inputData+=$_GET; 
		}
		if(!empty($_FILES)){ 
			$inputData['www-files']=$_FILES;
		}
	
	}
	
	if(!empty($_COOKIE)){ 
		$inputData['www-cookie']=$_COOKIE;
		// Testing if namespace cookie has been set, if it has then checking for session variables
		if(isset($_COOKIE[$state->data['session-namespace']])){
			// Starting sessions
			$state->startSession();
		}
	}
	
// SENDING COMMAND TO API

	// Setting current API profile in state
	if(isset($inputData['www-profile'])){
		$state->data['api-profile']=$inputData['www-profile'];
	} else {
		$state->data['api-profile']=$state->data['api-public-profile'];
	}
	
	// API command is executed with all the data that was sent by the user agent, along with other www-* settings
	$apiResult=$api->command($inputData,false,true,true);
	
// LOGGER
	
	// API Logging
	if(isset($config['api-logging']) && $config['api-logging']!=false && isset($inputData['www-command']) && ((in_array('*',$config['api-logging']) && !in_array('!'.$state->data['api-profile'],$config['api-logging'])) || in_array($state->data['api-profile'],$config['api-logging']))){
		file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'api.log',$state->data['request-time']."\t".$state->data['api-profile']."\t".$inputData['www-command']."\n",FILE_APPEND);
	}

	// Logger notifications
	if(isset($logger)){
		$logger->setCustomLogData(array('category'=>'API['.$apiHandler.']','api-profile'=>$state->data['api-profile'],'database-query-count'=>((isset($databaseConnection))?$databaseConnection->queryCounter:0))+$api->apiLoggerData);
		$logger->writeLog();
	}

?>