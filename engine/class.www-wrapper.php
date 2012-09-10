<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * PHP API Wrapper Class
 *
 * Main purpose of an API Wrapper is to make it easier to make API requests over HTTP to a system 
 * built on Wave Framework. API Wrapper class does everything for the developer without requiring 
 * the developer to learn the ins and outs of technical details about how to build an API request. 
 * Wave Framework comes with two separate API authentication methods, one more secure than the 
 * other, both which are handled by this Wrapper class.
 *
 * @package    API
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/wrapper_php.htm
 * @since      2.0.0
 * @version    3.2.1
 */

class WWW_Wrapper {

	/**
	 * This is the address and URL of the API that the Wrapper will connect to. The API address 
	 * must be for Wave Framework API. This value is set either in object creation or when 
	 * setting 'www-address' input variable.
	 */
	private $apiAddress;
	
	/**
	 * This holds the current language of the API, it can be useful if the API commands return 
	 * language-specific responses and translations from the API. This variable is set by sending 
	 * 'www-language' input variable.
	 */
	private $apiLanguage=false;
	
	/**
	 * This holds information about current API state, such as profile name, secret key and 
	 * various API-related flags for callbacks, asyncrhonous status and more. This variable is 
	 * passed around per each API call.
	 */
	private $apiState=array(
		'apiProfile'=>false,
		'apiSecretKey'=>false,
		'apiToken'=>false,
		'apiPublicToken'=>false,
		'apiHashValidation'=>true,
		'apiStateKey'=>false,
		'headers'=>false,
		'returnHash'=>false,
		'returnTimestamp'=>false,
		'trueCallback'=>false,
		'falseCallback'=>false,
		'errorCallback'=>false,
		'requestTimeout'=>10,
		'timestampDuration'=>60,
		'unserialize'=>true,
		'lastModified'=>false
	);
	
	/**
	 * This variable holds the last known error message returned from the API.
	 */
	public $errorMessage=false;
	
	/**
	 * This variable holds the last known response code returned from the API.
	 */
	public $responseCode=false;
	
	/**
	 * Input data is a variable that stores all the plain-text input sent with the API request, 
	 * it's a key-value pair of variables and their values for the API.
	 */
	private $inputData=array();
	
	/**
	 * Crypted input is an array of keys and values that holds data that will be encrypted 
	 * prior to be sent to API. This will be encrypted with the session token of the API in 
	 * serialized form.
	 */
	private $cryptedData=array();
	
	/**
	 * This array stores keys and values for files that will be sent to API. Key is the 'input 
	 * file name' and value is the location of the file in filesystem.
	 */
	private $inputFiles=array();
	
	/**
	 * This flag holds state about support for cURL. cURL will be used to make requests unless 
	 * it is not enabled on the server.
	 */
	private $curlEnabled=false;
	
	/**
	 * This is an array that gathers log information about the requests made through the API 
	 * that can be used for debugging purposes should something go wrong.
	 */
	private $log=array();
	
	/**
	 * This is a flag that halts the entire functionality of the Wrapper object, if it is set. 
	 * Once this happens you should check the log to see what went wrong.
	 */
	private $criticalError=false;
	
	/**
	 * This variable holds the address for the file that is used as a cookie container in the 
	 * file system. This allows Wrapper to use cookies when making API requests.
	 */
	private $cookieContainer=false;
	
	/**
	 * This is the user-agent string of the API Wrapper and it is sent by the Wrapper when making 
	 * cURL requests. It is useful later on to determine where the requests come from. Note that 
	 * when cURL is not supported and file_get_contents() makes the request, then user agent is 
	 * not sent with the request.
	 */
	private $userAgent='WaveFramework/3.2.1 (PHP)';
	
	/**
	 * This is the GET string maximum length. Most servers should easily be able to deal with 
	 * 2048 bytes of request string length, but this value can be changed by submitting a 
	 * different length with 'www-get-length' input value.
	 */
	private $getLimit=2048;
	
	/**
	 * If this value is set, then API log will be reset after each API request. This value can 
	 * be sent with 'www-reset-log' keyword sent to Wrapper.
	 */
	private $resetLog=true;

