<?php

/*
WWW Framework
WWW API connection wrapper class

This class should be used when you wish to communicate with WWW Framework that is set up in another 
server. This class is independent from WWW Framework itself and can be used by other servers alone. 
This class allows sending and receiving encrypted data as well as sending files and creating session 
tokens in a system set up with WWW Framework. This class also requires PHP 5.3.3 or newer due to 
has validations with JSON encoded string and numeric conversions.

* cURL is used for requests by default. POST and file upload requests are only possible with cURL
* If cURL is not enabled, then system falls back to GET requests only with file_get_contents() and file uploads are not possible
* Outside connections must be allowed in server for this to be used with file_get_contents()

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Wrapper {

	// HTTP address of WWW-Framework-based API
	private $apiAddress;
	
	// This is current API state
	private $apiState=array(
		'apiProfile'=>false,
		'apiSecretKey'=>false,
		'apiToken'=>false,
		'returnHash'=>false,
		'returnTimestamp'=>false,
		'successCallback'=>false,
		'failureCallback'=>false,
		'requestTimeout'=>10,
		'timestampDuration'=>60,
		'unserialize'=>true,
		'lastModified'=>false
	);
	
	// Information about last error
	public $errorMessage=false;
	public $responseCode=false;
	
	// Input data
	private $inputData=array();
	private $cryptedData=array();
	private $inputFiles=array();
	
	// State settings
	private $curlEnabled=false;
	private $log=array();
	private $criticalError=false;
	private $cookieContainer=false;
	
	// User agent
	private $userAgent='WWWFramework/2.0.0 (PHP)';

	// API Address and custom user agent are assigned during object creation
	// * apiAddress - Full URL is required, like http://www.example.com/www.api
	// Object is created
	public function __construct($apiAddress){
		// This should be URL to API of WWW Framework
		$this->apiAddress=$apiAddress;
		// This checks for cURL support, which is required for making POST requests
		// cURL is also faster than file_get_contents() method
		if(extension_loaded('curl')){
			// Flag is checked during request creation
			$this->curlEnabled=true;
		} elseif(ini_get('allow_url_fopen')!=1){
			// This means that requests cannot be made at all
			$this->criticalError=true;
			// If cURL is enabled, then file_get_contents() requires PHP setting to make requests to URL's
			$this->responseCode=212;
			$this->errorMessage='Cannot make URL requests: PHP cannot make URL requests, please enable allow_url_fopen setting';
			$this->log[]=$this->errorMessage;
		}
		// JSON is required
		if(!function_exists('json_encode')){
			// This means that requests cannot be made at all
			$this->criticalError=true;
			$this->responseCode=213;
			$this->errorMessage='Cannot serialize data: JSON is required for API requests to work properly';
			$this->log[]=$this->errorMessage;
		}
		// Log entry
		$this->log[]='WWW API Wrapper object created with API address: '.$apiAddress;
	}
	
	// SETTINGS
		
		// This function simply returns current log
		// * implode - String to implode the log with
		// Function returns current log as an array
		public function returnLog($implode=false){
			$this->log[]='Returning log';
			// Imploding, if requested
			if(!$implode){
				return $this->log;
			} else {
				return implode($implode,$this->log);
			}
		}
		
		// This function simply clears current log
		// Function returns true
		public function clearLog(){
			$this->log=array();
			return true;
		}
		
		// This method allows to set cookie method for cURL calls
		// If set to 'false' then cookies are turned off
		// * location - Location for cookie file in file system
		// Returns true, if successfully set or false if failed
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
		
		// This method empties the cookie jar file, if it exists
		// * location - Location of cookies file, if this is not set then uses currently defined cookie file
		// Returns true, if success and false if failed
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
		
	// INPUT
		
		// This populates input array either with an array of input or a key and a value
		// * input - Can be an array or a key value of input
		// * value - If input value is not an array, then this is what the input key will get as a value
		// Sets the input value
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
		
		// This private function is used to seek out various private www-* settings that are not actually sent in request to API
		// * input - Input data key
		// * value - Input data value
		// Returns true after setting the data
		private function inputSetter($input,$value){
			switch($input){
				case 'www-api':
					$this->apiAddress=$value;
					$this->log[]='API address changed to: '.$value;
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
				case 'www-return-hash':
					if($value){
						$this->apiState['returnHash']=$value;
						$this->log[]='API request will require hash validation';
					}
					break;
				case 'www-return-timestamp':
					if($value){
						$this->apiState['returnTimestamp']=$value;
						$this->log[]='API request will require timestamp validation';
					}
					break;
				case 'www-request-timeout':
					$this->apiState['requestTimeout']=$value;
					$this->log[]='API request timeout set to: '.$value;
					break;
				case 'www-unserialize':
					if($value){
						$this->apiState['unserialize']=true;
						$this->log[]='Returned result will be automatically unserialized';
					} else {
						$this->apiState['unserialize']=false;
						$this->log[]='Returned result will not be automatically unserialized';
					}
					break;
				case 'www-success-callback':
					if($value){
						$this->apiState['successCallback']=$value;
						$ths->log[]='API return success callback set to '.$value.'()';
					}
					break;
				case 'www-failure-callback':
					if($value){
						$this->apiState['failureCallback']=$value;
						$ths->log[]='API return failure callback set to '.$value.'()';
					}
					break;
				case 'www-last-modified':
					if($value){
						$this->apiState['lastModified']=$value;
						$ths->log[]='API last-modified request time set to '.$value;
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
		
		// This populates crypted input array either with an array of input or a key and a value
		// * input - Can be an array or a key value of input
		// * value - If input value is not an array, then this is what the input key will get as a value
		// Sets the crypted input value
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
		
		// This sets file names and locations to be uploaded with the cURL request
		// * file - Can be an array or a key value of file
		// * location - If input value is not an array, then this is what the input file address is
		// Sets the file to be uploaded
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
		
		// This function simply deletes all current input values
		// This also deletes crypted input and input files
		// * clearAuth - True or false flag whether to also reset authentication and state data
		// Returns true
		public function clearInput($clearAuth=false){
			// If authentication should also be cleared
			if($clearAuth){
				$this->apiState['apiProfile']=false;
				$this->apiState['apiSecretKey']=false;
				$this->apiState['apiToken']=false;
				$this->apiState['returnHash']=false;
				$this->apiState['returnTimestamp']=false;
				$this->apiState['requestTimeout']=10;
				$this->apiState['timestampDuration']=60;
			}
			// Neutralizing state settings
			$this->apiState['unserialize']=true;
			$this->apiState['lastModified']=false;
			// Neutralizing callbacks
			$this->apiState['successCallback']=false;
			$this->apiState['failureCallback']=false;
			// Input data
			$this->inputData=array();
			$this->cryptedData=array();
			$this->inputFiles=array();
			// Log entry
			$this->log[]='Input data, crypted input and file data is unset';
			return true;
		}
		
	// SENDING REQUEST
		
		// This is the main function for making the request
		// This method builds a request string and then makes a request to API and attempts to fetch and parse the returned result
		// Returns the result of the request
		public function sendRequest(){
		
			// This is the input data used
			$thisInputData=$this->inputData;
			$thisCryptedData=$this->cryptedData;
			
			// Current state settings
			$thisApiState=$this->apiState;
			
			// Assigning authentication options that are sent with the request
			if($thisApiState['apiProfile']!=false){
				$thisInputData['www-profile']=$thisApiState['apiProfile'];
			}
			// Assigning return-timestamp flag to request
			if($thisApiState['returnTimestamp']==true || $thisApiState['returnTimestamp']==1){
				$thisInputData['www-return-timestamp']=1;
			}
			// Assigning return-timestamp flag to request
			if($thisApiState['returnHash']==true || $thisApiState['returnHash']==1){
				$thisInputData['www-return-hash']=1;
			}
			
			// Clears the source input data
			$this->clearInput();
		
			// Returns false if there is an existing critical error
			if($this->criticalError){
				return $this->failureHandler($thisInputData,$this->responseCode,$this->errorMessage,$thisApiState['failureCallback']);
			}
			
			// Log entry
			$this->log[]='Starting to build request';
		
			// Correct request requires command to be set
			if(!isset($thisInputData['www-command'])){
				return $this->failureHandler($thisInputData,201,'API command is not set, this is required',$thisApiState['failureCallback']);
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
							return $this->failureHandler($thisInputData,203,'Unable to encrypt data, server configuration problem',$thisApiState['failureCallback']);
						}
					} else {
						return $this->failureHandler($thisInputData,202,'Crypted input can only be used with a set secret key',$thisApiState['failureCallback']);
					}
				}
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
				
				// Get request is made if the URL is shorter than 2048 bytes (2KB).
				// While servers can easily handle 8KB of data, servers are recommended to be vary if the GET request is longer than 2KB
				if(strlen($requestURL.'?'.$requestData)<=2048 && empty($this->inputFiles)){
				
					// Log entry
					$this->log[]='Data sent with request: '.$requestData;
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
					
						// Log entry
						$this->log[]='Making GET request to API using cURL to URL: '.$requestURL;
						
						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_URL=>$requestURL.'?'.$requestData,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
							CURLOPT_HTTPGET=>true,
							CURLOPT_TIMEOUT=>$thisApiState['requestTimeout'],
							CURLOPT_USERAGENT=>$this->userAgent,
							CURLOPT_HEADER=>false,
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
						
						// Returning false if the request failed
						if(!$resultData){
							if($thisApiState['lastModified'] && curl_getinfo($cURL,CURLINFO_HTTP_CODE)==304){
								// Closing the resource
								curl_close($cURL);
								return $this->failureHandler($thisInputData,214,'Not modified',$thisApiState['failureCallback']);
							} else {
								$error='POST request failed: cURL error '.curl_getinfo($cURL,CURLINFO_HTTP_CODE).' - '.curl_error($cURL);
								// Closing the resource
								curl_close($cURL);
								return $this->failureHandler($thisInputData,204,$error,$thisApiState['failureCallback']);
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
							return $this->failureHandler($thisInputData,204,'GET request failed: file_get_contents() failed',$thisApiState['failureCallback']);
						}
						
					}
					
				} else {
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
						
						// If files are to be uploaded
						if(!empty($this->inputFiles)){
							foreach($this->inputFiles as $file=>$location){
								$this->log[]='Attaching a file to request: '.$location;
								$thisInputData[$file]='@'.$location;
							}
						}
						
						// Logging the data
						foreach($thisInputData as $key=>$val){
							$this->log[]='Attaching variable to request: '.$key.'='.$val;
						}

						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_URL=>$this->apiAddress,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
							CURLOPT_POST=>true,
							CURLOPT_POSTFIELDS=>$thisInputData,
							CURLOPT_TIMEOUT=>$thisApiState['requestTimeout'],
							CURLOPT_USERAGENT=>$this->userAgent,
							CURLOPT_HEADER=>false,
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
					
						// Log entry
						$this->log[]='Making POST request to API using cURL to URL: '.$this->apiAddress;
						
						// Returning false if the request failed
						if(!$resultData){
							if($thisApiState['lastModified'] && curl_getinfo($cURL,CURLINFO_HTTP_CODE)==304){
								// Closing the resource
								curl_close($cURL);
								return $this->failureHandler($thisInputData,214,'Not modified',$thisApiState['failureCallback']);
							} else {
								$error='POST request failed: cURL error '.curl_getinfo($cURL,CURLINFO_HTTP_CODE).' - '.curl_error($cURL);
								// Closing the resource
								curl_close($cURL);
								return $this->failureHandler($thisInputData,205,$error,$thisApiState['failureCallback']);
							}
						} else {
							$this->log[]='POST request successful: '.curl_getinfo($cURL,CURLINFO_HTTP_CODE);
						}
						
						// Closing the resource
						curl_close($cURL);
					
					} else {
						return $this->failureHandler($thisInputData,205,'POST request failed: cURL is not supported',$thisApiState['failureCallback']);
					}
				}
				
				// Log entry
				$this->log[]='Result of request: '.$resultData;
				
			// DECRYPTION
				
				// If requested data was encrypted, then this attempts to decrypt the data
				// This also checks to make sure that a serialized data was not returned (which is usually an error)
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
						return $this->failureHandler($thisInputData,206,'Output decryption has failed',$thisApiState['failureCallback']);
					}
				}
				
			// PARSING REQUEST RESULT
			
				// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
				if($thisApiState['unserialize'] && (!isset($thisInputData['www-return-type']) || $thisInputData['www-return-type']=='json')){
					// JSON support is required
					$resultData=json_decode($resultData,true);
				} else if($thisApiState['unserialize'] && $thisInputData['www-return-type']=='serializedarray'){
					// Return data is unserialized
					$resultData=unserialize($resultData,true);
					if(!$resultData){
						return $this->failureHandler($thisInputData,207,'Cannot unserialize returned data: unserialize() failed',$thisApiState['failureCallback']);
					} else {
						$this->log[]='Returning unserialized result';
					}
				} else if($thisApiState['unserialize'] && $thisInputData['www-return-type']=='querystring'){
					// Return data is filtered through string parsing and url decoding to create return array
					$resultData=parse_str(urldecode($resultData),$resultData);
					if(!$resultData){
						return $this->failureHandler($thisInputData,207,'Cannot unserialize returned data: Cannot parse query data string',$thisApiState['failureCallback']);
					} else {
						$this->log[]='Returning parsed query string result';
					}
				} else if($thisApiState['unserialize']){
					return $this->failureHandler($thisInputData,207,'Cannot unserialize returned data: Data type '.$thisInputData['www-return-type'].' not supported',$thisApiState['failureCallback']);
				} else {
					// Data is simply returned if serialization was not requested
					$this->log[]='Returning result';
				}
				
			// ERRORS
			
				if($thisApiState['unserialize'] && isset($resultData['www-error'])){
					return $this->failureHandler($thisInputData,$resultData['www-response-code'],$resultData['www-error'],$thisApiState['failureCallback']);
				}
				
			// RESULT VALIDATION
			
				// Result validation only applies to non-public profiles
				if($thisApiState['apiProfile'] && ($thisApiState['returnHash'] || $thisApiState['returnTimestamp'])){
				
					// Only unserialized data can be validated
					if($thisApiState['unserialize']){
					
						// If it was requested that validation timestamp is returned
						if($thisApiState['returnTimestamp']){
							if(isset($resultData['www-timestamp'])){
								// Making sure that the returned result is within accepted time limit
								if((time()-$thisApiState['timestampDuration'])>$resultData['www-timestamp']){
									return $this->failureHandler($thisInputData,210,'Validation timestamp is too old',$thisApiState['failureCallback']);
								}
							} else {
								return $this->failureHandler($thisInputData,209,'Validation data missing: Timestamp was not returned',$thisApiState['failureCallback']);
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
									return $this->failureHandler($thisInputData,211,'Hash validation failed',$thisApiState['failureCallback']);
								}
								
							} else {
								return $this->failureHandler($thisInputData,209,'Validation data missing: Hash was not returned',$thisApiState['failureCallback']);
							}
						}
					
					} else {
						return $this->failureHandler($thisInputData,208,'Cannot validate hash: Unserialization not possible',$thisApiState['failureCallback']);
					}
				
				}
			
			// Resetting the error variables
			$this->responseCode=false;
			$this->errorMessage=false;
			
			// Return specific actions
			if($thisApiState['unserialize']){
				// If this command was to create a token
				if($thisInputData['www-command']=='www-create-session' && isset($resultData['www-token'])){
					$this->apiState['apiToken']=$resultData['www-token'];
					$this->log[]='Session token was found in reply, API session token set to: '.$resultData['www-token'];
				}
			}
			
			// If callback has been defined
			if($thisApiState['successCallback']){
				// Calling user function
				if(function_exists($thisApiState['successCallback'])){
					$this->log[]='Sending data to callback: '.$thisApiState['successCallback'].'()';
					// Callback execution
					return call_user_func($thisApiState['successCallback'],$resultData);
				} else {
					return $this->failureHandler($thisInputData,216,'Callback method not found',$thisApiState['failureCallback']);
				}
			} else {
				// Returning request result
				return $resultData;
			}
			
		}
		
	// REQUIRED FUNCTIONS
	
		// Calculates validation hash
		// * validationData - Data to build a hash from
		// * postFix - Data used to salt the hash
		// Returns the hash
		private function validationHash($validationData,$postFix){
			// Sorting and encoding the output data
			$validationData=$this->ksortArray($validationData);
			// Returning validation hash
			return sha1(http_build_query($validationData).$postFix);
		}
		
		// This function applies key-based sorting recursively to an array of arrays
		// * array - Array to be sorted
		// Returns sorted array
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
		
		// This method is simply meant for returning a result if there was an error in the sent request
		// * inputData - Data sent to request
		// * responseCode - Code number to be set as an error
		// * errorMessage - Clear text error message
		// * failureCallback - Callback function to call with the error message
		// Returns either false or the result of callback function
		private function failureHandler($inputData,$responseCode,$errorMessage,$failureCallback){
			// Assigning error details to object state
			$this->responseCode=$responseCode;
			$this->errorMessage=$errorMessage;
			$this->log[]=$errorMessage;
			// If failure callback has been defined
			if($failureCallback){
				// Looking for function of that name
				if(function_exists($failureCallback)){
					$this->log[]='Sending failure data to callback: '.$failureCallback.'()';
					// Callback execution
					return call_user_func($failureCallback,array('www-input'=>$inputData,'www-response-code'=>$responseCode,'www-error'=>$errorMessage));
				} else {
					$this->responseCode=216;
					$this->errorMessage='Callback method not found: '.$apiState['failureCallback'].'()';
					$this->log[]=$this->errorMessage;
					return false;
				}
			} else {
				return false;
			}
		}


}

?>