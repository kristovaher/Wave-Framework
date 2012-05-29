<?php

/*
WWW Framework
Factory class

Factory class is required for MVC objects. Factory is used as factory class for supporting the 
MVC components of the system, every MVC component is (usually) inherited from this class. Factory
carries with itself currently used API.

* Factory can dynamically load and return new classes of its own per demand
* All child classes can access system state and API objects
* Factory also acts as a wrapper for database calls
* Factory also allows loading of API Wrapper for communicating with other WWW Framework API's

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Factory {

	// API object is stored here
	private $WWW_API=false;
	private $WWW_API_callIndex=0;

	// When a model, view or controller is created, it can be loaded with existing state or API
	// * api - WWW_API object
	final public function __construct($api=false){
		// API is passed to the object, if defined
		if($api){ 
			$this->WWW_API=$api; 
		}
		// This acts as __construct() for the MVC objects
		if(method_exists($this,'__initialize')){
			$this->__initialize();
		}
	}
	
	// API CALLS
	
		// Factory created objects can make API calls
		// * command - Command string to be processed, just like the one accepted by WWW_API object
		// * inputData - If data is input
		// * useBuffer - This tells API to use buffer (returns data from memory if the same command with -exact- same input has already been sent)
		// Returns data based on API call
		final protected function api($command,$inputData=array(),$useBuffer=true){
			// Input data has to be an array
			if(!is_array($inputData)){
				trigger_error('API input data is not an array',E_USER_ERROR);
			}
			// Setting the set command variable to proper format for API
			$inputData['www-command']=$command;
			// This defaults return data type
			if(!isset($inputData['www-return-type'])){
				$inputData['www-return-type']='php';
			}
			// This defaults whether API command is pushed with headers or not
			if(!isset($inputData['www-output'])){
				$inputData['www-output']=0;
			}
			// This is the call index after the API call is made
			$this->WWW_API_callIndex=$this->WWW_API->callIndex;
			// Returning the result from API
			return $this->WWW_API->command($inputData,$useBuffer,false);
		}
		
		// This function returns API wrapper for external API connections
		// * address - Address of the API file
		// * userAgent - Custom user agent string
		// Returns the Wrapper object if successful
		final protected function apiConnection($address){
			// Address is required
			if($address && $address!=''){
				if(!class_exists('WWW_Wrapper') && file_exists(__DIR__.DIRECTORY_SEPARATOR.'class.www-factory.php')){
					require(__DIR__.DIRECTORY_SEPARATOR.'class.www-wrapper.php');
				}
				// Returning new Wrapper object
				return new WWW_Wrapper($address);
			} else {
				return false;
			}
		}
	
		// This function clears current API buffer
		public function clearBuffer(){
			$this->WWW_API->clearBuffer();
		}
	
	// STATE DATA SET AND GET
	
		// Returns data from State objects data array
		// * variable - variable name of State
		// * subvariable - subvariable of State
		// Returns requested variable, otherwise returns false
		final protected function getState($variable=false,$subvariable=false){
			return $this->WWW_API->state->getState($variable,$subvariable);
		}
		
		// Sets data for State objects data array
		// * variable - Variable to be set
		// * value - Value to be set
		// If variable was set, this function always returns true
		final protected function setState($variable,$value=true){
			return $this->WWW_API->state->setState($variable,$value);
		}
		
		// This function returns all the translations for a specific language
		// * language - Language keyword, if this is not set then returns current language translations
		// * keyword - If only single keyword needs to be returned
		// Returns an array of translations and their keywords
		final protected function getTranslations($language=false,$keyword=false){
			return $this->WWW_API->state->getTranslations($language,$keyword);
		}
		
		// This function returns sitemap data for a specific language
		// * language - Language keyword, if this is not set then returns current language sitemap
		// * keyword - If only a single URL node needs to be returned
		// Returns sitemap array of set language, this sitemap array has been modified for use for building links
		final protected function getSitemap($language=false,$keyword=false){
			return $this->WWW_API->state->getSitemap($language,$keyword);
		}
		
		// This function returns sitemap data for a specific language
		// * language - Language keyword, if this is not set then returns current language sitemap
		// * keyword - If only a single URL node needs to be returned
		// Returns sitemap array of set language, raw array is just the way it is in sitemap INI file
		final protected function getSitemapRaw($language=false,$keyword=false){
			return $this->WWW_API->state->getSitemapRaw($language,$keyword);
		}
	
	// MVC FACTORY
	
		// Factory function for loading a class and returning an object or a specific function of that object
		// * model - Name of the model
		// * methodName - if set, the function will create the object and return the result of this function of that object
		// * methodData - if method name is set, this will also submit data to that function call
		// Returns the object or a result of a function call of that object
		final protected function getModel($model,$methodName=false,$methodData=array()){
		
			// Dynamically creating class name
			$className='WWW_model_'.$model;
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'model.'.$model.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'model.'.$model.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'models'.DIRECTORY_SEPARATOR.'model.'.$model.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'models'.DIRECTORY_SEPARATOR.'model.'.$model.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error('Model ['.$model.'] does not exist',E_USER_ERROR);
				}
			}
			
			// Object is returned if no specific method name is called
			if(!$methodName){
				// If method name was not defined then this function returns the entire class with current State and API set
				return new $className($this->WWW_API);
			} else {
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('Model ['.$model.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		// Factory function for loading a class and returning an object or a specific function of that object
		// * controller - Name of the controller
		// * methodName - if set, the function will create the object and return the result of this function of that object
		// * methodData - if method name is set, this will also submit data to that function call
		// Returns the object or a result of a function call of that object
		final protected function getController($controller,$methodName=false,$methodData=array()){
		
			// Dynamically creating class name
			$className='WWW_controller_'.$controller;
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'controller.'.$controller.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'controller.'.$controller.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'controller.'.$controller.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'controller.'.$controller.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error('Controller ['.$controller.'] does not exist',E_USER_ERROR);
				}
			}
			
			// Object is returned if no specific method name is called
			if(!$methodName){
				// If method name was not defined then this function returns the entire class with current State and API set
				return new $className($this->WWW_API);
			} else {
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('Controller ['.$controller.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		// Factory function for loading a class and returning an object or a specific function of that object
		// * view - Name of the view
		// * methodName - if set, the function will create the object and return the result of this function of that object
		// * methodData - if method name is set, this will also submit data to that function call
		// Returns the object or a result of a function call of that object
		final protected function getView($view,$methodName=false,$methodData=array()){
		
			// Dynamically creating class name
			$className='WWW_view_'.$view;
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'view.'.$view.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'view.'.$view.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'views'.DIRECTORY_SEPARATOR.'view.'.$view.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'views'.DIRECTORY_SEPARATOR.'view.'.$view.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error('View ['.$view.'] does not exist',E_USER_ERROR);
				}
			}
			
			// Object is returned if no specific method name is called
			if(!$methodName){
				// If method name was not defined then this function returns the entire class with current State and API set
				return new $className($this->WWW_API);
			} else {
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('View ['.$view.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		// This function attempts to get an object from /resources/classes/ folder
		// Class has to be defined in format class.{class}.php
		// * className - Class name
		// Returns either the object or result of the method call from the object
		final protected function getObject($className){
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'view.'.$className.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error('Class ['.$className.'] does not exist',E_USER_ERROR);
				}
			}
			
			// Returning object
			return new $className();
			
		}
		
	// CUSTOM RETURNED ARRAYS
		
		// This is simply used to return an error from MVC elements, it generates a proper return array
		// * message - Error message
		// * customData - Additional data returned
		// * responseCode - Error code number
		// Returns an array
		final protected function errorArray($message='Error',$customData=false,$responseCode=300){
			if(is_array($customData)){
				return array('www-error'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-error'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
		// This is simply used to return a success array from MVC elements, it generates a proper return array
		// * message - Success message
		// * customData - Additional data returned
		// * responseCode - Response code number
		// Returns an array
		final protected function successArray($message='OK',$customData=false,$responseCode=400){
			if(is_array($customData)){
				return array('www-success'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-success'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
		// This is a simple check function that is used to test if an array has www-error or www-success set
		// It is used to validate responses from standardized controller responses if API is not used to call a method
		// * data - Data that is checked
		// Returns either true or false, if www-error or www-success array keys are set, in every other case simply returns the data value itself
		final protected function checkTrueFalse($data){
			// These values are only checked from an array
			if(is_array($data)){
				if(isset($data['www-error'])){
					return false;
				} elseif(isset($data['www-success'])){
					return true;
				} else {
					// Since neither was set, system simply returns the original value
					return $data;
				}
			} else {
				// Since result was not an array, system returns the input data instead, whatever the value is
				return $data;
			}
		}
		
	// CACHE
	
		// This function allows to disable caching of current result, no matter what
		// Essentially this allows the result to be loaded from cache, but not 'written' into cache
		// * state - True or false flag
		// Always return true
		final protected function disableCache($state){
			// This is stored as a flag
			$this->WWW_API->noCache[$this->WWW_API_callIndex]=$state;
			return true;
		}
		
		// This method allows to remove tagged cache files from filesystem
		// * tags - comma separated list of tag(s) that the cache was stored under
		// Always returns true
		final protected function clearCache($tags){
			return $this->WWW_API->clearCache($tags);
		}
		
	// INTERNAL LOG ENTRY WRAPPER
	
		// This function is used to add data to internal log, if it is turned on
		// * key - Descriptive key that the log entry will be stored under
		// * data - Data contained in the entry
		// Returns true, if logging is used
		final protected function internalLogEntry($key,$data=false){
			return $this->WWW_API->internalLogEntry($key,$data);
		}
		
		// This method writes to internal log the duration from the start object was constructed or from the last time this function was called
		// * key - Identifier for splitTime group, API is always initialized at the start of API construct
		final protected function splitTime($key='api'){
			return $this->WWW_API->splitTime($key);
		}
		
	// STATE MESSENGER WRAPPERS
	
		// This method sets the request messenger key
		// * address - Key that messenger data will be saved under
		// Always returns true
		final protected function stateMessenger($address){
			return $this->WWW_API->state->stateMessenger($address);
		}
		
		// This sets messenger data
		// * data - Key or data array
		// * value - Value, if data is a key
		// Returns true or false
		final protected function setStateMessengerData($data,$value=false){
			// Attempting to get the result
			$result=$this->WWW_API->state->setMessengerData($data,$value);
			if($result){
				// Setting no-cache flag to true
				$this->WWW_API->noCache[$this->WWW_API_callIndex]=true;
				// Returning the result
				return $result;
			}
			return false;
		}
		
		// This function removes data from state messenger
		// * key - Key that will be removed, if set to false then removes the entire current state messenger
		// Returns true if data was set and is now removed
		final protected function unsetStateMessengerData($key=false){
			return $this->WWW_API->state->unsetMessengerData($key);
		}
		
		// This function returns messenger data either from filesystem or from current session
		// * address - Messenger address
		// * remove - True or false flag whether to delete the request data after returning it
		// Returns request messenger data
		final protected function getStateMessengerData($address=false,$remove=true){
			// Attempting to get the result
			$result=$this->WWW_API->state->getMessengerData($address,$remove);
			if($result){
				// Setting no-cache flag to true
				$this->WWW_API->noCache[$this->WWW_API_callIndex]=true;
				// Returning the result
				return $result;
			}
			return false;
		}
	
	// SESSION AND COOKIE WRAPPERS
	
		// This starts session in current namespace
		final protected function startSession(){
			return $this->WWW_API->state->startSession();
		}
	
		// This function regenerates ongoing session
		final protected function regenerateSession(){
			return $this->WWW_API->state->regenerateSession();
		}
		
		// This function regenerates ongoing session
		final protected function destroySession(){
			return $this->WWW_API->state->destroySession();
		}
	
		// This sets session variable in current session namespace
		// * key - Key of the variable, can be an array
		// * value - Value to be set
		// Returns true
		final protected function setSession($key,$value){
			return $this->WWW_API->state->setSession($key,$value);
		}
		
		// Gets a value based on a key from current namespace
		// * key - Key of the value to be returned, can be an array
		// Returns the value if it exists
		final protected function getSession($key){
			return $this->WWW_API->state->getSession($key);
		}
		
		// Unsets session variable
		// * key - Key of the value to be unset, can be an array
		// Returns true
		final protected function unsetSession($key){
			return $this->WWW_API->state->unsetSession($key);
		}
		
		// This sets session variable
		// * key - Key of the variable, can be an array
		// * value - Value to be set
		// * configuration - Cookie configuration options
		// Returns true
		final protected function setCookie($key,$value,$configuration=array()){
			return $this->WWW_API->state->setCookie($key,$value,$configuration);
		}
		
		// Gets a value based on a key from current cookies
		// * key - Key of the value to be returned, can be an array
		// Returns the value if it exists
		final protected function getCookie($key){
			return $this->WWW_API->state->getCookie($key);
		}
		
		// Unsets cookie
		// * key - Key of the cookie to be unset, can be an array
		// * config - Additional configuration options about the cookie, such as path
		// Returns true
		final protected function unsetCookie($key,$config=array()){
			return $this->WWW_API->state->unsetCookie($key,$config);
		}
		
	// SESSION USER AND PERMISSIONS
		
		// This sets user data to current session
		// * data - Data array set to user
		final public function setUser($data){
			return $this->WWW_API->state->setUser($data);
		}
		
		// This returns either entire current user session or a single key from it
		// * key - Element returned from user data, if not set then returns the entire user data
		// Returns either the whole data as array or just a single element or false, if not found
		final public function getUser($key=false){
			return $this->WWW_API->state->getUser($key);
		}
		
		// This method unsets existing user
		// Always returns true
		final public function unsetUser(){
			return $this->WWW_API->state->unsetUser();
		}
	
		// This function checks for session permissions
		// * check - String that is checked against permissions array
		// Returns either true or false, depending whether permissions are set or not
		final protected function checkPermissions($check){
			return $this->WWW_API->state->checkPermissions($check);
		}
		
		// This function returns all current session permissions
		// Returns an array of permissions
		final protected function getPermissions(){
			return $this->WWW_API->state->getPermissions();
		}
		
		// This function sets current session permissions
		// * permissions - An array or a string of permissions
		// Always returns true
		final protected function setPermissions($permissions){
			return $this->WWW_API->state->setPermissions($permissions);
		}
		
		// This method unsets existing permissions
		// Always returns true
		final public function unsetPermissions(){
			return $this->WWW_API->state->unsetPermissions();
		}
		
	// DATABASE WRAPPERS
	
		// This function is used to create a new database object
		// * type - Database type
		// * host - Database host
		// * database - Database name
		// * username - Database username
		// * password - Database password
		// * showErrors - True or false flag regarding whether to show errors
		// * persistentConnection - True of false flag regarding whether connection is assigned to be permanent
		// Returns WWW_Database object
		final protected function dbNew($type,$host,$database,$username,$password,$showErrors=false,$persistentConnection=false){
			// Requiring database class files, if class has not been defined
			if(!class_exists('WWW_Database')){
				// Including the required class and creating the object
				require($this->WWW_API->state['system-root'].'engine'.DIRECTORY_SEPARATOR.'class.www-database.php');
			}
			$databaseConnection=new WWW_Database($type,$host,$database,$username,$password,$showErrors,$persistentConnection);
			// Passing the database to State object
			return $databaseConnection;
		}
			
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		// Returns result of that query
		final protected function dbSingle($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbSingle($query,$variables);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		// Returns result of that query
		final protected function dbMultiple($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbMultiple($query,$variables);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		// Returns result of that query
		final protected function dbCommand($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbCommand($query,$variables);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final protected function dbLastId(){
			return $this->WWW_API->state->databaseConnection->dbLastId();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final protected function dbTransaction(){
			return $this->WWW_API->state->databaseConnection->dbTransaction();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final protected function dbRollback(){
			return $this->WWW_API->state->databaseConnection->dbRollback();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final protected function dbCommit(){
			return $this->WWW_API->state->databaseConnection->dbCommit();
		}
		
		// This function escapes a string for use in query
		// * value - Input value
		// * type - Method of quoting, either 'escape', 'integer', 'latin', 'field' or 'like'
		// * stripQuotes - Whether the resulting quotes will be stripped from the string, if they get set
		final protected function dbQuote($value,$type='escape',$stripQuotes=false){
			return $this->WWW_API->state->databaseConnection->dbQuote($value,$type);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final protected function dbPDO(){
			return $this->WWW_API->state->databaseConnection->pdo;
		}
		
	// TERMINAL
	
		// This function looks for available terminal/command line option and attempts to execute it
		// * command - Command to be executed
		// Returns command result, if available
		final protected function terminal($command){
			return $this->WWW_API->state->terminal($command);			
		}

}
	
?>