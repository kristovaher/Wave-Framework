<?php

/*
WWW Framework
WWW API connnection wrapper class

This class should be used when you wish to communicate with WWW Framework that is set up in another 
server. This class is independent from WWW Framework itself and can be used by other servers alone. 
This class allows sending and receiving encrypted data as well as sending files and creating session 
tokens in a system set up with WWW Framework. This class also requires PHP 5.3.3 or newer due to 
has validations with JSON encoded string and numeric conversions.

* cURL is used for requests by default. POST and file upload requests are only possible with cURL
* If cURL is not enabled, then system falls back to GET requests only with file_get_contents() and file uploads are not possible
* Outside connections must be allowed in server for this to be used with file_get_contents()

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Wrapper {

	// HTTP address of WWW-Framework-based API
	private $apiAddress;
	
	// API profile information
	private $apiProfile=false;
	private $apiSecretKey=false;
	private $apiToken=false;
	private $apiEncryptionKey=false;
	
	// Command to be run
	private $apiCommand=false;
	
	// API settings
	private $apiMinify=0;
	private $apiCacheTimeout=0;
	private $apiReturnType='json';
	private $apiRequestReturnHash=false;
	private $apiReturnValidTimestamp=10;
	
	// Input data
	private $inputData=array();
	private $cryptedData=array();
	private $inputFiles=array();
	
	// State settings
	private $curlEnabled=false;
	private $log=array();
	private $requestTimeout=10;

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
			// If cURL is enabled, then file_get_contents() requires PHP setting to make requests to URL's
			throw new Exception('PHP cannot make URL requests, please enable allow_url_fopen setting');
		}
		// JSON is required with JSON_NUMERIC_CHECK option
		if(!function_exists('json_encode') || !defined('JSON_NUMERIC_CHECK')){
			throw new Exception('JSON with automatic numeric check is required for API requests to work properly');
		}
	}
	
	// SETTINGS
	
		// This sets how long cURL will wait for response before failing with the request
		// * timeout - Time in seconds
		// Returns true
		public function setRequestTimeout($timeout=10){
			// Timeout value must be a number higher than 0
			if(is_numeric($timeout) && $timeout>0){
				$this->requestTimeout=$timeout;
				$this->log[]='Request timeout set to '.$timeout.' seconds';
			} else {
				throw new Exception('Request timeout is not defined correctly, must be a number above 0');
			}
			return true;
		}
		
		// This function simply returns current log
		// Function returns current log as an array
		public function returnLog(){
			$this->log[]='Returning log';
			return $this->log;
		}
		
		// This function simply clears current log
		// Function returns true
		public function clearLog(){
			$this->log=array();
			return true;
		}
	
	// AUTHENTICATION
	
		// This function sets current API profile and secret key
		// * apiProfile - Current profile name, this must be defined in the receiving system /resources/api.profiles.php file
		// * apiSecretKey - This is secret key of the used profile
		// Returns true as long as profile and secret key are set
		public function setAuthentication($apiProfile=false,$apiSecretKey=false){
			// Requires non-empty strings
			if($apiProfile && $apiSecretKey && $apiProfile!='' && $apiSecretKey!=''){
				$this->apiProfile=$apiProfile;
				$this->inputData['www-profile']=$apiProfile;
				$this->apiSecretKey=$apiSecretKey;
				$this->log[]='Authentication set to API profile "'.$apiProfile.'" with secret key: '.$apiSecretKey;
			} else {
				throw new Exception('API profile name and secret key are incorrect for authentication');
			}
			return true;
		}
		
		// This sets current session token to be used
		// * apiToken - Current token
		// Returns true as long as token value is set
		public function setToken($apiToken){
			// Requires non-empty string
			if($apiToken && $apiToken!=''){
				$this->apiToken=$apiToken;
				$this->log[]='API token set to: '.$apiToken;
			} else {
				throw new Exception('API token is incorrect for authentication');
			}
			return true;
		}
		
		// This sets encryption key for data encryption
		// * apiToken - Current token
		// Returns true as long as token value is set
		public function setEncryptionKey($encryptionKey){
			// Requires non-empty string
			if($encryptionKey && $encryptionKey!=''){
				$this->apiEncryptionKey=$encryptionKey;
				$this->log[]='API return data encryption key set to: '.$encryptionKey;
			} else {
				throw new Exception('Encryption key is incorrect for use');
			}
			return true;
		}
		
		// This clears all authentication data
		// Returns true
		public function clearAuthentication(){
			$this->log[]='API profile, API secret key and API token removed, public profile assumed';
			$this->apiProfile=false;
			$this->apiSecretKey=false;
			// If profile is set in input data array
			if(isset($this->inputData['www-profile'])){
				unset($this->inputData['www-profile']);
			}
			return true;
		}
		
		// This clears all authentication data
		// Returns true
		public function clearToken(){
			$this->log[]='API token removed';
			$this->apiToken=false;
			return true;
		}
		
		// This clears encryption key
		// Returns true
		public function clearEncryptionKey(){
			$this->log[]='API return data encryption key unset';
			$this->apiEncryptionKey=false;
			return true;
		}
		
	// INPUT
		
		// This populates input array either with an array of input or a key and a value
		// * input - Can be an array or a key value of input
		// * value - If input value is not an array, then this is what the input key will get as a value
		public function setInput($input,$value=false){
			// If this is an array then it populates input array recursively
			if(is_array($input)){
				foreach($input as $key=>$val){
					// Value is converted to string to make sure that json_encode() includes quotes in hash calculations
					$this->inputData[$key]=$val;
					$this->log[]='Input value of "'.$key.'" set to: '.$val;
				}
			} else {
				// Value is converted to string to make sure that json_encode() includes quotes in hash calculations
				$this->inputData[$input]=$value;
				$this->log[]='Input value of "'.$input.'" set to: '.$value;
			}
			return true;
		}
		
		// This populates crypted input array either with an array of input or a key and a value
		// * input - Can be an array or a key value of input
		// * value - If input value is not an array, then this is what the input key will get as a value
		public function setCryptedInput($input,$value=false){
			// If this is an array then it populates input array recursively
			if(is_array($input)){
				foreach($input as $key=>$val){
					// Value is converted to string to make sure that json_encode() includes quotes in hash calculations
					$this->cryptedData[$key]=$val;
					$this->log[]='Crypted input value of "'.$key.'" set to: '.$val;
				}
			} else {
				// Value is converted to string to make sure that json_encode() includes quotes in hash calculations
				$this->cryptedData[$input]=$value;
				$this->log[]='Crypted input value of "'.$input.'" set to: '.$value;
			}
			return true;
		}
		
		// This sets file names and locations to be uploaded with the cURL request
		// * file - Can be an array or a key value of file
		// * location - If input value is not an array, then this is what the input file address is
		public function setFile($file,$location=false){
			// If this is an array then it populates input array recursively
			if(is_array($file)){
				foreach($file as $key=>$loc){
					// File needs to exist in filesystem
					if($loc && file_exists($loc)){
						$this->inputFiles[$key]=$loc;
						$this->log[]='Input file "'.$key.'" location set to: '.$loc;
					} else {
						throw new Exception('File location not defined or file does not exist in that location: '.$loc);
					}
				}
			} else {
				// File needs to exist in filesystem
				if($location && file_exists($location)){
					$this->inputFiles[$file]=$location;
					$this->log[]='Input file "'.$file.'" location set to: '.$location;
				} else {
					throw new Exception('File location not defined or file does not exist in that location: '.$location);
				}
			}
			return true;
		}
		
		// This function simply deletes current input values
		// Returns true
		public function clearInput(){
			$this->inputData=array();
			$this->cryptedData=array();
			$this->inputFiles=array();
			$this->log[]='Input data, crypted input and file data is unset';
			return true;
		}
		
	// API SETTINGS
	
		// This sets current API command
		// * command - Correctly formed API command, for example 'example-get'
		// Returns true if command is correctly formated
		public function setCommand($command=false){
			// Command must not be empty
			if($command && $command!=''){
				// Command is lowercased just in case
				$command=strtolower(trim($command));
				$this->apiCommand=$command;
				$this->inputData['www-command']=$command;
				$this->log[]='API command set to: '.$command;
			} else {
				throw new Exception('API command is incorrect');
			}
			return true;
		}
		
		// This function sets the return type of API request, essentially what type of data is expected to be returned
		// * type - Return type expected, either json, xml, html, rss, csv, js, css, vcard, ini, serializedarray, text or binary
		// Returns true if correct type is used.
		public function setReturnType($type){
			// Making sure that type is lowercased
			$type=strtolower(trim($type));
			// Type has to be in supported format
			if(in_array($type,array('json','xml','html','rss','csv','js','css','vcard','ini','serializedarray','text','binary'))){
				$this->apiReturnType=$type;
				$this->log[]='Returned data type set to: '.$type;
				// If default value is set in input data from previous requests
				if($type=='json' && isset($this->inputData['www-return-type'])){
					unset($this->inputData['www-return-type']);
				} else {
					$this->inputData['www-return-type']=$type;
				}
			} else {
				throw new Exception('This return data type is not supported: '.$type);
			}
			return true;
		}
	
		// This sets the cache timeout value of API commands, if this is set to 0 then cache is never used
		// Set this higher than 0 to requests that are expected to change less frequently
		// * timeout - Time in seconds how old cache is allowed by this request
		// Returns true if cache is a number
		public function setCacheTimeout($timeout=0){
			// Timeout value must be a number
			if(is_numeric($timeout)){
				$this->apiCacheTimeout=$timeout;
				$this->log[]='Cache timeout set to '.$timeout.' seconds';
				// If the default is already set in the input array
				if($timeout==0 && isset($this->inputData['www-cache-timeout'])){
					unset($this->inputData['www-cache-timeout']);
				} else {
					$this->inputData['www-cache-timeout']=$timeout;
				}
			} else {
				throw new Exception('API cache timeout is not defined correctly, must be a number');
			}
			return true;
		}
		
		// This tells API to return minified results
		// This only applies when returned data is XML, CSS, HTML or JavaScript
		// * flag - Either true or false
		// Returns true
		public function setMinify($flag){
			if($flag){
				$this->apiMinify=1;
				$this->inputData['www-minify']=1;
				$this->log[]='API minification request for returned result is turned on';
			} else {
				$this->apiMinify=0;
				$this->log[]='API minification request for returned result is turned off';
				// If the default is already set in the input array
				if(isset($this->inputData['www-minify'])){
					unset($this->inputData['www-minify']);
				}
			}
			return true;
		}
		
		// This tells API to also return a validation hash and timestamp for return data validation
		// * flag - Either true or false
		// Returns true
		public function setRequestReturnHash($flag,$timestamp=10){
			if($flag && $timestamp>0){
				$this->apiRequestReturnHash=1;
				$this->apiReturnValidTimestamp=$timestamp;
				$this->inputData['www-return-hash']=1;
				$this->log[]='API request will require a hash and timestamp validation from API';
			} else {
				$this->apiRequestReturnHash=0;
				$this->log[]='API request will not require a hash and timestamp validation from API';
				// If the default is already set in the input array
				if(isset($this->inputData['www-return-hash'])){
					unset($this->inputData['www-return-hash']);
				}
			}
			return true;
		}
		
	// SENDING REQUEST
		
		// This is the main function for making the request
		// This method builds a request string and then makes a request to API and attempts to fetch and parse the returned result
		// * unserializeResult - Whether the result is automatically unserialized or not
		public function sendRequest($unserializeResult=true){
			
			// Log entry
			$this->log[]='Starting to build request';
		
			// Correct request requires command to be set
			if(!isset($this->inputData['www-command']) && $this->apiCommand && $this->apiCommand!=''){
				$this->inputData['www-command']=$this->apiCommand;
			} elseif(!isset($this->inputData['www-command'])){
				throw new Exception('API command is not defined');
			}
		
			// If return data type is anything except JSON, then it is defined in request string
			if(!isset($this->inputData['www-return-type']) && $this->apiReturnType!='json'){
				$this->inputData['www-return-type']=$this->apiReturnType;
			}
			// If cache timeout is set above 0 then it is defined in request string
			if(!isset($this->inputData['www-cache-timeout']) && $this->apiCacheTimeout>0){
				$this->inputData['www-cache-timeout']=$this->apiCacheTimeout;
			}
			// If minification is set then it is defined in request string
			if(!isset($this->inputData['www-minify']) && $this->apiMinify==1){
				$this->inputData['www-minify']='1';
			}
		
			// If API profile and secret key are set, then wrapper assumes that non-public profile is used, thus hash and timestamp have to be included
			if($this->apiProfile && $this->apiSecretKey){
			
				// Log entry
				$this->log[]='API profile set, hash authentication will be used';
			
				// If encryption key is set, then this is sent together with crypted data
				if(!isset($this->inputData['www-crypt-output']) && $this->apiEncryptionKey){
					$this->cryptedData['www-crypt-output']=$this->apiEncryptionKey;
				}
			
				// Non-public profile use also has to be defined in request string
				if(!isset($this->inputData['www-profile'])){
					$this->inputData['www-profile']=$this->apiProfile;
				}
				// Timestamp is required in API requests since it will be used for request validation and replay attack protection
				if(!isset($this->inputData['www-timestamp'])){
					$this->inputData['www-timestamp']=time();
				}
				
				// If this is set, then API is requested to also return a timestamp and hash validation
				if(!isset($this->inputData['www-return-hash']) && $this->apiRequestReturnHash==1){
					$this->inputData['www-return-hash']='1';
				}
				
				// Token has to be provided for every request that is not a 'www-create-session' or 'www-destroy-session'
				if(!$this->apiToken && $this->apiCommand!='www-create-session' && $this->apiCommand!='www-destroy-session'){
					throw new Exception('Token is required for this request');
				}
		
				// If crypted data array is populated, then this data is encrypted in www-crypt-input key
				if(!isset($this->inputData['www-crypt-input']) && !empty($this->cryptedData)){
					// This is only possible if API token is set
					if($this->apiToken){
						// Mcrypt extension is required
						if(extension_loaded('mcrypt')){
							// Data is encrypted with Rijndael 256bit encryption
							$this->inputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$this->apiToken,json_encode($this->cryptedData),MCRYPT_MODE_CBC,md5($this->apiSecretKey)));
							$this->log[]='Crypted input created using JSON encoded input data, token and secret key';
						} else {
							throw new Exception('Mcrypt extension is required to use encrypted input');
							return false;
						}
					} else {
						throw new Exception('API token is required for encrypted input data');
						return false;
					}
				}
				
				// Input data has to be sorted based on key
				ksort($this->inputData);
				
				// Validation hash is generated based on current serialization option
				if(!isset($this->inputData['www-hash'])){
					$this->inputData['www-hash']=sha1(json_encode($this->inputData,JSON_NUMERIC_CHECK).$this->apiToken.$this->apiSecretKey);
				}

				// Log entry
				if($this->apiToken){
					$this->log[]='Validation hash created using JSON encoded input data, API token and secret key';
				} else {
					$this->log[]='Validation hash created using JSON encoded input data and secret key';
				}
				
			} else {
				$this->log[]='API profile is not set, using public profile';
			
				// If encryption key is set, then this is sent together with input data string
				if(!isset($this->inputData['www-crypt-output']) && $this->apiEncryptionKey){
					$this->inputData['www-crypt-output']=$this->apiEncryptionKey;
				}
				
			}
			
			// MAKING A REQUEST
			
				// Building the request URL
				$requestURL=$this->apiAddress.'?'.http_build_query($this->inputData);
				
				// Get request is made if the URL is shorter than 2048 bytes (2KB).
				// While servers can easily handle 8KB of data, servers are recommended to be vary if the request is longer than 2KB
				if(strlen($requestURL)<=2048 && empty($this->inputFiles)){
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
					
						// Log entry
						$this->log[]='Making GET request to API using cURL to URL: '.$requestURL;
						
						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_URL=>$requestURL,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'],
							CURLOPT_HTTPGET=>true,
							CURLOPT_TIMEOUT=>$this->requestTimeout,
							CURLOPT_USERAGENT=>'WWWFramework/2.0.0',
							CURLOPT_HEADER=>false,
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_FOLLOWLOCATION=>false,
							CURLOPT_COOKIESESSION=>true,
							CURLOPT_SSL_VERIFYPEER=>false
						);
						// Assigning options to cURL object
						curl_setopt_array($cURL,$requestOptions);
						// Executing the request
						$result=curl_exec($cURL);
						
						// Returning false if the request failed
						if(!$result){
							$this->log[]='cURL GET request to failed: '.curl_error($cURL);
							// Closing the resource
							curl_close($cURL);
							return false;
						}
						
						// Closing the resource
						curl_close($cURL);
						
					} else {
					
						// GET request an also be made by file_get_contents()
						$this->log[]='Making GET request to API using file-get-contents to URL: '.$requestURL;
						if(!$result=file_get_contents($requestURL)){
							$this->log[]='File-get-contents GET request to failed';
							return false;
						}
						
					}
					
				} else {
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
					
						// Log entry
						$this->log[]='Making POST request to API using cURL to URL '.$this->apiAddress.' with data: '.serialize($this->inputData);
						
						// If files are to be uploaded
						if(!empty($this->inputFiles)){
							foreach($this->inputFiles as $file=>$location){
								$this->log[]='Attaching a file '.$location.' to request';
								$this->inputData[$file]='@'.$location;
							}
						}

						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_URL=>$this->apiAddress,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'],
							CURLOPT_POST=>true,
							CURLOPT_POSTFIELDS=>$this->inputData,
							CURLOPT_TIMEOUT=>$this->requestTimeout,
							CURLOPT_USERAGENT=>'WWWFramework/2.0.0',
							CURLOPT_HEADER=>false,
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_FOLLOWLOCATION=>false,
							CURLOPT_COOKIESESSION=>true,
							CURLOPT_SSL_VERIFYPEER=>false
						);
						// Assigning options to cURL object
						curl_setopt_array($cURL,$requestOptions);
						// Executing the request
						$result=curl_exec($cURL);
						
						// Returning false if the request failed
						if(!$result){
							$this->log[]='cURL GET request to failed: '.curl_error($cURL);
							// Closing the resource
							curl_close($cURL);
							return false;
						}
						
						// Closing the resource
						curl_close($cURL);
					
					} else {
						$this->log[]='POST method requires cURL support in the server, this extension is not enabled';
						return false;
					}
				}
				
			// PARSING REQUEST RESULT
				
				// Log entry
				$this->log[]='Result of request: '.$result;
				
				// If requested data was encrypted, then this attempts to decrypt the data
				if($this->apiEncryptionKey){
					if($this->apiSecretKey){
						// If secret key was set, then decryption uses the secret key for initialization vector
						$result=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$this->apiEncryptionKey,base64_decode($result),MCRYPT_MODE_CBC,md5($this->apiSecretKey));
					} else {
						// Without secret key the system assumes that public profile is used and decryption is done in ECB mode
						$result=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$this->apiEncryptionKey,base64_decode($result),MCRYPT_MODE_ECB);
					}
					if($result){
						$result=trim($result);
						$this->log[]='Result of decrypted request: '.$result;
					} else {
						$this->log[]='Decryption has failed';
						return false;
					}
				}
				
			// PARSING REQUEST RESULT
			
				// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
				if($this->apiReturnType=='json' && $unserializeResult){
					// JSON supportis required
					$return=json_decode($result,true);
				} else if($this->apiReturnType=='serializedarray' && $unserializeResult){
					// Return data is unserialized
					$return=unserialize($result,true);
					if(!$return){
						$this->log[]='Cannot unserialize data string';
						return false;
					} else {
						$this->log[]='Returning unserialized result';
					}
				} else if($this->apiReturnType=='querystring' && $unserializeResult){
					// Return data is filtered through string parsing and url decoding to create return array
					$return=parse_str(urldecode($result),$return);
					if(!$return){
						$this->log[]='Cannot parse query data string';
						return false;
					} else {
						$this->log[]='Returning parsed query string result';
					}
				} else if($unserializeResult){
					// Every other unserialization attempt fails
					$this->log[]='Cannot unserialize this return data type: '.$this->apiReturnType;
					return false;
				} else {
					// Data is simply returned if serialization was not requested
					$this->log[]='Returning result';
				}
				
			// RESULT VALIDATION
				
				// If it was requested that validation hash and timestamp are also returned
				// This only applies to non-public profiles
				if($this->apiProfile && $this->apiSecretKey && $this->apiRequestReturnHash){
					// This validation is only done if string was unserialized
					if($unserializeResult){
						// Hash and timestamp have to be defined in response
						if(isset($return['www-hash']) && isset($return['www-timestamp'])){
							// Making sure that the returned result is within accepted time limit
							if((time()-$this->apiReturnValidTimestamp)<=$return['www-timestamp']){
								// Assigning returned array to hash validation array
								$validationHash=$return;
								// Hash itself is removed from validation
								unset($validationHash['www-hash']);
								// Data is sorted
								ksort($validationHash);
								// Validation depends on whether session creation or destruction commands were called
								if($this->inputData['www-command']=='www-create-session' || $this->inputData['www-command']=='www-destroy-session'){
									$hash=sha1(json_encode($validationHash,JSON_NUMERIC_CHECK).$this->apiSecretKey);
								} else {
									$hash=sha1(json_encode($validationHash,JSON_NUMERIC_CHECK).$this->apiToken.$this->apiSecretKey);
								}
								// If sent hash is the same as calculated hash
								if($hash==$return['www-hash']){
									$this->log[]='Hash validation successful';
								} else {
									$this->log[]='Hash validation failed';
									return false;
								}
							} else {
								$this->log[]='Returned data timestamp is too old, return was accepted only within '.$this->apiReturnValidTimestamp.' seconds';
								return false;
							}
						} else {
							$this->log[]='Returned data validation failed, hash and timestamp not returned';
							return false;
						}
					} else {
						$this->log[]='Return hash validation was requested, but it cannot be unserialized by wrapper so manual validation is required';
					}
				}
				
			// Clears input data
			$this->clearInput();
				
			// Returning request result
			return $return;
			
		}


}

?>