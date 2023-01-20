<?php

/**
 * Wave Framework <http://github.com/kristovaher/Wave-Framework>
 * Factory Class
 *
 * Factory class is the base class for a lot of Wave Framework dynamically loaded model, view 
 * and controller objects. It is not required to build your MVC objects by extending Factory 
 * Class, but doing so can make the development of these objects easier, as Factory also has 
 * wrappers to State, Wave Framework API and other functionality.
 *
 * @package    Factory
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/factory.htm
 * @since      1.0.0
 * @version    3.7.0
 */

class WWW_Factory {

	/**
	 * This variable holds the API object that is currently used by Wave Framework and 
	 * Factory. API object is the core engine of Wave Framework.
	 */
	private $WWW_API=false;
	
	/**
	 * This is an internal variable that holds a counter of how many times the API has been 
	 * called.  It is possible to call API calls within other API calls, so this is used to 
	 * keep track of how deep the current API call is, which is used to calculate buffering 
	 * layers and other internal functionality in API class.
	 */
	private $WWW_API_callIndex=0;
	
	/**
	 * This variable stores information about dynamically loaded libraries, if they are loaded.
	 */
	private $WWW_Libraries=array();

	/**
	 * Construction method of Factory class initializes currently used API and API call index
	 * settings within the new object that is extended from Factory. This method will also call
	 * __initialize() method, if such a method is set in the class that is extended from 
	 * Factory, as it is not allowed to overwrite __construct class itself.
	 * 
	 * @param boolean|object $api WWW_API object
	 * @param integer $callIndex current level of depth in the API call
	 * @return WWW_Factory
	 */
	final public function __construct($api=false,$callIndex=0){
	
		// API is passed to the object, if defined
		if($api){ 
			$this->WWW_API=$api; 
		}
		// API call index
		$this->WWW_API_callIndex=$callIndex;
		// This acts as __construct() for the MVC objects
		if(method_exists($this,'__initialize')){
			$this->__initialize();
		}
		
	}
	
	// API CALLS
	
