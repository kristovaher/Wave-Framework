<?php

/*
WWW - PHP micro-framework
WWW API connnection class

This class should be used when you wish to communicate with WWW Framework that is set up in another 
server. This class is independent from WWW Framework itself and can be used by other servers alone.

* Class requires cURL for POST requests, otherwise it falls back on GET requests through file_get_connect().
* Outside connections must be allowed in server for this to be used.

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Wrapper {

	// HTTP address of WWW-Framework-based API
	private $apiAddress;
	
	private $apiProfile=false;
	private $secretKey=false;
	private $apiEncryptionKey=false;
	private $apiCommand=false;
	private $apiToken=false;
	
	private $apiMinify=0;
	private $apiCacheTimeout=0;
	private $apiReturnDataType='json';
	private $apiSerializer='json';
	
	private $inputData=array();
	private $cryptedData=array();
	
	private $curlEnabled=false;
	private $debugMode=false;
	private $log=array();
	private $requestTimeout=10;
	
	private $userAgentString='WWWFramework/2.0.0';

	public function __construct($apiAddress,$customUserAgent=false){
		$this->apiAddress=$apiAddress;
		if(extension_loaded('curl')){
			$this->curlEnabled=true;
		} elseif(ini_get('allow_url_fopen')!=1){
			throw new Exception('PHP cannot make outside requests, please enable allow_url_fopen setting');
		}
		if(!function_exists('json_encode')){
			$this->apiSerializer='serialize';
		}
		if($customUserAgent){
			$this->userAgentString=$customUserAgent;
		}
	}
	
	public function setDebugMode($flag){
		if($flag){
			$this->debugMode=true;
			$this->log[]='Debug mode started';
		} else {
			$this->debugMode=false;
			if(!empty($log)){
				$log=array();
			}
		}
	}
	
	public function setRequestTimeout($timeout=10){
		if(is_numeric($timeout) && $timeout>0){
			$this->requestTimeout=$timeout;
			if($this->debugMode){
				$this->log[]='Request timeout set to '.$timeout.' seconds';
			}
		} else {
			throw new Exception('Request timeout is not defined correctly, must be a number above 0');
		}
	}
	
	public function returnLog(){
		if($this->debugMode){
			$this->log[]='Returning log';
			return $this->log;
		} else {
			throw new Exception('Cannot return log, debug mode is turned off');
		}
	}
	
	// AUTHENTICATION
	
		public function setAuthentication($apiProfile=false,$apiSecretKey=false,$apiToken=false){
			if($apiProfile && $apiSecretKey){
				$this->apiProfile=$apiProfile;
				$this->apiSecretKey=$apiSecretKey;
				if($apiToken){
					$this->apiToken=$apiToken;
					if($this->debugMode){
						$this->log[]='Authentication set to API profile "'.$apiProfile.'" with secret key: '.$apiSecretKey.' and roken:'.$apiToken;
					}
				} else {
					if($this->debugMode){
						$this->log[]='Authentication set to API profile "'.$apiProfile.'" with secret key: '.$apiSecretKey;
					}
				}
			} else {
				throw new Exception('API profile name and secret key are missing for authentication');
			}
		}
		
		public function clearAuthentication(){
			if(isset($this->inputData['www-profile'])){
				if($this->debugMode){
					$this->log[]='API profile, API secret key and API encryption key removed, public profile assumed';
				}
				$this->apiProfile=false;
				$this->apiSecretKey=false;
			}
		}
		
	// INPUT
		
		public function setInput($input,$value){
			if(is_array($input)){
				foreach($input as $key=>$val){
					$this->inputData[$key]=$val;
					if($this->debugMode){
						$this->log[]='Input value of "'.$key.'" set to: '.$val;
					}
				}
			} else {
				$this->inputData[$input]=$value;
				if($this->debugMode){
					$this->log[]='Input value of "'.$input.'" set to: '.$value;
				}
			}
		}
		
		public function setCryptedInput($input,$value){
			if(is_array($input)){
				foreach($input as $key=>$val){
					$this->cryptedData[$key]=$val;
					if($this->debugMode){
						$this->log[]='Crypted input value of "'.$key.'" set to: '.$val;
					}
				}
			} else {
				$this->cryptedData[$input]=$value;
				if($this->debugMode){
					$this->log[]='Crypted input value of "'.$input.'" set to: '.$value;
				}
			}
		}
		
		public function unsetInput(){
			$this->inputData=array();
			$this->cryptedData=array();
			if($this->debugMode){
				$this->log[]='Input and crypted input values unset';
			}
		}
		
	// API SETTINGS
	
		public function setCommand($command=false){
			$command=strtolower(trim($command));
			if($command && $command!=''){
				$this->apiCommand=$command;
				if($this->debugMode){
					$this->log[]='API command set to: '.$command;
				}
			} else {
				throw new Exception('API command is incorrect');
			}
		}
	
		public function setCacheTimeout($timeout=0){
			if(is_numeric($timeout)){
				$this->apiCacheTimeout=$timeout;
				if($this->debugMode){
					$this->log[]='Cache timeout set to '.$timeout.' seconds';
				}
			} else {
				throw new Exception('API cache timeout is not defined correctly, must be a number');
			}
		}
		
		public function setMinify($flag){
			if($flag){
				$this->apiMinify=1;
				if($this->debugMode){
					$this->log[]='API minification request for returned result is turned on';
				}
			} else {
				$this->apiMinify=0;
				if($this->debugMode){
					$this->log[]='API minification request for returned result is turned off';
				}
			}
		}
		
		public function setReturnDataType($type){
			$type=strtolower(trim($type));
			if(in_array($type,array('json','xml','html','rss','csv','js','css','vcard','ini','serializedarray','text','binary'))){
				$this->apiReturnDataType=$type;
				if($this->debugMode){
					$this->log[]='Returned data type set to: '.$type;
				}
			} else {
				throw new Exception('This return data type is not supported: '.$type);
			}	
		}
		
	// SENDING REQUEST
		
		public function sendRequest($method='get',$unserialize=true){
			
			if($this->debugMode){
				$this->log[]='Starting to build request';
			}
		
			if($this->apiCommand && $this->apiCommand!=''){
				$this->inputData['www-command']=$this->apiCommand;
			} else {
				throw new Exception('API command is not defined');
			}
		
			if($this->apiCacheTimeout>0){
				$this->inputData['www-cache-timeout']=$this->apiCacheTimeout;
			}
			
			if($this->apiMinify==1){
				$this->inputData['www-minify']=1;
			}
			
			if($this->apiReturnDataType!='json'){
				$this->inputData['www-return-data-type']=$this->apiReturnDataType;
			}
		
			if($this->apiProfile && $this->apiSecretKey){
			
				if($this->debugMode){
					$this->log[]='API profile set, hash authentication will be used';
				}
				
				$this->inputData['www-timestamp']=time();
				
				$this->inputData['www-profile']=$this->apiProfile;
				
				if(!$this->apiToken && $this->apiCommand!='www-create-session' && $this->apiCommand!='www-destroy-session'){
					throw new Exception('Token is required for this request');
				} else {
					$apiToken='';
				}
		
				if(!empty($this->cryptedData)){
					if(extension_loaded('mcrypt')){
						if($this->apiSerializer=='json'){
							$this->inputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$this->apiToken,json_encode($this->cryptedData),MCRYPT_MODE_CBC,md5($this->apiSecretKey)));
							if($this->debugMode){
								$this->log[]='Crypted input created using encryption key, JSON encoded input data and secret key';
							}
						} else {
							$this->inputData['www-crypt-input']=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$this->apiToken,serialize($this->cryptedData),MCRYPT_MODE_CBC,md5($this->apiSecretKey)));
							if($this->debugMode){
								$this->log[]='Crypted input created using encryption key, serialized input data and secret key';
							}
						}
					} else {
						if($this->debugMode){
							$this->log[]='Mcrypt extension is required to use encrypted requests, this extension is not enabled, canceling request';
						}
						return false;
					}
				}
				
				ksort($this->inputData);
				
				if($this->apiSerializer=='json'){
					$this->inputData['www-hash']=sha1(json_encode($this->inputData).$apiToken.$this->apiSecretKey);
					if($this->debugMode){
						$this->log[]='Validation hash created using JSON encoded input data and secret key';
					}
				} else {
					$this->inputData['www-hash']=sha1(serialize($this->inputData).$apiToken.$this->apiSecretKey);
					if($this->debugMode){
						$this->log[]='Validation hash created using serialized input data and secret key';
					}
				}
				
			} elseif($this->debugMode){
				$this->log[]='API profile is not set, using public profile';
			}
			
			switch(strtolower($method)){
				case 'get':
					$url=$this->apiAddress.'?'.http_build_query($this->inputData);
					if(strlen($url<=8192)){
						if($this->curlEnabled){
							if($this->debugMode){
								$this->log[]='Making GET request to API using cURL to URL: '.$url;
							}
							$cURL=curl_init();
							if(isset($_SERVER['SCRIPT_URI'])){
								curl_setopt($cURL,CURLOPT_REFERER,$_SERVER['SCRIPT_URI']);
							}
							curl_setopt($cURL,CURLOPT_URL,$url);
							curl_setopt($cURL,CURLOPT_HTTPGET,true);
							curl_setopt($cURL,CURLOPT_TIMEOUT,$this->requestTimeout);
							if($this->userAgentString!=''){
								curl_setopt($cURL,CURLOPT_USERAGENT,$this->userAgentString);
							}
							curl_setopt($cURL,CURLOPT_HEADER,false);
							curl_setopt($cURL,CURLOPT_RETURNTRANSFER,true);
							curl_setopt($cURL,CURLOPT_FOLLOWLOCATION,false);
							curl_setopt($cURL,CURLOPT_COOKIESESSION,true);
							curl_setopt($cURL,CURLOPT_SSL_VERIFYPEER,false);
							$result=curl_exec($cURL);
							curl_close($cURL);
							if(!$result){
								if($this->debugMode){
									$this->log[]='cURL GET request to failed';
								}
								return false;
							}
						} else {
							if($this->debugMode){
								$this->log[]='Making GET request to API using file-get-contents to URL: '.$url;
							}
							if(!$result=file_get_contents($url)){
								if($this->debugMode){
									$this->log[]='File-get-contents GET request to failed';
								}
								return false;
							}
						}
					} else {
						if($this->debugMode){
							$this->log[]='Data sent is too large for a GET request, attempted to send '.strlen($url).' bytes but maximum allowed is 8192 bytes';
						}
						return false;
					}
					break;
				case 'post':
					if($this->curlEnabled){
						if($this->debugMode){
							$this->log[]='Making POST request to API using cURL to URL '.$this->apiAddress.' with data: '.serialize($this->inputData);
						}
						$cURL=curl_init();
						if(isset($_SERVER['SCRIPT_URI'])){
							curl_setopt($cURL,CURLOPT_REFERER,$_SERVER['SCRIPT_URI']);
						}
						curl_setopt($cURL,CURLOPT_URL,$this->apiAddress);
						curl_setopt($cURL,CURLOPT_POST,true);
						//File upload in POST should be in CURLOPT_POSTFIELDS as "file_box"=>"@/path/to/myfile.jpg"
						curl_setopt($cURL,CURLOPT_POSTFIELDS,$this->inputData);
						curl_setopt($cURL,CURLOPT_TIMEOUT,$this->requestTimeout);
						if($this->userAgentString!=''){
							curl_setopt($cURL,CURLOPT_USERAGENT,$this->userAgentString);
						}
						curl_setopt($cURL,CURLOPT_HEADER,false);
						curl_setopt($cURL,CURLOPT_RETURNTRANSFER,true);
						curl_setopt($cURL,CURLOPT_FOLLOWLOCATION,false);
						curl_setopt($cURL,CURLOPT_COOKIESESSION,true);
						curl_setopt($cURL,CURLOPT_SSL_VERIFYPEER,false);
						$result=curl_exec($cURL);
						curl_close($cURL);
						if(!$result){
							if($this->debugMode){
								$this->log[]='cURL GET request to failed';
							}
							return false;
						}
					
					} else {
						if($this->debugMode){
							$this->log[]='POST method requires cURL support in the server, this extension is not enabled';
						}
						return false;
					}
					break;
				default:
					if($this->debugMode){
						$this->log[]='This request method is not supported: '.$method;
					}
					return false;
					break;
			}
			
			if($result){
			
				if($this->debugMode){
					$this->log[]='Result of request: '.$result;
				}
			
				if($this->apiReturnDataType=='json' && $unserialize){
					if(function_exists('json_decode')){
						$return=json_decode($result,true);
						if($return==null){
							if($this->debugMode){
								$this->log[]='Cannot JSON decode data string';
							}
							return false;
						} else {
							if($this->debugMode){
								$this->log[]='Returning JSON decoded result';
							}
							return $return;
						}
					} else {
						if($this->debugMode){
							$this->log[]='JSON is not supported on the server, cannot decode JSON response';
						}
						return false;
					}
				} else if($this->apiReturnDataType=='serializedarray' && $unserialize){
					$return=unserialize($result,true);
					if(!$return){
						if($this->debugMode){
							$this->log[]='Cannot unserialize data string';
						}
						return false;
					} else {
						if($this->debugMode){
							$this->log[]='Returning unserialized result';
						}
						return $return;
					}
				} else if($unserialize){
					if($this->debugMode){
						$this->log[]='Cannot unserialize this return data type: '.$this->apiReturnDataType;
					}
					return false;
				} else {
					if($this->debugMode){
						$this->log[]='Returning result';
					}
					return $result;
				}
			
			} else {
				return false;
			}
			
		}


}

?>