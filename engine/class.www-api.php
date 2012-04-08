<?php

/*
WWW Framework
API class

This is a class that is used for routing all of API commands through, it deals with validation 
and caching, calling the proper controller and returning data with right headers and the right 
format. API has encryption and input-output data validations, as well as some framework specific 
callbacks. There is usually just one instance of this object in WWW Framework and it can be used 
within MVC objects through Factory class.

* Either uses existing State object or creates a new one
* API profile data loaded from /resources/api.profiles.php file
* Input with keys of www-* are assigned to API state and can affect API functionality
* Data encryption and decryption is done with Rijndael 256bit in CBC or ECB mode (latter in non-public profiles)
* Custom-formatted returned data types are JSON, binary, XML, RSS, CSV, serialized array, query string, INI or PHP
* Custom callbacks for setting headers, cookies, sessions and redirecting user agent

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_API {
	
	// This stores API command results in a buffer
	private $commandBuffer=array();
	
	// API profile data from /resources/api.profiles.php file or loaded during construction
	private $apiProfiles=array();
	
	// Logger state stores information about current API use
	// This is later fished by Logger object for system-wide log
	public $apiLoggerData=array();

	// This stores WWW_State object
	public $state=false;
			
	// API requires State object for majority of functionality
	// * state - Object of WWW_State class
	// * apiProfiles - Array of API profile information, array should be in format such as in /resources/api.profiles.php
	// Throws an error if profiles and profile file is inaccessible or Factory class cannot be loaded
	public function __construct($state=false,$apiProfiles=false){

		// API expects to be able to use State object
		if($state){
			$this->state=$state;
		} else {
			// If State object does not exist, it is defined and loaded as State
			if(!class_exists('WWW_State')){
				require(__DIR__.DIRECTORY_SEPARATOR.'class.www-state.php');
			}
			$this->state=new WWW_State();
		}
		
		// Factory class is loaded, if it doesn't already exist, since MVC classes require it
		if(!class_exists('WWW_Factory')){
			require(__DIR__.DIRECTORY_SEPARATOR.'class.www-factory.php');
		}
		
		// System attempts to load API keys from the default location if they were not defined
		if(!$apiProfiles){
			// Loading and storing API keys
			if(file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'api.profiles.php')){
				require(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'api.profiles.php');
			} else {
				require(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'api.profiles.php');
			}
			$this->apiProfiles=$apiProfiles;
		}
		
	}
	
	// This function replaces the current API state
	// * state - new state object
	public function setState($state){
		$this->state=$state;
	}
	
	// This function clears current API buffer
	// * state - new state object
	public function clearBuffer(){
		$this->buffer=array();
	}
	
	// The main function of API
	// * apiInputData - array of input data
	// * useBuffer - This turns off internal buffer that is used when the same API call is executed many times
	// * apiValidation - internally called API does not need to be hash validated, unless necessary
	// * useLogger - Whether logger array is updated during execution
	// Returns the result of the API call, depending on command and classes it loads
	public function command($apiInputData=array(),$useBuffer=false,$apiValidation=true,$useLogger=false){
	
		// DEFAULT VALUES, COMMAND AND BUFFER CHECK
		
			// This stores information about current API command state
			// Various defaults are loaded from State
			$apiState=array(
				'command'=>false,
				'hash'=>false,
				'return-hash'=>false,
				'secret-key'=>false,
				'token'=>false,
				'output-crypt-key'=>false,
				'profile'=>$this->state->data['api-public-profile'],
				'timestamp-timeout'=>$this->state->data['api-timestamp-timeout'],
				'token-timeout'=>$this->state->data['api-token-timeout'],
				'cache-timeout'=>false,
				'return-type'=>'json',
				'push-output'=>true,
				'content-type-header'=>false,
				'minify-output'=>false,
				'last-modified'=>$this->state->data['request-time'],
				'custom-header'=>false
			);
	
			// If API command is not set and is not set in input array either, the system will return false
			if(isset($apiInputData['www-command'])){
				$apiState['command']=$apiInputData['www-command'];
			} else {
				return $this->output(array('www-error'=>'API command not set','www-error-code'=>1),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
			}
			
			// Existing response is checked from buffer if it exists
			if($useBuffer){
				$commandBufferAddress=md5($apiState['command'].json_encode($apiInputData));
				// If result already exists in buffer then it is simply returned
				if(isset($this->buffer[$commandBufferAddress])){
					return $this->buffer[$commandBufferAddress];
				}
			}
			
		// API PROFILE INITIALIZATION AND INITIAL VALIDATION
			
			// API profile data is loaded only if API profile is set and is not set as public profile
			// If profile is public, then hash validations and certain encryption options are not available
			if(isset($apiInputData['www-profile']) && $apiInputData['www-profile']!='' && $apiInputData['www-profile']!=$this->state->data['api-public-profile']){
			
				// Current API profile is assigned to state
				// This is useful for controller development if you wish to restrict certain controllers to only certain API profiles
				$apiState['profile']=$apiInputData['www-profile'];
				
				// This checks whether API profile information is defined in /resources/api.profiles.php file
				if(isset($this->apiProfiles[$apiInputData['www-profile']]) && isset($this->apiProfiles[$apiInputData['www-profile']]['secret-key'])){
				
					// Both hash and timestamp are required to be set in the request, when API profile is used
					if(isset($apiInputData['www-hash']) && $apiInputData['www-hash']!='' && isset($apiInputData['www-timestamp']) && $apiInputData['www-timestamp']!=''){
					
						// Checking for conditions found in API profile parameters
						if(isset($this->apiProfiles[$apiInputData['www-profile']]['disabled']) && $this->apiProfiles[$apiInputData['www-profile']]['disabled']==1){
							// If profile is set to be disabled
							return $this->output(array('www-error'=>'API profile is disabled','www-error-code'=>5),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
						} elseif(isset($this->apiProfiles[$apiInputData['www-profile']]['ip']) && $this->apiProfiles[$apiInputData['www-profile']]['ip']!='*' && !in_array($this->state->data['true-client-ip'],explode(',',$this->apiProfiles[$apiInputData['www-profile']]['ip']))){
							// If profile has IP set and current IP is not allowed
							return $this->output(array('www-error'=>'API profile not allowed from this IP','www-error-code'=>6),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
						}
						
						// Timestamp timeout
						if(isset($this->apiProfiles[$apiInputData['www-profile']]['timestamp-timeout'])){
							$apiState['timestamp-timeout']=$this->apiProfiles[$apiInputData['www-profile']]['timestamp-timeout'];
						}
						
						// Request-sent timestamp must be within set amount of seconds for API to accept the command
						if($apiState['timestamp-timeout']>=($this->state->data['request-time']-$apiInputData['www-timestamp'])){
					
							// Sent request timestamp, this is used to validate command 'time'
							$apiState['hash']=$apiInputData['www-hash'];
							// Secret key
							$apiState['secret-key']=$this->apiProfiles[$apiInputData['www-profile']]['secret-key'];
							
							// Token timeout
							if(isset($this->apiProfiles[$apiInputData['www-profile']]['token-timeout'])){
								$apiState['token-timeout']=$this->apiProfiles[$apiInputData['www-profile']]['token-timeout'];
							}
							
							// Session filename is a simple hashed API profile name
							$apiTokenFile=md5($apiState['profile']).'.tmp';
							// Session folder in filesystem
							$apiTokenDirectory=$this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.substr($apiTokenFile,0,2).DIRECTORY_SEPARATOR;
							
							// Checking if valid token is active
							if(file_exists($apiTokenDirectory.$apiTokenFile) && filemtime($apiTokenDirectory.$apiTokenFile)>=($this->state->data['request-time']-$apiState['token-timeout'])){
								// Loading contents of current token file to API state
								$apiState['token']=file_get_contents($apiTokenDirectory.$apiTokenFile);
								// This updates the last-modified time, thus postponing the time how long the token is considered valid
								touch($apiTokenDirectory.$apiTokenFile);
							} elseif($apiState['command']!='www-create-session' && $apiState['command']!='www-destroy-session'){
								// Token is not required for commands that create or destroy existing tokens
								return $this->output(array('www-error'=>'API token does not exist or is timed out','www-error-code'=>7),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
							}
							
						} else {
							return $this->output(array('www-error'=>'Request timestamp is too old','www-error-code'=>4),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
						}
						
					} else {
						return $this->output(array('www-error'=>'Request validation hash or timestamp is missing','www-error-code'=>3),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
					}
				} else {
					return $this->output(array('www-error'=>'Valid API profile not found','www-error-code'=>2),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
				}
			}
			
		// ASSIGNING WWW-* VALUES FROM INPUT TO API STATE
			
			// This tests if cache value sent through input is valid
			if(isset($apiInputData['www-cache-timeout']) && $apiInputData['www-cache-timeout']>=0 && (!isset($apiInputData['www-no-cache']) || $apiInputData['www-no-cache']==false)){
				$apiState['cache-timeout']=$apiInputData['www-cache-timeout'];
			}
		
			// By default the API command returns a JSON string, but another type can be used if defined
			if(isset($apiInputData['www-return-type'])){
				$apiState['return-type']=$apiInputData['www-return-type'];
			}
			
			// By default the API assumes that the result is 'echoed/printed' out together with headers, but this behavior can be supressed
			if(isset($apiInputData['www-output'])){
				$apiState['push-output']=$apiInputData['www-output'];
			}
			
			// If this is set then user agent requests that API also returns a hash validation check (calculated from input and secret key) and timestamp in the response
			if(isset($apiInputData['www-return-hash'])){
				$apiState['return-hash']=$apiInputData['www-return-hash'];
			}
			
			// This can be used to overwrite the default content type that is returned. Note that this does not affect the data type itself
			// This can be used to send HTML headers and return JSON string, for example
			if(isset($apiInputData['www-content-type'])){
				$apiState['content-type-header']=$apiInputData['www-content-type'];
			}
			
			// Minification can be applied to certain data types, such as CSS, JavaScript, HTML and XML
			// This is not recommended to be added to all output, but can be useful in some situations
			if(isset($apiInputData['www-minify'])){
				$apiState['minify-output']=$apiInputData['www-minify'];
			}
			
		// HANDLING CRYPTED INPUT
			
			// If crypted input is set
			if(isset($apiInputData['www-crypt-input'])){
				// Crypted input is only possible with non-public profiles and an active session
				if($apiState['profile']!=$this->state->data['api-public-profile'] && $apiState['token']){
					// Mcrypt is required for decryption
					if(extension_loaded('mcrypt')){
						// Rijndael 256 bit decryption is used in CBC mode
						$decryptedData=$this->decryptRijndael256($apiInputData['www-crypt-input'],$apiState['token'],$apiState['secret-key']);
						if($decryptedData){
							// Unserializing crypted data with JSON
							$decryptedData=json_decode($decryptedData,true);
							// Unserialization can fail if the data is not in correct format
							if($decryptedData && is_array($decryptedData)){
								// Merging crypted input with set input data
								$apiInputData=$decryptedData+$apiInputData;
							} else {
								return $this->output(array('www-error'=>'Unserialized decrypted data is not an array','www-error-code'=>11),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
							}
						} else {
							return $this->output(array('www-error'=>'Failed to decrypt data','www-error-code'=>10),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
						}	
					} else {
						return $this->output(array('www-error'=>'Unable to decrypt data','www-error-code'=>9),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
					}
				} else {
					return $this->output(array('www-error'=>'Crypted input can only be sent with non-public API profile with an active token','www-error-code'=>8),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
				}
			}
			
			// If this is set, then the value of this is used to crypt the output
			// Please note that while www-crypt-output key can be set outside www-crypt-input data, it is recommended to keep that key within crypted input when making a request
			if(isset($apiInputData['www-crypt-output'])){
				$apiState['output-crypt-key']=$apiInputData['www-crypt-output'];
			}
			
		// NON-PUBLIC PROFILE VALIDATION
		
			// Input data and hash validation is only done for non-public profiles
			if($apiState['profile']!=$this->state->data['api-public-profile']){
			
				// BUILDING VALIDATION HASH
			
					// Validation hash is built from set input data
					$validationHash=$apiInputData;
					// Unsetting validation hash itself from input
					unset($validationHash['www-hash']);
					// Session input is not considered for validation hash and is unset
					if(isset($validationHash['www-session'])){
						unset($validationHash['www-session']);
					}
					// Cookie input is not considered for validation hash and is unset
					if(isset($validationHash['www-cookies'])){
						unset($validationHash['www-cookies']);
					}
					
					// Validation data is sorted by keys
					ksort($validationHash);
					
					// Final validation hash depends on whether the command was about session creation, destruction or any other command
					if($apiState['command']=='www-create-session' || $apiState['command']=='www-destroy-session'){
						// Session creation and destruction commands have validation hashes built only with the secret key
						$validationHash=sha1(json_encode($validationHash,JSON_NUMERIC_CHECK).$apiState['secret-key']);
					} else {
						// Every other command takes token into account when calculating the validation hash
						$validationHash=sha1(json_encode($validationHash,JSON_NUMERIC_CHECK).$apiState['token'].$apiState['secret-key']);
					}
					
				// HASH VALIDATION AND SESSION COMMANDS
					
					// Provided hash in the request must match the calculated hash
					if($validationHash==$apiState['hash']){
					
						// These two commands are an exception to the rule, these are the only non-public profile commands that can be executed without requiring a valid token
						if($apiState['command']=='www-create-session'){
						
							// If session token subdirectory does not exist, it is created
							if(!is_dir($apiTokenDirectory)){
								if(!mkdir($apiTokenDirectory,0777)){
									throw new Exception('Cannot create sessions folder');
								}
							}
							// Token for API access is generated simply from current profile name and request time
							$apiState['token']=md5($apiState['profile'].$this->state->data['request-time']);
							// Session token file is created and token itself is returned to the user agent as a successful request
							if(file_put_contents($apiTokenDirectory.$apiTokenFile,$apiState['token'])){
								// Token is returned to user agent together with current token timeout setting
								return $this->output(array('www-token'=>$apiState['token'],'www-token-timeout'=>$apiState['token-timeout']),$apiState);
							} else {
								throw new Exception('Cannot create API token file');
							}

						} elseif($apiState['command']=='www-destroy-session'){
						
							// Making sure that the token file exists, then removing it
							if(file_exists($apiTokenDirectory.$apiTokenFile)){
								unlink($apiTokenDirectory.$apiTokenFile);
							}
							// Returning success message
							return $this->output(array('www-result'=>'Token destroyed'),$apiState);
						
						} elseif($apiState['command']=='www-validate-session'){
							// This simply returns output
							return $this->output(array('www-result'=>'API profile validation successful'),$apiState);
						}	
					
					} else {
						return $this->output(array('www-error'=>'API profile input hash validation failed','www-error-code'=>12),$apiState+array('custom-header'=>'HTTP/1.1 403 Forbidden'));
					}
			
			}
		
		// CACHE HANDLING IF CACHE IS USED
			
			// Result of the API call is stored in this variable
			$apiResult=false;
			
			// This stores a flag about whether cache is used or not
			$this->apiLoggerData['cache-used']=false;
		
			// If cache timeout is set
			// If this value is 0, then no cache is used for command
			if($apiState['cache-timeout']){
			
				// Calculating cache validation string
				$cacheValidator=$apiInputData;
				// If session cookie is defined, it is removed from input data for cache validation
				if($this->state->data['session-cookie'] && isset($cacheValidator['www-cookies'][$this->state->data['session-cookie']])){
					unset($cacheValidator['www-cookies'][$this->state->data['session-cookie']]);
				}
				// MD5 is used for slight performance benefits over sha1() when calculating cache validation hash string
				$cacheValidator=md5($apiState['command'].json_encode($cacheValidator).$apiState['return-type'].$apiState['push-output']);
				
				// Cache filename consists of API command, serialized input data, return type and whether API output is used.
				$cacheFile=$cacheValidator.'.tmp';
				// Setting cache folder
				$cacheFolder=$this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR;
				// Cache subfolder is taken from first three characters of cache filename
				$cacheSubfolder=substr($cacheFile,0,2).DIRECTORY_SEPARATOR;
				
				// If cache file exists, it will be parsed and set as API value
				if(file_exists($cacheFolder.$cacheSubfolder.$cacheFile)){
				
					// Current cache timeout is used to return to browser information about how long browser should store this result
					$apiState['last-modified']=filemtime($cacheFolder.$cacheSubfolder.$cacheFile);
					
					// If server detects its cache to still within cache limit
					if($apiState['last-modified']>=$this->state->data['request-time']-$apiState['cache-timeout']){
					
						// If this request has already been made and the last-modified timestamp is exactly the same
						if($apiState['push-output'] && $this->state->data['http-if-modified-since'] && $this->state->data['http-if-modified-since']==$apiState['last-modified']){
							// Adding log data
							if($useLogger){
								$this->apiLoggerData['cache-used']=true;
								$this->apiLoggerData['response-code']=304;
							}
							// Cache headers (Last modified is never sent with 304 header)
							if($this->state->data['http-authentication']==true){
								header('Cache-Control: private,max-age='.($apiState['last-modified']+$apiState['cache-timeout']-$this->state->data['request-time']).',must-revalidate');
							} else {
								header('Cache-Control: public,max-age='.($apiState['last-modified']+$apiState['cache-timeout']-$this->state->data['request-time']).',must-revalidate');
							}
							header('Expires: '.gmdate('D, d M Y H:i:s',($apiState['last-modified']+$apiState['cache-timeout'])).' GMT');
							// Returning 304 header
							header('HTTP/1.1 304 Not Modified');
							return true;
						}
						
						// System loads the result from cache file based on return data type
						if($apiState['return-type']=='html'){
							$apiResult=file_get_contents($cacheFolder.$cacheSubfolder.$cacheFile);
						} elseif($apiState['return-type']=='php'){
							$apiResult=unserialize(file_get_contents($cacheFolder.$cacheSubfolder.$cacheFile));
						} else {
							$apiResult=json_decode(file_get_contents($cacheFolder.$cacheSubfolder.$cacheFile),true);
						}
						
						// Since cache was used
						$this->apiLoggerData['cache-used']=true;
						
					} else {
						// Since cache seems to be outdated, a new one will be generated with new request time
						$apiState['last-modified']=$this->state->data['request-time'];
					}
					
				} else {
					// Current cache timeout is used to return to browser information about how long browser should store this result
					$apiState['last-modified']=$this->state->data['request-time'];
				}
				
			} else {
				// Since cache is not used, last modified time is the time of the request
				$apiState['last-modified']=$this->state->data['request-time'];
			}
		
		// SOLVING API RESULT IF RESULT WAS NOT FOUND IN CACHE
		
			// If cache was not used and command result is not yet defined, system will execute the API command
			if(!$apiResult){
			
				// HTML and text data types can be echoed/printed in their view files, result of this is gathered by output buffer
				if($apiState['return-type']=='html' || $apiState['return-type']=='text'){
					ob_start();
				}

				// API command is solved into bits to be parsed
				$commandBits=explode('-',$apiState['command'],2);
				// Class name is found based on command
				$className='WWW_controller_'.$commandBits[0];
				// Class is defined and loaded, if it is not already defined
				if(!class_exists($className)){
					// Overrides can be used for controllers
					if(file_exists($this->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php')){
						require($this->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php');
					} elseif(file_exists($this->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php')){
						require($this->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php');
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'User agent request recognized, but unable to handle (controller not found)','www-error-code'=>13),$apiState+array('custom-header'=>'HTTP/1.1 501 Not Implemented'));
					}
				}
				
				// Second half of the command string is used to solve the function that is called by the command
				if(isset($commandBits[1])){
					// Solving method name, dashes are underscored
					$methodName=str_replace('-','_',$commandBits[1]);
					// New controller is created based on API call
					$controller=new $className($this);
					// If command method does not exist, 501 page is returned or error triggered
					if(!method_exists($controller,$methodName)){
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'User agent request recognized, but unable to handle (controller method not found)','www-error-code'=>14),$apiState+array('custom-header'=>'HTTP/1.1 501 Not Implemented'));
					}
					// Result of the command is solved with this call
					// Input data is also submitted to this function
					$apiResult=$controller->$methodName($apiInputData);
				} else {
					// Since an error was detected, system pushes for output immediately
					return $this->output(array('www-error'=>'User agent request recognized, but unable to handle (controller method not found)','www-error-code'=>14),$apiState+array('custom-header'=>'HTTP/1.1 501 Not Implemented'));
				}
				
				// If returned data type was using output buffer, then that is gathered for the result instead
				if($apiState['return-type']=='html' || $apiState['return-type']=='text'){
					$apiResult=ob_get_clean();
				}
				
				// If cache timeout was set then the result is stored as a cache in the filesystem
				if($apiState['cache-timeout']){
					// If cache subdirectory does not exist, it is created
					if(!is_dir($cacheFolder.$cacheSubfolder)){
						if(!mkdir($cacheFolder.$cacheSubfolder,0777)){
							throw new Exception('Cannot create cache folder');
						}
					}
					// If returned data is HTML or text, it is simply written into cache file
					// Other results are serialized before being written to cache
					if($apiState['return-type']=='html' || $apiState['return-type']=='text'){
						if(!file_put_contents($cacheFolder.$cacheSubfolder.$cacheFile,$apiResult)){
							throw new Exception('Cannot write cache file');
						}
					} elseif($apiState['return-type']=='php'){
						if(!file_put_contents($cacheFolder.$cacheSubfolder.$cacheFile,serialize($apiResult))){
							throw new Exception('Cannot write cache file');
						}
					} else {
						if(!file_put_contents($cacheFolder.$cacheSubfolder.$cacheFile,json_encode($apiResult))){
							throw new Exception('Cannot write cache file');
						}
					}
				}
				
			}
		
		// SENDING RESULT TO OUTPUT
		
			// If buffer is not disabled, response is checked from buffer
			if($useBuffer){
				$commandBufferAddress=md5($apiState['command'].json_encode($apiInputData));
				// Storing result in buffer
				$this->buffer[$commandBufferAddress]=$this->output($apiResult,$apiState,$useLogger);
				// Returning result from newly created buffer
				return $this->buffer[$commandBufferAddress];
			} else {
				// System returns correctly formatted output data
				return $this->output($apiResult,$apiState,$useLogger);
			}
		
	}
	
	// This function returns the data, whether final data or the one returned with error messages
	// * apiResult - Result of the API call
	// * apiState - Various flags from command execution
	// * useLogger - If logger is used
	// Returns final-formatted data
	private function output($apiResult,$apiState,$useLogger=true){
		
		// This filters the result through various PHP and header specific commands
		if(!isset($apiResult['www-disable-callbacks']) || $apiResult['www-disable-callbacks']==false){
			$this->apiCallbacks($apiResult,$useLogger);
		}
		
		// OUTPUT HASH VALIDATION
		
			// If request demanded a hash to also be returned
			// This is only valid when the result is not an 'error' and has a secret key set
			if($apiState['return-hash'] && !isset($apiResult['www-error']) && $apiState['secret-key']){
				// This is used to timestamp the time hash was created
				$apiResult['www-timestamp']=time();
				// Hash is calculated from all the data returned to the user agent, except hash itself
				$returnHash=$apiResult;
				// Sorting the output data
				ksort($returnHash);
				// Hash is written to returned result
				// Session creation and destruction commands return data is hashed without token
				if($apiState['command']=='www-create-session' || $apiState['command']=='www-destroy-session'){
					$apiResult['www-hash']=sha1(json_encode($returnHash,JSON_NUMERIC_CHECK).$apiState['secret-key']);
				} else {
					$apiResult['www-hash']=sha1(json_encode($returnHash,JSON_NUMERIC_CHECK).$apiState['token'].$apiState['secret-key']);
				}
			}
		
		// DATA CONVERSION FROM RESULT TO REQUESTED FORMAT
	
			// Data is custom-formatted based on request
			switch($apiState['return-type']){
				case 'json':
					// Encodes the resulting array in JSON
					$apiResult=json_encode($apiResult);
					break;
				case 'binary':
					// If the result is empty string or empty array or false, then binary returns a 0, otherwise it returns 1
					$apiResult=$this->toBinary($apiResult);
					break;
				case 'xml':
					// This is only properly returned if SimpleXML is supported
					if(extension_loaded('SimpleXML')){
						// Result array is turned into an XML string with SimpleXML
						$apiResult=$this->toXML($apiResult);
					} else {
						$apiResult='<?xml version="1.0" encoding="utf-8"?><www><error>XML is not supported</error></www>';
					}
					break;
				case 'rss':
					// This is only properly returned if SimpleXML is supported
					if(extension_loaded('SimpleXML')){
						// Result array is turned into an XML string with SimpleXML with RSS headers.
						// The data should be formatted based on RSS 2.0 specification
						$apiResult=$this->toXML($apiResult,'rss');
					} else {
						$apiResult='<?xml version="1.0" encoding="utf-8"?><www><error>RSS is not supported</error></www>';
					}
					break;
				case 'csv':
					// Result array is turned into a CSV file
					$apiResult=$this->toCSV($apiResult);
					break;
				case 'serializedarray':
					// Array is simply serialized
					$apiResult=serialize($apiResult);
					break;
				case 'querystring':
					// Array is built into serialized query string
					$apiResult=http_build_query($apiResult);
					break;
				case 'ini':
					// This converts result into an INI string
					$apiResult=$this->toINI($apiResult);
					break;
				case 'php':
					// If PHP is used, then it can not be 'echoed' out due to being a PHP variable, so this is turned off
					$apiState['push-output']=false;
					break;
			}
		
		// IF OUTPUT IS REQUESTED TO BE CRYPTED
		
			if($apiState['return-type']!='php' && $apiState['push-output'] && $apiState['output-crypt-key']){
				// Returned result will be with plain text instead of requested format, but only if header is not already overwritten
				if(!$apiState['content-type-header']){
					$apiState['content-type-header']='Content-Type: text/plain;charset=utf-8';
				}
				// If token timeout is set, then profile must be defined
				if($apiState['secret-key']){
					// If secret key is set, then output will be crypted with CBC mode
					$apiResult=$this->encryptRijndael256($apiResult,$apiState['output-crypt-key'],$apiState['secret-key']);
				} else {
					// If secret key is not set (for public profiles), then output will be crypted with ECB mode
					$apiResult=$this->encryptRijndael256($apiResult,$apiState['output-crypt-key']);
				}
			}
		
		// MINIFICATION
	
			// If minification is requested from API
			if($apiState['minify-output']==1){
			
				// Including minification class if it is not yet defined
				if(!class_exists('WWW_Minify')){
					require(__DIR__.DIRECTORY_SEPARATOR.'class.www-minifier.php');
				}
				
				// Minification is based on the type of class
				switch($apiState['return-type']){
					case 'xml':
						// XML minification eliminates extra spaces and newlines and other formatting
						$apiResult=WWW_Minifier::minifyXML($apiResult);
						break;
					case 'html':
						// HTML minification eliminates extra spaces and newlines and other formatting
						$apiResult=WWW_Minifier::minifyHTML($apiResult);
						break;
					case 'js':
						// JavaScript minification eliminates extra spaces and newlines and other formatting
						$apiResult=WWW_Minifier::minifyJS($apiResult);
						break;
					case 'css':
						// CSS minification eliminates extra spaces and newlines and other formatting
						$apiResult=WWW_Minifier::minifyCSS($apiResult);
						break;
					case 'rss':
						// RSS minification eliminates extra spaces and newlines and other formatting
						$apiResult=WWW_Minifier::minifyXML($apiResult);
						break;
				}
				
			}
		
		// FINAL OUTPUT OF RESULT
	
			// Result is printed out, headers and cache control are returned to the user agent, if output flag was set for the command
			if($apiState['push-output']){
			
				// CACHE AND CUSTOM HEADERS
			
					// Cache control settings sent to the user agent depend on cache timeout settings
					if($apiState['cache-timeout']!=0){
						// Cache control depends whether HTTP authentication is used or not
						if($this->state->data['http-authentication']==true){
							header('Cache-Control: private,max-age='.($apiState['last-modified']+$apiState['cache-timeout']-$this->state->data['request-time']).',must-revalidate');
						} else {
							header('Cache-Control: public,max-age='.($apiState['last-modified']+$apiState['cache-timeout']-$this->state->data['request-time']).',must-revalidate');
						}
						header('Expires: '.gmdate('D, d M Y H:i:s',($apiState['last-modified']+$apiState['cache-timeout'])).' GMT');
						header('Last-Modified: '.gmdate('D, d M Y H:i:s',$apiState['last-modified']).' GMT');
					} else {
						// When no cache is used, request tells specifically that
						header('Cache-Control: no-store;');
						header('Expires: '.gmdate('D, d M Y H:i:s',$this->state->data['request-time']).' GMT');
						header('Last-Modified: '.$apiState['last-modified'].' GMT');
					}
					
					// If custom header was assigned, it is added
					if($apiState['custom-header']){
						header($apiState['custom-header']);
					}
					
					// Removing all Pragma headers if server has set them
					header_remove('Pragma');
				
				// CONTENT TYPE HEADERS
				
					// If content type was set in the request then that is used for content type
					if($apiState['content-type-header']){
						// UTF-8 is always used for returned data
						header('Content-Type: '.$apiState['content-type-header'].';charset=utf-8');
					} else {
						// Data is echoed/printed based on return data type formatting with the proper header
						switch($apiState['return-type']){
							case 'json':
								header('Content-Type: application/json;charset=utf-8;');
								break;
							case 'xml':
								header('Content-Type: text/xml;charset=utf-8;');
								break;
							case 'html':
								header('Content-Type: text/html;charset=utf-8');
								break;
							case 'rss':
								header('Content-Type: application/rss+xml;charset=utf-8;');
								break;
							case 'csv':
								header('Content-Type: text/csv;charset=utf-8;');
								break;
							case 'js':
								header('Content-Type: application/javascript;charset=utf-8');
								break;
							case 'css':
								header('Content-Type: text/css;charset=utf-8');
								break;
							case 'vcard':
								header('Content-Type: text/vcard;charset=utf-8');
								break;
							default:
								// Every other case assumes text/plain response from server
								header('Content-Type: text/plain;charset=utf-8');
								break;
						}
					}
				
				// OUTPUT COMPRESSION
				
					// Gathering output in buffer
					ob_start();
					
					// Since output was turned on, result is loaded into the output buffer
					echo $apiResult;
					
					// If output compression is turned on then the content is compressed
					if($this->state->data['output-compression']!=false){
						// Different compression options can be used
						switch($this->state->data['output-compression']){
							case 'deflate':
								// Notifying user agent of deflated output
								header('Content-Encoding: deflate');
								$apiResult=gzdeflate(ob_get_clean(),9);
								break;
							case 'gzip':
								// Notifying user agent of gzipped output
								header('Content-Encoding: gzip');
								// This tells proxies to store both compressed and uncompressed version
								header('Vary: Accept-Encoding');
								$apiResult=gzencode(ob_get_clean(),9);
								break;
							default:
								$apiResult=ob_get_clean();
								break;
						}
					} else {
						// Getting data from output buffer
						$apiResult=ob_get_clean();
					}
				
				// PUSHING TO USER AGENT
				
					// Current output content length
					$contentLength=strlen($apiResult);
					
					// Content length is defined that can speed up website requests, letting user agent to determine file size
					header('Content-Length: '.$contentLength); 
					
					// Data is only updated if logger is used
					if($useLogger){
						// Notifying logger of content length
						$this->apiLoggerData['content-length']=$contentLength;
					}
					
					// Data is returned to the user agent
					echo $apiResult;
					
					// Processing is done
					return true;
				
			} else {
				// Since result was not output it is simply returned
				return $apiResult;
			}
		
	}
	
	// This function checks the data for various keys and values that might affect headers
	// * data - Data array
	// * useLogger - If logger is used
	// Always returns true after filtering
	private function apiCallbacks($data,$useLogger){
	
		// HEADERS
	
			// This sets a specific header
			if(isset($data['www-set-header'])){
				// It is possible to set multiple headers simultaneously
				if(is_array($data['www-set-header'])){
					foreach($data['www-set-header'] as $header){
						header($header);
					}
				} else {
					header($data['www-set-header']);
				}
			}
			
		// COOKIES AND SESSIONS
		
			// This adds cookie from an array of settings
			if(isset($data['www-set-cookie']) && is_array($data['www-set-cookie'])){
				foreach($data['www-set-cookie'] as $cookie){
					// Cookies require name and value to be set
					if(isset($cookie['name']) && isset($cookie['value'])){
						// Setting defaults to non-required cookies
						if(!isset($cookie['expire'])){
							$cookie['expire']=2147483647;
						}
						if(!isset($cookie['path'])){
							$cookie['path']=$this->state->data['web-root'];
						}
						if(!isset($cookie['domain'])){
							$cookie['domain']=$this->state->data['http-host'];
						}
						if(!isset($cookie['secure'])){
							$cookie['secure']=false;
						}
						if(!isset($cookie['httponly'])){
							$cookie['httponly']=false;
						}
						// Setting the cookie
						setcookie($cookie['name'],$cookie['value'],$cookie['expire'],$cookie['path'],$cookie['domain'],$cookie['secure'],$cookie['httponly']);
					}
				}
			}
			
			// This unsets cookie in user agent
			if(isset($data['www-unset-cookie'])){
				// Multiple cookies can be unset simultaneously
				if(is_array($data['www-unset-cookie'])){
					foreach($data['www-unset-cookie'] as $cookie){
						// Cookie is unset only if it actually exists
						if(isset($_COOKIE[$cookie])){
							// Setting 0 as timestamp will delete the cokie at the end of session
							setcookie($cookie,0);
						}
					}
				} else {
					// Cookie is unset only if it actually exists
					if(isset($_COOKIE[$data['www-unset-cookie']])){
						setcookie($data['www-unset-cookie'],0);
					}
				}
			}
		
			// This starts sessions if session-specific funtions are used
			if((isset($data['www-set-session']) || isset($data['www-unset-session'])) && !session_id()){
				if($this->state->data['session-cookie']){
					session_name($this->state->data['session-cookie']);
				}
				session_start();
			}

			// This adds a session
			if(isset($data['www-set-session']) && is_array($data['www-set-session'])){
				foreach($data['www-set-session'] as $session){
					// Sessions require name and value to be set
					if(isset($session['name']) && isset($session['value'])){
						$_SESSION[$session['name']]=$session['value'];
					}
				}
			}
			
			// This unsets cookie in user agent
			// Multiple sessions can be unset simultaneously
			if(isset($data['www-unset-session'])){
				if(is_array($data['www-unset-session'])){
					foreach($data['www-unset-session'] as $session){
						// Session is unset only if it actually exists
						if(isset($_SESSION[$session])){
							unset($_SESSION[$session]);
						}
					}
				} else {
					// Session is unset only if it actually exists
					if(isset($_SESSION[$data['www-unset-session']])){
						unset($_SESSION[$data['www-unset-session']]);
					}
				}
			}
		
		// REDIRECTS
		
			// It is possible to re-direct API after submission
			if(isset($data['www-temporary-redirect'])){
				if($useLogger){
					// Adding log entry
					$this->apiLoggerData['response-code']=302;
					$this->apiLoggerData['temporary-redirect']=$data['www-temporary-redirect'];
				}
				// Redirection header
				header('Location: '.$data['www-temporary-redirect'],true,302);
				
			} elseif(isset($data['www-permanent-redirect'])){
				if($useLogger){
					// Adding log entry
					$this->apiLoggerData['response-code']=301;
					$this->apiLoggerData['permanent-redirect']=$data['www-permanent-redirect'];
				}
				// Redirection header
				header('Location: '.$data['www-permanent-redirect'],true,301);
				
			}
	
		// Processing complete
		return true;
	
	}
	
	// Formats the API result array to XML string
	// * apiResult - data returned from API call
	// Returns text XML string
	private function toXML($apiResult,$type=false){
	
		// If result is an array, then separate processing is required
		if(is_array($apiResult)){
			// Different XML header is used based on whether it is an RSS or not
			if(!$type){
				$xml=new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><www></www>');
			} elseif($type=='rss'){
				$xml=new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rss version="2.0"></rss>');
			}
			// This is the custom function used to generate XML from array nodes
			$f=create_function('$f,$c,$apiResult','
					foreach($apiResult as $k=>$v) {
						if(is_array($v)) {
							if(is_numeric($k)){ 
								$k=\'node\'; 
							}
							$ch=$c->addChild($k);
							$f($f,$ch,$v);
						} else {
							$c->addChild($k,$v);
						}
					}');
			// Custom function is applied to result
			$f($f,$xml,$apiResult);
			// Returning the result as an XML string
			return $xml->asXML();
		} else {
			// System returns a simple XML string since the result was not an array
			if(!$type){
				return '<?xml version="1.0" encoding="utf-8"?><www><![CDATA['.$apiResult.']]></www>';
			} elseif($type=='rss'){
				return '<?xml version="1.0" encoding="utf-8"?><rss version="2.0"><![CDATA['.$apiResult.']]></rss>';
			}
		}
		
	}
	
	// Formats the API result array to CSV string
	// * apiResult - data returned from API call
	// Returns text CSV string
	private function toCSV($apiResult){
	
		// If result is an array then 
		if(is_array($apiResult)){
		
			// Resulting rows are stored in this value
			$result=array();
			
			// First element of the array is output
			$first=array_shift(array_slice($apiResult,0,1,true));
			
			// If the first array element is also an array then multidimensional CSV will be output
			if(is_array($first)){
			
				// System assumes that in a multidimensional array the keys are the column names
				$result[]=implode("\t",array_keys($first));
				
				// Rows will be processed as a result
				foreach($apiResult as $subResult){
					foreach($subResult as $key=>$subSubResult){
						// If result is still an array, then the values are imploded with commas
						if(is_array($subSubResult)){
							$subSubResult=implode(',',$subSubResult);
						}
						// Potential characters will be replaced
						$subResult[$key]=str_replace(array("\n","\t","\r"),array('\n','\t',''),$subSubResult);
					}
					// Rows are separated with a tab character
					$result[]=implode("\t",$subResult);
				}
				
			} else {
			
				// Since first element was not an array, it is assumed that other rows are not either
				foreach($apiResult as $subResult){
					// If other rows are an array, then the result is imploded with a comma
					if(is_array($subResult)){
						$result[]=str_replace(array("\n","\t","\r"),array('\n','\t',''),implode(',',$subResult));
					} else {
						$result[]=str_replace(array("\n","\t","\r"),array('\n','\t',''),$subResult);
					}
				}
				
			}
			
			// Result is imploded and returned
			return implode("\n",$result);
			
		} else {
			// Result was not an array, so we write the result as a single line
			return str_replace(array("\n","\t","\r"),array('\n','\t',''),$apiResult);
		}
		
	}
	
	// Checks if the string or array is not empty, in which case it returns 1, otherwise it returns 0
	// * apiResult - data returned from API call
	// Returns either a 1 or a 0
	private function toBinary($apiResult){
	
		// Separate check depending on whether result was an array or not
		if(is_array($apiResult)){
			// If result is an array and it is not empty, then result is considered 'true'
			if(!empty($apiResult)){
				return 1;
			} else {
				return 0;
			}
		} else {
			// If result was not an array and is not false or is not empty, then it is considered 'true'
			if($apiResult!=false && trim($apiResult)!=''){
				return 1;
			} else {
				return 0;
			}
		}
		
	}
	
	// Converts the result array to a useful INI file
	// * apiResult - data returned from API call
	// Returns text string formatted as INI file
	private function toINI($apiResult){
	
		// INI file is only processed properly if the result is an array
		if(is_array($apiResult)){
		
			// Rows of INI file are stored in this variable
			$result=array();
			
			// Every array value is parsed separately
			foreach($apiResult as $key=>$value){
				// Separate handling based on whether the value is an array or not
				if(is_array($value)){
					// If value is an array then INI group is created for the value
					$result[]='['.$key.']';
					// All of the group values are output
					foreach($value as $subkey=>$subvalue){
						// If this sub-value is another array then it is imploded with commas
						if(is_array($subvalue)){
							$result[]=$subkey.'='.implode(',',$subvalue);
						} else {
							// If the value was not an array, then value is simply output in INI format
							$result[]=$subkey.'='.$subvalue;
						}
					}
				} else {
					// If the value was not an array, then value is simply output in INI format
					$result[]=$key.'='.$value;
				}
			}
			
			// Result is imploded into line-breaks for INI format
			return implode("\n",$result);
			
		} else {
			// Just the resulting string is returned since the result was not an array
			return $apiResult;
		}
		
	}
	
	// Function encrypts data based on Rijndael 256bit encryption
	// * data - data to be encrypted
	// * key - key used for encryption
	// * secretKey - used for calculating initialization vector (IV), if this is not set then ECB mode is used
	// Returns encrypted data
	private function encryptRijndael256($data,$key,$secretKey=false){
		if(strlen($key)>32){
			$key=substr($key,0,32);
		}
		if($secretKey){
			return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,$data,MCRYPT_MODE_CBC,md5($secretKey)));
		} else {
			return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,$data,MCRYPT_MODE_ECB));
		}
	}
	
	// Function decrypts data based on Rijndael 256bit encryption
	// * data - data to be decrypted
	// * key - key used for decryption
	// * secretKey - used for calculating initialization vector (IV), if this is not set then ECB mode is used
	// Returns decrypted data
	private function decryptRijndael256($data,$key,$secretKey=false){
		if(strlen($key)>32){
			$key=substr($key,0,32);
		}
		if($secretKey){
			return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$key,base64_decode($data),MCRYPT_MODE_CBC,md5($secretKey)));
		} else {
			return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$key,base64_decode($data),MCRYPT_MODE_ECB));
		}
	}
	
}
	
?>