		/**
		 * This method is used by objects, that have been extended from Factory class, to make 
		 * new API calls to Wave Framework. It takes a 'www-command' value in $command variable 
		 * and an array of key and value pairs in $inputData variable that is used as input data. 
		 * If $useBuffer is set to true, then API is told that it can use an internal API buffer
		 * call, which means that if API has already gotten the same exact API call with the 
		 * same exact input variables, then it returns the result from buffer directly rather 
		 * than execute it again.
		 *
		 * @param string $command command string to be processed, just like the one accepted by WWW_API object
		 * @param array $inputData key and value pairs of input data
		 * @param boolean $useBuffer this tells API to use buffer (returns data from memory for same calls)
		 * @return array/string based on request
		 */
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
			// This tells API not to include session as input data for the API call
			if(!isset($inputData['www-session'])){
				$inputData['www-session']=0;
			}
			// Returning the result from API
			return $this->WWW_API->command($inputData,$useBuffer,false);
			
		}
		
		/**
		 * This method is used to initialize PHP API Wrapper object. This object can be used to 
		 * send API calls to other API systems, that have been built on Wave Framework, over 
		 * HTTP. $address is the API address URL that Wave Framework connects with. This method 
		 * returns a new API Wrapper object.
		 *
		 * @param string $address address of the API file
		 * @return object or false if failed
		 */
		final protected function apiConnection($address){
		
			// Address is required
			if($address && $address!=''){
				if(!class_exists('WWW_Wrapper',false) && file_exists(__DIR__.DIRECTORY_SEPARATOR.'class.www-wrapper.php')){
					require(__DIR__.DIRECTORY_SEPARATOR.'class.www-wrapper.php');
				}
				// Returning new Wrapper object
				return new WWW_Wrapper($address);
			} else {
				return false;
			}
			
		}
	
		/**
		 * This method is used to clear current API command buffer. This is an optimization 
		 * method and should be used only of a lot of API calls are made that might fill the 
		 * memory allocated to PHP. What this method does is that it tells API object to empty 
		 * the internal variable that stores the results of all API calls that have already been 
		 * sent to API.
		 *
		 * @return boolean
		 */
		final protected function clearBuffer(){
			return $this->WWW_API->clearBuffer();
		}
		
		/**
		 * This returns the www-return-type of currently known API request. This can be used to 
		 * modify the request response based on what data is requested, if this is needed.
		 *
		 * @return string
		 */
		final protected function getReturnType(){
			// This is returned from API
			return $this->WWW_API->returnTypes[$this->WWW_API_callIndex];
		}
		
		/** 
		 * This method returns the currently used version numbers for API, system and
		 * Wave Framework itself.
		 *
		 * @param $type either 'system', 'www' or 'api'
		 * return boolean|string
		 */
		final protected function getVersion($type='api'){
			if($type=='api' && $this->WWW_API->requestedVersion){
				return $this->WWW_API->requestedVersion;
			} elseif(isset($this->WWW_API->state->data['version-'.$type])){
				return $this->WWW_API->state->data['version-'.$type];
			} else {
				return false;
			}
		}
	
	// STATE DATA SET AND GET
	
		/**
		 * This is just a wrapper function for getting state data for view. This method 
		 * returns $variable from the current view settings or the entire view data array, 
		 * if this is not set.
		 *
		 * @param boolean|string $variable key of view data to get
		 * @return mixed
		 */
		final protected function viewData($variable=false){
			// If variable is set
			if($variable){
				return $this->getState('view',$variable);
			} else {
				return $this->getState('view');
			}
		}
		
		/**
		 * This is just a wrapper function for getting state from global state storage. This 
		 * method returns $variable from the storage or the whole storage at once if this is 
		 * not set.
		 *
		 * @param boolean|string $key key of view data to get
		 * @return mixed
		 */
		final protected function getStorage($key=false){
			if(isset($this->WWW_API->state->data['storage'][$key])){
				return $this->WWW_API->state->data['storage'][$key];
			} else {
				return false;
			}
		}
		
		/**
		 * This is just a wrapper function for getting state from global state storage. This 
		 * method returns $variable from the storage or the whole storage at once if this is 
		 * not set.
		 *
		 * @param string $key key of storage data to set
		 * @param boolean|string $value value of the key in storage
		 * @return mixed
		 */
		final protected function setStorage($key,$value=true){
			$this->WWW_API->state->data['storage'][$key]=$value;
			return true;
		}
	
		/**
		 * This is the basic call to return a State variable from the object. If this method is 
		 * called without any variables, then the entire State array is returned. You can send 
		 * one or more key variables to this method to return a specific key from State. If you 
		 * send multiple parameters then this method attempts to find keys of a key in case the 
		 * State variable is an array itself. $input variable is only used within State class 
		 * itself.
		 * 
		 * @return mixed
		 */
		final protected function getState(){
			return $this->WWW_API->state->getState(func_get_args());
		}
		
		/**
		 * This method is used to set a $data variable value in State object. $variable can 
		 * also be an array of keys and values, in which case multiple variables are set at 
		 * once. This method uses stateChanged() for variables that carry additional 
		 * functionality, such as setting timezone. $input variable is only used within 
		 * State class itself.
		 *
		 * @return boolean
		 */
		final protected function setState(){
			return $this->WWW_API->state->setState(func_get_args());
		}
		
		/**
		 * This method returns an array of currently active translations, or for a language set 
		 * with $language variable. If $keyword is also set, then it returns a specific translation 
		 * with that keyword from $language translations. If $keyword is an array, then $subkeyword
		 * can be used to return specific translation from that keyword.
		 *
		 * @param boolean|string $language language keyword, if this is not set then returns current language translations
		 * @param boolean|string $keyword if only single keyword needs to be returned
		 * @param boolean|string $subkeyword if the $keyword is an array, then $subkeyword is the actual translation that is requested
		 * @return array, string or false if failed
		 */
		final protected function getTranslations($language=false,$keyword=false,$subkeyword=false){
			return $this->WWW_API->state->getTranslations($language,$keyword,$subkeyword);
		}
		
		/**
		 * This method includes or reads in a file from '/resources/content/' folder $name is the 
		 * modified filename that can also include subfolders, but without the language prefix and 
		 * without extension in the filename itself. If $language is not defined then currently 
		 * active language is used.
		 *
		 * @param string $name filename without language prefix
		 * @param boolean|string $language language keyword
		 * @return string
		 */
		final public function getContent($name,$language=false){
			return $this->WWW_API->state->getContent($name,$language);
		}
		
		/**
		 * This returns sitemap array that is modified for use with View controller and other 
		 * parts of the system. It returns sitemap for current language or a language set with 
		 * $language variable and can return a specific sitemap node based on $keyword.
		 *
		 * @param boolean|string $language language keyword, if this is not set then returns current language sitemap
		 * @param boolean|string $keyword if only a single URL node needs to be returned
		 * @return mixed
		 */
		final protected function getSitemap($language=false,$keyword=false){
			return $this->WWW_API->state->getSitemap($language,$keyword);
		}
		
		/**
		 * This method returns an array of currently active sitemap, or a sitemap for a language 
		 * set with $language variable. If $keyword is also set, then it returns a specific 
		 * sitemap node with that keyword from $language sitemap file. This method returns the 
		 * original, non-modified sitemap that has not been parsed for use with URL controller.
		 *
		 * @param boolean|string $language language keyword, if this is not set then returns current language sitemap
		 * @param boolean|string $keyword if only a single URL node needs to be returned
		 * @return mixed
		 */
		final protected function getSitemapRaw($language=false,$keyword=false){
			return $this->WWW_API->state->getSitemapRaw($language,$keyword);
		}
		
		/**
		 * This method allows you to send View Controller additional headers. This means 
		 * that your views, models and controllers can all affect additional files that 
		 * are loaded when View is returned to the browser. This allows for an extensive 
		 * flexibility when building a dynamic back-end-front-end relations. $type can be 
		 * 'script', 'library' or 'style' and they are loaded from '/resources/scripts/', 
		 * '/resources/libraries/' or '/resources/styles/' folders. If type is not assigned 
		 * or set to 'other', then the entire $content string will be added to the end of 
		 * the HTML header.
		 * 
		 * @param string $content either file name or an entire header string
		 * @param string $type assigns the type of header to load
		 * @return boolean
		 */
		final protected function viewHeader($content,$type='other'){
			
			// Multiple headers can be sent at once
			if(!is_array($content)){
				$this->WWW_API->state->data['view-headers'][$type][]=$content;
			} else {
				foreach($content as $c){
					$this->WWW_API->state->data['view-headers'][$type][]=$c;
				}
			}
			// Making sure that the array is unique
			$this->WWW_API->state->data['view-headers'][$type]=array_unique($this->WWW_API->state->data['view-headers'][$type]);
			
			// Always returns true
			return true;
		}
	
	// MVC FACTORY
	
		/**
		 * This is one of the core methods of Factory class. This method is used to return a new 
		 * Wave Framework model. Model name is $model and it should exist as a file in /models/ 
		 * or /overrides/models/ subfolder as model.[$model].php file. If $methodName is set, 
		 * then this method will also automatically call that method and return result of that 
		 * method instead of the model object itself. If $methodData is also set, then this is 
		 * the input variable sent to $methodName.
		 *
		 * @param string $model name of the model
		 * @param boolean|string $methodName name of the method called once the object is loaded
		 * @param array $methodData input variable for the method that is called
		 * @return object or mixed if function called
		 */
		final protected function getModel($model,$methodName=false,$methodData=array()){
		
			// Returning the object or result, if possible
			return $this->getMVC('model',$model,$methodName,$methodData);
			
		}
		
		/**
		 * This is one of the core methods of Factory class. This method is used to return a new 
		 * Wave Framework controller. Controller name is $controller and it should exist as a file 
		 * in /models/ or /overrides/models/ subfolder as controller.[$controller].php file. If
		 * $methodName is set, then this method will also automatically call that method and 
		 * return result of that method instead of the controller object itself. If $methodData 
		 * is also set, then this is the input variable sent to $methodName.
		 *
		 * @param string $controller name of the controller
		 * @param boolean|string $methodName name of the method called once the object is loaded
		 * @param array $methodData input variable for the method that is called
		 * @return object or mixed if function called
		 */
		final protected function getController($controller,$methodName=false,$methodData=array()){
		
			// Returning the object or result, if possible
			return $this->getMVC('controller',$controller,$methodName,$methodData);
			
		}
		
		/**
		 * This is one of the core methods of Factory class. This method is used to return a new 
		 * Wave Framework view. View name is $view and it should exist as a file in /models/ 
		 * or /overrides/models/ subfolder as view.[$view].php file. If $methodName is set, 
		 * then this method will also automatically call that method and return result of that 
		 * method instead of the view object itself. If $methodData is also set, then this is 
		 * the input variable sent to $methodName.
		 *
		 * @param string $view name of the view
		 * @param boolean|string $methodName name of the method called once the object is loaded
		 * @param boolean|string $methodData input variable for the method that is called
		 * @return object or mixed if function called
		 */
		final protected function getView($view,$methodName=false,$methodData=false){
		
			// Returning the object or result, if possible
			return $this->getMVC('view',$view,$methodName,$methodData);
			
		}
		
		/**
		 * This is one of the core methods of Factory class. This method is used to return a new 
		 * Wave Framework MVC object. Object name is $mvc and it should exist as a file in /models/, 
		 * /views/ or /controllers/ subfolders (or the /overrides/ folders). If $methodName is set, 
		 * then this method will also automatically call that method and return result of that 
		 * method instead of the object itself. If $methodData is also set, then this is the input 
		 * variable sent to $methodName.
		 *
		 * @param string $type either 'model', 'view' or 'controller'
		 * @param string $view name of the view
		 * @param boolean|string $methodName name of the method called once the object is loaded
		 * @param boolean|string $methodData input variable for the method that is called
		 * @return object or mixed if function called
		 */
		final private function getMVC($type,$mvc,$methodName=false,$methodData=false){
		
			// Dynamically creating class name
			$className='WWW_'.$type.'_'.str_replace('-','_',$mvc);
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className,false)){
				// Class file can be loaded from version or /overrides/ directories, if set
				if($this->WWW_API->requestedVersion && file_exists($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.$type.'s'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php')){
					// Requiring versioned override file
					require($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.$type.'s'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php');
				} elseif($this->WWW_API->requestedVersion && file_exists($this->WWW_API->state->data['directory-system'].$type.'s'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php')){
					// Requiring version file
					require($this->WWW_API->state->data['directory-system'].$type.'s'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php');
				} elseif(file_exists($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.$type.'s'.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.$type.'s'.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php');
				} elseif(file_exists($this->WWW_API->state->data['directory-system'].$type.'s'.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['directory-system'].$type.'s'.DIRECTORY_SEPARATOR.$type.'.'.$mvc.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error(ucfirst($type).' ['.$mvc.'] does not exist',E_USER_ERROR);
				}
			}
			
			// Object is returned if no specific method name is called
			if(!$methodName){
				// If method name was not defined then this function returns the entire class with current State and API set
				return new $className($this->WWW_API,$this->WWW_API_callIndex);
			} else {
				// Replacing dashes with underscores
				$methodName=str_replace('-','_',$methodName);
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API,$this->WWW_API_callIndex);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error(ucfirst($type).' ['.$mvc.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		/**
		 * This method loads and returns a new object for a class $className. Factory attempts 
		 * to load this class definition from a file in /resources/classes/ or in temporary folder 
		 * /overrides/resources/classes/ and file name should be written in class.[$className].php 
		 * format in that folder. If $methodName is set, then this method will also automatically 
		 * call that method and return result of that method instead of the view object itself. If 
		 * $methodData is also set, then this is the input variable sent to $methodName.
		 *
		 * @param string $className name of the class
		 * @param boolean|string $methodName name of the method called once the object is loaded
		 * @param boolean|string $methodData input variable for the method that is called
		 * @return object or mixed if function called
		 */
		final protected function getObject($className,$methodName=false,$methodData=false){
		
			// Replacing dashes with underscores
			$className=str_replace('-','_',$className);
			
			// It's made sure that the class has not already been defined
			if(!class_exists($className,false)){
				// Class file can be loaded from version or /overrides/ directories, if set
				if($this->WWW_API->requestedVersion && file_exists($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.'class.'.$className.'.php')){
					// Requiring versioned override file
					require($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.'class.'.$className.'.php');
				} elseif($this->WWW_API->requestedVersion && file_exists($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.'class.'.$className.'.php')){
					// Requiring version file
					require($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.'class.'.$className.'.php');
				} elseif(file_exists($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php');
				} elseif(file_exists($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.'.$className.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error('Class ['.$className.'] does not exist',E_USER_ERROR);
				}
			}
			
			// Object is returned if no specific method name is called
			if(!$methodName){
				// Returning object and also sending method data to the constructor
				return new $className();
			} else {
				// Replacing dashes with underscores
				$methodName=str_replace('-','_',$methodName);
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className();
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('Object ['.$className.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		/**
		 * This method is used to dynamically load a library from /resources/libaries/ subfolder. 
		 * If additional parameters are set, then this method can also automatically call one 
		 * of the functions in the file.
		 *
		 * @param string $libraryName this is the library identifying name
		 * @param boolean|string $functionName this is the name of the function that can be called after libary is loaded
		 * @param boolean|mixed $functionData this is the optional data that can be sent to the method
         * @return boolean
		 */
		final protected function loadLibrary($libraryName,$functionName=false,$functionData=false){
		
			// Making sure that the library is not already loaded
			if(!isset($WWW_Libraries[$libraryName])){
				// Library file can be loaded from version or /overrides/ directories, if set
				if($this->WWW_API->requestedVersion && file_exists($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$libraryName.'.php')){
					// Requiring versioned override file
					require($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$libraryName.'.php');
				} elseif($this->WWW_API->requestedVersion && file_exists($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$libraryName.'.php')){
					// Requiring version file
					require($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$this->WWW_API->requestedVersion.DIRECTORY_SEPARATOR.$libraryName.'.php');
				} elseif(file_exists($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$libraryName.'.php')){
					// Requiring override file
					require($this->WWW_API->state->data['directory-system'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$libraryName.'.php');
				} elseif(file_exists($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$libraryName.'.php')){
					// Requiring original file
					require($this->WWW_API->state->data['directory-system'].'resources'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$libraryName.'.php');
				} else {
					// Error is thrown if class was not found
					trigger_error('Library ['.$libraryName.'] does not exist',E_USER_ERROR);
				}
				// Setting a flag to make sure that the library is not loaded again
				$WWW_Libraries[$libraryName]=true;
			}
			
			// If function name is set to be loaded
			if($functionName){
				// Making sure that the function is actually declared
				if(function_exists($functionName)){
					return $functionName($functionData);
				} else {
					trigger_error('Library ['.$libraryName.'] function ['.$functionName.'] does not exist',E_USER_ERROR);
				}
			} else {
				return true;
			}
			
		}
		
	// IMAGER AND MINIFIER
	
		/**
		 * This method loads Imager class that is used for image editing. If $source is set, then 
		 * it also automatically assigns a source image file for that imager.
		 *
		 * @param boolean|string $source filesystem location of the image to be loaded
		 * @return object
		 */
		final protected function getImager($source=false){
		
			// Loading the Imager class
			if(!class_exists('WWW_Imager',false)){
				require($this->WWW_API->state->data['directory-system'].'engine'.DIRECTORY_SEPARATOR.'class.www-imager.php');
			}
			
			// Applying the image command son the file
			$imager=new WWW_Imager();
			if($source){
				$imager->input($source);
			}
			
			// Returning the new Imager object
			return $imager;
			
		}
		
		/**
		 * This method loads tools object if it doesn't exist yet and then allows to 
		 * call various methods of the tool. You can call filesystem cleaner, indexer
		 * or a file-size calculator (and each work recursively).
		 *
		 * @param string $type the type of tool to be loaded
		 * @param mixed $arg1 additional parameter for the tool
		 * @param mixed $arg2 additional parameter for the tool
		 * @return mixed based on the tool
		 */
		final protected function callTool($type,$arg1=false,$arg2=false){
			return $this->WWW_API->state->callTool($type,$arg1,$arg2);
		}
		
		/**
		 * This is a wrapper method for Imager class method commands(). This method allows to load 
		 * a file, edit a file and store a file in filesystem in just a single method call. $source 
		 * should be the image file in filesystem, $command is the Wave Framework styled Image 
		 * Handler accepted parameter string, like '320x240&amp;filter(grayscale)' and $target should 
		 * be the destination address for the file. If $target is missing then result is pushed to 
		 * output buffer.
		 *
		 * @param string $source source file location
		 * @param string $parameters series of commands that will be applied to the image
		 * @param boolean|string $target target file location
		 * @return boolean
		 */
		final protected function applyImager($source,$parameters,$target=false){
		
			// Loading the Imager class
			if(!class_exists('WWW_Imager',false)){
				require($this->WWW_API->state->data['directory-system'].'engine'.DIRECTORY_SEPARATOR.'class.www-imager.php');
			}
			
			// Loading Imager object
			$picture=new WWW_Imager();
			
			// Current image file is loaded into Imager
			if(!$picture->input($source)){
				trigger_error('Cannot load image from '.$source,E_USER_ERROR);
			}
			
			// Parsing through the settings
			$settings=$picture->parseParameters(explode('&',$parameters));
			
			// If settings are allowed and correct
			if($settings){
				// If the parameters are applied successfully
				if($picture->applyParameters($settings)){
					// If target is not set, then result is pushed to output buffer
					return $picture->output($target,$settings['quality'],$settings['format']);
				} else {
					return false;
				}
			} else {
				return false;
			}
						
		}
		
		/**
		 * This method is used to apply static Minifier class method to a text string. $data 
		 * should be the data string to be modified and $type should be the format type (either 
		 * xml, html, js or css). This method returns modified string that is minified by 
		 * Minifier class.
		 *
		 * @param string $data string to be minified
		 * @param string $type type of minification, either 'xml', 'html', 'js' or 'css'
		 * @return string
		 */
		final protected function applyMinifier($data,$type){
		
			// Loading the Minifier class
			if(!class_exists('WWW_Minifier',false)){
				require($this->WWW_API->state->data['directory-system'].'engine'.DIRECTORY_SEPARATOR.'class.www-minifier.php');
			}
			
			// Applying minifying based on result
			switch($type){
				case 'xml':
					return WWW_Minifier::minifyXML($data);
					break;
				case 'html':
					return WWW_Minifier::minifyHTML($data);
					break;
				case 'js':
					return WWW_Minifier::minifyJS($data);
					break;
				case 'css':
					return WWW_Minifier::minifyCSS($data);
					break;
				default:
					// Error is thrown for unsupported types
					trigger_error('This type ['.$type.'] is not supported by minifier',E_USER_ERROR);
				
			}
			
		}
		
	// CUSTOM RETURNED ARRAYS
		
		/**
		 * Wave Framework MVC object method calls can return their data in whatever format 
		 * developer finds necessary, but it is recommended to return data in a standardized 
		 * form. This method returns an array in the format of an error. $message should be 
		 * the verbose message returned, $customData should be an array of data returned as 
		 * part of the result and $responseCode should be an response code in 3XX namespace, 
		 * which is used for errors in Wave Framework.
		 *
		 * @param string $message message to be returned
		 * @param boolean|array $customData additional data returned
		 * @param integer $responseCode response code number
		 * @return array
		 */
		final protected function resultError($message='Error',$customData=false,$responseCode=300){
		
			// Custom data is merged to the results array, if sent
			if(is_array($customData)){
				return array('www-message'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-message'=>$message,'www-response-code'=>$responseCode);
			}
			
		}
		
		/**
		 * Wave Framework MVC object method calls can return their data in whatever format 
		 * developer finds necessary, but it is recommended to return data in a standardized 
		 * form. This method returns an array in the format of a false result, which should be 
		 * used when an action failed but is not considered an error. $message should be the 
		 * verbose message returned, $customData should be an array of data returned as part 
		 * of the result and $responseCode should be a response code in 4XX namespace, which 
		 * is used for false results in Wave Framework.
		 *
		 * @param string $message message to be returned
		 * @param boolean|array $customData additional data returned
		 * @param integer $responseCode response code number
		 * @return array
		 */
		final protected function resultFalse($message='OK',$customData=false,$responseCode=400){
		
			// Custom data is merged to the results array, if sent
			if(is_array($customData)){
				return array('www-message'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-message'=>$message,'www-response-code'=>$responseCode);
			}
			
		}
		
		/**
		 * Wave Framework MVC object method calls can return their data in whatever format 
		 * developer finds necessary, but it is recommended to return data in a standardized 
		 * form. This method returns an array in the format of a successful result. $message 
		 * should be the verbose message returned, $customData should be an array of data 
		 * returned as part of the result and $responseCode should be an response code in 
		 * 5XX namespace, which is used for successful results in Wave Framework.
		 *
		 * @param string $message message to be returned
		 * @param boolean|array $customData additional data returned
		 * @param integer $responseCode response code number
		 * @return array
		 */
		final protected function resultTrue($message='OK',$customData=false,$responseCode=500){
		
			// Custom data is merged to the results array, if sent
			if(is_array($customData)){
				return array('www-message'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-message'=>$message,'www-response-code'=>$responseCode);
			}
			
		}
		
		/**
		 * This allows to simply return a string from an API. It sets text headers in the response.
		 *
		 * @param string $stream text string to be returned
		 * @return void
		 */
		final protected function resultStream($stream){
			return $this->WWW_API->resultStream($stream);
		}
		
		/**
		 * This returns contents from a file as a response to API request. This can be used
		 * for file downloads without revealing the actual file path in filesystem.
		 *
		 * @param string $location file location in filesystem
		 * @param boolean|string $name name of the downloadable file, by default the name of the actual file
		 * @param boolean|string $contentType this is set as a content type string in the response
		 * @return mixed
		 */
		final protected function resultFile($location,$name=false,$contentType=false){
			return $this->WWW_API->resultFile($location,$name,$contentType);
		}
		
		/**
		 * This method is used to check for standardized API call result and determine if the 
		 * API call was successful or not. It essentially checks for response codes in the array.
		 * $data is the result data array that is checked.
		 *
		 * @param array|mixed $data result data array that is checked
		 * @return boolean or mixed if non-standard array
		 */
		final protected function checkTrueFalse($data){
		
			// These values are only checked from an array
			if(is_array($data)){
				// 4XX response code namespace is for 'false' results, 5XX namespace is for true results
				if(isset($data['www-response-code']) && $data['www-response-code']<500){
					return false;
				} elseif(isset($data['www-response-code']) && $data['www-response-code']>=500){
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
		
		/**
		 * This method is used to check for standardized API call result and determine if the 
		 * API call had an error. It essentially checks for response codes in the array.
		 * $data is the result data array that is checked.
		 *
		 * @param array|mixed $data result data array that is checked
		 * @return boolean
		 */
		final protected function checkError($data){
		
			// These values are only checked from an array
			if(is_array($data)){
				// Error messages have response code namespace of 1XX, 2XX and 3XX (latter is developer defined)
				if(isset($data['www-response-code']) && $data['www-response-code']<400){
					return true;
				}
				// Since response code was not found, system returns false, as standardized error was not found
				return false;
			} else {
				// Since result was not an array, system returns false, as standardized error was not found
				return false;
			}
			
		}
		
	// CACHE
		
		/**
		 * This method will tell Wave Framework API not to cache current API call, no matter 
		 * what. This means that while current API call may be loaded from cache, but in case 
		 * the cache does not exist the result will not be written to cache.
		 *
		 * @param boolean $state true or false flag for whether cache is disabled or not
		 * @return boolean
		 */
		final protected function disableCache($state=true){
		
			// This is stored as a flag
			$this->WWW_API->noCache[$this->WWW_API_callIndex]=$state;
			return true;
			
		}
		
		/**
		 * This method unsets all cache that has been stored with a specific tag keyword. 
		 * $tags variable can both be a string or an array of keywords. Every cache related 
		 * to those keywords will be removed.
		 *
		 * @param string|array $tags an array or comma separated list of tags that the cache was stored under
		 * @return boolean
		 */
		final protected function unsetTaggedCache($tags){
			return $this->WWW_API->unsetTaggedCache($tags);
		}
		
		/**
		 * This method returns currently existing cache for currently executed API call, if it 
		 * exists. This allows you to always load cache from system in case a new response cannot 
		 * be generated. It returns cache with the key $key.
		 *
		 * @return mixed depending if cache is found, false if failed
		 */
		final protected function getExistingCache(){
			return $this->WWW_API->getExistingCache($this->WWW_API_callIndex);
		}
		
		/**
		 * If cache exists for currently executed API call, then this method returns the UNIX 
		 * timestamp of the time when that cache was written. It returns cache timestamp with 
		 * the key $key.
		 *
		 * @return integer or false, if timestamp does not exist
		 */
		final protected function getExistingCacheTime(){
			return $this->WWW_API->getExistingCacheTime($this->WWW_API_callIndex);
		}
		
		/**
		 * This method can be used to store cache for whatever needs by storing $key and 
		 * giving it a value of $value. Cache tagging can also be used with custom tag by 
		 * sending a keyword with $tags or an array of keywords.
		 * 
		 * @param string $key unique cache URL, name or key
		 * @param mixed $value variable value to be stored
         * @param boolean|array|string $tags tags array or comma-separated list of tags to attach to cache
		 * @return boolean
		 */
		final protected function setCache($key,$value,$tags=false){
			return $this->WWW_API->setCache($key,$value,$tags,true);
		}
		
		/**
		 * This method fetches data from cache based on cache keyword $keyAddress, if cache exists. 
		 * This should be the same keyword that was used in setCache() method, when storing cache.
		 *
		 * @param string $key unique cache URL, name or key
		 * @param boolean|integer $limit this is timestamp after which cache won't result an accepted value
		 * @return mixed
		 */
		final protected function getCache($key,$limit=false){
			return $this->WWW_API->getCache($key,$limit,true);
		}
		
		/**
		 * This function returns the timestamp of when the cache of keyword $keyAddress, was created, 
		 * if such a cache exists.
		 *
		 * @param string $key unique cache URL, name or key
		 * @return integer or false if cache is not found
		 */
		final protected function cacheTime($key){
			return $this->WWW_API->cacheTime($key,true);
		}
		
		/**
		 * This method removes cache that was stored with the keyword $keyAddress, if such a cache exists.
		 *
		 * @param string $key unique cache URL, name or key
		 * @return boolean
		 */
		final protected function unsetCache($key){
			return $this->WWW_API->unsetCache($key,true);
		}
		
	// ENCRYPTION AND DECRYPTION
	
		/**
		 * This method uses API class internal encryption function to encrypt $data string with 
		 * a key and a secret key (if set). If only $key is set, then ECB mode is used for 
		 * Rijndael encryption.
		 *
		 * @param string $data data to be encrypted
		 * @param string $key key used for encryption
		 * @param boolean|string $secretKey used for calculating initialization vector (IV)
		 * @return string
		 */
		final protected function encryptData($data,$key,$secretKey=false){
			return $this->WWW_API->encryptData($data,$key,$secretKey);
		}
		
		/**
		 * This will decrypt Rijndael encoded data string, set with $data. $key and $secretKey 
		 * should be the same that they were when the data was encrypted.
		 *
		 * @param string $data data to be decrypted
		 * @param string $key key used for decryption
		 * @param boolean|string $secretKey used for calculating initialization vector (IV)
		 * @return string
		 */
		final protected function decryptData($data,$key,$secretKey=false){
			return $this->WWW_API->decryptData($data,$key,$secretKey);
		}		
		
	// INTERNAL LOG ENTRY WRAPPER
	
		/**
		 * This method attempts to write an entry to internal log. Log entry is stored with 
		 * a $key and entry itself should be the $data. $key is needed to easily find the 
		 * log entry later on.
		 *
		 * @param string $key descriptive key that the log entry will be stored under
		 * @param mixed $data data entered in log
		 * @return boolean
		 */
		final protected function logEntry($key,$data=false){
			return $this->WWW_API->logEntry($key,$data);
		}
		
		/**
		 * This method is a timer that can be used to grade performance within the system. 
		 * When this method is called with some $key first, it will start the timer and write 
		 * an entry to log about it. If the same $key is called again, then a log entry is 
		 * created with the amount of microseconds that have passed since the last time this 
		 * method was called with this $key.
		 *
		 * @param string $key identifier for splitTime group
		 * @return float 
		 */
		final protected function splitTime($key='api'){
			return $this->WWW_API->splitTime($key);
		}
		
	// STATE MESSENGER WRAPPERS
	
		/**
		 * This method initializes State messenger by giving it an address and assigning the file 
		 * that State messenger will be stored under. If the file already exists and $overwrite is 
		 * not turned on, then it automatically loads contents of that file from filesystem.
		 *
		 * @param string $address key that messenger data will be saved under
		 * @param boolean $overwrite if this is set then existing state messenger file will be overwritten
		 * @return boolean
		 */
		final protected function stateMessenger($address,$overwrite=false){
			return $this->WWW_API->state->stateMessenger($address,$overwrite);
		}
		
		/**
		 * This writes data to State messenger. $data is the key and $value is the value of the 
		 * key. $data can also be an array of keys and values, in which case multiple values are 
		 * set at the same time. Additionally using this function also turns off caching of the 
		 * page which uses it.
		 *
		 * @param array|string $key key or data array
		 * @param mixed $value value, if data is a key
		 * @return boolean
		 */
		final protected function setMessengerData($key,$value=false){
		
			// Attempting to get the result
			$result=$this->WWW_API->state->setMessengerData($key,$value);
			if($result){
				// Setting no-cache flag to true
				$this->WWW_API->noCache[$this->WWW_API_callIndex]=true;
				// Returning the result
				return $result;
			}
			return false;
			
		}
		
		/**
		 * This method removes key from State messenger based on value of $key. If $key is not 
		 * set, then the entire State messenger data is cleared.
		 *
		 * @param boolean|string $key key that will be removed, if set to false then removes the entire data
		 * @return boolean
		 */
		final protected function unsetMessengerData($key=false){
			return $this->WWW_API->state->unsetMessengerData($key);
		}
		
		/**
		 * This method returns data from State messenger. It either returns all the data from 
		 * initialized state messenger, or just a $key from it. If $remove is set, then data is 
		 * also set for deletion after it has been accessed. Additionally using this function 
		 * also turns off caching of the page which uses it.
		 *
		 * @param boolean|string $key messenger address
		 * @param boolean|boolean $remove true or false flag whether to delete the request data after returning it
		 * @return mixed or false if failed
		 */
		final protected function getMessengerData($key=false,$remove=true){
		
			// Attempting to get the result
			$result=$this->WWW_API->state->getMessengerData($key,$remove);
			if($result){
				// Setting no-cache flag to true
				$this->WWW_API->noCache[$this->WWW_API_callIndex]=true;
				// Returning the result
				return $result;
			}
			return false;
			
		}
	
	// SESSION AND COOKIE WRAPPERS
	
		/**
		 * This method validates current session data and checks for lifetime as well as 
		 * session fingerprint. It notifies session handler if any changes have to be made.
         * This is a list of configuration options in the configuration array:
         *  'name' - session cookie name
         *  'lifetime' - session cookie lifetime
         *  'path' - URL path for session cookie
         *  'domain' - domain for session cookie
         *  'secure' - whether session cookie is for secure connections only
         *  'http-only' - whether session cookie is for HTTP requests only and unavailable for scripts
         *  'user-key' - session key for storing user data
         *  'permissions-key' - session key for storing user permissions
         *  'fingerprint-key' - session key for storing session fingerprint
         *  'timestamp-key' - session key for storing session timestamp
         *  'token-key' - session key for public request tokens for protecting against replay attacks
         *  'fingerprint' - session key for session fingerprinting for protecting against replay attacks
		 *
		 * @param boolean|array $configuration list of session configuration options, keys below
		 * @return boolean
		 */
		final protected function startSession($configuration=false){
			return $this->WWW_API->state->startSession($configuration);
		}
	
		/**
		 * This method notifies the session handler that the session data should be
		 * stored under a new ID.
		 *
		 * @param boolean $regenerate to regenerate or not
		 * @param boolean $deleteOld deletes the previous one, if set to true
		 * @return boolean
		 */
		final protected function regenerateSession($regenerate=true,$deleteOld=true){
			return $this->WWW_API->state->regenerateSession($regenerate,$deleteOld);
		}
	
		/**
		 * This method sets a session variable $key with a $value. If $key is an array of 
		 * keys and values, then multiple session variables are set at once.
		 *
		 * @param array|string $key key of the variable or an array of keys and values
		 * @param mixed $value value to be set
		 * @return boolean
		 */
		final protected function setSession($key,$value){
			return $this->WWW_API->state->setSession($key,$value);
		}
		
		/**
		 * This method returns $key value from session data. If $key is an array of keys, then 
		 * it can return multiple variables from session at once. If $key is not set, then entire 
		 * session array is returned.
		 *
		 * @param boolean|string|array $key key to return or an array of keys
		 * @return mixed
		 */
		final protected function getSession($key=false){
			return $this->WWW_API->state->getSession($key);
		}
		
		/**
		 * This method unsets $key value from current session. If $key is an array of keys, then 
		 * multiple variables can be unset at once. If $key is not set at all, then this simply 
		 * destroys the entire session.
		 *
		 * @param boolean|string|array $key key of the value to be unset, or an array of keys
		 * @return boolean
		 */
		final protected function unsetSession($key=false){
			return $this->WWW_API->state->unsetSession($key);
		}
		
		/**
		 * This method sets a cookie with $key and a $value. $configuration is an array of 
		 * cookie parameters that can be set.
		 *
		 * @param string|array $key key of the variable, or an array of keys and values
		 * @param string|array $value value to be set, can also be an array
		 * @param array $configuration cookie configuration options, list of keys below
		 *  'expire' - timestamp when the cookie is set to expire
		 *  'timeout' - alternative to 'expire', this sets how long the cookie can survive
		 *  'path' - cookie limited to URL path
		 *  'domain' - cookie limited to domain
		 *  'secure' - whether cookie is for secure connections only
		 *  'http-only' - if the cookie is only available for HTTP requests
		 * @return boolean
		 */
		final protected function setCookie($key,$value,$configuration=array()){
			return $this->WWW_API->state->setCookie($key,$value,$configuration);
		}
		
		/**
		 * This method returns a cookie value with the set $key. $key can also be an array of 
		 * keys, in which case multiple cookie values are returned in an array.
		 *
		 * @param string $key key of the value to be returned, can be an array
		 * @return mixed
		 */
		final protected function getCookie($key){
			return $this->WWW_API->state->getCookie($key);
		}
		
		/**
		 * This method unsets a cookie with the set key of $key. If $key is an array, then 
		 * it can remove multiple cookies at once.
		 *
		 * @param string|array $key key of the value to be unset or an array of keys
		 * @return boolean
		 */
		final protected function unsetCookie($key){
			return $this->WWW_API->state->unsetCookie($key);
		}
		
	// SESSION USER AND PERMISSIONS
	
		/**
		 * This method sets user data array in session. This is a simple helper function used 
		 * for holding user-specific data for a web service. $data is an array of user data.
		 *
		 * @param array $data data array set to user
		 * @return boolean
		 */
		final protected function setUser($data){
			return $this->WWW_API->state->setUser($data);
		}
		
		/**
		 * This either returns the entire user data array or just a specific $key of user data 
		 * from the session.
		 *
		 * @param boolean|string $key element returned from user data, if not set then returns the entire user data
		 * @return mixed
		 */
		final protected function getUser($key=false){
			return $this->WWW_API->state->getUser($key);
		}
		
		/**
		 * This unsets user data and removes the session of user data.
		 *
		 * @return boolean
		 */
		final protected function unsetUser(){
			return $this->WWW_API->state->unsetUser();
		}
	
		/**
		 * This checks for an existence of permissions in the user permissions session array.
		 * $permissions is either a comma-separated string of permissions to be checked, or an 
		 * array. This method returns false when one of those permission keys is not set in the
		 * permissions session. Method returns true, if $permissions exist in the permissions 
		 * session array. If $trueNonPublic is set, then non-public-profiles will always return
		 * true with this call, since API profile should already be considered as validated.
		 *
		 * @param string|array $permissions comma-separated string or an array that is checked against permissions array
		 * @param boolean $trueNonPublic whether non-public API profiles always return true for permission checks
		 * @return boolean
		 */
		final protected function checkPermissions($permissions,$trueNonPublic=true){
			return $this->WWW_API->state->checkPermissions($permissions,$trueNonPublic);
		}
		
		/**
		 * This method returns an array of currently set user permissions from the session.
		 *
		 * @return array
		 */
		final protected function getPermissions(){
			return $this->WWW_API->state->getPermissions();
		}
		
		/**
		 * This method sets an array of $permissions or a comma-separated string of permissions 
		 * for the current user permissions session.
		 *
		 * @param array|string $permissions an array or a string of permissions
		 * @return boolean
		 */
		final protected function setPermissions($permissions){
			return $this->WWW_API->state->setPermissions($permissions);
		}
		
		/**
		 * This unsets permissions data from session similarly to how unsetUser() method unsets 
		 * user data from session.
		 *
		 * @return boolean
		 */
		final protected function unsetPermissions(){
			return $this->WWW_API->state->unsetPermissions();
		}
		
		/**
		 * This checks whether developer IP and user agent access the system. This is a light check 
		 * that can be used to test out features for developers while other IP's and user agents are 
		 * not affected. It returns true if developer browser and IP is detected.
		 *
		 * @return boolean
		 */
		final protected function isDeveloper(){
			return $this->WWW_API->state->data['developer'];
		}
		
		/**
		 * This method returns the currently active public token that is used to increase security 
		 * against cross-site-request-forgery attacks. This method returns false if user session 
		 * is not populated, in which case public token is not needed. $regenerate sets if the token 
		 * should be regenerated if it already exists, this invalidates forms when Back button is 
		 * used after submitting data, but is more secure. $forced is used to force token generation 
		 * even if no user session is active.
		 *
		 * @param boolean $regenerate if public token should be regenerated
		 * @param boolean $forced if token is generated even when there is no actual user session active
		 * @return string or boolean if no user session active
		 */
		final protected function getPublicToken($regenerate=false,$forced=false){
			return $this->WWW_API->state->getPublicToken($regenerate,$forced);
		}
		
		/**
		 * This method is useful when 'api-public-token' setting is off in configuration, but you
		 * still want to protect your API method from public API requests from XSS and other attacks.
		 * This returns false if the provided public API token is incorrect.
		 *
		 * @return boolean
		 */
		final protected function checkPublicToken(){
			return $this->WWW_API->state->checkPublicToken();
		}
		
	// DATABASE WRAPPERS
	
		/**
		 * This method can be used to dynamically create a new database connection object from 
		 * Database class. $type is the database type, $host, $database, $username, $password 
		 * are database connection credentials. And $showErrors defines whether errors should 
		 * be thrown if encountered and $persistentConnection sets whether connection should be 
		 * reused, if such already exists.
		 *
		 * @param string $type database type
		 * @param string $host database host
		 * @param string $database database name
		 * @param string $username database username
		 * @param string $password database password
		 * @param boolean $showErrors whether to show errors
		 * @param boolean $persistentConnection whether connection is assigned to be permanent
		 * @return object
		 */
		final protected function dbNew($type,$host,$database,$username,$password,$showErrors=false,$persistentConnection=false){
		
			// Requiring database class files, if class has not been defined
			if(!class_exists('WWW_Database',false)){
				// Including the required class and creating the object
				require($this->WWW_API->state['directory-system'].'engine'.DIRECTORY_SEPARATOR.'class.www-database.php');
			}
			$databaseConnection=new WWW_Database($type,$host,$database,$username,$password,$showErrors,$persistentConnection);
			// Passing the database to State object
			return $databaseConnection;
			
		}
			
		/**
		 * This sends query information to PDO. $queryString is the query string and $variables 
		 * is an array of variables sent with the request. Question marks (?) in $queryString 
		 * will be replaced by values from $variables array for PDO prepared statements. This 
		 * method returns the first row of the matching result, or it returns false, if the query 
		 * failed. This method is mostly meant for SELECT queries that return a single row.
		 *
		 * @param string $query query string, a statement to prepare with PDO
		 * @param array $variables array of variables to use in prepared statement
		 * @return array|boolean
		 */
		final protected function dbSingle($query,$variables=array()){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbSingle($query,$variables);
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This sends query information to PDO. $queryString is the query string and $variables 
		 * is an array of variables sent with the request. Question marks (?) in $queryString 
		 * will be replaced by values from $variables array for PDO prepared statements. This 
		 * method returns an array where each key is one returned row from the database, or it 
		 * returns false, if the query failed. This method is mostly meant for SELECT queries 
		 * that return multiple rows.
		 *
		 * @param string $query query string, a statement to prepare with PDO
		 * @param array $variables array of variables to use in prepared statement
		 * @return array or false if failed
		 */
		final protected function dbMultiple($query,$variables=array()){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbMultiple($query,$variables);
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This sends query information to PDO. $queryString is the query string and $variables 
		 * is an array of variables sent with the request. Question marks (?) in $queryString 
		 * will be replaced by values from $variables array for PDO prepared statements. This 
		 * method only returns the number of rows affected or true or false, depending whether 
		 * the query was successful or not. This method is mostly meant for INSERT, UPDATE and 
		 * DELETE type of queries.
		 *
		 * @param string $query query string, a statement to prepare with PDO
		 * @param array $variables array of variables to use in prepared statement
		 * @return boolean or integer of affected rows
		 */
		final protected function dbCommand($query,$variables=array()){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbCommand($query,$variables);
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This method attempts to return the last ID (a primary key) that was inserted to the 
		 * database. If one was not found, then the method returns false.
		 * 
		 * @return integer or false if not found
		 */
		final protected function dbLastId(){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbLastId();
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This method begins a database transaction, if the database type supports transactions. 
		 * If this process fails, then method returns false, otherwise it returns true.
		 *
		 * @return boolean
		 */
		final protected function dbTransaction(){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbTransaction();
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This method cancels all the queries that have been sent since the transaction was 
		 * started with dbTransaction(). If rollback is successful, then method returns true, 
		 * otherwise returns false.
		 *
		 * @return boolean
		 */
		final protected function dbRollback(){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbRollback();
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This commits all the queries sent to database after transactions were started. If 
		 * commit fails, then it returns false, otherwise this method returns true.
		 *
		 * @return boolean
		 */
		final protected function dbCommit(){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbCommit();
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This is a database helper function that can be used to escape specific variables if 
		 * that variable is not sent with as part of variables array and is written in the 
		 * query string instead. $value is the variable value that will be escaped or modified. 
		 * $type is the type of escaping and modifying that takes place. This can be 'escape' 
		 * (which just applies regular PDO quote to the variable) or 'like', which does the same
		 * as escape, but also escapes wildcard characters '_' and '%'.
		 *
		 * @param string $value input value
		 * @param string $type Method of quoting, either 'escape', 'integer', 'alpha', 'field' or 'like'
		 * @param boolean $stripQuotes whether the resulting quotes will be stripped from the string, if they get set
		 * @return string
		 */
		final protected function dbQuote($value,$type='escape',$stripQuotes=false){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbQuote($value,$type,$stripQuotes);
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This is a database helper method, that simply creates an array of database result array 
		 * (like the one returned by dbMultiple). It takes the database result array and collects 
		 * all $key values from that array into a new, separate array. If $unique is set, then it 
		 * only returns unique keys.
		 *
		 * @param array $array array to filter from
		 * @param string $key key to return
		 * @param boolean $unique if returned array should only have only unique values
		 * @return array or mixed if source is not an array
		 */
		final protected function dbArray($array,$key,$unique=false){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbArray($array,$key,$unique);
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This method attempts to simulate the query that PDO builds when it makes a request to 
		 * database. $query should be the query string and $variables should be the array of 
		 * variables, like the ones sent to dbSingle(), dbMultiple() and dbCommand() requests. 
		 * It returns a prepared query string.
		 *
		 * @param string $query query string
		 * @param array $variables values sent to PDO
		 * @return string
		 */
		final protected function dbDebug($query,$variables=array()){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->dbDebug($query,$variables);
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
		/**
		 * This method returns the PDO class that API is currently using, if one is active.
		 *
		 * @return object
		 */
		final protected function dbPDO(){
			// If database configuration is not set then databaseConnection is not defined
			if($this->WWW_API->state->databaseConnection){
				return $this->WWW_API->state->databaseConnection->pdo;
			} else {
				trigger_error('Database is not used, configuration missing',E_USER_WARNING);
				return false;
			}
		}
		
	// HEADERS
		
		/**
		 * This method adds a header to the array of headers to be added before data is pushed 
		 * to the client, when headers are sent. $header is the header string to add and $replace 
		 * is a true/false setting for whether previously sent header like this is replaced or not.
		 *
		 * @param string|array $header header string to add or an array of header strings
		 * @param boolean $replace whether the header should be replaced, if previously set
		 * @return boolean
		 */
		final protected function setHeader($header,$replace=true){
			return $this->WWW_API->state->setHeader($header,$replace);
		}
	
		/**
		 * This method adds a header to the array of headers to be removed before data is pushed 
		 * to the client, when headers are sent. $header is the header string to remove.
		 *
		 * @param string|array $header header string to add or an array of header strings
		 * @return boolean
		 */
		final protected function unsetHeader($header){
			return $this->WWW_API->state->unsetHeader($header);
		}
		
	// TERMINAL
	
		/**
		 * This method is wrapper function for making terminal calls. It attempts to detect 
		 * what terminal is available on the system, if any, and then execute the call and 
		 * return the results of the call.
		 *
		 * @param string $command command to be executed
		 * return mixed
		 */
		final protected function terminal($command){
			return $this->WWW_API->state->terminal($command);			
		}
		
	// DATA HANDLING
	
		/**
		 * This method simply filters a string and returns the filtered string. Various exception 
		 * characters can be set in $exceptions string and these will not be filtered. You can set
		 * the type to 'integer', 'float', 'numeric', 'alpha' or 'alphanumeric'.
		 *
		 * @param string $string value to be filtered
		 * @param string $type filtering type, can be 'integer', 'float', 'numeric', 'alpha' or 'alphanumeric'
		 * @param boolean|string $exceptions is a string of all characters used as exceptions
		 * @return string
		 */
		final protected function filter($string,$type,$exceptions=false){
			return $this->WWW_API->filter($string,$type,$exceptions);
		}

}
	
?>