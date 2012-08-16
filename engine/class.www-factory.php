<?php

/*
Wave Framework
Factory class

Factory class is the base class for a lot of Wave Framework dynamically loaded model, view 
and controller objects. It is not required to build your MVC objects by extending Factory 
Class, but doing so can make the development of these objects easier, as Factory also has 
wrappers to State, Wave Framework API and other functionality.

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Factory {

	// This variable holds the API object that is currently used by Wave Framework and 
	// Factory. API object is the core engine of Wave Framework.
	private $WWW_API=false;
	
	// This is an internal variable that holds a counter of how many times the API has been 
	// called.  It is possible to call API calls within other API calls, so this is used to 
	// keep track of how deep the current API call is, which is used to calculate buffering 
	// layers and other internal functionality in API class.
	private $WWW_API_callIndex=0;

	// Construction method of Factory class initializes currently used API and API call index
	// settings within the new object that is extended from Factory. This method will also call
	// __initialize() method, if such a method is set in the class that is extended from 
	// Factory, as it is not allowed to overwrite __construct class itself.
	// * api - WWW_API object
	// * callIndex - Current level of depth in the API call
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
	
		// This method is used by objects, that have been extended from Factory class, to make 
		// new API calls to Wave Framework. It takes a 'www-command' value in $command variable 
		// and an array of key and value pairs in $inputData variable that is used as input data. 
		// If $useBuffer is set to true, then API is told that it can use an internal API buffer
		// call, which means that if API has already gotten the same exact API call with the 
		// same exact input variables, then it returns the result from buffer directly rather 
		// than execute it again.
		// * command - Command string to be processed, just like the one accepted by WWW_API object
		// * inputData - Key and value pairs of input data
		// * useBuffer - This tells API to use buffer (returns data from memory for same calls)
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
			// Returning the result from API
			return $this->WWW_API->command($inputData,$useBuffer,false);
		}
		
		// This method is used to initialize PHP API Wrapper object. This object can be used to 
		// send API calls to other API systems, that have been built on Wave Framework, over 
		// HTTP. $address is the API address URL that Wave Framework connects with. This method 
		// returns a new API Wrapper object.
		// * address - Address of the API file
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
	
		// This method is used to clear current API command buffer. This is an optimization 
		// method and should be used only of a lot of API calls are made that might fill the 
		// memory allocated to PHP. What this method does is that it tells API object to empty 
		// the internal variable that stores the results of all API calls that have already been 
		// sent to API.
		final protected function clearBuffer(){
			$this->WWW_API->clearBuffer();
		}
	
	// STATE DATA SET AND GET
	
		// This is a wrapper method that requests data from currently used State object. $variable 
		// is the key that is requested and $subVariable is the key of an array, if the requested
		// variable is an array.
		// * variable - variable name of State
		// * subVariable - subvariable of State
		final protected function getState($variable=false,$subVariable=false){
			return $this->WWW_API->state->getState($variable,$subVariable);
		}
		
		// This method sets a state variable of currently used State object in the API. $variable
		// should be the key that is set and $value is the value of that variable.
		// * variable - Variable to be set
		// * value - Value to be set
		final protected function setState($variable,$value=true){
			return $this->WWW_API->state->setState($variable,$value);
		}
		
		// This wrapper method is used to return either currently active language translations 
		// array or translations of the language with keyword $language. If $keyword is set, 
		// then this asks for a specific translation within the translations array.
		// * language - Language keyword, if this is not set then returns current language translations
		// * keyword - If only single keyword needs to be returned
		final protected function getTranslations($language=false,$keyword=false){
			return $this->WWW_API->state->getTranslations($language,$keyword);
		}
		
		// This wrapper method is used to return either currently active language sitemap array 
		// or sitemap of the language with keyword $language. If $keyword is set, then this asks 
		// for a specific sitemap node within the sitemap array.
		// * language - Language keyword, if this is not set then returns current language sitemap
		// * keyword - If only a single URL node needs to be returned
		final protected function getSitemap($language=false,$keyword=false){
			return $this->WWW_API->state->getSitemap($language,$keyword);
		}
		
		// This wrapper method is used to return either currently active language raw sitemap 
		// array or raw sitemap of the language with keyword $language. If $keyword is set, then 
		// this asks for a specific raw sitemap node within the raw sitemap array. This method
		// returns the original, non-modified sitemap that has not been parsed for use with URL
		// controller.
		// * language - Language keyword, if this is not set then returns current language sitemap
		// * keyword - If only a single URL node needs to be returned
		final protected function getSitemapRaw($language=false,$keyword=false){
			return $this->WWW_API->state->getSitemapRaw($language,$keyword);
		}
	
	// MVC FACTORY
	
		// This is one of the core methods of Factory class. This method is used to return a new 
		// Wave Framework model. Model name is $model and it should exist as a file in /models/ 
		// or /overrides/models/ subfolder as model.[$model].php file. If $methodName is set, 
		// then this method will also automatically call that method and return result of that 
		// method instead of the model object itself. If $methodData is also set, then this is 
		// the input variable sent to $methodName.
		// * model - Name of the model
		// * methodName - Name of the method called once the object is loaded
		// * methodData - Input variable for the method that is called
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
				return new $className($this->WWW_API,$this->WWW_API_callIndex);
			} else {
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API,$this->WWW_API_callIndex);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('Model ['.$model.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		// This is one of the core methods of Factory class. This method is used to return a new 
		// Wave Framework controller. Controller name is $controller and it should exist as a file 
		// in /models/ or /overrides/models/ subfolder as controller.[$controller].php file. If
		// $methodName is set, then this method will also automatically call that method and 
		// return result of that method instead of the controller object itself. If $methodData 
		// is also set, then this is the input variable sent to $methodName.
		// * controller - Name of the controller
		// * methodName - Name of the method called once the object is loaded
		// * methodData - Input variable for the method that is called
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
				return new $className($this->WWW_API,$this->WWW_API_callIndex);
			} else {
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API,$this->WWW_API_callIndex);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('Controller ['.$controller.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		// This is one of the core methods of Factory class. This method is used to return a new 
		// Wave Framework view. View name is $view and it should exist as a file in /models/ 
		// or /overrides/models/ subfolder as view.[$view].php file. If $methodName is set, 
		// then this method will also automatically call that method and return result of that 
		// method instead of the view object itself. If $methodData is also set, then this is 
		// the input variable sent to $methodName.
		// * view - Name of the view
		// * methodName - Name of the method called once the object is loaded
		// * methodData - Input variable for the method that is called
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
				return new $className($this->WWW_API,$this->WWW_API_callIndex);
			} else {
				// If method name was set, then this function creates a new temporary object
				$tempObject=new $className($this->WWW_API,$this->WWW_API_callIndex);
				// If method exists, then the result of this method is returned as a result
				if(method_exists($tempObject,$methodName)){
					return $tempObject->$methodName($methodData);
				} else {
					// Error is thrown if method was not found
					trigger_error('View ['.$view.'] method ['.$methodName.'] does not exist',E_USER_ERROR);
				}
			}
			
		}
		
		// This method loads and returns a new object for a class $className. Factory attempts 
		// to load this class definition from a file in /resources/classes/ or in temporary folder 
		// /overrides/resources/classes/ and file name should be written in class.[$className].php 
		// format in that folder. If $methodName is set, then this method will also automatically 
		// call that method and return result of that method instead of the view object itself. If 
		// $methodData is also set, then this is the input variable sent to $methodName.
		// * className - Class name
		// * methodName - Name of the method called once the object is loaded
		// * methodData - Input variable for the method that is called
		final protected function getObject($className,$methodName=false,$methodData=array()){
			
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
			
			// Object is returned if no specific method name is called
			if(!$methodName){
				// Returning object
				return new $className();
			} else {
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
		
	// IMAGER AND MINIFIER
	
		// This method loads Imager class that is used for image editing. If $source is set, then 
		// it also automatically assigns a source image file for that imager.
		// * source - Source image to be loaded
		final protected function getImager($source=false){
		
			// Loading the Imager class
			if(!class_exists('WWW_Imager')){
				require($this->WWW_API->state->data['system-root'].'engine'.DIRECTORY_SEPARATOR.'class.www-imager.php');
			}
			
			// Applying the image command son the file
			$imager=new WWW_Imager();
			if($source){
				$imager->input($source);
			}
			
			// Returning the new Imager object
			return $imager;
			
		}
		
		// This is a wrapper method for Imager class method commands(). This method allows to load 
		// a file, edit a file and store a file in filesystem in just a single method call. $source 
		// should be the image file in filesystem, $command is the Wave Framework styled Image 
		// Handler accepted parameter string, like '320x240&amp;filter(grayscale)' and $target should 
		// be the destination address for the file. If $target is missing then result is pushed to 
		// output buffer.
		// * source - Source file location
		// * command - Series of commands that will be applied to the image
		// * target - Target file location
		final protected function applyImager($source,$command,$target=false){
		
			// Loading the Imager class
			if(!class_exists('WWW_Imager')){
				require($this->WWW_API->state->data['system-root'].'engine'.DIRECTORY_SEPARATOR.'class.www-imager.php');
			}
			
			// Loading Imager object
			$picture=new WWW_Imager();
			
			// Applying the image commands to the picture
			return $picture->commands($source,$command,$target);
			
		}
		
		// This method is used to apply static Minifier class method to a text string. $data 
		// should be the data string to be modified and $type should be the format type (either 
		// xml, html, js or css). This method returns modified string that is minified by 
		// Minifier class.
		// * data - String to be minified
		// * type - Type of minification, either xml, html, js or css
		final protected function applyMinifier($data,$type){
		
			// Loading the Minifier class
			if(!class_exists('WWW_Minifier')){
				require($this->WWW_API->state->data['system-root'].'engine'.DIRECTORY_SEPARATOR.'class.www-minifier.php');
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
		
		// Wave Framework MVC object method calls can return their data in whatever format 
		// developer finds necessary, but it is recommended to return data in a standardized 
		// form. This method returns an array in the format of an error. $message should be 
		// the verbose message returned, $customData should be an array of data returned as 
		// part of the result and $responseCode should be an response code in 3XX namespace, 
		// which is used for errors in Wave Framework.
		// * message - Error message
		// * customData - Additional data returned
		// * responseCode - Error code number
		final protected function resultError($message='Error',$customData=false,$responseCode=300){
			if(is_array($customData)){
				return array('www-message'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-message'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
		// Wave Framework MVC object method calls can return their data in whatever format 
		// developer finds necessary, but it is recommended to return data in a standardized 
		// form. This method returns an array in the format of a false result, which should be 
		// used when an action failed but is not considered an error. $message should be the 
		// verbose message returned, $customData should be an array of data returned as part 
		// of the result and $responseCode should be a response code in 4XX namespace, which 
		// is used for false results in Wave Framework.
		// * message - False message
		// * customData - Additional data returned
		// * responseCode - Response code number
		final protected function resultFalse($message='OK',$customData=false,$responseCode=400){
			if(is_array($customData)){
				return array('www-message'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-message'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
		// Wave Framework MVC object method calls can return their data in whatever format 
		// developer finds necessary, but it is recommended to return data in a standardized 
		// form. This method returns an array in the format of a successful result. $message 
		// should be the verbose message returned, $customData should be an array of data 
		// returned as part of the result and $responseCode should be an response code in 
		// 5XX namespace, which is used for successful results in Wave Framework.
		// * message - Success message
		// * customData - Additional data returned
		// * responseCode - Response code number
		final protected function resultTrue($message='OK',$customData=false,$responseCode=500){
			if(is_array($customData)){
				return array('www-message'=>$message,'www-response-code'=>$responseCode)+$customData;
			} else {
				return array('www-message'=>$message,'www-response-code'=>$responseCode);
			}
		}
		
		// This method is used to check for standardized API call result and determine if the 
		// API call was successful or not. It essentially checks for response codes in the array.
		// $data is the result data array that is checked.
		// * data - Result data array that is checked
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
		
		// This method is used to check for standardized API call result and determine if the 
		// API call had an error. It essentially checks for response codes in the array.
		// $data is the result data array that is checked.
		// * data - Result data array that is checked
		final protected function checkError($data){
			// These values are only checked from an array
			if(is_array($data)){
				// Error messages have response code namespace of 1XX, 2XX and 3XX (latter is developer defined)
				if(isset($data['www-response-code']) && $data['www-response-code']<400){
					return true;
				} elseif(isset($data['www-response-code']) && $data['www-response-code']>=400){
					return false;
				} else {
					// Since response code was not found, system returns false, as standardized error was not found
					return false;
				}
			} else {
				// Since result was not an array, system returns false, as standardized error was not found
				return false;
			}
		}
		
	// CACHE
	
		// This method will tell Wave Framework API not to cache current API call, no matter 
		// what. This means that while current API call may be loaded from cache, but in case 
		// the cache does not exist the result will not be written to cache.
		// * state - True or false flag
		final protected function disableCache($state=true){
			// This is stored as a flag
			$this->WWW_API->noCache[$this->WWW_API_callIndex]=$state;
			return true;
		}
		
		// This method unsets all cache that has been stored with a specific tag keyword. 
		// $tags variable can both be a string or an array of keywords. Every cache related 
		// to those keywords will be removed.
		// * tags - An array or comma separated list of tags that the cache was stored under
		final protected function unsetTaggedCache($tags){
			return $this->WWW_API->unsetTaggedCache($tags);
		}
		
		// This method returns currently existing cache for currently executed API call, if it 
		// exists. This allows you to always load cache from system in case a new response cannot 
		// be generated.
		final protected function getExistingCache(){
			return $this->WWW_API->getExistingCache($this->WWW_API_callIndex);
		}
		
		// If cache exists for currently executed API call, then this method returns the UNIX 
		// timestamp of the time when that cache was written.
		final protected function getExistingCacheTime(){
			return $this->WWW_API->getExistingCacheTime($this->WWW_API_callIndex);
		}
		
		// This method can be used to store cache for whatever needs by storing $key and 
		// giving it a value of $value. Cache tagging can also be used with custom tag by 
		// sending a keyword with $tags or an array of keywords.
		// * keyUrl - Key or address where cache should be stored
		// * value - Value to be stored
		// * tags - Tag or an array or a comma separated list of tags that cache will be stored with
		final protected function setCache($key,$value,$tags=false){
			return $this->WWW_API->setCache($key,$value,true,$tags);
		}
		
		// This method fetches data from cache based on cache keyword $key, if cache exists. 
		// This should be the same keyword that was used in setCache() method, when storing cache.
		// * key - Address to store cache at
		final protected function getCache($key){
			return $this->WWW_API->getCache($key,true);
		}
		
		// This function returns the timestamp of when the cache of keyword $key, was created, 
		// if such a cache exists.
		// * key - Address to store cache at
		final protected function cacheTime($key){
			return $this->WWW_API->cacheTime($key,true);
		}
		
		// This method removes cache that was stored with the keyword $key, if such a cache exists.
		// * key - Address where cache is stored
		final protected function unsetCache($key){
			return $this->WWW_API->unsetCache($key,true);
		}
		
	// ENCRYPTION AND DECRYPTION
	
		// This method uses API class internal encryption function to encrypt $data string with 
		// a key and a secret key (if set). If only $key is set, then ECB mode is used for 
		// Rijndael encryption.
		// * data - data to be encrypted
		// * key - key used for encryption
		// * secretKey - used for calculating initialization vector (IV)
		final protected function encryptRijndael256($data,$key,$secretKey=false){
			$this->WWW_API->encryptRijndael256($data,$key,$secretKey);
		}
		
		// This will decrypt Rijndael encoded data string, set with $data. $key and $secretKey 
		// should be the same that they were when the data was encrypted.
		// * data - data to be decrypted
		// * key - key used for decryption
		// * secretKey - used for calculating initialization vector (IV)
		final protected function decryptRijndael256($data,$key,$secretKey=false){
			$this->WWW_API->decryptRijndael256($data,$key,$secretKey);
		}		
		
	// INTERNAL LOG ENTRY WRAPPER
	
		// This method attempts to write an entry to internal log. Log entry is stored with 
		// a $key and entry itself should be the $data. $key is needed to easily find the 
		// log entry later on.
		// * key - Descriptive key that the log entry will be stored under
		// * data - Data contained in the entry
		final protected function internalLogEntry($key,$data=false){
			return $this->WWW_API->internalLogEntry($key,$data);
		}
		
		// This method is a timer that can be used to grade performance within the system. 
		// When this method is called with some $key first, it will start the timer and write 
		// an entry to log about it. If the same $key is called again, then a log entry is 
		// created with the amount of microseconds that have passed since the last time this 
		// method was called with this $key.
		// * key - Identifier for splitTime group
		final protected function splitTime($key='api'){
			return $this->WWW_API->splitTime($key);
		}
		
	// STATE MESSENGER WRAPPERS
	
		// This method initializes state messenger with the keyword $address. $address is the 
		// signature that the state messenger data will be stored under and can be later 
		// accessed again. Other state messenger calls cannot be made when this method is 
		// not called first.
		// * address - Key that messenger data will be saved under
		final protected function stateMessenger($address){
			return $this->WWW_API->state->stateMessenger($address);
		}
		
		// This method stores data $value under key of $key. It also turns off caching of the 
		// current API call as a result, since state messenger is outside the scope of caching.
		// * data - Key or data array
		// * value - Value, if data is a key
		final protected function setStateMessengerData($key,$value=false){
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
		
		// This method removes specific $key from current state messenger. If $key is not set, 
		// then entire currently active state messenger data is cleared.
		// * key - Key that will be removed
		final protected function unsetStateMessengerData($key=false){
			return $this->WWW_API->state->unsetMessengerData($key);
		}
		
		// This method returns data from state messenger with an address of $address. If $remove 
		// is set - as it is by default - then state messenger of this $address is also deleted 
		// and cannot be accessed again.
		// * address - Messenger address
		// * remove - True or false flag whether to delete the request data after returning it
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
	
		// This method starts sessions. This is called automatically if sessions are accessed 
		// but sessions have not yet been started. $secure flag is for session cookie to be 
		// secure and $httpOnly will mean that cookie is for HTTP only and cannot be accessed 
		// with scripts.
		final protected function startSession($secure=false,$httpOnly=true){
			return $this->WWW_API->state->startSession($secure,$httpOnly);
		}
	
		// This method regenerates ongoing session with a new ID.
		final protected function regenerateSession(){
			return $this->WWW_API->state->regenerateSession();
		}
		
		// This method destroys ongoing session and removes session cookie.
		final protected function destroySession(){
			return $this->WWW_API->state->destroySession();
		}
	
		// This method sets a session variable $key with a value $value. If $key is an array 
		// of keys and values, then multiple session variables are set at once.
		// * key - Key of the variable, can be an array
		// * value - Value to be set
		final protected function setSession($key,$value){
			return $this->WWW_API->state->setSession($key,$value);
		}
		
		// This method returns $key value from session data. If $key is an array of keys, 
		// then it can return multiple variables from session at once. If $key is not set, 
		// then entire session array is returned.
		// * key - Key of the value to be returned, can be an array
		final protected function getSession($key){
			return $this->WWW_API->state->getSession($key);
		}
		
		// This method unsets $key value from current session. If $key is an array of keys, 
		// then multiple variables can be unset at once. If $key is not set at all, then this 
		// simply destroys the entire session.
		// * key - Key of the value to be unset, can be an array
		final protected function unsetSession($key=false){
			return $this->WWW_API->state->unsetSession($key);
		}
		
		// This method sets a cookie with $key and a $value. $configuration is an array of 
		// cookie parameters that can be set.
		// * key - Key of the variable, can be an array
		// * value - Value to be set
		// * configuration - Cookie configuration options
		final protected function setCookie($key,$value,$configuration=array()){
			return $this->WWW_API->state->setCookie($key,$value,$configuration);
		}
		
		// This method returns a cookie value with the set $key. $key can also be an array of 
		// keys, in which case multiple cookie values are returned in an array.
		// * key - Key of the value to be returned, can be an array
		final protected function getCookie($key){
			return $this->WWW_API->state->getCookie($key);
		}
		
		// This method unsets a cookie with the set key of $key. If $key is an array, then 
		// it can remove multiple cookies at once.
		// * key - Key of the cookie to be unset, can be an array
		final protected function unsetCookie($key){
			return $this->WWW_API->state->unsetCookie($key);
		}
		
	// SESSION USER AND PERMISSIONS
		
		// This method sets user data array in session. This is a simple helper function used 
		// for holding user-specific data for a web service. $data is an array of user data.
		// * data - Data array set to user
		final public function setUser($data){
			return $this->WWW_API->state->setUser($data);
		}
		
		// This either returns the entire user data array or just a specific $key of user 
		// data from the session.
		// * key - Element returned from user data, if not set then returns the entire user data
		final public function getUser($key=false){
			return $this->WWW_API->state->getUser($key);
		}
		
		// This unsets user data and removes the session of user data.
		final public function unsetUser(){
			return $this->WWW_API->state->unsetUser();
		}
	
		// This checks for an existence of a specific $permission in the user permissions 
		// session array. $permission can also be an array, in which case the method returns 
		// false when one of those permission keys is not set in the permissions arrays. 
		// Method returns true, if $permission exists in the permissions session array.
		// * check - String that is checked against permissions array
		final protected function checkPermissions($check){
			return $this->WWW_API->state->checkPermissions($check);
		}
		
		// This method returns an array of currently set user permissions from the session.
		final protected function getPermissions(){
			return $this->WWW_API->state->getPermissions();
		}
		
		// This method sets an array of $permissions or a comma-separated string of 
		// permissions for the current user permissions session.
		// * permissions - An array or a string of permissions
		final protected function setPermissions($permissions){
			return $this->WWW_API->state->setPermissions($permissions);
		}
		
		// This unsets permissions data from session similarly to how unsetUser() method 
		// unsets user data from session.
		final public function unsetPermissions(){
			return $this->WWW_API->state->unsetPermissions();
		}
		
	// DATABASE WRAPPERS
	
		// This method can be used to dynamically create a new database connection object from 
		// Database class. $type is the database type, $host, $database, $username, $password 
		// are database connection credentials. And $showErrors defines whether errors should 
		// be thrown if encountered and $persistentConnection sets whether connection should be 
		// reused, if such already exists.
		// * type - Database type
		// * host - Database host
		// * database - Database name
		// * username - Database username
		// * password - Database password
		// * showErrors - True or false flag regarding whether to show errors
		// * persistentConnection - True of false flag regarding whether connection is assigned to be permanent
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
			
		// This sends query information to PDO. $queryString is the query string and $variables 
		// is an array of variables sent with the request. Question marks (?) in $queryString 
		// will be replaced by values from $variables array for PDO prepared statements. This 
		// method returns the first row of the matching result, or it returns false, if the 
		// query failed. This method is mostly meant for SELECT queries that return a single row.
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		final protected function dbSingle($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbSingle($query,$variables);
		}
		
		// This sends query information to PDO. $queryString is the query string and $variables 
		// is an array of variables sent with the request. Question marks (?) in $queryString 
		// will be replaced by values from $variables array for PDO prepared statements. This 
		// method returns an array where each key is one returned row from the database, or it 
		// returns false, if the query failed. This method is mostly meant for SELECT queries 
		// that return multiple rows.
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		final protected function dbMultiple($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbMultiple($query,$variables);
		}
		
		// This sends query information to PDO. $queryString is the query string and $variables 
		// is an array of variables sent with the request. Question marks (?) in $queryString 
		// will be replaced by values from $variables array for PDO prepared statements. This 
		// method only returns the number of rows affected or true or false, depending whether 
		// the query was successful or not. This method is mostly meant for INSERT, UPDATE and 
		// DELETE type of queries.
		// * query - query string, a statement to prepare with PDO
		// * variables - array of variables to use in prepared statement
		final protected function dbCommand($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbCommand($query,$variables);
		}
		
		// This method attempts to return the last ID (a primary key) that was inserted to 
		// the database. If one was not found, then the method returns false.
		final protected function dbLastId(){
			return $this->WWW_API->state->databaseConnection->dbLastId();
		}
		
		// This method begins a database transaction, if the database type supports transactions. 
		// If this process fails, then method returns false, otherwise it returns true.
		final protected function dbTransaction(){
			return $this->WWW_API->state->databaseConnection->dbTransaction();
		}
		
		// This method cancels all the queries that have been sent since the transaction was 
		// started with dbTransaction(). If rollback is successful, then method returns true, 
		// otherwise returns false.
		final protected function dbRollback(){
			return $this->WWW_API->state->databaseConnection->dbRollback();
		}
		
		// This commits all the queries sent to database after transactions were started. If 
		// commit fails, then it returns false, otherwise this method returns true.
		final protected function dbCommit(){
			return $this->WWW_API->state->databaseConnection->dbCommit();
		}
		
		// This is a database helper function that can be used to escape specific variables 
		// if that variable is not sent with as part of variables array and is written in 
		// the query string instead. $value is the variable value that will be escaped or 
		// modified. $type is the type of escaping and modifying that takes place.
		// * value - Input value
		// * type - Method of quoting, either 'escape', 'integer', 'latin', 'field' or 'like'
		// * stripQuotes - Whether the resulting quotes will be stripped from the string, if they get set
		final protected function dbQuote($value,$type='escape',$stripQuotes=false){
			return $this->WWW_API->state->databaseConnection->dbQuote($value,$type,$stripQuotes);
		}
		
		// This is a database helper method, that simply creates an array of database result 
		// array (like the one returned by dbMultiple). It takes the database result array and 
		// collects all $key values from that array into a new, separate array. If $unique is 
		// set, then it only returns unique keys.
		// * array - Array to filter from
		// * key - Key to return
		// * unique - If returned array should only have unique values
		final public function dbArray($array,$key,$unique=false){
			return $this->WWW_API->state->databaseConnection->dbArray($array,$key,$unique);
		}
		
		// This function attempts to 'simulate' the way PDO builds a query
		// * query - Query string
		// * variables - Values sent to PDO
		// Returns 'prepared' query string
		final public function dbDebug($query,$variables=array()){
			return $this->WWW_API->state->databaseConnection->dbDebug($query,$variables);
		}
		
		// This method returns the PDO class that API is currently using, if one is active.
		final protected function dbPDO(){
			return $this->WWW_API->state->databaseConnection->pdo;
		}
		
	// TERMINAL
	
		// This method is wrapper function for making terminal calls. It attempts to detect 
		// what terminal is available on the system, if any, and then execute the call and 
		// return the results of the call.
		// * command - Command to be executed
		final protected function terminal($command){
			return $this->WWW_API->state->terminal($command);			
		}

}
	
?>