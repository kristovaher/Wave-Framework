<?php

/*
WWW - PHP micro-framework
Factory class

One of the three main files of the framework, this file is always required. WWW_Factory is 
used as factory class for supporting the MVC components of the system, every MVC component 
is (usually) inherited from WWW_Factory. Note that this is not required, you can develop 
controllers that are not inherited from WWW_Factory.

* Factory can dynamically load and return new classes of its own per demand
* All child classes can access system state and API objects as well as make database requests

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Factory {

	// API and State objects are stored here
	private $WWW_API=false;
	private $WWW_State=false;

	// When a model, view or controller is created, it can be loaded with existing state or API
	// * state - WWW_State object
	// * api - WWW_API object
	final public function __construct($state=false,$api=false){
	
		// State is passed to the object, if defined
		if($state){ 
			$this->WWW_State=$state; 
		}
		// API is passed to the object, if defined
		if($api){ 
			$this->WWW_API=$api; 
		}
		// This acts as __construct() for the MVC objects
		if(method_exists($this,'__initialize')){
			$this->__initialize();
		}
		
	}
	
	// Factory created objects can make API calls
	// * command - Command string to be processed, just like the one accepted by WWW_API object
	// * inputData - If data is input
	// * useBuffer - This tells API to use buffer (returns data from memory if the same command with -exact- same input has already been sent)
	// Returns data based on API call
	final public function api($command,$inputData=array(),$useBuffer=true){
		// Setting the set command variable to proper format for API
		$inputData['www-command']=$command;
		// This defaults return data type
		if(!isset($inputData['www-datatype'])){
			$inputData['www-datatype']='php';
		}
		// This defaults whether API command is pushed with headers or not
		if(!isset($inputData['www-output'])){
			$inputData['www-output']=false;
		}
		return $this->WWW_API->command($inputData,$useBuffer,false);
	}
	
	// Returns data from State objects data array
	// * variable - variable name of State
	// * subvariable - subvariable of State
	// Returns requested variable, otherwise returns false
	final public function getState($variable=false,$subvariable=false){
		return $this->WWW_State->getState($variable,$subvariable);
	}
	
	// Sets data for State objects data array
	// * variable - Variable to be set
	// * value - Value to be set
	// If variable was set, this function always returns true
	final public function setState($variable,$value=true){
		return $this->WWW_State->setState($variable,$value);
	}
	
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
			if(file_exists($this->WWW_State->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php')){
				// Requiring override file
				require($this->WWW_State->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php');
			} elseif(file_exists($this->WWW_State->data['system-root'].'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php')){
				// Requiring original file
				require($this->WWW_State->data['system-root'].'models'.DIRECTORY_SEPARATOR.'class.'.$model.'.php');
			} else {
				// Error is thrown if class was not found
				throw new Exception('Model ['.$model.'] does not exist');
			}
		}
		
		// Object is returned if no specific method name is called
		if(!$methodName){
			// If method name was not defined then this function returns the entire class with current State and API set
			return new $className($this->WWW_State,$this->WWW_API);
		} else {
			// If method name was set, then this function creates a new temporary object
			$tempObject=new $className($this->WWW_State,$this->WWW_API);
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
			if(file_exists($this->WWW_State->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php')){
				// Requiring override file
				require($this->WWW_State->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php');
			} elseif(file_exists($this->WWW_State->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php')){
				// Requiring original file
				require($this->WWW_State->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$controller.'.php');
			} else {
				// Error is thrown if class was not found
				throw new Exception('Controller ['.$controller.'] does not exist');
			}
		}
		
		// Object is returned if no specific method name is called
		if(!$methodName){
			// If method name was not defined then this function returns the entire class with current State and API set
			return new $className($this->WWW_State,$this->WWW_API);
		} else {
			// If method name was set, then this function creates a new temporary object
			$tempObject=new $className($this->WWW_State,$this->WWW_API);
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
			if(file_exists($this->WWW_State->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php')){
				// Requiring override file
				require($this->WWW_State->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php');
			} elseif(file_exists($this->WWW_State->data['system-root'].'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php')){
				// Requiring original file
				require($this->WWW_State->data['system-root'].'views'.DIRECTORY_SEPARATOR.'class.'.$view.'.php');
			} else {
				// Error is thrown if class was not found
				throw new Exception('View ['.$view.'] does not exist');
			}
		}
		
		// Object is returned if no specific method name is called
		if(!$methodName){
			// If method name was not defined then this function returns the entire class with current State and API set
			return new $className($this->WWW_State,$this->WWW_API);
		} else {
			// If method name was set, then this function creates a new temporary object
			$tempObject=new $className($this->WWW_State,$this->WWW_API);
			// If method exists, then the result of this method is returned as a result
			if(method_exists($tempObject,$methodName)){
				return $tempObject->$methodName($methodData);
			} else {
				// Error is thrown if method was not found
				throw new Exception('View ['.$view.'] method ['.$methodName.'] does not exist');
			}
		}
		
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// * query - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns result of that query
	final public function dbSingle($query,$variables=array()){
		return $this->state->databaseConnection->single($query,$variables);
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// * query - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns result of that query
	final public function dbMultiple($query,$variables=array()){
		return $this->state->databaseConnection->multiple($query,$variables);
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// * query - query string, a statement to prepare with PDO
	// * variables - array of variables to use in prepared statement
	// Returns result of that query
	final public function dbCommand($query,$variables=array()){
		return $this->state->databaseConnection->command($query,$variables);
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// Returns result of that call
	final public function dbLastId(){
		return $this->state->databaseConnection->lastId();
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// Returns result of that call
	final public function dbTransaction(){
		return $this->state->databaseConnection->beginTransaction();
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// Returns result of that call
	final public function dbRollback(){
		return $this->state->databaseConnection->rollbackTransaction();
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// Returns result of that call
	final public function dbCommit(){
		return $this->state->databaseConnection->commitTransaction();
	}
	
	// This simply allows to call WWW_Database function from the object itself, routed through database class
	// Returns result of that call
	final public function dbPDO(){
		return $this->state->databaseConnection->pdo;
	}

}
	
?>