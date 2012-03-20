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
	
	// Simple flag to keep track whether cache has been used by this object
	public $cacheUsed=false;
			
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
	// * apiCheck - internally called API does not need to be hash validated, unless necessary
	// Returns the result of the API call, depending on command and classes it loads
	public function command($command='',$input=array(),$apiCheck=true){
	
		// Result of the API call is stored in this variable
		$apiResult=false;
		
		// API command is written into state
		$this->state->data['api-command']=$command; 

		// Input data is stored in the state
		$this->state->data['api-input-data']=$input;
		
		// If session cookie is defined, it is removed from input data for cache and hash validation reasons
		if(isset($this->state->data['api-input-data'][$this->state->data['session-cookie']])){
			unset($this->state->data['api-input-data'][$this->state->data['session-cookie']]);
		}
		
		// By default the API command returns a PHP variable, but another type can be used
		if(isset($this->state->data['api-input-data']['www-return-data-type'])){
			$this->state->data['api-return-data-type']=$this->state->data['api-input-data']['www-return-data-type'];
		} else {
			$this->state->data['api-return-data-type']='php';
		}
		
		// By default the API assumes that the result is not 'echoed/printed' out, so it does return result with content type headers
		if(isset($this->state->data['api-input-data']['www-output']) && $this->state->data['api-input-data']['www-output']!=0){
			$this->state->data['api-output']=1;
		} else {
			$this->state->data['api-output']=0;
		}
		
		// In some cases it is required to set content type that is returned, such as the case when using script.js form() function to disable browser formatting of returned data
		if(isset($this->state->data['api-input-data']['www-content-type']) && $this->state->data['api-input-data']['www-content-type']!=''){
			$this->state->data['api-content-type']=$this->state->data['api-input-data']['www-content-type'];
		} 
		
		// If minification is requested from output
		if(isset($this->state->data['api-input-data']['www-minify']) && $this->state->data['api-input-data']['www-minify']!=0){
			$this->state->data['api-minify']=$this->state->data['api-input-data']['www-minify'];
		}
		
		// If custom hash serializer function is defined then that is used for serialization
		if(isset($this->state->data['api-input-data']['www-serializer'])){
			$this->state->data['api-serializer']=$this->state->data['api-input-data']['www-serializer'];
		}
		
		// By default the API does not use cache, but if cache timeout is set then this is taken into account
		if(isset($this->state->data['api-input-data']['www-cache-timeout'])){
			$this->state->data['api-cache-timeout']=$this->state->data['api-input-data']['www-cache-timeout'];
		} else {
			$this->state->data['api-cache-timeout']=0;
		}
		
		// Profile name used is defined either from input variables or State
		if(isset($this->state->data['api-input-data']['www-profile'])){
			$apiProfile=$this->state->data['api-input-data']['www-profile'];
		} else {
			$apiProfile=$this->state->data['api-profile'];
		} 
		
		// API is checked only if API check is enabled and www-profile is set (and is not 'public', which is considered open access profile)
		if($apiCheck==true && $this->state->data['api-profile']!='public'){
			
			// If this profile has an API key defined, it is assigned for use
			// API keys should never be transmitted with the input data or they might become compromised
			if(isset($this->state->data['api-keys'][$apiProfile])){
				$this->state->data['api-key']=$this->state->data['api-keys'][$apiProfile];
			} else {
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'API key not found'),'HTTP/1.1 403 Forbidden');
			}
		
			// If client provided hash, then this is used, otherwise hash is loaded from State
			if(isset($this->state->data['api-input-data']['www-hash'])){
				$apiHash=$this->state->data['api-input-data']['www-hash'];
			} else if(isset($this->state->data['api-hash'])){
				$apiHash=$this->state->data['api-hash'];
			} else {
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'API hash is not provided'),'HTTP/1.1 403 Forbidden');
			}
			
			// Token-based validation is only created if tokens are enabled
			if($this->state->data['api-token-timeout']!=0){
			
				// These commands require separate validation methods
				if($this->state->data['api-command']=='www-create-token' || $this->state->data['api-command']=='www-destroy-token'){
				
					// Hash consists of API command, serialized input data and secret API key
					$checkHash=sha1($this->state->data['api-command'].$this->state->data['api-profile'].$this->state->data['api-key']);
				
					// If hash validation fails the request is blocked
					if($checkHash!=$apiHash){
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'API authentication failed'),'HTTP/1.1 403 Forbidden');
					}
					
				}
			
				// This can be used to return a token to use for further API commands without having to validate the entire input each time
				if($this->state->data['api-command']=='www-create-token'){
				
					// This action should never be cached
					$this->state->data['api-cache-timeout']=0;
					
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
					
					// If cache subdirectory does not exist, it is created
					if(!is_dir($this->state->data['system-root'].'filesystem/sessions/'.$sessionSubfolder.'/')){
						if(!mkdir($this->state->data['system-root'].'filesystem/sessions/'.$sessionSubfolder.'/',0777)){
							trigger_error('Cannot create sessions folder',E_USER_ERROR);
						}
					}
					
					// Token for API access is generated simply from current profile name, request time and client fingerprint
					$apiToken=md5($apiProfile.$this->state->data['api-request-time'].$this->state->data['fingerprint']);
					
					// Session token file is created and returned to the client as a successful request
					if(!file_put_contents($apiToken,$this->state->data['system-root'].'filesystem/sessions/'.$sessionSubfolder.'/'.$sessionFile.'.tmp')){
						// Result is output immediately
						return $this->output(array('www-result'=>$apiToken));
					} else {
						trigger_error('Cannot create session token file',E_USER_ERROR);
					}
					
				}
				
				// Tokens generated for API use can also be removed and timed out prior to their natural timeout
				if($this->state->data['api-command']=='www-destroy-token'){
				
					// This action should never be cached
					$this->state->data['api-cache-timeout']=0;
				
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
					
					// Only existing sessions can be destroyed
					if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')){
					
						// To destroy a token, the www-hash has to be correct sha1({token}{secret-key})
						$checkHash=sha1(file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp').$this->state->data['api-key']);
						if($checkHash==$apiHash){
						
							unlink($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp');
							// Result is output immediately
							return $this->output(array('www-result'=>'Session token destroyed'));
							
						} else {
						
							// Since an error was detected, system pushes for output immediately
							return $this->output(array('www-error'=>'Incorrect hash for destroying token'),'HTTP/1.1 403 Forbidden');
							
						}
						
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'Incorrect hash for destroying token'),'HTTP/1.1 403 Forbidden');
					}
				}
				
				// This is used to simply validate whether the token is still active or not
				if($this->state->data['api-command']=='www-validate-token'){
				
					// This action should never be cached
					$this->state->data['api-cache-timeout']=0;
				
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
					
					// Only existing sessions can be validated
					if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')){
					
						// To validate a token, the www-hash has to be correct sha1({token}{secret-key})
						$checkHash=sha1(file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp').$this->state->data['api-key']);
						if($checkHash==$apiHash){
						
							// Token was validated successfully
							return $this->output(array('www-result'=>'Token is active'));
							
						} else {
						
							// Since an error was detected, token must be invalid
							return $this->output(array('www-error'=>'Token is not active'));
							
						}
						
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-result'=>'Token does not exist'));
					}
				}
				
			} else if(isset($this->state->data['api-input-data']['www-create-token']) || isset($this->state->data['api-input-data']['www-destroy-token']) || isset($this->state->data['api-input-data']['www-validate-token'])){
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'Token-based validation is turned off'),'HTTP/1.1 403 Forbidden');
			}
			
			// It is possible to have an IP as the API key, in which case only IP is used for validation
			if($this->state->data['api-key']!=$this->state->data['client-ip'] && $this->state->data['api-key']!=$this->state->data['true-client-ip']){
			
				// In case token is requested then in the future commands only the token needs to be hashed by the secret key
				// Token method is less secure than input validation method, but can be easier for client to build
				if(isset($this->state->data['api-input-data']['www-use-token']) && $this->state->data['api-input-data']['www-use-token']==1){
					
					// Session filename is a simple hashed API profile name
					$sessionFile=md5($apiProfile);
					
					// Session subfolder is taken from first three characters of session token filename
					$sessionSubfolder=substr($sessionFile,0,2);
				
					// Session tokens are stored in hashed filename under /filesystem/sessions/ directory
					// If token exists, then it is 'recreated' with new timestamp, making API session last longer
					if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp') && ($this->state->data['api-token-timeout']==0 || filemtime($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp')>($this->state->data['request-time']-$this->state->data['api-token-timeout']))){
						// API validation is done only with the request token and secret key being hashed
						$checkHash=file_get_contents($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp');
						$checkHash=sha1($this->state->data['api-command'].$checkHash.$this->state->data['api-key']);
						// This sets the modification time of session token, extending its duration
						touch($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$sessionSubfolder.DIRECTORY_SEPARATOR.$sessionFile.'.tmp');
					} else {
						// Since an error was detected, system pushes for output immediately
						return $this->output(array('www-error'=>'API token is outdated or not found'),'HTTP/1.1 403 Forbidden');
					}
					
				} else {
				
					// Hash validation is done when all input data is sorted by keys. This should be also done by client prior to submitting a hash.
					ksort($this->state->data['api-input-data']);
					
					// Hash is calculated based on hash serializer that is used
					// Hash consists of API command, serialized input data and secret API key
					switch($this->state->data['api-serializer']){
						case 'json':
							$checkHash=sha1($this->state->data['api-command'].json_encode($this->state->data['api-input-data']).$this->state->data['api-key']);
							break;
						case 'serialize':
							$checkHash=sha1($this->state->data['api-command'].serialize($this->state->data['api-input-data']).$this->state->data['api-key']);
							break;
						default:
							// Since an error was detected, system pushes for output immediately
							return $this->output(array('www-error'=>'API hash validation serialization method not supported'),'HTTP/1.1 403 Forbidden');
					}
					
				}
				
				// If hash validation fails the request is blocked
				if($checkHash!=$apiHash){
					// Since an error was detected, system pushes for output immediately
					return $this->output(array('www-error'=>'API authentication failed'),'HTTP/1.1 403 Forbidden');
				}
				
			}
				
		}
		
		// If cache timeout is not 0, that is if cache should be checked for and used, if it exists
		if($this->state->data['api-cache-timeout']!=0){

			// Cache filename consists of API command, serialized input data, return type and whether API output is used.
			$cacheFile=md5($this->state->data['api-command'].json_encode($this->state->data['api-input-data']).$this->state->data['api-return-data-type'].$this->state->data['api-output']);
			
			// Cache subfolder is taken from first three characters of cache filename
			$cacheSubfolder=substr($cacheFile,0,2);
			
			// If cache file exists, it will be parsed and set as API value
			if(file_exists($this->state->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$cacheSubfolder.DIRECTORY_SEPARATOR.$cacheFile.'.tmp')){
				// Current cache timeout is used to return to browser information about how long browser should store this result
				$lastModified=filemtime($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp');
				if($lastModified>=$this->state->data['request-time']-$this->state->data['api-cache-timeout']){
				
					// The moment cache was created gets cache timeout added to it and returned to browser as 'expires' timestamp
					$this->state->data['current-cache-timeout']=$lastModified+$this->state->data['api-cache-timeout'];
					
					// System loads the result from cache file based on return data type
					if($this->state->data['api-return-data-type']=='html'){
						$apiResult=file_get_contents($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp');
					} else if($this->state->data['api-return-data-type']=='php'){
						$apiResult=unserialize(file_get_contents($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp'));
					} else {
						$apiResult=json_decode(file_get_contents($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp'),true);
					}
					
					// Flag is set to true, since cache is being used
					$this->cacheUsed=true;
					
				} else {
					// Current cache timeout is used to return to browser information about how long browser should store this result
					$this->state->data['current-cache-timeout']=$this->state->data['request-time']+$this->state->data['api-cache-timeout'];
				}
			} else {
				// Current cache timeout is used to return to browser information about how long browser should store this result
				$this->state->data['current-cache-timeout']=$this->state->data['request-time']+$this->state->data['api-cache-timeout'];
			}
		}
		
		// If cache was not used and command result is not yet defined, system will execute the API command
		if(!$apiResult){
		
			// HTML and text data types can be echoed/printed in their view files, result of this is gathered by output buffer
			if($this->state->data['api-return-data-type']=='html' || $this->state->data['api-return-data-type']=='text'){
				ob_start();
			}

			// API command is solved into bits to be parsed
			$commandBits=explode('-',$this->state->data['api-command'],2);
			
			// Class name is found based on command
			$className='WWW_controller_'.$commandBits[0];
			
			// Class is defined and loaded, if it is not already defined
			if(!class_exists($className)){
				// Overrides can be used for controllers
				if(file_exists($this->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php')){
					require($this->state->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php');
				} else if(file_exists($this->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php')){
					require($this->state->data['system-root'].'controllers'.DIRECTORY_SEPARATOR.'class.'.$commandBits[0].'.php');
				} else {
					// Since an error was detected, system pushes for output immediately
					return $this->output(array('www-error'=>'Client request recognized, but unable to handle'),'HTTP/1.1 501 Not Implemented');
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
					return $this->output(array('www-error'=>'Client request recognized, but unable to handle'),'HTTP/1.1 501 Not Implemented');
				}
				
				// Result of the command is solved with this call
				// Input data is also submitted to this function
				$apiResult=$controller->$methodName($this->state->data['api-input-data']);
				
			} else {
				// Since an error was detected, system pushes for output immediately
				return $this->output(array('www-error'=>'Client request recognized, but unable to handle'),'HTTP/1.1 501 Not Implemented');
			}
			
			// If returned data type was using output buffer, then that is gathered for the result instead
			if($this->state->data['api-return-data-type']=='html' || $this->state->data['api-return-data-type']=='text'){
				$apiResult=ob_get_clean();
			}
			
			// If cache timeout was set then the result is stored as a cache in the filesystem
			if($this->state->data['api-cache-timeout']!=0){
			
				// If cache subdirectory does not exist, it is created
				if(!is_dir($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/')){
					if(!mkdir($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/',0777)){
						trigger_error('Cannot create cache folder',E_USER_ERROR);
					}
				}
				
				// If returned data is HTML or text, it is simply written into cache file
				// Other results are serialized before being written to cache
				if($this->state->data['api-return-data-type']=='html' || $this->state->data['api-return-data-type']=='text'){
					if(!file_put_contents($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp',$apiResult)){
						trigger_error('Cannot write cache file',E_USER_ERROR);
					}
				} else if($this->state->data['api-return-data-type']=='php'){
					if(!file_put_contents($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp',serialize($apiResult))){
						trigger_error('Cannot write cache file',E_USER_ERROR);
					}
				} else {
					if(!file_put_contents($this->state->data['system-root'].'filesystem/cache/output/'.$cacheSubfolder.'/'.$cacheFile.'.tmp',json_encode($apiResult))){
						trigger_error('Cannot write cache file',E_USER_ERROR);
					}
				}
				
			}
		}
		
		// System returns correctly formatted output data
		return $this->output($apiResult);
	}
	
	// This function returns the data, whether final data or the one returned with error messages
	// * apiResult - Result of the API call
	// Returns final-formatted data
	private function output($apiResult){
	
		// Data is custom-formatted based on request
		switch($this->state->data['api-return-data-type']){
		
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
				
			case 'json':
				// Encodes the resulting array in JSON
				$apiResult=json_encode($apiResult);
				break;
				
			case 'serializedarray':
				// Array is simply serialized
				$apiResult=serialize($apiResult);
				break;
				
			case 'binary':
				// If the result is empty string or empty array or false, then binary returns a 0, otherwise it returns 1
				$apiResult=$this->toBinary($apiResult);
				break;
				
			case 'ini':
				// This converts result into an INI string
				$apiResult=$this->toINI($apiResult);
				break;
				
		}
	
		// If minification is requested from API
		if($this->state->data['api-minify']==1){
		
			// Including minification class if it is not yet defined
			if(!class_exists('WWW_Minify')){
				require(__DIR__.DIRECTORY_SEPARATOR.'class.www-minifier.php');
			}
			
			// Minification is based on the type of class
			switch($this->state->data['api-return-data-type']){
			
				case 'js':
					// JavaScript minification eliminates extra spaces and newlines and other formatting
					$apiResult=WWW_Minifier::minifyJS($apiResult);
					break;
					
				case 'css':
					// CSS minification eliminates extra spaces and newlines and other formatting
					$apiResult=WWW_Minifier::minifyCSS($apiResult);
					break;
					
				case 'html':
					// HTML minification eliminates extra spaces and newlines and other formatting
					$apiResult=WWW_Minifier::minifyHTML($apiResult);
					break;
					
				case 'xml':
					// XML minification eliminates extra spaces and newlines and other formatting
					$apiResult=WWW_Minifier::minifyXML($apiResult);
					break;
					
				case 'rss':
					// RSS minification eliminates extra spaces and newlines and other formatting
					$apiResult=WWW_Minifier::minifyXML($apiResult);
					break;
			}
			
		}
	
		// Result is printed out, headers and cache control are returned to the client, if output flag was set for the command
		if($this->state->data['api-output']==1){
		
			// Cache control settings sent to the client depend on cache timeout settings
			if($this->state->data['api-cache-timeout']!=0){
			
				// Cache control depends whether HTTP authentication is used or not
				if($this->state->data['http-authentication']==true){
					header('Cache-Control: private,max-age='.($this->state->data['current-cache-timeout']-$this->state->data['request-time']).',must-revalidate');
				} else {
					header('Cache-Control: public,max-age='.($this->state->data['current-cache-timeout']-$this->state->data['request-time']).',must-revalidate');
				}
				header('Expires: '.gmdate('D, d M Y H:i:s',$this->state->data['current-cache-timeout']).' GMT');
				
			} else {
			
				// When no cache is used, request tells specifically that
				header('Cache-Control: no-store;');
				header('Expires: '.gmdate('D, d M Y H:i:s',$this->state->data['request-time']).' GMT');
				
			}
			
			// Gathering output in buffer
			ob_start();
			
			// If content type was set, then that is used for content type
			if($this->state->data['api-content-type']!=''){
			
				// UTF-8 is always used for returned data
				header('Content-Type: '.$this->state->data['api-content-type'].';charset=utf-8',true);
				
			} else {
			
				// Data is echoed/printed based on return data type formatting with the proper header
				switch($this->state->data['api-return-data-type']){
				
					case 'xml':
						header('Content-Type:text/xml;charset=utf-8;');
						break;
					case 'rss':
						header('Content-Type:application/rss+xml;charset=utf-8;');
						break;
					case 'csv':
						header('Content-Type:text/csv;charset=utf-8;');
						break;
					case 'json':
						header('Content-Type:application/json;charset=utf-8;');
						break;
					case 'html':
						header('Content-Type: text/html;charset=utf-8');
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
			if(isset($lastModified)){
				header('Last-modified: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT');
			} else {
				header('Last-modified: '.gmdate('D, d M Y H:i:s',$this->state->data['request-time']).' GMT');
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
			
			// Content length is defined that can speed up website requests, letting client to determine file size
			header('Content-Length: '.strlen($content));  
			
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
				} else if($type=='rss'){
					$xml=new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rss version="2.0"></rss>');
				}
				
				// This is the custom function used to generate XML from array nodes
				$f=create_function('$f,$c,$apiResult','
						foreach($apiResult as $k=>$v) {
							if(is_array($v)) {
								if(is_numeric($k)){ $k=\'node\'; }
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
				} else if($type=='rss'){
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
