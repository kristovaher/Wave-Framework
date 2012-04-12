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
*/

class WWW_Wrapper {

	// HTTP address of WWW-Framework-based API
	private $apiAddress;
	
	// API profile information
	private $apiProfile=false;
	private $apiSecretKey=false;
	private $apiToken=false;
	private $returnHash=false;
	private $returnTimestamp=false;
	
	// Information about last error
	public $errorMessage=false;
	public $errorCode=false;
	
	// Input data
	private $inputData=array();
	private $cryptedData=array();
	private $inputFiles=array();
	
	// State settings
	private $curlEnabled=false;
	private $log=array();
	private $requestTimeout=10;
	private $timestampDuration=10;
	private $criticalError=false;

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
			$this->errorCode=212;
			$this->errorMessage='Cannot make URL requests: PHP cannot make URL requests, please enable allow_url_fopen setting';
			$this->log[]=$this->errorMessage;
		}
		// JSON is required with JSON_NUMERIC_CHECK option
		if(!function_exists('json_encode') || !defined('JSON_NUMERIC_CHECK')){
			// This means that requests cannot be made at all
			$this->criticalError=true;
			$this->errorCode=213;
			$this->errorMessage='Cannot serialize data: JSON with automatic numeric check is required for API requests to work properly';
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
					$this->apiSecretKey=$value;
					$this->log[]='API secret key set to: '.$value;
					break;
				case 'www-token':
					$this->apiToken=$value;
					$this->log[]='API session token set to: '.$value;
					break;
				case 'www-profile':
					$this->apiProfile=$value;
					$this->inputData[$input]=$value;
					$this->log[]='API profile set to: '.$value;
					break;
				case 'www-return-hash':
					$this->returnHash=$value;
					$this->log[]='API request will require hash validation';
					break;
				case 'www-return-timestamp':
					$this->returnTimestamp=$value;
					$this->log[]='API request will require timestamp validation';
					break;
				case 'www-request-timeout':
					$this->requestTimeout=$value;
					$this->log[]='API request timeout set to: '.$value;
					break;
				case 'www-timestamp-duration':
					$this->timestampDuration=$value;
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
		
		// This function simply deletes all current input values
		// This also deletes crypted input and input files
		// * clearAuth - True or false flag whether to also reset authentication and state data
		// Returns true
		public function clearInput($clearAuth=false){
			// If authentication should also be cleared
			if($clearAuth){
				$this->apiProfile=false;
				$this->apiSecretKey=false;
				$this->apiToken=false;
				$this->returnHash=false;
				$this->returnTimestamp=false;
				$this->requestTimeout=10;
				$this->timestampDuration=10;
			}
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
		// * unserializeResult - Whether the result is automatically unserialized or not
		// * lastModified - Unix timestamp of last-modified date, this can be used to check if data has been last modified or not
		// Returns the result of the request
		public function sendRequest($unserializeResult=true,$lastModified=false){
		
			// This is the input data used
			$thisInputData=$this->inputData;
			
			// Current state settings
			$apiProfile=$this->apiProfile;
			$apiSecretKey=$this->apiSecretKey;
			$apiToken=$this->apiToken;
			$requestTimeout=$this->requestTimeout;
			$timestampDuration=$this->timestampDuration;
			$returnTimestamp=$this->returnTimestamp;
			$returnHash=$this->returnHash;
			
			// Assigning authentication options that are sent with the request
			if($apiProfile!=false){
				$thisInputData['www-profile']=$apiProfile;
			}
			// Assigning return-timestamp flag to request
			if($returnTimestamp==true || $returnTimestamp==1){
				$thisInputData['www-return-timestamp']=1;
			}
			// Assigning return-timestamp flag to request
			if($returnHash==true || $returnHash==1){
				$thisInputData['www-return-hash']=1;
			}
			
			// Clears the source input data
			$this->clearInput();
		
			// Returns false if there is an existing error
			if($this->criticalError){
				return false;
			}
			
			// Log entry
			$this->log[]='Starting to build request';
		
			// Correct request requires command to be set
			if(!isset($thisInputData['www-command'])){
				$this->errorCode=201;
				$this->errorMessage='API command is not set, this is required';
				$this->log[]=$this->errorMessage;
				return false;
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
			if($apiProfile && isset($apiSecretKey) && isset($thisInputData['www-crypt-output'])){
				$this->log[]='Crypt output key was set as regular input for non-public profile API request, it is moved to crypted input instead';
				$this->cryptedData['www-crypt-output']=$thisInputData['www-crypt-output'];
				unset($thisInputData['www-crypt-output']);
			}
			
			// If profile is used, then timestamp will also be sent with the request
			if($apiProfile){
				// Timestamp is required in API requests since it will be used for request validation and replay attack protection
				if(!isset($thisInputData['www-timestamp'])){
					$thisInputData['www-timestamp']=time();
				}
			}
		
			// If API secret key is set, then wrapper assumes that non-public profile is used, thus hash and timestamp have to be included
			if($apiSecretKey){
			
				// Log entry
				$this->log[]='API secret key set, hash authentication will be used';
		
				// If crypted data array is populated, then this data is encrypted in www-crypt-input key
				if(!isset($thisInputData['www-crypt-input']) && !empty($this->cryptedData)){
					// This is only possible if API token is set
					if($apiSecretKey){
						// Mcrypt extension is required
						if(extension_loaded('mcrypt')){
							// Data is encrypted with Rijndael 256bit encryption
							if($apiToken){
								$thisInputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,md5($apiToken),json_encode($this->cryptedData),MCRYPT_MODE_CBC,md5($apiSecretKey)));
							} else {
								$thisInputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,md5($apiSecretKey),json_encode($this->cryptedData),MCRYPT_MODE_ECB));
							}
							$this->log[]='Crypted input created using JSON encoded input data, token and secret key';
						} else {
							$this->errorCode=203;
							$this->errorMessage='Unable to encrypt data, server configuration problem';
							$this->log[]=$this->errorMessage;
							return false;
						}
					} else {
						$this->errorCode=202;
						$this->errorMessage='Crypted input can only be used with a set secret key';
						$this->log[]=$this->errorMessage;
						return false;
					}
				}
				
				// Input data has to be sorted based on key
				ksort($thisInputData);
				
				// Validation hash is generated based on current serialization option
				if(!isset($thisInputData['www-hash'])){
					$thisInputData['www-hash']=sha1(json_encode($thisInputData,JSON_NUMERIC_CHECK).$apiToken.$apiSecretKey);
				}

				// Log entry
				if($apiToken){
					$this->log[]='Validation hash created using JSON encoded input data, API token and secret key';
				} else {
					$this->log[]='Validation hash created using JSON encoded input data and secret key';
				}
				
			} else {
			
				// Token-only validation means that token will be sent to the server, but data itself will not be hashed. This works like a cookie.
				if($apiToken){
					// Log entry
					$this->log[]='Using token-only validation';
				} else {
					// Log entry
					$this->log[]='API secret key is not set, hash validation will not be used';
				}
				
			}
			
			// MAKING A REQUEST
			
				// Building the request URL
				$requestURL=$this->apiAddress.'?'.http_build_query($thisInputData);
				
				// Get request is made if the URL is shorter than 2048 bytes (2KB).
				// While servers can easily handle 8KB of data, servers are recommended to be vary if the GET request is longer than 2KB
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
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
							CURLOPT_HTTPGET=>true,
							CURLOPT_TIMEOUT=>$requestTimeout,
							CURLOPT_USERAGENT=>'WWWFramework/2.0.0',
							CURLOPT_HEADER=>false,
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_FOLLOWLOCATION=>false,
							CURLOPT_COOKIESESSION=>true,
							CURLOPT_SSL_VERIFYPEER=>false
						);
						// If last modified header is sent
						if($lastModified){
							curl_setopt($cURL,CURLOPT_HTTPHEADER,array('If-Modified-Since: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT'));
						}
						// Assigning options to cURL object
						curl_setopt_array($cURL,$requestOptions);
						// Executing the request
						$result=curl_exec($cURL);
						
						// Returning false if the request failed
						if(!$result){
							if($lastModified && curl_getinfo($cURL,CURLINFO_HTTP_CODE)==304){
								$this->errorCode=214;
								$this->errorMessage='Not modified';
								$this->log[]=$this->errorMessage;
							} else {
								$this->errorCode=204;
								$this->errorMessage='GET request failed: cURL error '.curl_getinfo($cURL,CURLINFO_HTTP_CODE).' - '.curl_error($cURL);
								$this->log[]=$this->errorMessage;
							}
							// Closing the resource
							curl_close($cURL);
							return false;
						} else {
							$this->log[]='GET request successful: '.curl_getinfo($cURL,CURLINFO_HTTP_CODE);
						}
						
						// Closing the resource
						curl_close($cURL);
						
					} else {
					
						// GET request an also be made by file_get_contents()
						$this->log[]='Making GET request to API using file-get-contents to URL: '.$requestURL;
						if(!$result=file_get_contents($requestURL)){
							$this->errorCode=204;
							$this->errorMessage='GET request failed: file_get_contents() failed';
							$this->log[]=$this->errorMessage;
							return false;
						}
						
					}
					
				} else {
				
					// cURL is used unless it is not supported on the server
					if($this->curlEnabled){
					
						// Log entry
						$this->log[]='Making POST request to API using cURL to URL '.$this->apiAddress.' with data: '.serialize($thisInputData);
						
						// If files are to be uploaded
						if(!empty($this->inputFiles)){
							foreach($this->inputFiles as $file=>$location){
								$this->log[]='Attaching a file '.$location.' to request';
								$thisInputData[$file]='@'.$location;
							}
						}

						// Initializing cURL object
						$cURL=curl_init();
						// Setting cURL options
						$requestOptions=array(
							CURLOPT_HTTPHEADER=>array('If-Modified-Since: '.gmdate('D, d M Y H:i:s',time()).' GMT'),
							CURLOPT_URL=>$this->apiAddress,
							CURLOPT_REFERER=>((!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on'))?'http://':'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
							CURLOPT_POST=>true,
							CURLOPT_POSTFIELDS=>$thisInputData,
							CURLOPT_TIMEOUT=>$requestTimeout,
							CURLOPT_USERAGENT=>'WWWFramework/2.0.0',
							CURLOPT_HEADER=>false,
							CURLOPT_RETURNTRANSFER=>true,
							CURLOPT_FOLLOWLOCATION=>false,
							CURLOPT_COOKIESESSION=>true,
							CURLOPT_SSL_VERIFYPEER=>false
						);
						// If last modified header is sent
						if($lastModified){
							curl_setopt($cURL,CURLOPT_HTTPHEADER,array('If-Modified-Since: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT'));
						}
						// Assigning options to cURL object
						curl_setopt_array($cURL,$requestOptions);
						// Executing the request
						$result=curl_exec($cURL);
						
						// Returning false if the request failed
						if(!$result){
							if($lastModified && curl_getinfo($cURL,CURLINFO_HTTP_CODE)==304){
								$this->errorCode=214;
								$this->errorMessage='Not modified';
								$this->log[]=$this->errorMessage;
							} else {
								$this->errorCode=205;
								$this->errorMessage='POST request failed: cURL error '.curl_getinfo($cURL,CURLINFO_HTTP_CODE).' - '.curl_error($cURL);
								$this->log[]=$this->errorMessage;
							}
							// Closing the resource
							curl_close($cURL);
							return false;
						} else {
							$this->log[]='POST request successful: '.curl_getinfo($cURL,CURLINFO_HTTP_CODE);
						}
						
						// Closing the resource
						curl_close($cURL);
					
					} else {
						$this->errorCode=205;
						$this->errorMessage='POST request failed: cURL is not supported';
						$this->log[]=$this->errorMessage;
						return false;
					}
				}
				
				// Log entry
				$this->log[]='Result of request: '.$result;
				
			// DECRYPTION
				
				// If requested data was encrypted, then this attempts to decrypt the data
				// This also checks to make sure that a serialized data was not returned (which is usually an error)
				if(strpos($result,'{')===false && strpos($result,'[')===false && isset($this->cryptedData['www-crypt-output']) || isset($thisInputData['www-crypt-output'])){
					// Getting the decryption key
					if(isset($this->cryptedData['www-crypt-output'])){
						$cryptKey=$this->cryptedData['www-crypt-output'];
					} else {
						$cryptKey=$thisInputData['www-crypt-output'];
					}
					// Decryption is different based on whether secret key was used or not
					if($apiSecretKey){
						// If secret key was set, then decryption uses the secret key for initialization vector
						$result=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,md5($cryptKey),base64_decode($result),MCRYPT_MODE_CBC,md5($apiSecretKey));
					} else {
						// Without secret key the system assumes that public profile is used and decryption is done in ECB mode
						$result=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,md5($cryptKey),base64_decode($result),MCRYPT_MODE_ECB);
					}
					// If decryption was a success
					if($result){
						$result=trim($result);
						$this->log[]='Result of decrypted request: '.$result;
					} else {
						$this->errorCode=206;
						$this->errorMessage='Output decryption has failed';
						$this->log[]=$this->errorMessage;
						return false;
					}
				}
				
			// PARSING REQUEST RESULT
			
				// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
				if($unserializeResult && (!isset($thisInputData['www-return-type']) || $thisInputData['www-return-type']=='json')){
					// JSON support is required
					$result=json_decode($result,true);
				} else if($unserializeResult && $thisInputData['www-return-type']=='serializedarray'){
					// Return data is unserialized
					$result=unserialize($result,true);
					if(!$result){
						$this->errorCode=207;
						$this->errorMessage='Cannot unserialize returned data: unserialize() failed';
						$this->log[]=$this->errorMessage;
						return false;
					} else {
						$this->log[]='Returning unserialized result';
					}
				} else if($unserializeResult && $thisInputData['www-return-type']=='querystring'){
					// Return data is filtered through string parsing and url decoding to create return array
					$result=parse_str(urldecode($result),$result);
					if(!$result){
						$this->errorCode=207;
						$this->errorMessage='Cannot unserialize returned data: Cannot parse query data string';
						$this->log[]=$this->errorMessage;
						return false;
					} else {
						$this->log[]='Returning parsed query string result';
					}
				} else if($unserializeResult){
					// Every other unserialization attempt fails
					$this->errorCode=207;
					$this->errorMessage='Cannot unserialize returned data: Data type '.$thisInputData['www-return-type'].' not supported';
					$this->log[]=$this->errorMessage;
					return false;
				} else {
					// Data is simply returned if serialization was not requested
					$this->log[]='Returning result';
				}
				
			// ERRORS
			
				if($unserializeResult && isset($result['www-error'])){
					$this->errorCode=$result['www-error-code'];
					$this->errorMessage='Error from API: '.$result['www-error-code'].' - '.$result['www-error'];
					$this->log[]=$this->errorMessage;
					return false;
				}
				
			// RESULT VALIDATION
			
				// Result validation only applies to non-public profiles
				if($apiProfile){
				
					// Only unserialized data can be validated
					if($unserializeResult){
					
						// If it was requested that validation timestamp is returned
						if($returnTimestamp){
							if(isset($result['www-timestamp'])){
								// Making sure that the returned result is within accepted time limit
								if((time()-$timestampDuration)>$result['www-timestamp']){
									$this->errorCode=210;
									$this->errorMessage='Validation timestamp is too old';
									$this->log[]=$this->errorMessage;
									return false;
								}
							} else {
								$this->errorCode=209;
								$this->errorMessage='Validation data missing: Timestamp was not returned';
								$this->log[]=$this->errorMessage;
								return false;
							}
						}
						
						// If it was requested that validation hash is returned
						if($returnHash){
							// Hash and timestamp have to be defined in response
							if(isset($result['www-hash'])){
								// Assigning returned array to hash validation array
								$validationHash=$result;
								// Hash itself is removed from validation
								unset($validationHash['www-hash']);
								// Data is sorted
								ksort($validationHash);
								// Validation depends on whether session creation or destruction commands were called
								if($thisInputData['www-command']=='www-create-session'){
									$hash=sha1(json_encode($validationHash,JSON_NUMERIC_CHECK).$apiSecretKey);
								} else {
									$hash=sha1(json_encode($validationHash,JSON_NUMERIC_CHECK).$apiToken.$apiSecretKey);
								}
								// If sent hash is the same as calculated hash
								if($hash==$result['www-hash']){
									$this->log[]='Hash validation successful';
								} else {
									$this->errorCode=211;
									$this->errorMessage='Hash validation failed';
									$this->log[]=$this->errorMessage;
									return false;
								}
							} else {
								$this->errorCode=209;
								$this->errorMessage='Validation data missing: Hash was not returned';
								$this->log[]=$this->errorMessage;
								return false;
							}
						}
					
					} else {
						$this->errorCode=208;
						$this->errorMessage='Cannot validate hash: Unserialization not possible';
						$this->log[]=$this->errorMessage;
						return false;
					}
				
				}
			
			// Resetting the error variables
			$this->errorCode=false;
			$this->errorMessage=false;
			
			// If this command was to create a token
			if($thisInputData['www-command']=='www-create-session' && isset($result['www-token'])){
				$this->apiToken=$result['www-token'];
				$this->log[]='Session token was found in reply, API session token set to: '.$result['www-token'];
			}
				
			// Returning request result
			return $result;
			
		}


}

?>