	/**
	 * Wrapper object creation requires an 'address', which is the address that Wrapper will make 
	 * API requests to. If this is not defined, then 'address' assumes that the system it makes 
	 * requests to is the same where the API is loaded from. 'language' is a language keyword from 
	 * the system that API makes a connection with and is used whenever language-specific results 
	 * are returned from API.
	 * 
	 * @param string $address API address, default value is current domain presumed API address
	 * @param string $language language keyword, default value is current document language
	 * @return object
	 */
	public function __construct($address=false,$language=false){
	
		// For cases when the API address is not set
		if(!$address){
			$address=((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']==1 || $_SERVER['HTTPS']=='on'))?'https://':'http://').$_SERVER['HTTP_HOST'].'/json.api';
		}
		
		// If language is set, then this language is used across API
		if($language){
			$this->apiLanguage=$language;
		}
		
		// This should be URL to API of Wave Framework
		$this->apiAddress=$address;
		
		// This checks for cURL support, which is required for making POST requests
		// cURL is also faster than file_get_contents() method
		if(extension_loaded('curl')){
			// Flag is checked during request creation
			$this->curlEnabled=true;
		} elseif(!function_exists('ini_get') || ini_get('allow_url_fopen')!=1){
			// This means that requests cannot be made at all
			$this->criticalError=true;
			// If cURL is enabled, then file_get_contents() requires PHP setting to make requests to URL's
			$this->responseCode=213;
			$this->errorMessage='Cannot make URL requests: Cannot detect if PHP can make URL requests, please enable allow_url_fopen setting or ini_get() function';
			$this->log[]=$this->errorMessage;
		}
		
		// JSON is required
		if(!function_exists('json_encode')){
			// This means that requests cannot be made at all
			$this->criticalError=true;
			$this->responseCode=214;
			$this->errorMessage='Cannot serialize data: JSON is required for API requests to work properly';
			$this->log[]=$this->errorMessage;
		}
		
		// Log entry
		$this->log[]='Wave API Wrapper object created with API address: '.$address;
		
	}
	
	// SETTINGS
		
		/**
		 * This method returns current log of the API wrapper. If $implode is set, then the 
		 * value of $implode is used as a character to implode the log with. Otherwise the 
		 * log is returned as an array.
		 * 
		 * @param string $implode string to implode the log with
         * @return array/string depending if imploded
		 */
		public function returnLog($implode=false){
			$this->log[]='Returning log';
			// Imploding, if requested
			if(!$implode){
				return $this->log;
			} else {
				return implode($implode,$this->log);
			}
		}
		
		/**
		 * This method clears the API log. This method can be called manually or is called 
		 * automatically if log is assigned to be reset with each new API request made by 
		 * the object.
		 * 
		 * @return boolean
		 */
		public function clearLog(){
			$this->log=array();
			return true;
		}
		
		/**
		 * This method allows to set cookie container for cURL calls. If this is set to false, 
		 * then cookies are not used at all. $location is the file that is used for cookie 
		 * container, it is automatically created if the file does not exist.
		 *
		 * @param string $location cookie container file location in filesystem
		 * @return boolean
		 */
		public function setCookieContainer($location=false){
			// If value is anything but false
			if($location){
				// Testing if file exists or attempting to create that file
				if(file_exists($location) && is_writable($location)){
					$this->cookieContainer=$location;
					$this->log[]='Cookie container set to: '.$location;
					return true;
				} elseif(file_put_contents($location,'')){
					$this->cookieContainer=$location;
					$this->log[]='Cookie container set to: '.$location;
					return true;
				} else {
					// Cookie container is not accessible
					$this->cookieContainer=false;
					$this->log[]='Cannot set cookie container to: '.$location;
					return false;
				}
			} else {
				$this->cookieContainer=false;
				$this->log[]='Cookies are turned off';
				return true;
			}
		}
		
		/**
		 * This method will clear and delete all cookies stored in cookie container defined 
		 * in $location or in general. Warning, this method technically removes the contents 
		 * of any writable file if set in $location. If $location is not set, then it attempts 
		 * to use the previously defined cookie container.
		 *
		 * @param string $location location of cookies file, if this is not set then uses current one
		 * @return boolean
		 */
		public function clearCookieContainer($location=false){
			if(!$location){
				$location=$this->cookieContainer;
			}
			if($location){
				if(file_exists($location) && is_writable($location) && file_put_contents($location,'')){
					$this->log[]='Cookies cleared';
					return true;
				} else {
					$this->log[]='Cannot clear cookies';
					return false;
				}
			} else {
				return false;
			}
		}
		
		/**
		 * This method returns currently used token, if one exists. This can be stored for 
		 * subsequent requests with Wrapper (or manually over HTTP).
		 *
		 * @return string or false if token does not exist
		 */
		public function getToken(){
			// Returning from the state array
			return $this->apiState['apiToken'];
		}
		
	// INPUT
		
		/**
		 * This method is used to set an input value in the API Wrapper. $input is the key 
		 * to set and $value is the value of the input key. $input can also be an array, 
		 * in which case multiple input values will be set in the same call. This method 
		 * calls private inputSetter() function that checks the input value for any internal 
		 * flags that might not actually be sent as an input to the API.
		 * 
		 * @param string|array $input key of the input data, or an array of keys and values
		 * @param string $value value of the input data
		 * @return boolean
		 */
		public function setInput($input,$value=false){
			// If this is an array then it populates input array recursively
			if(is_array($input)){
				foreach($input as $key=>$val){
					// Value is filtered through inputSetter function
					$this->inputSetter($key,$val);
				}
			} else {
				// Value is filtered through inputSetter function
				$this->inputSetter($input,$value);
			}
			return true;
		}
		
		/**
		 * This is a helper function that setInput() method uses to actually assign $value 
		 * to the $input keyword. A lot of the keywords set carry additional functionality 
		 * that may entirely be API Wrapper specific. This method also creates a log entry 
		 * for any value that is changed or set.
		 * 
		 * @param string $input input data key
		 * @param string $value input data value
		 * @return boolean
		 */
		private function inputSetter($input,$value){
			switch($input){
				case 'www-api':
					$this->apiAddress=$value;
					$this->log[]='API address changed to: '.$value;
					break;
				case 'www-hash-validation':
					$this->apiState['apiHashValidation']=$value;
					if($value){
						$this->log[]='API hash validation is used';
					} else {
						$this->log[]='API hash validation is not used';
					}
					break;
				case 'www-secret-key':
					$this->apiState['apiSecretKey']=$value;
					$this->log[]='API secret key set to: '.$value;
					break;
				case 'www-token':
					$this->apiState['apiToken']=$value;
					$this->log[]='API session token set to: '.$value;
					break;
				case 'www-profile':
					$this->apiState['apiProfile']=$value;
					$this->log[]='API profile set to: '.$value;
					break;
				case 'www-state':
					$this->apiState['apiStateKey']=$value;
					$this->log[]='API state check key set to: '.$value;
					break;
				case 'www-headers':
					$this->apiState['headers']=$value;
					if($value){
						$this->log[]='System specific parameters will be returned as headers';
					} else {
						$this->log[]='System specific parameters will be returned as part of response';
					}
					break;
				case 'www-return-hash':
					$this->apiState['returnHash']=$value;
					if($value){
						$this->log[]='API request will require hash validation';
					} else {
						$this->log[]='API request will not require hash validation';
					}
					break;
				case 'www-return-timestamp':
					$this->apiState['returnTimestamp']=$value;
					if($value){
						$this->log[]='API request will require timestamp validation';
					} else {
						$this->log[]='API request will not require timestamp validation';
					}
					break;
				case 'www-public-token':
					$this->apiState['apiPublicToken']=$value;
					if($value){
						$this->log[]='API public token set to: '.$value;
					} else {
						$this->log[]='API public token unset';
					}
					break;
				case 'www-return-type':
					$this->inputData[$input]=$value;
					$this->log[]='Input value of "'.$input.'" set to: '.$value;
					if($value!='json' && $value!='serializedarray' && $value!='querystring'){
						$this->apiState['unserialize']=false;
						$this->log[]='API result cannot be unserialized, setting unserialize flag to false';
					}
					break;
				case 'www-request-timeout':
					$this->apiState['requestTimeout']=$value;
					$this->log[]='API request timeout set to: '.$value;
					break;
				case 'www-unserialize':
					$this->apiState['unserialize']=$value;
					$this->log[]='API serialization value set to: '.$value;
					break;
				case 'www-true-callback':
					$this->apiState['trueCallback']=$value;
					if($value){
						if(gettype($value)!=='object'){
							$this->log[]='API return true/success callback set to: '.$value.'()';
						} else {
							$this->log[]='API return true/success callback uses an anonymous function';
						}
					}
					break;
				case 'www-false-callback':
					$this->apiState['falseCallback']=$value;
					if($value){
						if(gettype($value)!=='object'){
							$this->log[]='API return false/failure callback set to: '.$value.'()';
						} else {
							$this->log[]='API return false/failure callback uses an anonymous function';
						}
					}
					break;
				case 'www-error-callback':
					$this->apiState['errorCallback']=$value;
					if($value){
						if(gettype($value)!=='object'){
							$this->log[]='API return error callback set to: '.$value.'()';
						} else {
							$this->log[]='API return error callback uses an anonymous function';
						}
					}
					break;
				case 'www-last-modified':
					$this->apiState['lastModified']=$value;
					$this->log[]='API last-modified request time set to: '.$value;
					break;
				case 'www-language':
					$this->apiLanguage=$value;
					if($value){
						$this->log[]='API result language set to: '.$value;
					} else {
						$this->log[]='API result language uninitialized';
					}
					break;
				case 'www-get-limit':
					$this->getLimit=$value;
					$this->log[]='Maximum GET string length is set to: '.$value;
					break;
				case 'www-reset-log':
					$this->resetLog=$value;
					if($value){
						$this->log[]='Log is reset after each new request';
					} else {
						$this->log[]='Log is kept for multiple requests';
					}
					break;
				case 'www-timestamp-duration':
					$this->apiState['timestampDuration']=$value;
					$this->log[]='API valid timestamp duration set to: '.$value;
					break;
				case 'www-output':
					$this->log[]='Ignoring www-output setting, wrapper always requires output to be set to true';
					break;
				default:
					// True/false conversions for input strings
					if($value===true){
						$value=1;
					} elseif($value===false){
						$value=0;
					}
					$this->inputData[$input]=$value;
					$this->log[]='Input value of "'.$input.'" set to: '.$value;
					break;				
			}
			return true;
		}
		
		/**
		 * This method sets a crypted input data that will be encrypted with secret key or a 
		 * token prior to making the HTTP request. This allows to transmit secure data across 
		 * servers. Note that crypted input should not be used when hash validation is not used 
		 * for making a request, since the token or secret key would also be sent with the 
		 * request. $input is the keyword and $value is the value. $input can also be an array 
		 * of keys and values.
		 *
		 * @param string|array $input input data key or an array of keys and values
		 * @value string [$value] input data value
		 * @return boolean
		 */
		public function setCryptedInput($input,$value=false){
			// If this is an array then it populates input array recursively
			if(is_array($input)){
				foreach($input as $key=>$val){
					// Value is converted to string to make sure that json_encode() includes quotes in hash calculations
					$this->cryptedData[$key]=$val;
					$this->log[]='Crypted input value of "'.$key.'" set to: '.$val;
				}
			} else {
				// Value is simply added to inputData array
				$this->cryptedData[$input]=$value;
				$this->log[]='Crypted input value of "'.$input.'" set to: '.$value;
			}
			return true;
		}
		
		/**
		 * This method sets files that will be uploaded with the API request. $file is the name
		 * of the file and $location is the address of the file in filesystem. Multiple files
		 * can be attached at once by sending $file as an array of filenames and locations. This 
		 * method also checks if the file actually exists.
		 *
		 * @param string|array $file file keyword or an array of keywords and file locations
		 * @param string $file file location in filesystem
		 * @return boolean/error depending on whether file exists or not
		 */
		public function setFile($file,$location=false){
			// If this is an array then it populates input array recursively
			if(is_array($file)){
				foreach($file as $key=>$loc){
					// File needs to exist in filesystem
					if($loc && file_exists($loc)){
						$this->inputFiles[$key]=$loc;
						$this->log[]='Input file "'.$key.'" location set to: '.$loc;
					} else {
						trigger_error('File location not defined or file does not exist in that location: '.$loc,E_USER_ERROR);
					}
				}
			} else {
				// File needs to exist in filesystem
				if($location && file_exists($location)){
					$this->inputFiles[$file]=$location;
					$this->log[]='Input file "'.$file.'" location set to: '.$location;
				} else {
					trigger_error('File location not defined or file does not exist in that location: '.$location,E_USER_ERROR);
				}
			}
			return true;
		}
		
		/**
		 * This method resets the state of API. It is called after each API request with 
		 * $clearAuth set to false. To entirely reset the state of API $clearAuth should be 
		 * set to true and this will reset everything except the log file.
		 * 
		 * @param boolean $clearAuth whether to also reset authentication and state data
		 * @return boolean
		 */
		public function clearInput($clearAuth=false){
			// If authentication should also be cleared
			if($clearAuth){
				$this->apiState['apiProfile']=false;
				$this->apiState['apiSecretKey']=false;
				$this->apiState['apiToken']=false;
				$this->apiState['apiPublicToken']=false;
				$this->apiState['apiHashValidation']=true;
				$this->apiState['headers']=false;
				$this->apiState['returnHash']=false;
				$this->apiState['returnTimestamp']=false;
				$this->apiState['requestTimeout']=10;
				$this->apiState['timestampDuration']=60;
			}
			// Resetting the API state test key
			$this->apiState['apiStateKey']=false;
			// Neutralizing state settings
			$this->apiState['unserialize']=true;
			$this->apiState['lastModified']=false;
			// Neutralizing callbacks
			$this->apiState['trueCallback']=false;
			$this->apiState['falseCallback']=false;
			$this->apiState['errorCallback']=false;
			// Input data
			$this->inputData=array();
			$this->cryptedData=array();
			$this->inputFiles=array();
			// Log entry
			$this->log[]='Input data, crypted input and file data is unset';
			return true;
		}
		
	// SENDING REQUEST
		
		/**
		 * This method executes the API request by building the request based on set input 
		 * data and sending it to API using cURL or file_get_contents() methods. It also 
		 * builds all validations as well as validates the returned response from the server 
		 * and calls callback functions, if they are set. It is possible to send input 
		 * variables directly with a single call by supplying $variables, $fileVariables and 
		 * $cryptedVariables arrays.
		 * 
		 * @param array $variables array of input variables
		 * @param array $fileVariables array of filenames and locations to upload
		 * @param array $cryptedVariables array of input data to be encrypted
		 * @return array/string depending on what is requested
		 */
		public function sendRequest($variables=false,$fileVariables=false,$cryptedVariables=false){
		
			// If log is assigned to be reset with each new API request
			if($this->resetLog){
				$this->clearLog();
			}
		
			// In case variables have been sent with a single request
			if($variables && is_array($variables)){
				foreach($variables as $key=>$value){
					// Settin variable throuhg input setter
					$this->setInput($key,$value);
				}
			}
			if($fileVariables && is_array($fileVariables)){
				foreach($fileVariables as $key=>$value){
					// Settin variable throuhg input setter
					$this->setFile($key,$value);
				}
			}
			if($cryptedVariables && is_array($cryptedVariables)){
				foreach($cryptedVariables as $key=>$value){
					// Settin variable throuhg input setter
					$this->setCryptedInput($key,$value);
				}
			}
		
			// This is the input data used
			$thisInputData=$this->inputData;
			$thisCryptedData=$this->cryptedData;
			
			// Current state settings
			$thisApiState=$this->apiState;
			
			// Assigning authentication options that are sent with the request
			if($thisApiState['apiProfile']!=false){
				$thisInputData['www-profile']=$thisApiState['apiProfile'];
			}
			// Assigning the state check key
			if($thisApiState['apiStateKey']!=false){
				$thisInputData['www-state']=$thisApiState['apiStateKey'];
			}
			// Notifying API to return www-* prefix data in headers
			if($thisApiState['headers']!=false){
				$thisInputData['www-headers']=1;
			}
			// Assigning return-timestamp flag to request
			if($thisApiState['returnTimestamp']==true || $thisApiState['returnTimestamp']==1){
				$thisInputData['www-return-timestamp']=1;
			}
			// Assigning return-hash flag to request
			if($thisApiState['returnHash']==true || $thisApiState['returnHash']==1){
				$thisInputData['www-return-hash']=1;
			}
			// Assigning public API token as part of the request
			if($thisApiState['apiPublicToken']){
				$thisInputData['www-public-token']=$thisApiState['apiPublicToken'];
			}
			
			// If language is set
			if($this->apiLanguage){
				$thisInputData['www-language']=$this->apiLanguage;
			}
			
			// Clears the source input data
			$this->clearInput();
		
			// Returns false if there is an existing critical error
			if($this->criticalError){
				return $this->errorHandler($thisInputData,$this->responseCode,$this->errorMessage,$thisApiState['errorCallback']);
			}
			
			// Log entry
			$this->log[]='Starting to build request';
		
			// Correct request requires command to be set
			if(!isset($thisInputData['www-command'])){
				return $this->errorHandler($thisInputData,201,'API command is not set, this is required',$thisApiState['errorCallback']);
			}
		
			// If default value is set, then it is removed
			if(isset($thisInputData['www-return-type']) && $thisInputData['www-return-type']=='json'){
				$this->log[]='Since www-return-type is set to default value, it is removed from input data';
				unset($thisInputData['www-return-type']);
			}
			// If default value is set, then it is removed
			if(isset($thisInputData['www-cache-timeout']) && $thisInputData['www-cache-timeout']==0){
				$this->log[]='Since www-cache-timeout is set to default value, it is removed from input data';
				unset($thisInputData['www-cache-timeout']);
			}
			// If default value is set, then it is removed
			if(isset($thisInputData['www-minify']) && $thisInputData['www-minify']==false){
				$this->log[]='Since www-minify is set to default value, it is removed from input data';
				unset($thisInputData['www-minify']);
			}
			
			
			// If encryption key is set, then this is sent together with crypted data
			if($thisApiState['apiProfile'] && isset($thisApiState['apiSecretKey'],$thisInputData['www-crypt-output'])){
				$this->log[]='Crypt output key was set as regular input for non-public profile API request, it is moved to crypted input instead';
				$thisCryptedData['www-crypt-output']=$thisInputData['www-crypt-output'];
				unset($thisInputData['www-crypt-output']);
			}
			
			// If profile is used, then timestamp will also be sent with the request
			if($thisApiState['apiProfile']){
				// Timestamp is required in API requests since it will be used for request validation and replay attack protection
				if(!isset($thisInputData['www-timestamp'])){
					$thisInputData['www-timestamp']=time();
				}
			}
		
			// If API secret key is set, then wrapper assumes that non-public profile is used, thus hash and timestamp have to be included
			if($thisApiState['apiSecretKey']){
			
				// Log entry
				$this->log[]='API secret key set, hash authentication will be used';
				// If crypted data array is populated, then this data is encrypted in www-crypt-input key
				if(!isset($thisInputData['www-crypt-input']) && !empty($thisCryptedData)){
					// This is only possible if API token is set
					if($thisApiState['apiSecretKey']){
						// Mcrypt extension is required
						if(extension_loaded('mcrypt')){
							// Data is encrypted with Rijndael 256bit encryption
							if($thisApiState['apiToken']){
								$thisInputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,md5($thisApiState['apiToken']),json_encode($thisCryptedData),MCRYPT_MODE_CBC,md5($thisApiState['apiSecretKey'])));
							} else {
								$thisInputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,md5($thisApiState['apiSecretKey']),json_encode($thisCryptedData),MCRYPT_MODE_ECB));
							}
							$this->log[]='Crypted input created using JSON encoded input data, token and secret key';
						} else {
							return $this->errorHandler($thisInputData,203,'Unable to encrypt data, server configuration problem',$thisApiState['errorCallback']);
						}
					} else {
						return $this->errorHandler($thisInputData,202,'Crypted input can only be used with a set secret key',$thisApiState['errorCallback']);
					}
				}
				
