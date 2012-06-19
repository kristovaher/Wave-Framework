<?php

/*
Wave Framework
State class

State is always required by Wave Framework. It is used by API and some handlers. State is used 
to keep track of system state and its changes, such as relevant PHP settings. It allows changing 
changing these settings, and thus affecting API or PHP configuration. State is assigned in API 
and is accessible in MVC objects as well. Multiple different states can be used by the same 
request, but usually just one is used per request. State is only kept for the duration of the 
request processing and is not stored beyond its use in the request.

* /config.ini file settings are loaded into State and can overwrite some State values
* Some state values affect PHP or framework internal settings
* State also stores database connection information, which is used by MVC objects through Factory

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
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
				'apc'=>1,
				'project-title'=>'Wave Framework',
				'api-public-profile'=>'public',
				'api-profile'=>'public',
				'api-token-timeout'=>3600,
				'api-timestamp-timeout'=>30,
				'base-url'=>false,
				'resource-cache-timeout'=>31536000,
				'home-view'=>'home',
				'404-view'=>'404',
				'timezone'=>false,
				'output-compression'=>'deflate',
				'http-host'=>$_SERVER['HTTP_HOST'],
				'http-accept'=>((isset($_SERVER['HTTP_ACCEPT']))?explode(',',$_SERVER['HTTP_ACCEPT']):''),
				'http-accept-encoding'=>((isset($_SERVER['HTTP_ACCEPT_ENCODING']))?explode(',',$_SERVER['HTTP_ACCEPT_ENCODING']):array()),
				'http-accept-charset'=>((isset($_SERVER['HTTP_ACCEPT_CHARSET']))?explode(',',$_SERVER['HTTP_ACCEPT_CHARSET']):array()),
				'http-accept-language'=>((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']):array()),
				'http-authentication'=>false,
				'http-authentication-username'=>'',
				'http-if-modified-since'=>false,
				'http-authentication-password'=>'',
				'http-content-type'=>((isset($_SERVER['CONTENT_TYPE']))?$_SERVER['CONTENT_TYPE']:false),
				'http-content-length'=>((isset($_SERVER['CONTENT_LENGTH']))?$_SERVER['CONTENT_LENGTH']:false),
				'http-input'=>false,
				'https-mode'=>((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']==1 || $_SERVER['HTTPS']=='on'))?true:false),
				'system-root'=>str_replace('engine'.DIRECTORY_SEPARATOR.'class.www-state.php','',__FILE__),
				'web-root'=>str_replace('index.php','',$_SERVER['SCRIPT_NAME']),
				'enforce-url-end-slash'=>true,
				'enforce-first-language-url'=>true,
				'languages'=>array('en'),
				'language'=>false,
				'robots'=>'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',
				'client-user-agent'=>((isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:''),
				'client-ip'=>__IP__,
				'server-ip'=>$_SERVER['SERVER_ADDR'],
				'trusted-proxies'=>array(),
				'request-id'=>((isset($_SERVER['UNIQUE_ID']))?$_SERVER['UNIQUE_ID']:''),
				'request-uri'=>$_SERVER['REQUEST_URI'],
				'request-time'=>$_SERVER['REQUEST_TIME'],
				'session-namespace'=>'WWW'.crc32(__ROOT__),
				'session-permissions-key'=>'www-permissions',
				'session-user-key'=>'www-user',
				'user-data'=>false,
				'user-permissions'=>false,
				'translations'=>array(),
				'sitemap-raw'=>array(),
				'sitemap'=>array(),
				'view'=>array(),
				'true-request'=>false,
				'internal-logging'=>false,
				'fingerprint'=>''
			);			
			
		// ASSIGNING STATE FROM CONFIGURATION FILE
					
			// If array of configuration data is set during object creation, it is used
			// This loops over all the configuration options from /config.ini file through setState() function
			// That function has key-specific functionality that can be tied to some internal commands and PHP functions
			if(!empty($config)){
				$this->setState($config);
			}
		
			// Removing full stop from the beginning of both directory URL's
			if($this->data['web-root'][0]=='.'){
				$this->data['web-root'][0]='';
			}
			
			// Finding base URL
			if(!$this->data['base-url']){
				$this->data['base-url']=(($this->data['https-mode'])?'https://':'http://').$this->data['http-host'].$this->data['web-root'];
			}
			
		// CHECKING FOR SERVER OR PHP SPECIFIC CONFIGURATION OPTIONS
				
			// If timezone is still set to false, then system attempts to set the currently set timezone
			// Some systems throw deprecated warning if this value is not set
			if($this->data['timezone']==false){
				// Some systems throw a deprecated warning without implicitly re-setting default timezone
				date_default_timezone_set('Europe/London');
				$this->data['timezone']='Europe/London';
			}
			
			// Setting if modified since, if it happens to be set
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $this->data['http-if-modified-since']==false){
				$this->data['http-if-modified-since']=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			}
			
			// If default API profile has been changed by configuration, we assign current API profile to default profile as well
			if($this->data['api-public-profile']!='public'){
				$this->data['api-profile']=$this->data['api-public-profile'];
			}
			
			// If first language is not defined then first node from languages array is used
			if($this->data['language']==false){
				$this->data['language']=$this->data['languages'][0];
			}
			
			// Making sure that boundary is not part of the content type definition
			if($this->data['http-content-type']){
				$tmp=explode(';',$this->data['http-content-type']);
				$this->data['http-content-type']=array_shift($tmp);
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
				$this->data['true-request']=preg_replace('/(^'.preg_quote($this->data['web-root'],'/').')/i','',$this->data['request-uri']);
			}
		
		// FINGERPRINTING
		
			// Fingerprint is created based on data sent by user agent, this can be useful for light detection without cookies
			$fingerprint=$this->data['client-ip'];
			$fingerprint.=$this->data['client-user-agent'];
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT']))?$_SERVER['HTTP_ACCEPT']:'';
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?$_SERVER['HTTP_ACCEPT_LANGUAGE']:'';
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_ENCODING']))?$_SERVER['HTTP_ACCEPT_ENCODING']:'';
			$fingerprint.=(isset($_SERVER['HTTP_ACCEPT_CHARSET']))?$_SERVER['HTTP_ACCEPT_CHARSET']:'';
			$fingerprint.=(isset($_SERVER['HTTP_KEEP_ALIVE']))?$_SERVER['HTTP_KEEP_ALIVE']:'';
			$fingerprint.=(isset($_SERVER['HTTP_CONNECTION']))?$_SERVER['HTTP_CONNECTION']:'';
			
			// Fingerprint is hashed with MD5
			$this->data['fingerprint']=md5($fingerprint);
			
		// JSON OR XML BASED INPUT
			
			// For custom content types, when data is sent as an XML or JSON string
			if(!in_array($this->data['http-content-type'],array('','application/x-www-form-urlencoded','multipart/form-data'))){
			
				// Gather sent input
				$phpInput=file_get_contents('php://input');
				
				// Testing if actual XML or JSON data was submitted at all
				if($phpInput && $phpInput!=''){
					
					// Parsing method depends on content type header
					if($this->data['http-content-type']=='application/json'){
					
						// JSON string is converted to associative array
						$this->data['http-input']=json_decode($phpInput,true);
						
					} elseif(extension_loaded('SimpleXML') && ($this->data['http-content-type']=='application/xml' || $this->data['http-content-type']=='text/xml')){
					
						// This is not supported in earlier versions of LibXML
						if(defined('LIBXML_PARSEHUGE')){
							$tmp=simplexml_load_string($phpInput,'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE | LIBXML_PARSEHUGE);
						} else {
							$tmp=simplexml_load_string($phpInput,'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE);
						}
						
						// Data is converted to array only if an object was created
						if($tmp){
							$this->data['http-input']=json_decode(json_encode($tmp),true);
						}
						
					}
					
				}
				
			} elseif(isset($_FILES['www-xml'])){
			
				// This is not supported in earlier versions of LibXML
				if(defined('LIBXML_PARSEHUGE')){
					$tmp=simplexml_load_file($_FILES['www-xml']['tmp_name'],'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE | LIBXML_PARSEHUGE);
				} else {
					$tmp=simplexml_load_file($_FILES['www-xml']['tmp_name'],'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE);
				}
				
				// Data is converted to array only if an object was created
				if($tmp){
					$this->data['http-input']=json_decode(json_encode($tmp),true);
				}
			
			} elseif(isset($_FILES['www-json'])){
			
				// JSON string is converted to associative array
				$this->data['http-input']=json_decode(file_get_contents($_FILES['www-json']['tmp_name']),true);
				
			}
		
	}
	
	// This is called when state is not used anymore
	// It is used to store request messenger data in filesystem
	final public function __destruct(){
		// Only applies if request messenger actually holds data
		if($this->messenger && !empty($this->messengerData)){
			// Finding data folder
			$dataFolder=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($this->messenger,0,2).DIRECTORY_SEPARATOR;
			if(!is_dir($dataFolder)){
				if(!mkdir($dataFolder,0777)){
					trigger_error('Cannot create messenger folder',E_USER_ERROR);
				}
			}
			// Writing messenger data to file
			if(!file_put_contents($dataFolder.$this->messenger.'.tmp',serialize($this->messengerData))){
				trigger_error('Cannot write messenger data',E_USER_ERROR);
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
			}
			
			// State has been changed
			return true;
			
		}
		
	// SITEMAP AND TRANSLATIONS

		// This function returns all the translations for a specific language
		// * language - Language keyword, if this is not set then returns current language translations
		// * keyword - If only single keyword needs to be returned
		// Returns an array of translations and their keywords
		final public function getTranslations($language=false,$keyword=false){
			// If language is not set, then assuming current language
			if(!$language){
				$language=$this->data['language'];
			}
			// If translations data is already stored in state
			if(!isset($this->data['translations'][$language])){
				// Translations can be loaded from overrides folder as well
				if(file_exists($this->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.translations.ini')){
					$sourceUrl=$this->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.translations.ini';
				} elseif(file_exists($this->data['system-root'].'resources'.DIRECTORY_SEPARATOR.$language.'.translations.ini')){
					$sourceUrl=$this->data['system-root'].'resources'.DIRECTORY_SEPARATOR.$language.'.translations.ini';
				} else {
					return false;
				}
				// This data can also be stored in cache
				$cacheUrl=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.translations.tmp';
				// Including the translations file
				if(!file_exists($cacheUrl) || filemtime($sourceUrl)>filemtime($cacheUrl)){
					// Translations are parsed from INI file in the resources folder
					$this->data['translations'][$language]=parse_ini_file($sourceUrl);
					if(!$this->data['translations'][$language]){
						trigger_error('Cannot parse INI file: '.$sourceUrl,E_USER_ERROR);
					}
					// Cache of parsed INI file is stored for later use
					if(!file_put_contents($cacheUrl,serialize($this->data['translations'][$language]))){
						trigger_error('Cannot store INI file cache at '.$cacheUrl,E_USER_ERROR);
					}
				} else {
					// Since INI file has not been changed, translations are loaded from cache
					$this->data['translations'][$language]=unserialize(file_get_contents($cacheUrl));
				}
			}
			// Returning keyword, if it is requested
			if($keyword){
				if(isset($this->data['translations'][$language][$keyword])){
					return $this->data['translations'][$language][$keyword];
				} else {
					return false;
				}
			} else {
				// If keyword was not set, then returning entire array
				return $this->data['translations'][$language];
			}
		}
		
		// This function returns sitemap data for a specific language
		// * language - Language keyword, if this is not set then returns current language sitemap
		// * keyword - If only a single URL node needs to be returned
		// Returns sitemap array of set language
		final public function getSitemapRaw($language=false,$keyword=false){
		
			// If language is not set, then assuming current language
			if(!$language){
				$language=$this->data['language'];
			}
			// If translations data is already stored in state
			if(!isset($this->data['sitemap-raw'][$language])){
				// Translations can be loaded from overrides folder as well
				if(file_exists($this->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.ini')){
					$sourceUrl=$this->data['system-root'].'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.ini';
				} elseif(file_exists($this->data['system-root'].'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.ini')){
					$sourceUrl=$this->data['system-root'].'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.ini';
				} else {
					return false;
				}
				// This data can also be stored in cache
				$cacheUrl=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.tmp';
				// Including the sitemap file
				if(!file_exists($cacheUrl) || filemtime($sourceUrl)>filemtime($cacheUrl)){
					// Sitemap is parsed from INI file in the resources folder
					$this->data['sitemap-raw'][$language]=parse_ini_file($sourceUrl,true);
					if(!$this->data['sitemap-raw'][$language]){
						trigger_error('Cannot parse INI file: '.$sourceUrl,E_USER_ERROR);
					}
					// Cache of parsed INI file is stored for later use
					if(!file_put_contents($cacheUrl,serialize($this->data['sitemap-raw'][$language]))){
						trigger_error('Cannot store INI file cache at '.$cacheUrl,E_USER_ERROR);
					}
				} else {
					// Since INI file has not been changed, translations are loaded from cache
					$this->data['sitemap-raw'][$language]=unserialize(file_get_contents($cacheUrl));
				}
			}
			// Returning keyword, if it is requested
			if($keyword){
				if(isset($this->data['sitemap-raw'][$language][$keyword])){
					return $this->data['sitemap-raw'][$language][$keyword];
				} else {
					return false;
				}
			} else {
				// If keyword was not set, then returning entire array
				return $this->data['sitemap-raw'][$language];
			}
			
		}
		
		// This function returns sitemap data for a specific language
		// * language - Language keyword, if this is not set then returns current language sitemap
		// * keyword - If only a single URL node needs to be returned
		// Returns sitemap array of set language
		final public function getSitemap($language=false,$keyword=false){
			// If language is not set, then assuming current language
			if(!$language){
				$language=$this->data['language'];
			}
			// If translations data is already stored in state
			if(!isset($this->data['sitemap'][$language])){
				// Getting raw sitemap data
				$siteMapRaw=$this->getSitemapRaw($language);
				if(!$siteMapRaw){
					return false;
				}
				// This is output array
				$this->data['sitemap'][$language]=array();
				// System builds usable URL map for views
				foreach($siteMapRaw as $key=>$node){
					// Only sitemap nodes with set view will be assigned to reference
					if(isset($node['view'])){
						// Since the same view can be referenced in multiple locations
						if(isset($node['subview'])){
							$node['view']=$node['view'].'/'.$node['subview'];
						}
						// This is used only if view has not yet been defined
						if(!isset($this->data['sitemap'][$language][$node['view']])){
							$this->data['sitemap'][$language][$node['view']]=$key;
						}
						// Home views do not need a URL node
						if($node['view']!=$this->data['home-view']){
							$url=$key.'/';
						} else {
							$url='';
						}
						// Storing data from Sitemap file
						$this->data['sitemap'][$language][$node['view']]=$siteMapRaw[$key];
						// If first language URL is not enforced, then this is taken into account
						if($language==$this->data['languages'][0] && $this->data['enforce-first-language-url']==false){
							$this->data['sitemap'][$language][$node['view']]['url']=$this->data['web-root'].$url;
						} else {
							$this->data['sitemap'][$language][$node['view']]['url']=$this->data['web-root'].$language.'/'.$url;
						}
					}
				}
			}
			// Returning keyword, if it is requested
			if($keyword){
				if(isset($this->data['sitemap'][$language][$keyword])){
					return $this->data['sitemap'][$language][$keyword];
				} else {
					return false;
				}
			} else {
				// If keyword was not set, then returning entire array
				return $this->data['sitemap'][$language];
			}
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
				$this->messengerData=unserialize(file_get_contents($dataAddress));
			}
			return true;
		}
		
		// This sets messenger data
		// * data - Key or data array
		// * value - Value, if data is a key
		// Returns true or false
		final public function setMessengerData($data,$value=false){
			// If messenger address is set
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
		// * key - Key that will be removed, if set to false then removes the entire data
		// Returns true if data was set and is now removed
		final public function unsetMessengerData($key=false){
			// If messenger address is set
			if($this->messenger && $key){
				if(isset($this->messengerData[$key])){
					unset($this->messengerData[$key]);
					return true;
				} else {
					return false;
				}
			} elseif($this->messenger){
				$this->messengerData=array();			
			} else {
				return false;
			}
		}
		
		// This function returns messenger data either from filesystem or from current session
		// * address - Messenger address
		// * remove - True or false flag whether to delete the request data after returning it
		// Returns request messenger data
		final public function getMessengerData($address=false,$remove=true){
			// If messenger address is set
			if($address){
				// File is stored in file system as hashed
				$address=md5($address);
				// Solving the address of messenger data
				$dataAddress=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($address,0,2).DIRECTORY_SEPARATOR.$address.'.tmp';
				if(file_exists($dataAddress)){
					// Data is stored as encoded JSON
					$data=unserialize(file_get_contents($dataAddress));
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
		
	// SESSION USER AND PERMISSIONS
	
		// This sets user data to current session
		// * data - Data array set to user
		final public function setUser($data){
			// Setting the session
			$this->setSession($this->data['session-user-key'],$data);
			// Setting the state variable
			$this->data['user-data']=$data;
			return true;
		}
		
		// This returns either entire current user session or a single key from it
		// * key - Element returned from user data, if not set then returns the entire user data
		// Returns either the whole data as array or just a single element or false, if not found
		final public function getUser($key=false){
			// Testing if permissions state has been populated or not
			if(!$this->data['user-data']){
				$this->data['user-data']=$this->getSession($this->data['session-user-key']);
				// If this session key did not exist, then returning false
				if(!$this->data['user-data']){
					return false;
				}
			}
			// If key is set
			if($key){
				if(isset($this->data['user-data'][$key])){
					// Returning key data
					return $this->data['user-data'][$key];
				} else {
					return false;
				}
			} else {
				// Returning entire user array
				return $this->data['user-data'];
			}

		}
		
		// This method unsets existing user
		// Always returns true
		final public function unsetUser(){
			// Unsetting the session
			$this->unsetSession($this->data['session-user-key']);
			// Unsetting the state variable
			$this->data['user-data']=false;
			return true;
		}
	
		// This function checks for session permissions
		// * check - String that is checked against permissions array
		// Returns either true or false, depending whether permissions are set or not
		final public function checkPermissions($check){
			// Testing if permissions state has been populated or not
			if(!$this->data['user-permissions']){
				$this->data['user-permissions']=$this->getSession($this->data['session-permissions-key']);
				// If this session key did not exist, then returning false
				if(!$this->data['user-permissions']){
					return false;
				}
			}
			// If all permissions are set, then permissions will not be separately validated and true is assumed
			if(!in_array('*',$this->data['user-permissions'])){
				if(is_array($check)){
					foreach($check as $c){
						// Returning true or false depending on whether this key exists or not
						if(!in_array($c,$this->data['user-permissions'])){
							return false;
						}
						return true;
					}
				} else {
					// Returning true or false depending on whether this key exists or not
					if(in_array($check,$this->data['user-permissions'])){
						return true;
					} else {
						return false;
					}
				}
			} else {
				return true;
			}
		}
		
		// This function returns all current session permissions
		// Returns an array of permissions
		final public function getPermissions(){
			// Testing if permissions state has been populated or not
			if(!$this->data['user-permissions']){
				$this->data['user-permissions']=$this->getSession($this->data['session-permissions-key']);
			}
			return $this->data['user-permissions'];
		}
		
		// This function sets current session permissions
		// * permissions - An array or a string of permissions
		// Always returns true
		final public function setPermissions($permissions){
			if(!is_array($permissions)){
				$permissions=explode(',',$permissions);
			}
			// Setting the session variable
			$this->setSession($this->data['session-permissions-key'],$permissions);
			// Setting the state variable
			$this->data['user-permissions']=$permissions;
			return true;
		}
		
		// This method unsets existing permissions
		// Always returns true
		final public function unsetPermissions(){
			// Unsetting the session
			$this->unsetSession($this->data['session-permissions-key']);
			// Unsetting the state variable
			$this->data['user-permissions']=false;
			return true;
		}
		
	// SESSION AND COOKIES
	
		// This starts session in current namespace
		// * secure - If secure cookie is used
		// * httponly - If cookie is HTTP only
		// Returns true
		final public function startSession($secure=false,$httpOnly=false){
			// Making sure that sessions have not already been started
			if(!session_id()){
				// Defining session name
				session_name($this->data['session-namespace']);
				// Setting session cookie parameters
				session_set_cookie_params(0,$this->data['web-root'],$this->data['http-host'],$secure,$httpOnly);
				// Starting sessions
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
			$cookieParams=session_get_cookie_params();
			setcookie($this->data['session-namespace'],'',($this->data['request-time']-3600),$cookieParams['path'],$cookieParams['domain'],$cookieParams['secure'],$cookieParams['httponly']);
			// Session state flag
			$this->sessionStarted=false;
			return true;
		}
		
		// This sets session variable in current session namespace
		// * key - Key of the variable, can be an array
		// * value - Value to be set
		// Returns true
		final public function setSession($key=false,$value=false){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			// Multiple values can be set if key is an array
			if(is_array($key)){
				foreach($key as $k=>$v){
					// setting value based on key
					$_SESSION[$this->data['session-namespace']][$k]=$v;
				}
			} elseif($key){
				// Setting value based on key
				$_SESSION[$this->data['session-namespace']][$key]=$value;
			} else {
				// If key is false, then replacing the entire session variable
				$_SESSION[$this->data['session-namespace']]=$value;
			}
			return true;
		}
		
		// Gets a value based on a key from current namespace
		// * key - Key of the value to be returned
		// Returns the value if it exists
		final public function getSession($key=false){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			// Multiple keys can be returned
			if(is_array($key)){
				// This array will hold multiple values
				$return=array();
				// This array will hold multiple values
				foreach($key as $val){
					// Getting value based on key
					if(isset($_SESSION[$this->data['session-namespace']][$val])){
						$return[$val]=$_SESSION[$this->data['session-namespace']][$val];
					} else {
						$return[$val]=false;
					}
				}
				return $return;
			} elseif($key){
				// Return data from specific key
				if(isset($_SESSION[$this->data['session-namespace']][$key])){
					return $_SESSION[$this->data['session-namespace']][$key];
				} else {
					return false;
				}
			} else {
				// Return entire session data, if key was not set
				if(isset($_SESSION[$this->data['session-namespace']])){
					return $_SESSION[$this->data['session-namespace']];
				} else {
					return false;
				}
			}
		}
		
		// Unsets session variable
		// * key - Key of the value to be unset, can be an array
		// Returns true
		final public function unsetSession($key=false){
			// Making sure that sessions have been started
			if(!$this->sessionStarted){
				$this->startSession();
			}
			// Can unset multiple values
			if(is_array($key)){
				foreach($key as $value){
					if(isset($_SESSION[$this->data['session-namespace']][$value])){
						unset($_SESSION[$this->data['session-namespace']][$value]);
					}
				}
				//If session array is empty
				if(empty($_SESSION[$this->data['session-namespace']])){
					// Destroying the session
					$this->destroySession();
				}
			} elseif($key){
				// If key is set
				if(isset($_SESSION[$this->data['session-namespace']][$key])){
					unset($_SESSION[$this->data['session-namespace']][$key]);
					//If session array is empty
					if(empty($_SESSION[$this->data['session-namespace']])){
						// Destroying the session
						$this->destroySession();
					}
				} else {
					//If session array is empty
					if(empty($_SESSION[$this->data['session-namespace']])){
						// Destroying the session
						$this->destroySession();
					}
					return false;
				}
			} else {
				// Destroying the session
				$this->destroySession();
			}
			return true;
		}
		
		// This sets session variable
		// * key - Key of the variable, can be an array
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
			// Can set multiple values
			if(is_array($key)){
				foreach($key as $k=>$v){
					// Value can be an array, in which case the values set will be an array
					if(is_array($v)){
						foreach($v as $index=>$val){
							setcookie($k.'['.$index.']',$val,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
						}
					} else {
						// Setting the cookie
						setcookie($k,$v,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
					}
				}
			} else {
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
		}
		
		// Gets a value based on a key from current cookies
		// * key - Key of the value to be returned, can be an array
		// Returns the value if it exists
		final public function getCookie($key){
			// Multiple keys can be returned
			if(is_array($key)){
				// This array will hold multiple values
				$return=array();
				foreach($key as $val){
					if(isset($_COOKIE[$val])){
						$return[$val]=$_COOKIE[$val];
					} else {
						$return[$val]=false;
					}
				}
				return $return;
			} else {
				// Returning single cookie value
				if(isset($_COOKIE[$key])){
					return $_COOKIE[$key];
				} else {
					return false;
				}
			}
		}
		
		// Unsets session variable
		// * key - Key of the value to be unset
		// * config - Additional configuration options about the cookie, such as path
		// Returns true
		final public function unsetCookie($key,$config=array()){
			// Checking for configuration options
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
			// Can set multiple values
			if(is_array($key)){
				foreach($key as $value){
					if(isset($_COOKIE[$value])){
						// Removes cookie by setting its duration to 0
						setcookie($value,'',($this->data['request-time']-3600),$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
					}
				}
			} else {
				if(isset($_COOKIE[$key])){
					// Removes cookie by setting its duration to 0
					setcookie($key,'',($this->data['request-time']-3600),$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
				} else {
					return false;
				}
			}
			return true;
		}
	
	// TERMINAL
	
		// This function looks for available terminal/command line option and attempts to execute it
		// * command - Command to be executed
		// Returns command result, if available
		final protected function terminal($command){
		
			// Status variable
			$status=1;
		
			// Checking all possibleterminal functions
			if(function_exists('system')){
				ob_start();
				system($command,$status);
				$output=ob_get_contents();
				ob_end_clean();
			} elseif(function_exists('passthru')){
				ob_start();
				passthru($command,$status);
				$output=ob_get_contents();
				ob_end_clean();
			} elseif(function_exists('exec')){
				exec($command,$output,$status);
				$output=implode("\n",$output);
			} elseif(function_exists('shell_exec')){
				$output=shell_exec($command);
			} else {
				// No function was available, returning false
				return false;
			}

			// Returning result
			return array('output'=>$output,'status'=>$return_var);
			
		}
	
}
	
?>