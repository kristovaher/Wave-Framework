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
*/

class WWW_Factory {

	// API object is stored here
	private $WWW_API=false;

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
		final public function api($command,$inputData=array(),$useBuffer=true){
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
			return $this->WWW_API->command($inputData,$useBuffer,false);
		}
		
		// This function returns API wrapper for external API connections
		// * address - Address of the API file
		// * userAgent - Custom user agent string
		// Returns the Wrapper object if successful
		final public function apiConnection($address){
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
	
	// STATE DATA SET AND GET
	
		// Returns data from State objects data array
		// * variable - variable name of State
		// * subvariable - subvariable of State
		// Returns requested variable, otherwise returns false
		final public function getState($variable=false,$subvariable=false){
			return $this->WWW_API->state->getState($variable,$subvariable);
		}
		
		// Sets data for State objects data array
		// * variable - Variable to be set
		// * value - Value to be set
		// If variable was set, this function always returns true
		final public function setState($variable,$value=true){
			return $this->WWW_API->state->setState($variable,$value);
		}
	
	// MVC FACTORY
	
		// Factory function for loading a class and returning an object or a specific function of that object
		// * model - Name of the model
		// * methodName - if set, the function will create the object and return the result of this function of that object
		// * methodData - if method name is set, this will also submit data to that function call
		// Returns the object or a result of a function call of that object
		final public function getModel($model,$methodName=false,$methodData=array()){
		
			// Dynamically creating class name
			$className='WWW_model_'.$model;
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php');
				} else {
					// Error is thrown if class was not found
					throw new Exception('Model ['.$model.'] does not exist');
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
					throw new Exception('Model ['.$model.'] method ['.$methodName.'] does not exist');
				}
			}
			
		}
		
		// Factory function for loading a class and returning an object or a specific function of that object
		// * controller - Name of the controller
		// * methodName - if set, the function will create the object and return the result of this function of that object
		// * methodData - if method name is set, this will also submit data to that function call
		// Returns the object or a result of a function call of that object
		final public function getController($controller,$methodName=false,$methodData=array()){
		
			// Dynamically creating class name
			$className='WWW_controller_'.$controller;
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php');
				} else {
					// Error is thrown if class was not found
					throw new Exception('Controller ['.$controller.'] does not exist');
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
					throw new Exception('Controller ['.$controller.'] method ['.$methodName.'] does not exist');
				}
			}
			
		}
		
		// Factory function for loading a class and returning an object or a specific function of that object
		// * view - Name of the view
		// * methodName - if set, the function will create the object and return the result of this function of that object
		// * methodData - if method name is set, this will also submit data to that function call
		// Returns the object or a result of a function call of that object
		final public function getView($view,$methodName=false,$methodData=array()){
		
			// Dynamically creating class name
			$className='WWW_view_'.$view;
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className)){
				// Class file can be loaded from /overrides/ directories, if set
				if(file_exists($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php');
				} elseif(file_exists($this->WWW_API->state->data['system-root'].'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['system-root'].'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php');
				} else {
					// Error is thrown if class was not found
					throw new Exception('View ['.$view.'] does not exist');
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
					throw new Exception('View ['.$view.'] method ['.$methodName.'] does not exist');
				}
			}
			
		}
		
	// CUSTOM RETURNED ARRAYS
		
		// This is simply used to return an error from MVC elements, it generates a proper return array
		// * message - Error message
		// * responseCode - Error code number
		// Returns an array
		final public function errorArray($message,$responseCode=300,$customData=false){
			if($customData){
				return array('www-error'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-error'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
		// This is simply used to return a success array from MVC elements, it generates a proper return array
		// * message - Success message
		// * responseCode - Response code number
		// Returns an array
		final public function successArray($message,$responseCode=400,$customData=false){
			if($customData){
				return array('www-success'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-success'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
	// INTERNAL LOG ENTRY WRAPPER
	
		// This function is used to add data to internal log, if it is turned on
		// * key - Descriptive key that the log entry will be stored under
		// * data - Data contained in the entry
		// Returns true, if logging is used
		final public function internalLogEntry($key,$data=false){
			return $this->WWW_API->internalLogEntry($key,$data);
		}
		
	// STATE MESSENGER WRAPPERS
	
		// This method sets the request messenger key
		// * address - Key that messenger data will be saved under
		// Always returns true
		final public function stateMessenger($address){
			return $this->WWW_API->state->stateMessenger($address);
		}
		
		// This sets messenger data
		// * data - Key or data array
		// * value - Value, if data is a key
		// Returns true or false
		final public function setStateMessengerData($data,$value=false){
			return $this->WWW_API->state->setMessengerData($data,$value);
		}
		
		// This function removes data from state messenger
		// * key - Key that will be removed
		// Returns true if data was set and is now removed
		final public function unsetStateMessengerData($key){
			return $this->WWW_API->state->unsetMessengerData($key);
		}
		
		// This function returns messenger data either from filesystem or from current session
		// * address - Messenger address
		// * remove - True or false flag whether to delete the request data after returning it
		// Returns request messenger data
		final public function getStateMessengerData($address=false,$remove=true){
			return $this->WWW_API->state->getMessengerData($address,$remove);
		}
	
	// SESSION AND COOKIE WRAPPERS
	
		// This starts session in current namespace
		final public function startSession(){
			return $this->WWW_API->state->startSession();
		}
	
		// This function regenerates ongoing session
		final public function regenerateSession(){
			return $this->WWW_API->state->regenerateSession();
		}
		
		// This function regenerates ongoing session
		final public function destroySession(){
			return $this->WWW_API->state->destroySession();
		}
	
		// This sets session variable in current session namespace
		// * key - Key of the variable
		// * value - Value to be set
		// Returns true
		final public function setSession($key,$value){
			return $this->WWW_API->state->setSession($key,$value);
		}
		
		// Gets a value based on a key from current namespace
		// * key - Key of the value to be returned
		// Returns the value if it exists
		final public function getSession($key){
			return $this->WWW_API->state->getSession($key);
		}
		
		// Unsets session variable
		// * key - Key of the value to be unset
		// Returns true
		final public function unsetSession($key){
			return $this->WWW_API->state->unsetSession($key);
		}
		
		// This sets session variable
		// * key - Key of the variable
		// * value - Value to be set
		// * configuration - Cookie configuration options
		// Returns true
		final public function setCookie($key,$value,$configuration=array()){
			return $this->WWW_API->state->setCookie($key,$value,$configuration);
		}
		
		// Gets a value based on a key from current cookies
		// * key - Key of the value to be returned
		// Returns the value if it exists
		final public function getCookie($key){
			return $this->WWW_API->state->getCookie($key);
		}
		
		// Unsets cookie
		// * key - Key of the cookie to be unset
		// Returns true
		final public function unsetCookie($key){
			return $this->WWW_API->state->unsetCookie($key);
		}
		
	// DATABASE WRAPPERS
			
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		// Returns result of that query
		final public function dbSingle($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->single($query,$variables);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		// Returns result of that query
		final public function dbMultiple($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->multiple($query,$variables);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		// Returns result of that query
		final public function dbCommand($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->command($query,$variables);
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final public function dbLastId(){
			return $this->WWW_API->state->databaseConnection->lastId();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final public function dbTransaction(){
			return $this->WWW_API->state->databaseConnection->beginTransaction();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final public function dbRollback(){
			return $this->WWW_API->state->databaseConnection->rollbackTransaction();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final public function dbCommit(){
			return $this->WWW_API->state->databaseConnection->commitTransaction();
		}
		
		// This simply allows to call WWW_Database function from the object itself, routed through database class
		// Returns result of that call
		final public function dbPDO(){
			return $this->WWW_API->state->databaseConnection->pdo;
		}
		
	// TERMINAL
	
		// This function looks for available terminal/command line option and attempts to execute it
		// * command - Command to be executed
		// Returns command result, if available
		final public function terminal($command){
		
			// Status variable
			$status=1;
		
			// Checking all possibleterminal functions
			if(function_exists('system')){
				ob_start();
				system($command,$status);
				$output=ob_get_contents();
				ob_end_clean();
			} elseif(function_exists('passthru')){
				ob_start();
				passthru($command,$status);
				$output=ob_get_contents();
				ob_end_clean();
			} elseif(function_exists('exec')){
				exec($command,$output,$status);
				$output=implode("\n",$output);
			} elseif(function_exists('shell_exec')){
				$output=shell_exec($command);
			} else {
				// No function was available, returning false
				return false;
			}

			// Returning result
			return array('output'=>$output,'status'=>$return_var);
			
		}

}
	
?>