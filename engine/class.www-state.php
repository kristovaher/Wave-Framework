<?php

/*
WWW - PHP micro-framework
State class

One of the three main files of the framework, this file is always required. WWW_State class is 
used to keep track of system state, such as relevant PHP settings, changing these settings, 
system and API configuration and other variables. This class is used by both WWW_API and 
WWW_Factory (and thus most, if not all, MVC files in models, views and controllers directories). 
Multiple different states can be used by the same request, but usually just one is used per 
request. State is only kept for the duration of the request processing and is not stored.

* State file is also used to set specific configuration options that affect PHP
* State basically stores data and database connection information
* Multiple state files can be used for requests and the state can be changed request per request

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_State	{

	// State data is stored in this public array
	public $data=array();
	
	// Database connection is stored in this variable, if set
	public $databaseConnection=false;
	
	// When state file is initiated, it populates data with default values from system and PHP settings
	// * config - If set, State file has additional data loaded from provided configuration array
	public function __construct($config=array()){
	
		// A lot of default State variables are loaded from PHP settings, others are simply pre-defined
		$this->data=array(
			'api-cache-timeout'=>0,
			'api-keys'=>array(),
			'api-key'=>'',
			'api-profile'=>'public',
			'api-hash'=>'',
			'api-serializer'=>'json',
			'api-return-data-type'=>'php',
			'api-output'=>0,
			'api-command'=>'',
			'api-content-type'=>'',
			'api-token-timeout'=>0,
			'api-minify'=>0,
			'api-input-data'=>array(),
			'resource-cache-timeout'=>31536000,
			'home-view'=>'home',
			'error-reporting'=>0,
			'timezone'=>false,
			'disable-session-start'=>false,
			'output-compression'=>'deflate',
			'http-host'=>$_SERVER['HTTP_HOST'],
			'http-accept'=>((isset($_SERVER['HTTP_ACCEPT']))?explode(',',$_SERVER['HTTP_ACCEPT']):''),
			'http-accept-encoding'=>((isset($_SERVER['HTTP_ACCEPT_ENCODING']))?explode(',',$_SERVER['HTTP_ACCEPT_ENCODING']):array()),
			'http-accept-charset'=>((isset($_SERVER['HTTP_ACCEPT_CHARSET']))?explode(',',$_SERVER['HTTP_ACCEPT_CHARSET']):array()),
			'http-accept-language'=>((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']):array()),
			'http-authentication'=>false,
			'http-authentication-username'=>'',
			'http-authentication-password'=>'',
			'https-mode'=>((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']==1 || $_SERVER['HTTPS']=='on'))?true:false),
			'system-root'=>str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']),
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
			'request-uri'=>$_SERVER['REQUEST_URI'],
			'request-time'=>$_SERVER['REQUEST_TIME'],
			'request'=>false,
			'fingerprint'=>'',
			'session-cookie'=>false,
			'current-cache-timeout'=>$_SERVER['REQUEST_TIME']
		);
		
		// If array of configuration data is set during object creation, it is used
		if(!empty($config)){
			$this->setState($config);
		}
		
		// If timezone is still set to false
		if($this->data['timezone']==false){
			// Some systems throw a deprecated warning without implicitly re-setting default timezone
			date_default_timezone_set(date_default_timezone_get());
		}
		
		// If first language is not defined then first node from languages array is used
		if($this->data['langauge']==false){
			$this->data['language']=$this->data['languages'][0];
		}
		
		// Sessions are started only if sessions are not already started and auto-start is not disabled
		if($this->data['disable-session-start']==false){
			if(!session_id()){
				// Assigning current session name to session-cookie variable
				if(!isset($config['session-cookie'])){
					$this->data['session-cookie']=session_name();
				}
				// Starting sessions
				session_start();
			}
		}
		
		// Compressed output is turned off if the requesting client does not support it
		if($this->data['output-compression']!=false){
			if(!in_array($this->data['output-compression'],$this->data['http-accept-encoding']) || !extension_loaded('Zlib')){
				$this->data['output-compression']=false;
			}
		}
		
		// If configuration has not sent request string, State solves it by request-uri
		if(!$this->data['request']){
			// If install is at www.example.com/w/ subfolder and user requests www.example.com/w/en/page/ then this would be parsed to 'en/page/'
			$this->data['request']=preg_replace('/(^'.str_replace('/','\/',$this->data['web-root']).')/i','',$this->data['request-uri']);
		}
		
		// IP may be forwarded, this can check for such an occasion
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$this->data['true-client-ip']=$_SERVER['HTTP_CLIENT_IP'];
		} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$this->data['true-client-ip']=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		// Fingerprint is created based on data sent by client, this can be useful for light detection without cookies
		$fingerprint=$this->data['true-client-ip'].$this->data['client-ip'];
		$fingerprint.=$this->data['client-user-agent'];
		$fingerprint.=(isset($_SERVER['HTTP_ACCEPT']))?$_SERVER['HTTP_ACCEPT']:'';
		$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?$_SERVER['HTTP_ACCEPT_LANGUAGE']:'';
		$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_ENCODING']))?$_SERVER['HTTP_ACCEPT_ENCODING']:'';
		$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_CHARSET']))?$_SERVER['HTTP_ACCEPT_CHARSET']:'';
		$fingerprint.=(isset($_SERVER['HTTP_KEEP_ALIVE']))?$_SERVER['HTTP_KEEP_ALIVE']:'';
		$fingerprint.=(isset($_SERVER['HTTP_CONNECTION']))?$_SERVER['HTTP_CONNECTION']:'';
		
		// Fingerprint is hashed as MD5
		$this->data['fingerprint']=md5($fingerprint);
		
	}
	
	// Returns data from State data array
	// * variable - data array key to be returned
	// * subvariable - if returned element is an array itself, this returns the value of that key
	public function getState($variable=false,$subvariable=false){
	
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
	// Returns true
	public function setState($variable,$value=true){
	
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
	private function stateChanged($variable,$value=true){
	
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
				
			case 'session-cookie':
				// If session cookie name is set
				if(!$value){
					session_name($value);
				}
				break;
				
			case 'output-compression':
				// If client does not expect compressed data and PHP extension is not loaded, then this value cannot be turned on
				if($value==false || !in_array($value,$this->data['http-accept-encoding']) || !extension_loaded('Zlib')){
					$this->data[$variable]=false;
				}
				break;
				
		}
		
		// State has been changed
		return true;
		
	}
	
}
	
?>