				// If API hash validation is used
				if($thisApiState['apiHashValidation']){
				
					// Validation hash is generated based on current serialization option
					if(!isset($thisInputData['www-hash'])){
						
						// Calculating validation hash
						if($thisApiState['apiToken'] && $thisInputData['www-command']!='www-create-session'){
							$thisInputData['www-hash']=$this->validationHash($thisInputData,$thisApiState['apiToken'].$thisApiState['apiSecretKey']);
						} else {
							$thisInputData['www-hash']=$this->validationHash($thisInputData,$thisApiState['apiSecretKey']);
						}
						
					}

					// Log entry
					if($thisApiState['apiToken']){
						$this->log[]='Validation hash created using JSON encoded input data, API token and secret key';
					} else {
						$this->log[]='Validation hash created using JSON encoded input data and secret key';
					}
				
				} else {
				
					// Attaching secret key or token to the request
					if($thisInputData['www-command']=='www-create-session' && $thisApiState['apiSecretKey']){
						$thisInputData['www-secret-key']=$thisApiState['apiSecretKey'];
						$this->log[]='Validation will be secret key based';
					} elseif($thisApiState['apiToken']){
						$thisInputData['www-token']=$thisApiState['apiToken'];
						$this->log[]='Validation will be session token based';
					}
				
				}
				
			} else {
			
				// Token-only validation means that token will be sent to the server, but data itself will not be hashed. This works like a cookie.
				if($thisApiState['apiToken']){
					// Log entry
					$this->log[]='Using token-only validation';
				} else {
					// Log entry
					$this->log[]='API secret key is not set, hash validation will not be used';
				}
				
			}
			
			// MAKING A REQUEST
			
				// Building the request URL
				$requestURL=$this->apiAddress;
				$requestData=http_build_query($thisInputData);
				$getRequestLength=strlen($requestURL.'?'.$requestData);
				
				// Get request is made if the URL is shorter than 2048 bytes (2KB).
				// While servers can easily handle 8KB of data, servers are recommended to be vary if the GET request is longer than 2KB
				if($getRequestLength<=$this->getLimit && empty($this->inputFiles)){
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
					
						// Log entry
						$this->log[]='Making GET request to API using cURL to URL: '.$requestURL.'?'.$requestData;
						
						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_URL=>$requestURL.'?'.$requestData,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
							CURLOPT_HTTPGET=>true,
							CURLOPT_TIMEOUT=>$thisApiState['requestTimeout'],
							CURLOPT_USERAGENT=>$this->userAgent,
							CURLOPT_HEADER=>true,
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_FOLLOWLOCATION=>false,
							CURLOPT_COOKIESESSION=>true,
							CURLOPT_SSL_VERIFYPEER=>false
						);
						// Cookies
						if($this->cookieContainer){
							curl_setopt_array($cURL,array(CURLOPT_COOKIESESSION=>false,CURLOPT_COOKIEFILE=>$this->cookieContainer,CURLOPT_COOKIEJAR=>$this->cookieContainer));
						} else {
							curl_setopt($cURL,CURLOPT_COOKIESESSION,true);
						}
						// If last modified header is sent
						if($thisApiState['lastModified']){
							curl_setopt($cURL,CURLOPT_HTTPHEADER,array('If-Modified-Since: '.gmdate('D, d M Y H:i:s',$thisApiState['lastModified']).' GMT'));
						}
						// Assigning options to cURL object
						curl_setopt_array($cURL,$requestOptions);
						// Executing the request
						$resultData=curl_exec($cURL);
						list($resultHeaders,$resultData)=explode("\r\n\r\n",$resultData,2);
						$resultHeaders=explode("\n",$resultHeaders);
						
						// Returning false if the request failed
						if(!$resultData){
							if($thisApiState['lastModified'] && curl_getinfo($cURL,CURLINFO_HTTP_CODE)==304){
								// Closing the resource
								curl_close($cURL);
								return $this->errorHandler($thisInputData,214,'Not modified',$thisApiState['errorCallback']);
							} else {
								$error='POST request failed: cURL error '.curl_getinfo($cURL,CURLINFO_HTTP_CODE).' - '.curl_error($cURL);
								// Closing the resource
								curl_close($cURL);
								return $this->errorHandler($thisInputData,204,$error,$thisApiState['errorCallback']);
							}
						} else {
							$this->log[]='GET request successful: '.curl_getinfo($cURL,CURLINFO_HTTP_CODE);
						}
						
						// Closing the resource
						curl_close($cURL);
						
					} else {
					
						// Log entry
						$this->log[]='Making GET request to API using file-get-contents to URL: '.$requestURL;
					
						// GET request an also be made by file_get_contents()
						if(!$resultData=file_get_contents($requestURL.'?'.$requestData)){
							return $this->errorHandler($thisInputData,204,'GET request failed: file_get_contents() failed',$thisApiState['errorCallback']);
						}
						// It is not possible to get headers with file_get_contents()
						$resultHeaders=array();
						
					}
					
				} else {
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
					
						// This variable will hold the eventual POST data
						$postData=array();
					
						// If there are no input files, then the entire data stream is added as a POST variable stream
						if(!empty($this->inputFiles)){
						
							// This is the variable that carries POST variables that will be sent to cURL
							$postData=$thisInputData;
							
							// Regular input variables can be sent over GET if they are not too long
							if($getRequestLength<=$this->getLimit){
							
								// Changes the request URL and clears input array from data
								$requestURL=$this->apiAddress.'?'.$requestData;
								$postData=array();
								
							} else {
								
								// This stores input variables that include @ symbol that cURL interprets for file upload
								$securityInput=array();
								
								// Escaping possible security hole in cURL by escaping the @ sign
								foreach($postData as $key=>$val){
									// If the first character is @
									if($val[0]=='@'){
										// Adding the variable to separate array and clearing original input array
										$securityInput[$key]=$val;
										unset($postData[$key]);
										// Log entry
										$this->log[]='Variable '.$key.' has a value that starts with @, sending it as GET variable';
									} else {
										// Log entry
										$this->log[]='Attaching variable to request: '.$key.'='.$val;
									}
								}
								
								// If unsecure variables are part of the POST request
								// Please note that if this data is too long for GET then the request will fail entirely
								if(!empty($securityInput)){
									$requestURL=$requestURL.'?'.http_build_query($securityInput);
								}
							
							}
							
							// Attaching files to the request
							foreach($this->inputFiles as $file=>$location){
								$this->log[]='Attaching a file to request: '.$location;
								$postData[$file]='@'.$location;
							}
							
						} else {
							// This sends all the variables to cURL as a string
							$postData=$requestData;
						}
							
						// Log entry
						$this->log[]='Making POST request to API using cURL to URL: '.$requestURL;
						
						// If the request GET URL is too long, then request is not made
						if(strlen($requestURL)>$this->getLimit){
							return $this->errorHandler($thisInputData,205,'POST request failed: Request URL is too long',$thisApiState['errorCallback']);
						}

						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_URL=>$requestURL,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
							CURLOPT_POST=>true,
							CURLOPT_POSTFIELDS=>$postData,
							CURLOPT_TIMEOUT=>$thisApiState['requestTimeout'],
							CURLOPT_USERAGENT=>$this->userAgent,
							CURLOPT_HEADER=>true,
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_FOLLOWLOCATION=>false,
							CURLOPT_COOKIESESSION=>true,
							CURLOPT_SSL_VERIFYPEER=>false
						);
						// Cookies
						if($this->cookieContainer){
							curl_setopt_array($cURL,array(CURLOPT_COOKIESESSION=>false,CURLOPT_COOKIEFILE=>$this->cookieContainer,CURLOPT_COOKIEJAR=>$this->cookieContainer));
						} else {
							curl_setopt($cURL,CURLOPT_COOKIESESSION,true);
						}
						// If last modified header is sent
						if($thisApiState['lastModified']){
							curl_setopt($cURL,CURLOPT_HTTPHEADER,array('If-Modified-Since: '.gmdate('D, d M Y H:i:s',$thisApiState['lastModified']).' GMT'));
						}
						// Assigning options to cURL object
						curl_setopt_array($cURL,$requestOptions);
						// Executing the request
						$resultData=curl_exec($cURL);
						list($resultHeaders,$resultData)=explode("\r\n\r\n",$resultData,2);
						$resultHeaders=explode("\n",$resultHeaders);
					
						// Log entry
						$this->log[]='Making POST request to API using cURL to URL: '.$this->apiAddress;
						
						// Returning false if the request failed
						if(!$resultData){
							if($thisApiState['lastModified'] && curl_getinfo($cURL,CURLINFO_HTTP_CODE)==304){
								// Closing the resource
								curl_close($cURL);
								return $this->errorHandler($thisInputData,214,'Not modified',$thisApiState['errorCallback']);
							} else {
								$error='POST request failed: cURL error '.curl_getinfo($cURL,CURLINFO_HTTP_CODE).' - '.curl_error($cURL);
								// Closing the resource
								curl_close($cURL);
								return $this->errorHandler($thisInputData,205,$error,$thisApiState['errorCallback']);
							}
						} else {
							$this->log[]='POST request successful: '.curl_getinfo($cURL,CURLINFO_HTTP_CODE);
						}
						
						// Closing the resource
						curl_close($cURL);
					
					} else {
						return $this->errorHandler($thisInputData,205,'POST request failed: cURL is not supported',$thisApiState['errorCallback']);
					}
				}
				
				// Log entry
				$this->log[]='Result of request: '.$resultData;
				$this->log[]='Headers of request: '.implode(',',$resultHeaders);
				
			// DECRYPTION
				
				// If requested data was encrypted, then this attempts to decrypt the data
				// This also checks to make sure that a serialized data was not returned (which usually means an error)
				if(strpos($resultData,'{')===false && strpos($resultData,'[')===false && isset($thisCryptedData['www-crypt-output']) || isset($thisInputData['www-crypt-output'])){
					// Getting the decryption key
					if(isset($thisCryptedData['www-crypt-output'])){
						$cryptKey=$thisCryptedData['www-crypt-output'];
					} else {
						$cryptKey=$thisInputData['www-crypt-output'];
					}
					// Decryption is different based on whether secret key was used or not
					if($thisApiState['apiSecretKey']){
						// If secret key was set, then decryption uses the secret key for initialization vector
						$resultData=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,md5($cryptKey),base64_decode($resultData),MCRYPT_MODE_CBC,md5($thisApiState['apiSecretKey']));
					} else {
						// Without secret key the system assumes that public profile is used and decryption is done in ECB mode
						$resultData=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,md5($cryptKey),base64_decode($resultData),MCRYPT_MODE_ECB);
					}
					// If decryption was a success
					if($resultData){
						$resultData=trim($resultData);
						$this->log[]='Result of decrypted request: '.$resultData;
					} else {
						return $this->errorHandler($thisInputData,206,'Output decryption has failed',$thisApiState['errorCallback']);
					}
				}
				
			// Returning the result directly if the result is not intended to be unserialized
			if(!$thisApiState['unserialize']){
			
				// Log entry for returning data
				$this->log[]='Returning result without unserializing';
				// Data is simply returned if serialization was not requested
				return $resultData;
				
			} else {
				
				// PARSING REQUEST RESULT
				
					// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
					if(!isset($thisInputData['www-return-type']) || $thisInputData['www-return-type']=='json'){
						// JSON support is required
						$resultData=json_decode($resultData,true);
					} else if($thisInputData['www-return-type']=='serializedarray'){
						// Return data is unserialized
						$resultData=unserialize($resultData,true);
						if(!$resultData){
							return $this->errorHandler($thisInputData,207,'Cannot unserialize returned data: unserialize() failed',$thisApiState['errorCallback']);
						} else {
							$this->log[]='Returning unserialized result';
						}
					} else if($thisInputData['www-return-type']=='querystring'){
						// Return data is filtered through string parsing and url decoding to create return array
						parse_str(urldecode($resultData),$resultData);
						if(!$resultData){
							return $this->errorHandler($thisInputData,207,'Cannot unserialize returned data: Cannot parse query data string',$thisApiState['errorCallback']);
						} else {
							$this->log[]='Returning parsed query string result';
						}
					}
					
					// Default response code and message
					$responseCode=500;
					$responseMessage='OK';
					
					// Checking if the result had differend response code and message
					if(isset($resultData['www-response-code'])){
						$responseCode=$resultData['www-response-code'];
						if(isset($resultData['www-message'])){
							$responseMessage=$resultData['www-message'];
						}
					}
					
				// WAVE HEADER RESPONSES
			
					// If headers are assigned to contain www-* response values
					if($thisApiState['headers']){
						if(isset($resultHeaders['www-response-code'])){
							$responseCode=$wwwHeaders['www-response-code'];
						}
						if(isset($resultHeaders['www-message'])){
							$responseMessage=$wwwHeaders['www-message'];
						}
					}
					
				// ERRORS
				
					if($responseCode<400){
						if(isset($responseMessage)){
							return $this->errorHandler($thisInputData,$responseCode,$responseMessage,$thisApiState['errorCallback']);
						} else {
							return $this->errorHandler($thisInputData,$responseCode,'Error',$thisApiState['errorCallback']);
						}
					}
					
				// RESULT VALIDATION
				
					// Result validation only applies to non-public profiles
					if($thisApiState['apiProfile'] && ($thisApiState['returnHash'] || $thisApiState['returnTimestamp'])){
						
						// If it was requested that validation timestamp is returned
						if($thisApiState['returnTimestamp']){
							if(isset($resultData['www-timestamp'])){
								// Making sure that the returned result is within accepted time limit
								if((time()-$thisApiState['timestampDuration'])>$resultData['www-timestamp']){
									return $this->errorHandler($thisInputData,209,'Validation timestamp is too old',$thisApiState['errorCallback']);
								}
							} else {
								return $this->errorHandler($thisInputData,208,'Validation data missing: Timestamp was not returned',$thisApiState['errorCallback']);
							}
						}
					
						// If it was requested that validation timestamp is returned
						if($thisApiState['apiStateKey']){
							if(!isset($resultData['www-state']) || $resultData['www-state']!=$thisApiState['apiStateKey']){
								return $this->errorHandler($thisInputData,210,'Validation state keys do not match',$thisApiState['errorCallback']);
							}
						}
						
						// If it was requested that validation hash is returned
						if($thisApiState['returnHash']){
							// Hash and timestamp have to be defined in response
							if(isset($resultData['www-hash'])){
							
								// Assigning returned array to hash validation array
								$validationData=$resultData;
								// Hash itself is removed from validation
								unset($validationData['www-hash']);
								
								// Validation depends on whether session creation or destruction commands were called
								if($thisInputData['www-command']=='www-create-session'){
									$hash=$this->validationHash($validationData,$thisApiState['apiSecretKey']);
								} else {
									$hash=$this->validationHash($validationData,$thisApiState['apiToken'].$thisApiState['apiSecretKey']);
								}
								
								// Unsetting the validation hash since it is not used
								unset($validationData);
								
								// If sent hash is the same as calculated hash
								if($hash==$resultData['www-hash']){
									$this->log[]='Hash validation successful';
								} else {
									return $this->errorHandler($thisInputData,210,'Hash validation failed',$thisApiState['errorCallback']);
								}
								
							} else {
								return $this->errorHandler($thisInputData,208,'Validation data missing: Hash was not returned',$thisApiState['errorCallback']);
							}
						}
					
					}
				
				// Resetting the error variables
				$this->responseCode=false;
				$this->errorMessage=false;
				
				// If this command was to create a token
				if($thisInputData['www-command']=='www-create-session' && isset($resultData['www-token'])){
					$this->apiState['apiToken']=$resultData['www-token'];
					$this->log[]='Session token was found in reply, API session token set to: '.$resultData['www-token'];
				} elseif($thisInputData['www-command']=='www-destroy-session'){
					$this->apiState['apiToken']=false;
					$this->log[]='Session has been destroyed';
				}
				
				// If callback has been defined
				if($thisApiState['trueCallback'] && $responseCode>=500){
					// If the callback is a function name and not a function itself
					if(gettype($thisApiState['trueCallback'])!=='object'){
						// Calling user function
						if(function_exists($thisApiState['trueCallback'])){
							$this->log[]='Sending data to callback: '.$thisApiState['trueCallback'].'()';
							// Callback execution
							return call_user_func($thisApiState['trueCallback'],$resultData);
						} else {
							return $this->errorHandler($thisInputData,216,'Callback method not found',$thisApiState['errorCallback']);
						}
					} else {
						// Returning data from callback
						return $thisApiState['trueCallback']($resultData);
					}
				} elseif($thisApiState['falseCallback'] && $responseCode<500){
					// If the callback is a function name and not a function itself
					if(gettype($thisApiState['falseCallback'])!=='object'){
						// Calling user function
						if(function_exists($thisApiState['falseCallback'])){
							$this->log[]='Sending data to callback: '.$thisApiState['falseCallback'].'()';
							// Callback execution
							return call_user_func($thisApiState['falseCallback'],$resultData);
						} else {
							return $this->errorHandler($thisInputData,216,'Callback method not found',$thisApiState['falseCallback']);
						}
					} else {
						// Returning data from callback
						return $thisApiState['falseCallback']($resultData);
					}
				} else {
					// Returning request result
					return $resultData;
				}
			
			}
			
		}
		
	// REQUIRED FUNCTIONS
	
		/**
		 * This method is used to build an input data validation hash string for authenticating 
		 * API requests. The entire input array of $validationData is serialized and hashed 
		 * with SHA-1 and a salt string set in $postFix. This is used for all API requests where 
		 * input has to be validated.
		 *
		 * @param array $validationData array to build a hash from
		 * @param string $postFix string that is used to salt the hash
		 * @return string
		 */
		private function validationHash($validationData,$postFix){
			// Sorting and encoding the output data
			$validationData=$this->ksortArray($validationData);
			// Returning validation hash
			return sha1(http_build_query($validationData).$postFix);
		}
		
		/**
		 * This is a helper function used by validationHash() function to serialize an array 
		 * recursively. It applies ksort() to main method as well as to all sub-arrays. $data 
		 * is the array to be sorted.
		 * 
		 * @param array|mixed $data variable to be sorted
		 * @return array/mixed
		 */
		private function ksortArray($data){
			// Method is based on the current data type
			if(is_array($data)){
				// Sorting the current array
				ksort($data);
				// Sorting every sub-array, if it is one
				$keys=array_keys($data);
				$keySize=sizeOf($keys);
				for($i=0;$i<$keySize;$i++){
					$data[$keys[$i]]=$this->ksortArray($data[$keys[$i]]);
				}
			}
			return $data;
		}
		
		/**
		 * This method is simply meant for returning a result if there was an error in the 
		 * sent request.
		 * 
		 * @param array $inputData data that was sent as input
		 * @param string $responseCode response code of the error
		 * @param string $errorMessage verbose error message
		 * @param string|function $errorCallback anonymous function or function name to call
		 * @return boolean/mixed depending on if callback function was used
		 */
		private function errorHandler($inputData,$responseCode,$errorMessage,$errorCallback){
			// Assigning error details to object state
			$this->responseCode=$responseCode;
			$this->errorMessage=$errorMessage;
			$this->log[]=$errorMessage;
			// If failure callback has been defined
			if($errorCallback){
				// If the callback is a function name and not a function itself
				if(gettype($errorCallback)!=='object'){
					// Looking for function of that name
					if(function_exists($errorCallback)){
						$this->log[]='Sending failure data to callback: '.$errorCallback.'()';
						// Callback execution
						return call_user_func($errorCallback,array('www-input'=>$inputData,'www-response-code'=>$responseCode,'www-message'=>$errorMessage));
					} else {
						$this->responseCode=217;
						$this->errorMessage='Callback method not found: '.$errorCallback.'()';
						$this->log[]=$this->errorMessage;
						return false;
					}
				} else {
					// Returning data from callback
					return $errorCallback($inputData);
				}
			} else {
				return false;
			}
		}


}

?>