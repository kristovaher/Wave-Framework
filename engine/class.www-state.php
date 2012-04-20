<?php

/*
WWW Framework
State class

State is always required by WWW Framework. It is used by API and some handlers. State is used 
to keep track of system state and its changes, such as relevant PHP settings. It allows changing 
changing these settings, and thus affecting API or PHP configuration. State is assigned in API 
and is accessible in MVC objects as well. Multiple different states can be used by the same 
request, but usually just one is used per request. State is only kept for the duration of the 
request processing and is not stored beyond its use in the request.

* /config.php file settings are loaded into State and can overwrite some State values
* Some state values affect PHP or framework internal settings
* State also stores database connection information, which is used by MVC objects through Factory

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_State	{

	// State data is stored in this public array
	public $data=array();
	
	// Database connection is stored in this variable, if set
	public $databaseConnection=false;
	
	// This stores connection to request messenger
	private $messenger=false;
	private $messengerData=array();
	
	// Flag that stores if sessions have been started or not
	public $sessionStarted=false;
	
	// When state file is initiated, it populates data with default values from system and PHP settings
	// * config - If set, State file has additional data loaded from provided configuration array
	final public function __construct($config=array()){
	
		// PRE-DEFINED STATE VALUES
	
			// A lot of default State variables are loaded from PHP settings, others are simply pre-defined
			$this->data=array(
				'project-title'=>'WWW Framework',
				'api-public-profile'=>'public',
				'api-profile'=>'public',
				'api-token-timeout'=>3600,
				'api-timestamp-timeout'=>30,
				'resource-cache-timeout'=>31536000,
				'home-view'=>'home',
				'404-view'=>'404',
				'error-reporting'=>0,
				'timezone'=>false,
				'output-compression'=>'deflate',
				'http-host'=>$_SERVER['HTTP_HOST'],
				'http-accept'=>((isset($_SERVER['HTTP_ACCEPT']))?explode(',',$_SERVER['HTTP_ACCEPT']):''),
				'http-accept-encoding'=>((isset($_SERVER['HTTP_ACCEPT_ENCODING']))?explode(',',$_SERVER['HTTP_ACCEPT_ENCODING']):array()),
				'http-accept-charset'=>((isset($_SERVER['HTTP_ACCEPT_CHARSET']))?explode(',',$_SERVER['HTTP_ACCEPT_CHARSET']):array()),
				'http-accept-language'=>((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']):array()),
				'http-authentication'=>false,
				'http-authentication-username'=>'',
				'http-if-modified-since'=>((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))?strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']):false),
				'http-authentication-password'=>'',
				'https-mode'=>((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']==1 || $_SERVER['HTTPS']=='on'))?true:false),
				'system-root'=>str_replace('index.php','',((DIRECTORY_SEPARATOR!='/')?str_replace('/',DIRECTORY_SEPARATOR,$this->data['system-root']):$_SERVER['SCRIPT_FILENAME'])),
				'web-root'=>str_replace('index.php','',$_SERVER['SCRIPT_NAME']),
				'enforce-url-end-slash'=>true,
				'enforce-first-language-url'=>true,
				'languages'=>array('en'),
				'language'=>false,
				'robots'=>'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',
				'client-user-agent'=>((isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:''),
				'client-ip'=>$_SERVER['REMOTE_ADDR'],
				'true-client-ip'=>$_SERVER['REMOTE_ADDR'],
				'server-ip'=>$_SERVER['SERVER_ADDR'],
				'request-id'=>((isset($_SERVER['UNIQUE_ID']))?$_SERVER['UNIQUE_ID']:''),
				'request-uri'=>$_SERVER['REQUEST_URI'],
				'request-time'=>$_SERVER['REQUEST_TIME'],
				'session-namespace'=>'WWW'.crc32(__ROOT__),
				'true-request'=>false,
				'internal-logging'=>false,
				'fingerprint'=>''
			);
			
			
			
		// ASSIGNING STATE FROM CONFIGURATION FILE
		
			// Removing full stop from the beginning of both directory URL's
			if($this->data['web-root'][0]=='.'){
				$this->data['web-root'][0]='';
			}
			if($this->data['system-root'][0]=='.'){
				$this->data['system-root'][0]='';
			}
		
			// If array of configuration data is set during object creation, it is used
			// This loops over all the configuration options from /config.php file through setState() function
			// That function has key-specific functionality that can be tied to some internal commands and PHP functions
			if(!empty($config)){
				$this->setState($config);
			}
			
		// CHECKING FOR SERVER OR PHP SPECIFIC CONFIGURATION OPTIONS
		
		
			// If timezone is still set to false, then system attempts to set the currently set timezone
			// Some systems throw deprecated warning if this value is not set
			if($this->data['timezone']==false){
				// Some systems throw a deprecated warning without implicitly re-setting default timezone
				date_default_timezone_set(date_default_timezone_get());
			}
			
			// If default API profile has been changed by configuration, we assign current API profile to default profile as well
			if($this->data['api-public-profile']!='public'){
				$this->data['api-profile']=$this->data['api-public-profile'];
			}
			
			// If first language is not defined then first node from languages array is used
			if($this->data['language']==false){
				$this->data['language']=$this->data['languages'][0];
			}
			
			// Compressed output is turned off if the requesting user agent does not support it
			// This is also turned off if PHP does not support Zlib compressions
			if($this->data['output-compression']!=false){
				if(!in_array($this->data['output-compression'],$this->data['http-accept-encoding']) || !extension_loaded('Zlib')){
					$this->data['output-compression']=false;
				}
			}
			
			// If configuration has not sent a request string then State solves it using request-uri
			if(!$this->data['true-request']){
				// If install is at www.example.com/w/ subfolder and user requests www.example.com/w/en/page/ then this would be parsed to 'en/page/'
				$this->data['true-request']=preg_replace('/(^'.str_replace('/','\/',$this->data['web-root']).')/i','',$this->data['request-uri']);
			}
			
			// IP may be forwarded, this can check for such an occasion
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
				$this->data['true-client-ip']=$_SERVER['HTTP_CLIENT_IP'];
			} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$this->data['true-client-ip']=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		
		// FINGERPRINTING
		
			// Fingerprint is created based on data sent by user agent, this can be useful for light detection without cookies
			$fingerprint=$this->data['true-client-ip'].$this->data['client-ip'];
			$fingerprint.=$this->data['client-user-agent'];
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT']))?$_SERVER['HTTP_ACCEPT']:'';
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?$_SERVER['HTTP_ACCEPT_LANGUAGE']:'';
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_ENCODING']))?$_SERVER['HTTP_ACCEPT_ENCODING']:'';
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_CHARSET']))?$_SERVER['HTTP_ACCEPT_CHARSET']:'';
			$fingerprint.=(isset($_SERVER['HTTP_KEEP_ALIVE']))?$_SERVER['HTTP_KEEP_ALIVE']:'';
			$fingerprint.=(isset($_SERVER['HTTP_CONNECTION']))?$_SERVER['HTTP_CONNECTION']:'';
			
			// Fingerprint is hashed with MD5
			$this->data['fingerprint']=md5($fingerprint);
		
	}
	
	// This is called when state is not used anymore
	// It is used to store request messenger data in filesystem
	final public function __destruct(){
		// Only applies if request messenger actually holds data
		if($this->messenger){
			// Finding data folder
			$dataFolder=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($this->messenger,0,2).DIRECTORY_SEPARATOR;
			if(!is_dir($dataFolder)){
				if(!mkdir($dataFolder,0777)){
					throw new Exception('Cannot create messenger folder');
				}
			}
			// Writing messenger data to file
			if(!file_put_contents($dataFolder.$this->messenger.'.tmp',json_encode($this->messengerData))){
				throw new Exception('Cannot write messenger data');
			}
		}
	}
	
	// STATE MANIPULATION
	
		// Returns data from State data array
		// * variable - data array key to be returned
		// * subvariable - if returned element is an array itself, this returns the value of that key
		// Returns variable if found, false if failed
		final public function getState($variable=false,$subvariable=false){
		
			// Unless variable and subvariable are set, the script returns entire State data array
			if($subvariable && $variable){
				// If variable and subvariable are both defined and data exists
				if(isset($this->data[$variable][$subvariable])){
					return $this->data[$variable][$subvariable];
				} else {
					return false;
				}
			} elseif($variable){
				// If variable is defined and data exists
				if(isset($this->data[$variable])){
					return $this->data[$variable];
				} else {
					return false;
				}
			} else {
				// If no variable was requested the entire data array is returned
				return $this->data;
			}
			
		}
		
		// Used for setting state data
		// This will take into account internal mechanics, such as PHP settings
		// * variable - Data key to be set
		// * value - Value of the new data
		// Returns true, since it just sets a new variable
		final public function setState($variable,$value=true){
		
			// If variable is an array with values it assumes that array keys are variables and values are to be set for those variables
			if(is_array($variable)){
				foreach($variable as $key=>$val){
					// Certain variables can affect system behavior and this is checked here
					$this->stateChanged($key,$val);
				}
			} else {
				// Certain variables can affect system behavior and this is checked here
				$this->stateChanged($variable,$value);
			}
			// State has been set
			return true;
			
		}
		
		// This function is used to set certain system flags, if certain values are set for State
		// * variable - Variable name that is changed
		// * value - New value of the variable
		// Returns always true since it just checks for certain variable conditions
		final private function stateChanged($variable,$value=true){
		
			// Value is set instantly
			$this->data[$variable]=$value;
			
			// Certain variables are checked that might change system flags
			switch ($variable) {
				case 'error-reporting':
					// Attempting to turn on PHP error-reporting
					if($value!=0 && $value!=false){
						// In some environments the ini_set() function is not enabled
						if(function_exists('ini_set')){
							ini_set('display_errors',1);
							ini_set('error_reporting',$value);
						}
						error_reporting($value);
					} else {
						error_reporting(0);
					}
					break;
				case 'timezone':
					// Attempting to set default timezone
					date_default_timezone_set($value);
					break;
				case 'output-compression':
					// If user agent does not expect compressed data and PHP extension is not loaded, then this value cannot be turned on
					if($value==false || !in_array($value,$this->data['http-accept-encoding']) || !extension_loaded('Zlib')){
						$this->data[$variable]=false;
					}
					break;
				case 'languages':
					// If user agent does not expect compressed data and PHP extension is not loaded, then this value cannot be turned on
					if($value!=false && $value!=''){
						$this->data[$variable]=explode(',',$value);
					}
					break;
			}
			
			// State has been changed
			return true;
			
		}
		
	// REQUEST MESSENGER
	
		// This method sets the request messenger key
		// * address - Key that messenger data will be saved under
		// Always returns true
		final public function stateMessenger($address){
			// File is stored in file system as hashed
			$this->messenger=md5($address);
			$dataAddress=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($address,0,2).DIRECTORY_SEPARATOR.$address.'.tmp';
			// If this state messenger address already stores data, then it is loaded
			if(file_exists($dataAddress)){
				$this->messengerData=json_decode(file_get_contents($dataAddress),true);
			}
			return true;
		}
		
		// This sets messenger data
		// * data - Key or data array
		// * value - Value, if data is a key
		// Returns true or false
		final public function setMessengerData($data,$value=false){
			if($this->messenger){
				// If data is an array, then it adds data recursively
				if(is_array($data)){
					foreach($data as $key=>$value){
						// Setting messenger data
						$this->messengerData[$key]=$value;
					}
				} else {
					// Setting messenger data
					$this->messengerData[$data]=$value;
				}
				return true;
			} else {
				return false;
			}
		}
		
		// This function removes data from state messenger
		// * key - Key that will be removed
		// Returns true if data was set and is now removed
		final public function unsetMessengerData($key){
			if($this->messenger){
				if(isset($this->messengerData[$key])){
					unset($this->messengerData[$key]);
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		// This function returns messenger data either from filesystem or from current session
		// * address - Messenger address
		// * remove - True or false flag whether to delete the request data after returning it
		// Returns request messenger data
		final public function getMessengerData($address=false,$remove=true){
			if($address){
				// File is stored in file system as hashed
				$address=md5($address);
				// Solving the address of messenger data
				$dataAddress=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($address,0,2).DIRECTORY_SEPARATOR.$address.'.tmp';
				if(file_exists($dataAddress)){
					// Data is stored as encoded JSON
					$data=json_decode(file_get_contents($dataAddress),true);
					// Removing messenger data, if flag is set
					if($remove){
						unlink($dataAddress);
					}
					// Data returned
					return $data;
				} else {
					return false;
				}
			} else {
				// if there is a messenger active
				if($this->messenger){
					// Data returned
					return $this->messengerData;
				} else {
					return false;
				}
			}
		}
		
	// SESSION AND COOKIES
	
		// This starts session in current namespace
		final public function startSession(){
			// Making sure that sessions have not already been started
			if(!session_id()){
				session_name($this->data['session-namespace']);
				session_start();
			}
			// Flag for session state
			$this->sessionStarted=true;
			return true;
		}
		
		// This function regenerates ongoing session
		final public function regenerateSession(){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			// Regenerating session id
			session_regenerate_id();
			return true;
		}
		
		// This function regenerates ongoing session
		final public function destroySession(){
			// Making sure that sessions have been started
			if($this->sessionStarted){
				$this->startSession();
			}
			// Regenerating session id
			session_destroy();
			// Unsetting session cookie
			$this->unsetCookie($this->data['session-namespace']);
			return true;
		}
		
		// This sets session variable in current session namespace
		// * key - Key of the variable
		// * value - Value to be set
		// Returns true
		final public function setSession($key,$value){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			$_SESSION[$this->data['session-namespace']][$key]=$value;
			return true;
		}
		
		// Gets a value based on a key from current namespace
		// * key - Key of the value to be returned
		// Returns the value if it exists
		final public function getSession($key){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			if(isset($_SESSION[$this->data['session-namespace']][$key])){
				return $_SESSION[$this->data['session-namespace']][$key];
			} else {
				return false;
			}	
		}
		
		// Unsets session variable
		// * key - Key of the value to be unset
		// Returns true
		final public function unsetSession($key){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			if(isset($_SESSION[$this->data['session-namespace']][$key])){
				unset($_SESSION[$this->data['session-namespace']][$key]);
			} else {
				return false;
			}
			return true;
		}
		
		// This sets session variable
		// * key - Key of the variable
		// * value - Value to be set, can also be an array
		// * configuration - Cookie configuration options
		// Returns true
		final public function setCookie($key,$value,$configuration=array()){
			// Checking for configuration options
			if(!isset($configuration['expire'])){
				$configuration['expire']=2147483647;
			}
			if(!isset($configuration['path'])){
				$configuration['path']=$this->data['web-root'];
			}
			if(!isset($configuration['domain'])){
				$configuration['domain']=$this->data['http-host'];
			}
			if(!isset($configuration['secure'])){
				$configuration['secure']=false;
			}
			if(!isset($configuration['httponly'])){
				$configuration['httponly']=false;
			}
			// Value can be an array, in which case the values set will be an array
			if(is_array($value)){
				foreach($value as $index=>$val){
					setcookie($key.'['.$index.']',$val,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
				}
			} else {
				// Setting the cookie
				setcookie($key,$value,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
			}
		}
		
		// Gets a value based on a key from current cookies
		// * key - Key of the value to be returned
		// Returns the value if it exists
		final public function getCookie($key){
			if(isset($_COOKIE[$key])){
				return $_COOKIE[$key];
			} else {
				return false;
			}	
		}
		
		// Unsets session variable
		// * key - Key of the value to be unset
		// Returns true
		final public function unsetCookie($key){
			if(isset($_COOKIE[$key])){
				// Removes cookie by setting its duration to 0
				setcookie($key,'',($this->data['request-time']-3600));
			} else {
				return false;
			}
			return true;
		}
	
}
	
?>