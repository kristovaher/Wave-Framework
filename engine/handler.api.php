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
 * @version    3.4.3
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
	} elseif(file_exists(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'script.php')){
		require(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'script.php');
	}
	
// LOADING API AND GATHERING INPUT DATA

	// API is used to process all requests and it handles caching and API validations
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-api.php');
	$api=new WWW_API($state);

	// All the data sent by the user agent is stored in this variable
	$inputData=array();

	// If additional data was sent as part of input stream
	if($state->data['http-input']){
		// If state has converted the stream to array (if it was in XML or JSON format)
		if(is_array($state->data['http-input']) && !empty($state->data['http-input'])){
			$inputData=$state->data['http-input'];
		} else {
			$inputData['www-data']=$state->data['http-input'];
		}
	}
	
	// This holds information about API validation and its exceptions
	$apiValidation=array();
	
	// All the data sent by user agent is added here and merged into one array
	if(!empty($_POST)){
		$inputData+=$_POST; 
	}
	if(!empty($_GET)){ 
		$inputData+=$_GET; 
	}
	if(!empty($_COOKIE)){ 
		foreach($_COOKIE as $key=>$cookie){
			// This is a security measure to make sure that only actual cookies can be negated from validation
			if(!isset($inputData[$key])){
				$inputData[$key]=$cookie;
				// Cookies are not part of input data validation, so they are added to exceptions
				$apiValidation[]=$key;
			}
		}
	}
	if(!empty($_FILES)){
		foreach($_FILES as $key=>$file){
			// This is a security measure to make sure that only uploaded files can be negated from validation
			if(!isset($inputData[$key])){
				$inputData[$key]=$file;
				// File uploads are not part of input data validation, so they are added to exceptions
				$apiValidation[]=$key;
			}
		}
	}
	
	// Removing input stream related data that was read in the previous section
	if($state->data['http-input']){
		// Removing input stream related data
		unset($inputData['www-xml'],$inputData['www-json']);
	}
	
// SENDING COMMAND TO API

	// Setting current API profile in state
	if(isset($inputData['www-profile'])){
		$state->data['api-profile']=$inputData['www-profile'];
	} else {
		$state->data['api-profile']=$state->data['api-public-profile'];
	}
	
	// API command is executed with all the data that was sent by the user agent, along with other www-* settings
	$apiResult=$api->command($inputData,false,$apiValidation,true);
	
// LOGGER
	
	// API Logging
	if(isset($config['api-logging']) && $config['api-logging']!=false && isset($inputData['www-command']) && ((in_array('*',$config['api-logging']) && !in_array('!'.$state->data['api-profile'],$config['api-logging'])) || in_array($state->data['api-profile'],$config['api-logging']))){
		file_put_contents(__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'api.tmp',$state->data['request-time']."\t".$state->data['api-profile']."\t".$inputData['www-command']."\n",FILE_APPEND);
	}

	// Logger notifications
	if(isset($logger)){
		$logger->setCustomLogData(array('category'=>'API['.$apiHandler.']','api-profile'=>$state->data['api-profile'],'database-query-count'=>(($databaseConnection)?$databaseConnection->queryCounter:0))+$api->apiLoggerData);
		$logger->writeLog();
	}

?>