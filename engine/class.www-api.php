<?php

/*
WWW - PHP micro-framework
API class

One of the three main files of the framework, this file is always required. WWW_API is a class 
that is used for routing all of API commands through, it deals with validation and caching, 
calling the proper controller and returning data with right headers and the right format. It 
consists of essentially just a single function call, but the instance of this API object is 
transferred and used by other components in the system, including all MVC objects created 
with WWW_Factory.

* API profile and hash validation are done by the class
* This class expects proper state object to function properly
* API returns output of JSON, XML, PHP, HTML and plain text formats
* Look at API configuration documentation for details about how API calls can be adjusted

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_API {

	// This stores WWW_State object
	private $state=false;
	
	// This stores API command results in a buffer
	private $buffer=array();
			
	// API requires State object for majority of functionality
	public function __construct($state=false){

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
		if(!class_exists('WWW_Factory') && file_exists(__DIR__.DIRECTORY_SEPARATOR.'class.www-factory.php')){
			require(__DIR__.DIRECTORY_SEPARATOR.'class.www-factory.php');
		}
		
	}
	
	// The main function of API
	// * command - string command for the API
	// * data - array of input data
	// * useBuffer - This turns off internal buffer that is used when the same API call is executed many times
	// * apiCheck - internally called API does not need to be hash validated, unless necessary
	// Returns the result of the API call, depending on command and classes it loads
	public function command($apiCommand='',$apiInputData=array(),$useBuffer=false,$apiCheck=true){
	
		// If buffer is not disabled, response is checked from buffer
		if(!$useBuffer){
			$bufferAddress=md5($apiCommand.json_encode($apiInputData));
			// If result already exists in buffer then it is simply returned
			if(isset($this->buffer[$bufferAddress])){
				return $this->buffer[$bufferAddress];
			}
		}
	
		// Result of the API call is stored in this variable
		$apiResult=false;
		
		// Notifying logger of cache being used
		if($this->state->logger){
			$this->state->logger->cacheUsed=false;
		}
		
		// Setting input data to State as well
		$this->state->data['api-input-data']=$apiInputData;
		
		// If session cookie is defined, it is removed from input data for cache and hash validation reasons
		if(isset($apiInputData[$this->state->data['session-cookie']])){
			unset($apiInputData[$this->state->data['session-cookie']]);
		}
		
		// By default the API command returns a PHP variable, but another type can be used
		if(isset($apiInputData['www-return-data-type'])){
			$returnDataType=$apiInputData['www-return-data-type'];
		} else {
			$returnDataType='php';
		}
		
		// By default the API assumes that the result is not 'echoed/printed' out, so it does return result with content type headers
		if(isset($apiInputData['www-output']) && $apiInputData['www-output']!=0){
			$apiOutput=1;
		} else {
			$apiOutput=0;
		}
		
		// In some cases it is required to set content type that is returned, such as the case when using script.js form() function to disable browser formatting of returned data
		if(isset($apiInputData['www-content-type']) && $apiInputData['www-content-type']!=''){
			$apiContentType=$apiInputData['www-content-type'];
		} else {
			// This set to empty string means that content type header depends on return data type
			$apiContentType='';
		}
		
		// If minification is requested from output
		if(isset($apiInputData['www-minify']) && $apiInputData['www-minify']!=0){
			$apiMinify=$apiInputData['www-minify'];
		} else {
			$apiMinify=false;
		}
		
		// If custom hash serializer function is defined then that is used for serialization
		if(isset($apiInputData['www-serializer'])){
			$apiSerializer=$apiInputData['www-serializer'];
		} else {
			$apiSerializer=$this->state->data['api-serializer'];
		}
		
		// By default the API does not use cache, but if cache timeout is set then this is taken into account
		if(isset($apiInputData['www-cache-timeout'])){
			$cacheTimeout=$apiInputData['www-cache-timeout'];
		} else {
			// This is set to 0 if timeout is not assigned, this setting can never be set in configuration
			$cacheTimeout=0;
		}
		
		// Last modified time of this request, used for caching purposes
		$lastModified=$_SERVER['REQUEST_TIME'];
		
		// Profile name used is defined either from input variables or State
		if(isset($apiInputData['www-profile'])){
			$apiProfile=$apiInputData['www-profile'];
		} else {
			$apiProfile=$this->state->data['api-profile'];
		} 
		
		// API is checked only if API check is enabled and www-profile is set (and is not 'public', which is considered open access profile)
		if($apiCheck && $apiProfile!='public'){
			
			// If this profile has an API key defined, it is assigned for use
			// API keys should never be transmitted with the input data or they might become compromised
			if(isset($this->state->data['api-keys'][$apiProfile])){
				$apiKey=$this->state->data['api-keys'][$apiProfile];
			} else {
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'API key not found'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
			}
		
			// If client provided hash, then this is used, otherwise hash is loaded from State
			if(isset($apiInputData['www-hash'])){
				$apiHash=$apiInputData['www-hash'];
			} else {
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'API hash is not provided'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
			}
			
			// Token-based validation is only created if tokens are enabled
			if($this->state->data['api-token-timeout']!=0 && ($apiCommand=='www-create-token' || $apiCommand=='www-destroy-token' || $apiCommand=='www-validate-token')){
				
				// Token actions should never be cached
				$cacheTimeout=0;
			
				// These commands require separate validation methods
				if($apiCommand=='www-create-token' || $apiCommand=='www-destroy-token'){
				
					// Hash consists of API command, serialized input data and secret API key
					$checkHash=sha1($apiCommand.$apiProfile.$apiKey);
				
					// If hash validation fails the request is blocked
					if($checkHash!=$apiHash){
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'API authentication failed'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
					}
					
				}
			
				// This can be used to return a token to use for further API commands without having to validate the entire input each time
				if($apiCommand=='www-create-token'){
					
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
					
					// If cache subdirectory does not exist, it is created
					if(!is_dir($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR)){
						if(!mkdir($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR,0777)){
							trigger_error('Cannot create sessions folder',E_USER_ERROR);
						}
					}
					
					// Token for API access is generated simply from current profile name, request time and client fingerprint
					$apiToken=md5($apiProfile.$this->state->data['api-request-time'].$this->state->data['fingerprint']);
					
					// Session token file is created and returned to the client as a successful request
					if(!file_put_contents($apiToken,$this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')){
						// Result is output immediately
						return $this->output(array('www-result'=>$apiToken),'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify);
					} else {
						trigger_error('Cannot create session token file',E_USER_ERROR);
					}
					
				}
				
				// Tokens generated for API use can also be removed and timed out prior to their natural timeout
				if($apiCommand=='www-destroy-token'){
				
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
					
					// Only existing sessions can be destroyed
					if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')){
					
						// To destroy a token, the www-hash has to be correct sha1({token}{secret-key})
						$checkHash=sha1(file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp').$apiKey);
						if($checkHash==$apiHash){
						
							unlink($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp');
							// Result is output immediately
							return $this->output(array('www-result'=>'Session token destroyed'),'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify);
							
						} else {
						
							// Since an error was detected, system pushes for output immediately
							return $this->output(array('www-error'=>'Incorrect hash for destroying token'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
							
						}
						
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'Incorrect hash for destroying token'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
					}
				}
				
				// This is used to simply validate whether the token is still active or not
				if($apiCommand=='www-validate-token'){
				
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
					
					// Only existing sessions can be validated
					if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')){
					
						// To validate a token, the www-hash has to be correct sha1({token}{secret-key})
						$checkHash=sha1(file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp').$apiKey);
						if($checkHash==$apiHash){
						
							// Token was validated successfully
							return $this->output(array('www-result'=>'Token is active'),'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify);
							
						} else {
						
							// Since an error was detected, token must be invalid
							return $this->output(array('www-error'=>'Token is not active'),'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify);
							
						}
						
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-result'=>'Token does not exist'),'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify);
					}
				}
				
			} elseif(isset($apiInputData['www-create-token']) || isset($apiInputData['www-destroy-token']) || isset($apiInputData['www-validate-token'])){
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'Token-based validation is turned off'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
			}
			
			// It is possible to have an IP as the API key, in which case only IP is used for validation
			if($apiKey!=$this->state->data['client-ip'] && $apiKey!=$this->state->data['true-client-ip']){
			
				// In case token is requested then in the future commands only the token needs to be hashed by the secret key
				// Token method is less secure than input validation method, but can be easier for client to build
				if(isset($apiInputData['www-use-token']) && $apiInputData['www-use-token']==1){
					
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
				
					// Session tokens are stored in hashed filename under /filesystem/sessions/ directory
					// If token exists, then it is 'recreated' with new timestamp, making API session last longer
					if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp') && ($this->state->data['api-token-timeout']==0 || filemtime($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')>($this->state->data['request-time']-$this->state->data['api-token-timeout']))){
						// API validation is done only with the request token and secret key being hashed
						$checkHash=file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp');
						$checkHash=sha1($apiCommand.$checkHash.$apiKey);
						// This sets the modification time of session token, extending its duration
						touch($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp');
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'API token is outdated or not found'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
					}
					
				} else {
				
					// Hash validation is done when all input data is sorted by keys. This should be also done by client prior to submitting a hash.
					ksort($apiInputData);
					
					// Hash is calculated based on hash serializer that is used
					// Hash consists of API command, serialized input data and secret API key
					switch($apiSerializer){
						case 'json':
							$checkHash=sha1($apiCommand.json_encode($apiInputData).$apiKey);
							break;
						case 'serialize':
							$checkHash=sha1($apiCommand.serialize($apiInputData).$apiKey);
							break;
						default:
							// Since an error was detected, system pushes for output immediately
							return $this->output(array('www-error'=>'API hash validation serialization method not supported'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
					}
					
				}
				
				// If hash validation fails the request is blocked
				if($checkHash!=$apiHash){
					// Since an error was detected, system pushes for output immediately
					return $this->output(array('www-error'=>'API authentication failed'),'HTTP/1.1 403 Forbidden',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
				}
				
			}
				
		}	
		
		// If cache timeout is not 0, that is if cache should be checked for and used, if it exists
		if($cacheTimeout!=0){

			// Cache filename consists of API command, serialized input data, return type and whether API output is used.
			$cacheFile=md5($apiCommand.json_encode($apiInputData).$returnDataType.$apiOutput);
			
			// Cache subfolder is taken from first three characters of cache filename
			$cacheSubfolder=substr($cacheFile,0,2);
			
			// If cache file exists, it will be parsed and set as API value
			if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp')){
			
				// Current cache timeout is used to return to browser information about how long browser should store this result
				$lastModified=filemtime($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp');
				
				// If server detects its cache to still within cache limit
				if($lastModified>=$this->state->data['request-time']-$cacheTimeout){
				
					// If this request has already been made and the last-modified timestamp is the same
					if($apiOutput==1 && $this->state->data['http-if-modified-since'] && $this->state->data['http-if-modified-since']==$lastModified){
						// Adding log entry	
						if($this->state->logger){
							$this->state->logger->cacheUsed=true;
							$this->state->logger->writeLog('304');
						}
						// Returning 304 header
						header('HTTP/1.1 304 Not Modified');
						die();
					}
					
					// System loads the result from cache file based on return data type
					if($returnDataType=='html'){
						$apiResult=file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp');
					} elseif($returnDataType=='php'){
						$apiResult=unserialize(file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp'));
					} else {
						$apiResult=json_decode(file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp'),true);
					}
					
					// Notifying logger of cache being used
					if($this->state->logger){
						$this->state->logger->cacheUsed=true;
					}
					
				} else {
					// Current cache timeout is used to return to browser information about how long browser should store this result
					$lastModified=$this->state->data['request-time'];
				}
				
			} else {
				// Current cache timeout is used to return to browser information about how long browser should store this result
				$lastModified=$this->state->data['request-time'];
			}
		}
		
		// If cache was not used and command result is not yet defined, system will execute the API command
		if(!$apiResult){
		
			// HTML and text data types can be echoed/printed in their view files, result of this is gathered by output buffer
			if($returnDataType=='html' || $returnDataType=='text'){
				ob_start();
			}

			// API command is solved into bits to be parsed
			$commandBits=explode('-',$apiCommand,2);
			
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
					return $this->output(array('www-error'=>'Client request recognized, but unable to handle'),'HTTP/1.1 501 Not Implemented',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
				}
			}
			
			// Second half of the command string is used to solve the function that is called by the command
			if(isset($commandBits[1])){
			
				// Solving method name, dashes are underscored
				$methodName=str_replace('-','_',$commandBits[1]);
				// New controller is created based on API call
				$controller=new $className($this->state,$this);
				
				// If command method does not exist, 501 page is returned or error triggered
				if(!method_exists($controller,$methodName)){
					// Since an error was detected, system pushes for output immediately
					return $this->output(array('www-error'=>'Client request recognized, but unable to handle'),'HTTP/1.1 501 Not Implemented',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
				}
				
				// Result of the command is solved with this call
				// Input data is also submitted to this function
				$apiResult=$controller->$methodName($apiInputData);
				
			} else {
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'Client request recognized, but unable to handle'),'HTTP/1.1 501 Not Implemented',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType);
			}
			
			// If returned data type was using output buffer, then that is gathered for the result instead
			if($returnDataType=='html' || $returnDataType=='text'){
				$apiResult=ob_get_clean();
			}
			
			// If cache timeout was set then the result is stored as a cache in the filesystem
			if($cacheTimeout!=0){
			
				// If cache subdirectory does not exist, it is created
				if(!is_dir($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR)){
					if(!mkdir($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR,0777)){
						trigger_error('Cannot create cache folder',E_USER_ERROR);
					}
				}
				
				// If returned data is HTML or text, it is simply written into cache file
				// Other results are serialized before being written to cache
				if($returnDataType=='html' || $returnDataType=='text'){
					if(!file_put_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp',$apiResult)){
						trigger_error('Cannot write cache file',E_USER_ERROR);
					}
				} elseif($returnDataType=='php'){
					if(!file_put_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp',serialize($apiResult))){
						trigger_error('Cannot write cache file',E_USER_ERROR);
					}
				} else {
					if(!file_put_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp',json_encode($apiResult))){
						trigger_error('Cannot write cache file',E_USER_ERROR);
					}
				}
				
			}
		}
		
		// If buffer is not disabled, response is checked from buffer
		if($useBuffer){
			$bufferAddress=md5($apiCommand.json_encode($apiInputData));
			// Storing result in buffer
			$this->buffer[$bufferAddress]=$this->output($apiResult,'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify,$lastModified);
			// Returning result from newly created buffer
			return $this->buffer[$bufferAddress];
		} else {
			// System returns correctly formatted output data
			return $this->output($apiResult,'',$returnDataType,$apiOutput,$cacheTimeout,$apiContentType,$apiMinify,$lastModified);
		}
	}
	
	// This function returns the data, whether final data or the one returned with error messages
	// * apiResult - Result of the API call
	// Returns final-formatted data
	private function output($apiResult,$customHeader='',$returnDataType='php',$apiOutput=0,$cacheTimeout=0,$apiContentType='',$apiMinify=false,$lastModified=false){
	
		// Data is custom-formatted based on request
		switch($returnDataType){
				
			case 'json':
				// Encodes the resulting array in JSON
				$apiResult=json_encode($apiResult);
				break;
				
			case 'binary':
				// If the result is empty string or empty array or false, then binary returns a 0, otherwise it returns 1
				$apiResult=$this->toBinary($apiResult);
				break;
				
			case 'xml':
				// Result array is turned into an XML string with SimpleXML
				$apiResult=$this->toXML($apiResult);
				break;
				
			case 'rss':
				// Result array is turned into an XML string with SimpleXML with RSS headers.
				// The data should be formatted based on RSS 2.0 specification
				$apiResult=$this->toXML($apiResult,'rss');
				break;
				
			case 'csv':
				// Result array is turned into a CSV file
				$apiResult=$this->toCSV($apiResult);
				break;
				
			case 'serializedarray':
				// Array is simply serialized
				$apiResult=serialize($apiResult);
				break;
				
			case 'ini':
				// This converts result into an INI string
				$apiResult=$this->toINI($apiResult);
				break;
				
		}
	
		// If minification is requested from API
		if($apiMinify==1){
		
			// Including minification class if it is not yet defined
			if(!class_exists('WWW_Minify')){
				require(__DIR__.DIRECTORY_SEPARATOR.'class.www-minifier.php');
			}
			
			// Minification is based on the type of class
			switch($returnDataType){
			
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
					
				case 'php':
					// If PHP is used, then it can not be 'echoed' out due to being a variable
					$apiOutput=0;
					break;
			}
			
		}
	
		// Result is printed out, headers and cache control are returned to the client, if output flag was set for the command
		if($apiOutput==1){
		
			// Cache control settings sent to the client depend on cache timeout settings
			if($cacheTimeout!=0){
			
				// Cache control depends whether HTTP authentication is used or not
				if($this->state->data['http-authentication']==true){
					header('Cache-Control: private,max-age='.($lastModified+$cacheTimeout-$this->state->data['request-time']).',must-revalidate');
				} else {
					header('Cache-Control: public,max-age='.($lastModified+$cacheTimeout-$this->state->data['request-time']).',must-revalidate');
				}
				header('Expires: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT');
				
			} else {
			
				// When no cache is used, request tells specifically that
				header('Cache-Control: no-store;');
				header('Expires: '.gmdate('D, d M Y H:i:s',$this->state->data['request-time']).' GMT');
				
			}
			
			// If custom header was assigned, it is added
			if($customHeader!=''){
				header($customHeader);
			}
			
			// Gathering output in buffer
			ob_start();
			
			// If content type was set, then that is used for content type
			if($apiContentType!=''){
			
				// UTF-8 is always used for returned data
				header('Content-Type: '.$apiContentType.';charset=utf-8',true);
				
			} else {
			
				// Data is echoed/printed based on return data type formatting with the proper header
				switch($returnDataType){
				
					case 'json':
						header('Content-Type:application/json;charset=utf-8;');
						break;
					case 'xml':
						header('Content-Type:text/xml;charset=utf-8;');
						break;
					case 'html':
						header('Content-Type: text/html;charset=utf-8');
						break;
					case 'rss':
						header('Content-Type:application/rss+xml;charset=utf-8;');
						break;
					case 'csv':
						header('Content-Type:text/csv;charset=utf-8;');
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
			
			// last modified date can come from cache file, or current request time, if cache was not used
			if($lastModified!=false){
				header('Last-Modified: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT');
			} else {
				header('Last-Modified: '.gmdate('D, d M Y H:i:s',$this->state->data['request-time']).' GMT');
			}
			
			// Pragma header is removed, if server has set that header
			header_remove('Pragma');
			
			// Since output was turned on, result is echoed
			echo $apiResult;
			
			// If output compression is turned on then the content is compressed
			if($this->state->data['output-compression']!=false){
			
				// Different compression options can be used
				switch($this->state->data['output-compression']){
				
					case 'deflate':
						header('Content-Encoding: deflate');
						$content=gzdeflate(ob_get_clean(),9);
						break;
						
					case 'gzip':
						header('Content-Encoding: gzip');
						$content=gzencode(ob_get_clean(),9);
						break;
						
					default:
						$content=ob_get_clean();
						break;
						
				}
				
				// This tells proxy's to store both compressed and uncompressed version of the resource in cache
				if($this->state->data['output-compression']=='deflate' || $this->state->data['output-compression']=='gzip'){
					// This tells proxies to store both compressed and uncompressed version
					header('Vary: Accept-Encoding');
				}
				
			} else {
			
				// Getting data from output buffer
				$content=ob_get_clean();
				
			}
			
			// Current output content length
			$contentLength=strlen($content);
			
			// Content length is defined that can speed up website requests, letting client to determine file size
			header('Content-Length: '.$contentLength); 
			
			// Notifying logger of content length
			if($this->state->logger){
				$this->state->logger->contentLength=$contentLength;
			}
			
			// Data is returned to the client
			echo $content;
			
			// Processing is done
			return true;
			
		} else {
		
			// Since result was not output it is simply returned
			return $apiResult;
		
		}
		
	}
	
	// Formats the API result array to XML string
	// * apiResult - data returned from API call
	// Returns text XML string
	private function toXML($apiResult,$type=false){
	
		// This function works only if SimpleXML is loaded
		if(extension_loaded('SimpleXML')){
		
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
		} else {
			trigger_error('SimpleXML not supported',E_USER_ERROR);
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
	
}
	
?>