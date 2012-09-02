<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * State Class
 *
 * State is always required by Wave Framework. It is used by API and some handlers. State is used 
 * to keep track of system state, configuration and its changes, such as relevant PHP settings. 
 * It allows changing these settings, and thus affecting API or PHP configuration. State also 
 * includes functionality for State Messenger, sessions, cookies, translations and sitemap data. 
 * State is assigned in API and is accessible in MVC objects through Factory wrapper methods. 
 * Multiple different states can be used by the same request, but usually just one is used per 
 * request. State is only kept for the duration of the request processing and is not stored 
 * beyond its use in the request.
 *
 * @package    State
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/state.htm
 * @since      1.0.0
 * @version    3.1.5
 */

class WWW_State	{

	/**
	 * This should hold WWW_Database class and connection data, if used.
	 */
	public $data=array();
	
	/**
	 * Database connection is stored in this variable, if set
	 */
	public $databaseConnection=false;
	
	/**
	 * This holds the 'keyword' or 'passkey' of currently used State messenger.
	 */
	private $messenger=false;
	
	/**
	 * This holds state messenger data as an array.
	 */
	private $messengerData=array();
	
	/**
	 * Construction of State object initializes the defaults for $data variable. A lot of the 
	 * data is either loaded from /config.ini file or initialized based on server environment 
	 * variables. Fingerprint string is also created during construction as well as input data 
	 * loaded from XML or JSON strings, if sent with POST directly.
	 *
	 * @param array [$config] configuration array
	 * @return object
	 */
	final public function __construct($config=array()){
	
		// PRE-DEFINED STATE VALUES
		
			// Required constants
			if(!defined('__IP__')){
				define('__IP__',$_SERVER['REMOTE_ADDR']);
			}
			if(!defined('__ROOT__')){
				define('__ROOT__',__DIR__.DIRECTORY_SEPARATOR);
			}
	
			// A lot of default State variables are loaded from PHP settings, others are simply pre-defined
			// Every setting from core configuration file is also listed here
			$this->data=array(
				'404-image-placeholder'=>true,
				'404-view'=>'404',
				'apc'=>1,
				'api-logging'=>false,
				'api-profile'=>'public',
				'api-public-profile'=>'public',
				'api-public-token'=>false,
				'base-url'=>false,
				'blacklist-limiter'=>false,
				'client-ip'=>__IP__,
				'client-user-agent'=>((isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:''),
				'data-root'=>false,
				'database-errors'=>true,
				'database-host'=>'localhost',
				'database-name'=>'',
				'database-password'=>'',
				'database-persistent'=>false,
				'database-type'=>'mysql',
				'database-username'=>'',
				'dynamic-color-whitelist'=>'',
				'dynamic-filter-whitelist'=>'',
				'dynamic-image-filters'=>true,
				'dynamic-image-loading'=>true,
				'dynamic-max-size'=>1000,
				'dynamic-position-whitelist'=>'',
				'dynamic-quality-whitelist'=>'',
				'dynamic-resource-loading'=>true,
				'dynamic-size-whitelist'=>'',
				'enforce-first-language-url'=>true,
				'enforce-url-end-slash'=>true,
				'file-robots'=>'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',
				'fingerprint'=>'',
				'forbidden-extensions'=>array('tmp','log','ht','htaccess','pem','crt','db','sql','version','conf','ini'),
				'headers-set'=>array(),
				'headers-unset'=>array(),
				'home-view'=>'home',
				'http-accept'=>((isset($_SERVER['HTTP_ACCEPT']))?explode(',',$_SERVER['HTTP_ACCEPT']):''),
				'http-accept-charset'=>((isset($_SERVER['HTTP_ACCEPT_CHARSET']))?explode(',',$_SERVER['HTTP_ACCEPT_CHARSET']):array()),
				'http-accept-encoding'=>((isset($_SERVER['HTTP_ACCEPT_ENCODING']))?explode(',',$_SERVER['HTTP_ACCEPT_ENCODING']):array()),
				'http-accept-language'=>((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))?explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']):array()),
				'http-authentication'=>false,
				'http-authentication-ip'=>'*',
				'http-authentication-limiter'=>false,
				'http-authentication-password'=>'',
				'http-authentication-username'=>'',
				'http-do-not-track'=>((isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT']==1)?true:false),
				'http-content-length'=>((isset($_SERVER['CONTENT_LENGTH']))?$_SERVER['CONTENT_LENGTH']:false),
				'http-content-type'=>((isset($_SERVER['CONTENT_TYPE']))?$_SERVER['CONTENT_TYPE']:false),
				'http-host'=>$_SERVER['HTTP_HOST'],
				'http-if-modified-since'=>false,
				'http-input'=>false,
				'http-referrer'=>((isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:false),
				'http-request-method'=>$_SERVER['REQUEST_METHOD'],
				'https-limiter'=>false,
				'https-mode'=>((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']==1 || $_SERVER['HTTPS']=='on'))?true:false),
				'image-extensions'=>array('jpeg','jpg','png'),
				'image-robots'=>'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',
				'index-url-cache-timeout'=>0,
				'index-view-cache-timeout'=>0,
				'internal-logging'=>false,
				'keys-root'=>false,
				'language'=>false,
				'languages'=>array('en'),
				'limiter'=>false,
				'load-limiter'=>false,
				'output-compression'=>'deflate',
				'project-title'=>'',
				'request-id'=>((isset($_SERVER['UNIQUE_ID']))?$_SERVER['UNIQUE_ID']:''),
				'request-limiter'=>false,
				'request-time'=>$_SERVER['REQUEST_TIME'],
				'request-uri'=>$_SERVER['REQUEST_URI'],
				'resource-cache-timeout'=>31536000,
				'resource-extensions'=>array('css','js','txt','csv','xml','html','htm','rss','vcard'),
				'resource-robots'=>'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',
				'robots'=>'noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',
				'robots-cache-timeout'=>14400,
				'server-ip'=>$_SERVER['SERVER_ADDR'],
				'session-data'=>array(),
				'session-fingerprint'=>0,
				'session-fingerprint-key'=>'www-fingerprint',
				'session-id'=>false,
				'session-lifetime'=>0,
				'session-namespace'=>'WWW'.crc32(__ROOT__),
				'session-permissions-key'=>'www-permissions',
				'session-user-key'=>'www-user',
				'sitemap'=>array(),
				'sitemap-cache-timeout'=>14400,
				'sitemap-raw'=>array(),
				'static-root'=>false,
				'system-root'=>str_replace('engine'.DIRECTORY_SEPARATOR.'class.www-state.php','',__FILE__),
				'timezone'=>false,
				'tmp-root'=>false,
				'translations'=>array(),
				'true-request'=>false,
				'trusted-proxies'=>array(),
				'user-data'=>false,
				'user-permissions'=>false,
				'user-root'=>false,
				'view'=>array(),
				'web-root'=>str_replace('index.php','',$_SERVER['SCRIPT_NAME']),
				'whitelist-limiter'=>false
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
			
			// Defining default user root folder
			if(!$this->data['user-root']){
				$this->data['user-root']=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR;
			}
			
			// Defining default user root folder
			if(!$this->data['data-root']){
				$this->data['data-root']=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR;
			}
			
			// Defining default static folder
			if(!$this->data['static-root']){
				$this->data['static-root']=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR;
			}
			
			// Defining temporary files root folder
			if(!$this->data['tmp-root']){
				$this->data['tmp-root']=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
			}
			
			// Defining certificates and keys folder
			if(!$this->data['keys-root']){
				$this->data['keys-root']=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'keys-'.DIRECTORY_SEPARATOR;
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
				
			} elseif(isset($_FILES['www-xml']) || isset($_REQUEST['www-xml'])){
			
				// If this is a file upload or not
				if(isset($_FILES['www-xml'])){
			
					// This is not supported in earlier versions of LibXML
					if(defined('LIBXML_PARSEHUGE')){
						$tmp=simplexml_load_file($_FILES['www-xml']['tmp_name'],'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE | LIBXML_PARSEHUGE);
					} else {
						$tmp=simplexml_load_file($_FILES['www-xml']['tmp_name'],'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE);
					}
					
				} else {
			
					// This is not supported in earlier versions of LibXML
					if(defined('LIBXML_PARSEHUGE')){
						$tmp=simplexml_load_string($_REQUEST['www-xml'],'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE | LIBXML_PARSEHUGE);
					} else {
						$tmp=simplexml_load_string($_REQUEST['www-xml'],'SimpleXMLElement',LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE);
					}
					
				}
				
				// Data is converted to array only if an object was created
				if($tmp){
					$this->data['http-input']=json_decode(json_encode($tmp),true);
				}
			
			} elseif(isset($_FILES['www-json']) || isset($_REQUEST['www-json'])){
			
				if(isset($_FILES['www-json'])){
					// JSON string is converted to associative array
					$this->data['http-input']=json_decode(file_get_contents($_FILES['www-json']['tmp_name']),true);
				} else {
					// JSON string is converted to associative array
					$this->data['http-input']=json_decode($_REQUEST['www-json'],true);
				}
			
				
			}
		
	}
	
	/**
	 * When State class is not used anymore, then state messenger data - if set - is written 
	 * to filesystem based on the State messenger key. This method also deletes session cookie, 
	 * if sessions have been used but the session variable itself is empty.
	 *
	 * @return null
	 */
	final public function __destruct(){
		// Only applies if request messenger actually holds data
		if($this->messenger && !empty($this->messengerData)){
			// Finding data folder
			$dataFolder=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($this->messenger,0,2).DIRECTORY_SEPARATOR;
			if(!is_dir($dataFolder)){
				if(!mkdir($dataFolder,0755)){
					trigger_error('Cannot create messenger folder',E_USER_ERROR);
				}
			}
			// Writing messenger data to file
			if(!file_put_contents($dataFolder.$this->messenger.'.tmp',serialize($this->messengerData))){
				trigger_error('Cannot write messenger data',E_USER_ERROR);
			}
		}
		// This will commit session to the session storage
		if(!headers_sent()){
			$this->commitHeaders();
		}
	}
	
	// STATE MANIPULATION
	
		/**
		 * This is the basic call to return a State variable from the object. When call is made 
		 * without any parameters, then the entire State data variable is returned. When 
		 * $variable is set, then this method returns key of that $variable from $data array. 
		 * If the returned array is an array as well, then setting $subvariable can set the 
		 * sub-key of that array and return that instead.
		 *
		 * @param string [$variable] data array key to be returned
		 * @param string [$subVariable] if returned element is an array itself, this returns the value of that key
		 * @return mixed
		 */
		final public function getState($variable=false,$subVariable=false){
		
			// Unless variable and subvariable are set, the script returns entire State data array
			if($subVariable && $variable){
				// If variable and subvariable are both defined and data exists
				if(isset($this->data[$variable][$subVariable])){
					return $this->data[$variable][$subVariable];
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
		
		/**
		 * This method is used to set a $data variable value in State object. $variable can 
		 * also be an array of keys and values, in which case multiple variables are set at 
		 * once. This method uses stateChanged() for variables that carry additional 
		 * functionality, such as setting timezone.
		 *
		 * @param string/array [$variable] data key to be set or an array of keys and values
		 * @param mixed [$value] value of the new data
		 * @return boolean
		 */
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
		
		/**
		 * This is a private method used internally whenever configuration is changed. It has 
		 * checks for cases when a variable is changed that carries additional functionality 
		 * such as when changing the timezone or output compression. For example, if output 
		 * compression is set, but not supported by user agent that is making the request, 
		 * then output supression is turned off.
		 *
		 * @param string [$variable] variable name that is changed
		 * @param mixed [$value] new value of the variable
		 * @return boolean
		 */
		final private function stateChanged($variable,$value=true){
		
			// Value is set instantly
			$this->data[$variable]=$value;
			
			// Certain variables are checked that might change system flags
			switch ($variable) {
				case 'timezone':
					// Attempting to set default timezone
					date_default_timezone_set($value);
					break;
				case 'session-lifetime':
					if($value!=0){
						if(function_exists('ini_set') && ini_set('session.gc_maxlifetime',$value)){
							$this->data[$variable]=$value;
						}
					}
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
		
		/**
		 * This function is called before output is pushed to browser by the API or when State 
		 * object is not used anymore. This method is not accessible to Factory class, but it 
		 * is not private.
		 *
		 * @return boolean
		 */
		final public function commitHeaders(){
			// Removes sessions and session cookies, if not used anymore
			if($this->data['session-id']){
				if(empty($this->data['session-data']) || (count($this->data['session-data'])==1 && isset($this->data['session-data']['www-session-start']))){
					// Getting cookie parameters
					$cookieParams=session_get_cookie_params();
					// Unsetting cookie
					setcookie($this->data['session-namespace'],'',1,$cookieParams['path'],$cookieParams['domain'],$cookieParams['secure'],$cookieParams['httponly']);
					// Destroy sessions
					session_destroy();
				} else {
					// Storing session data actually in session storage
					$_SESSION[$this->data['session-namespace']]=$this->data['session-data'];
					session_write_close();
				}
			}
			// Commiting headers
			if(!empty($this->data['headers-set'])){
				foreach($this->data['headers-set'] as $header=>$replace){
					header($header,$replace);
				}
			}
			if(!empty($this->data['headers-unset'])){
				foreach($this->data['headers-unset'] as $header){
					header_remove($header);
				}
			}
			
			// Headers have been commited
			return true;
			
		}
		
	// SITEMAP AND TRANSLATIONS

		/**
		 * This method returns an array of currently active translations, or for a language set 
		 * with $language variable. If $keyword is also set, then it returns a specific translation 
		 * with that keyword from $language translations.
		 *
		 * @param string [$language] language keyword, if this is not set then returns current language translations
		 * @param string [$keyword] if only single keyword needs to be returned
		 * @return array, string or false if failed
		 */
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
		
		/**
		 * This method returns an array of currently active sitemap, or a sitemap for a language 
		 * set with $language variable. If $keyword is also set, then it returns a specific 
		 * sitemap node with that keyword from $language sitemap file. This method returns the 
		 * original, non-modified sitemap that has not been parsed for use with URL controller.
		 *
		 * @param string [$language] language keyword, if this is not set then returns current language sitemap
		 * @param string [$keyword] if only a single URL node needs to be returned
		 * @return array or false if failed
		 */
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
		
		/**
		 * This returns sitemap array that is modified for use with View controller and other 
		 * parts of the system. It returns sitemap for current language or a language set with 
		 * $language variable and can return a specific sitemap node based on $keyword.
		 *
		 * @param string [$language] language keyword, if this is not set then returns current language sitemap
		 * @param string [$keyword] if only a single URL node needs to be returned
		 * @return array or false if failed
		 */
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
							if(strpos($key,':')!==false){
								$url='';
								$count=0;
								$bits=explode('/',$key);
								foreach($bits as $b){
									if($b[0]!=':'){
										$url.=$b.'/';
									} else {
										$url.=':'.$count.':/';
										$count++;
									}
								}
							} else {
								$url=$key.'/';
							}
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
		
	// STATE MESSENGER
	
		/**
		 * This method initializes State messenger by giving it an address and assigning the file 
		 * that State messenger will be stored under. If the file already exists and $overwrite is 
		 * not turned on, then it automatically loads contents of that file from filesystem.
		 *
		 * @param string [$address] key that messenger data will be saved under
		 * @param boolean [$overwrite] if this is set then existing state messenger file will be overwritten
		 * @return boolean
		 */
		final public function stateMessenger($address,$overwrite=false){
			// File is stored in file system as hashed
			$this->messenger=md5($address);
			$dataAddress=$this->data['system-root'].'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR.substr($address,0,2).DIRECTORY_SEPARATOR.$address.'.tmp';
			// If this state messenger address already stores data, then it is loaded
			if(!$overwrite && file_exists($dataAddress)){
				$this->messengerData=unserialize(file_get_contents($dataAddress));
			}
			return true;
		}
		
		/**
		 * This writes data to State messenger. $data is the key and $value is the value of the 
		 * key. $data can also be an array of keys and values, in which case multiple values are 
		 * set at the same time.
		 *
		 * @param array/string [$data] key or data array
		 * @param mixed [$value] value, if data is a key
		 * @return boolean
		 */
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
		
		/**
		 * This method removes key from State messenger based on value of $key. If $key is not 
		 * set, then the entire State messenger data is cleared.
		 *
		 * @param string [$key] key that will be removed, if set to false then removes the entire data
		 * @return boolean
		 */
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
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * This method returns data from State messenger. It returns the entire State messenger 
		 * data as an array based on $address keyword that is used as the fingerprint for data. 
		 * If $remove is set, then State messenger data is removed from filesystem or State 
		 * object after being called.
		 *
		 * @param string [$address] messenger address
		 * @param boolean [$remove] true or false flag whether to delete the request data after returning it
		 * $return mixed or false if failed
		 */
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
				if($this->messenger && $remove){
					// Resetting state messenger data from the object
					$tmp=$this->messengerData;
					$this->messengerData=array();
					// Data returned
					return $tmp;
				} elseif($this->messenger){
					// Data returned
					return $this->messengerData;
				} else {
					return false;
				}
			}
		}
		
	// SESSION USER AND PERMISSIONS
	
		/**
		 * This method sets user data array in session. This is a simple helper function used 
		 * for holding user-specific data for a web service. $data is an array of user data.
		 *
		 * @param array [$data] data array set to user
		 * @return boolean
		 */
		final public function setUser($data){
			// Setting the session
			$this->setSession($this->data['session-user-key'],$data);
			// Setting the state variable
			$this->data['user-data']=$data;
			return true;
		}
		
		/**
		 * This either returns the entire user data array or just a specific $key of user data 
		 * from the session.
		 *
		 * @param string [$key] element returned from user data, if not set then returns the entire user data
		 * @return mixed
		 */
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
		
		/**
		 * This unsets user data and removes the session of user data.
		 *
		 * @return boolean
		 */
		final public function unsetUser(){
			// Unsetting the session
			$this->unsetSession($this->data['session-user-key']);
			// Unsetting the state variable
			$this->data['user-data']=false;
			return true;
		}
		
		/**
		 * This method sets an array of $permissions or a comma-separated string of permissions 
		 * for the current user permissions session.
		 *
		 * @param array/string [$permissions] an array or a string of permissions
		 * @return boolean
		 */
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
		
		/**
		 * This method returns an array of currently set user permissions from the session.
		 *
		 * @return array
		 */
		final public function getPermissions(){
			// Testing if permissions state has been populated or not
			if(!$this->data['user-permissions']){
				$this->data['user-permissions']=$this->getSession($this->data['session-permissions-key']);
			}
			return $this->data['user-permissions'];
		}
	
		/**
		 * This checks for an existence of permissions in the user permissions session array.
		 * $permissions is either a comma-separated string of permissions to be checked, or an 
		 * array. This method returns false when one of those permission keys is not set in the
		 * permissions session. Method returns true, if $permissions exist in the permissions 
		 * session array.
		 *
		 * @param string/array [$permissions] comma-separated string or an array that is checked against permissions array
		 * @return boolean
		 */
		final public function checkPermissions($permissions){
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
				if(!is_array($permissions)){
                    $permissions=explode(',',$permissions);
				}
				foreach($permissions as $p){
					// Returning true or false depending on whether this key exists or not
					if(!in_array($p,$this->data['user-permissions'])){
						return false;
					}
					return true;
				}
			} else {
				return true;
			}
		}
		
		/**
		 * This unsets permissions data from session similarly to how unsetUser() method unsets 
		 * user data from session.
		 *
		 * @return boolean
		 */
		final public function unsetPermissions(){
			// Unsetting the session
			$this->unsetSession($this->data['session-permissions-key']);
			// Unsetting the state variable
			$this->data['user-permissions']=false;
			return true;
		}
		
		/**
		 * This method returns the currently active public token that is used to increase security 
		 * against cross-site-request-forgery attacks. This method returns false if user session 
		 * is not populated, in which case public token is not needed. $regenerate sets if the token 
		 * should be regenerated if it already exists, this invalidates forms when Back button is 
		 * used after submitting data, but is more secure.
		 *
		 * @param boolean [$regenerate] if public token should be regenerated
		 * @return string or boolean if no user session active
		 */
		final public function getPublicToken($regenerate=false){
			// This is only required to protect users with active sessions
			if($this->getUser()){
				// Public token is stored in the session
				$token=$this->getSession('www-public-token');
				if($token && $token!='' && !$regenerate){
					return $token;
				} else {
					// Generating a new token
					$token=sha1($this->data['client-ip'].$this->data['request-id'].microtime().rand(1,1000000));
					$this->setSession('www-public-token',$token);
					return $token;
				}
			} else {
				return false;
			}
		}
		
	// SESSION AND COOKIES
	
		/**
		 * This method starts sessions. This is called automatically if sessions are accessed 
		 * but sessions have not yet been started. $lifetime is the lifetime of the cookie in 
		 * seconds. $secure flag is for session cookie to be secure and $httpOnly will mean 
		 * that cookie is for HTTP only and cannot be accessed with scripts.
		 *
		 * @param integer [$lifetime] cookie lifetime in seconds
		 * @param boolean [$secure] if secure cookie is used
		 * @param boolean [$httpOnly] if cookie is HTTP only
		 * @return booleam
		 */
		final public function startSession($lifetime=0,$secure=false,$httpOnly=true){
			// Sessions cannot be started if the headers have already been sent to the user agent
			if(headers_sent()){
				return false;
			}
			// Making sure that sessions have not already been started
			if(!session_id()){
				// Defining session name
				session_name($this->data['session-namespace']);
				// If lifetime has been set for a cookie in the browser
				// Setting session cookie parameters
				session_set_cookie_params($lifetime,$this->data['web-root'],$this->data['http-host'],$secure,$httpOnly);
				// Starting sessions
				session_start();
				// Populating the session variable
				if(isset($_SESSION[$this->data['session-namespace']])){
					$this->data['session-data']=$_SESSION[$this->data['session-namespace']];
				}
				// If session lifetime value is not set
				if($this->data['session-lifetime']==0 && function_exists('ini_get')){
					$this->data['session-lifetime']=ini_get('session.gc-maxlifetime');
				}
				// This can regenerate the session ID, if enough time has passed
				if($this->data['session-lifetime']){
					if(!isset($this->data['session-data']['www-session-start'])){
						// Storing a session creation time in sessions
						$this->data['session-data']['www-session-start']=$this->data['request-time'];
					} elseif($this->data['request-time']>($this->data['session-data']['www-session-start']+$this->data['session-lifetime'])){
						// Regenerating the session ID
						session_regenerate_id(true);
						// Storing a session creation time in sessions
						$this->data['session-data']['www-session-start']=$this->data['request-time'];
					}
				}
				// If session fingerprinting is used
				if($this->data['session-fingerprint']){
					if(!isset($this->data['session-data'][$this->data['session-fingerprint-key']])){
						// Storing session fingerprint in sessions
						$this->data['session-data'][$this->data['session-fingerprint-key']]=$this->data['fingerprint'];
					} elseif($this->data['session-data'][$this->data['session-fingerprint-key']]!=$this->data['fingerprint']){
						// Regenerating the session ID
						session_regenerate_id(false);
						// Emptying the session array
						$this->data['session-data']=array();
					}
				}
				// Assigning session ID to be easily accessible from State
				$this->data['session-id']=session_id();
			}
			return true;
		}
		
		/**
		 * This method regenerates ongoing session with a new ID. $deleteOld, if set, 
		 * deletes the previous session.
		 *
		 * @param boolean [$deleteOld] deletes the previous one, if set to true
		 * @return boolean
		 */
		final public function regenerateSession($deleteOld=true){
			// Making sure that sessions have been started
			if(!$this->data['session-id']){
				$this->startSession();
			}
			// Regenerating session id
			session_regenerate_id($deleteOld);
			return true;
		}
		
		/**
		 * This method clears the session variable, if it is populated for current session.
		 * Session and the cookie is actually destroyed by State __destruct() method.
		 *
		 * @return boolean
		 */
		final public function destroySession(){
			// Making sure that sessions have been started
			if(!$this->data['session-id']){
				$this->startSession();
			}
			// Emptying the session array
			$this->data['session-data']=array();
			return true;
		}
		
		/**
		 * This method sets a session variable $key with a value $value. If $key is an array of 
		 * keys and values, then multiple session variables are set at once.
		 *
		 * @param array/string [$key] key of the variable or an array of keys and values
		 * @param mixed [$value] value to be set
		 * @return boolean
		 */
		final public function setSession($key=false,$value=false){
			// Making sure that sessions have been started
			if(!$this->data['session-id']){
				$this->startSession();
			}
			// Multiple values can be set if key is an array
			if(is_array($key)){
				foreach($key as $k=>$v){
					// setting value based on key
					$this->data['session-data'][$k]=$v;
				}
			} elseif($key){
				// Setting value based on key
				$this->data['session-data'][$key]=$value;
			} else {
				// If key is false, then replacing the entire session variable
				$this->data['session-data']=$value;
			}
			return true;
		}
		
		/**
		 * This method returns $key value from session data. If $key is an array of keys, then 
		 * it can return multiple variables from session at once. If $key is not set, then entire 
		 * session array is returned.
		 *
		 * @param string/array [$key] key to return or an array of keys
		 * @return mixed
		 */
		final public function getSession($key=false){
			// Making sure that sessions have been started
			if(!$this->data['session-id']){
				// Differently from session setting, this returns false if sessions are not started
				// THis is because Data and API Handlers automatically start sessions
				return false;
			}
			// Multiple keys can be returned
			if(is_array($key)){
				// This array will hold multiple values
				$return=array();
				// This array will hold multiple values
				foreach($key as $val){
					// Getting value based on key
					if(isset($this->data['session-data'][$val])){
						$return[$val]=$this->data['session-data'][$val];
					} else {
						$return[$val]=false;
					}
				}
				return $return;
			} elseif($key){
				// Return data from specific key
				if(isset($this->data['session-data'][$key])){
					return $this->data['session-data'][$key];
				} else {
					return false;
				}
			} else {
				// Return entire session data, if key was not set
				if(isset($this->data['session-data'])){
					return $this->data['session-data'];
				} else {
					return false;
				}
			}
		}
		
		/**
		 * This method unsets $key value from current session. If $key is an array of keys, then 
		 * multiple variables can be unset at once. If $key is not set at all, then this simply 
		 * destroys the entire session.
		 *
		 * @param string/array [$key] key of the value to be unset, or an array of keys
		 * @return boolean
		 */
		final public function unsetSession($key=false){
			// Making sure that sessions have been started
			if(!$this->data['session-id']){
				$this->startSession();
			}
			// Can unset multiple values
			if(is_array($key)){
				foreach($key as $value){
					if(isset($this->data['session-data'][$value])){
						unset($this->data['session-data'][$value]);
					}
				}
				//If session array is empty
				if(empty($this->data['session-data']) || (count($this->data['session-data'])==1 && isset($this->data['session-data']['www-session-start']))){
					// Destroying the session
					$this->destroySession();
				}
			} elseif($key){
				// If key is set
				if(isset($this->data['session-data'][$key])){
					unset($this->data['session-data'][$key]);
					//If session array is empty
					if(empty($this->data['session-data']) || (count($this->data['session-data'])==1 && isset($this->data['session-data']['www-session-start']))){
						// Destroying the session
						$this->destroySession();
					}
				} else {
					//If session array is empty
					if(empty($this->data['session-data']) || (count($this->data['session-data'])==1 && isset($this->data['session-data']['www-session-start']))){
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
		
		/**
		 * This method sets a cookie with $key and a $value. $configuration is an array of 
		 * cookie parameters that can be set.
		 *
		 * @param string/array [$key] key of the variable, or an array of keys and values
		 * @param string/array [$value] value to be set, can also be an array
		 * @param array [$configuration] cookie configuration options
		 * @return boolean
		 */
		final public function setCookie($key,$value,$configuration=array()){
			// Checking for configuration options
			if(!isset($configuration['expire'])){
				if(isset($configuration['timeout'])){
					$configuration['expire']=$this->data['request-time']+$configuration['timeout'];
				} else {
					$configuration['expire']=2147483647;
				}
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
				$configuration['httponly']=true;
			}
			// Can set multiple values
			if(is_array($key)){
				// Value can act as a configuration
				if(is_array($value)){
					$configuration=$value;
				}
				foreach($key as $k=>$v){
					// Value can be an array, in which case the values set will be an array
					if(is_array($v)){
						foreach($v as $index=>$val){
							setcookie($k.'['.$index.']',$val,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
							// Cookie values can be accessed immediately after they are set
							$_COOKIE[$k][$index]=$val;
						}
					} else {
						// Setting the cookie
						setcookie($k,$v,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
						// Cookie values can be accessed immediately after they are set
						$_COOKIE[$k]=$v;
					}
				}
			} else {
				// Value can be an array, in which case the values set will be an array
				if(is_array($value)){
					foreach($value as $index=>$val){
						setcookie($key.'['.$index.']',$val,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
						// Cookie values can be accessed immediately after they are set
						$_COOKIE[$key][$index]=$val;
					}
				} else {
					// Setting the cookie
					setcookie($key,$value,$configuration['expire'],$configuration['path'],$configuration['domain'],$configuration['secure'],$configuration['httponly']);
					// Cookie values can be accessed immediately after they are set
					$_COOKIE[$key]=$value;
				}
			}
		}
		
		/**
		 * This method returns a cookie value with the set $key. $key can also be an array of 
		 * keys, in which case multiple cookie values are returned in an array.
		 *
		 * @param string [$key] key of the value to be returned, can be an array
		 * @return mixed
		 */
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
		
		/**
		 * This method unsets a cookie with the set key of $key. If $key is an array, then 
		 * it can remove multiple cookies at once.
		 *
		 * @param string/array [$key] key of the value to be unset or an array of keys
		 * @return boolean
		 */
		final public function unsetCookie($key){
			// Can set multiple values
			if(is_array($key)){
				foreach($key as $value){
					if(isset($_COOKIE[$value])){
						// Removes cookie by setting its duration to 0
						setcookie($value,'',($this->data['request-time']-3600));
					}
				}
			} else {
				if(isset($_COOKIE[$key])){
					// Removes cookie by setting its duration to 0
					setcookie($key,'',($this->data['request-time']-3600));
				} else {
					return false;
				}
			}
			return true;
		}
		
	// HEADERS
	
		/**
		 * This method adds a header to the array of headers to be added before data is pushed 
		 * to the client, when headers are sent. $header is the header string to add and $replace 
		 * is a true/false setting for whether previously sent header like this is replaced or not.
		 *
		 * @param string/array [$header] header string to add or an array of header strings
		 * @param boolean [$replace] whether the header should be replaced, if previously set
		 * @return boolean
		 */
		final public function setHeader($header,$replace=true){
			// Multiple headers can be set at once
			if(is_array($header)){
				foreach($header as $h){
					// Removing the header from unset array
					unset($this->data['headers-unset'][$h]);
					// Assigning the setting to headers array
					$this->data['headers-set'][$h]=$replace;
				}
			} else {
				// Removing the header from unset array
				unset($this->data['headers-unset'][$header]);
				// Assigning the setting to headers array
				$this->data['headers-set'][$header]=$replace;
			}
			return true;
		}
	
		/**
		 * This method adds a header to the array of headers to be removed before data is pushed 
		 * to the client, when headers are sent. $header is the header string to remove.
		 *
		 * @param string/array [$header] header string to add or an array of header strings
		 * @return boolean
		 */
		final public function unsetHeader($header){
			// Multiple headers can be unset at once
			if(is_array($header)){
				foreach($header as $h){
					// Removing the header from unset array
					unset($this->data['headers-set'][$h]);
					// Assigning the setting to headers array
					$this->data['headers-unset'][$h]=true;
				}
			} else {
				// Unsetting the header, if previously set
				unset($this->data['headers-set'][$header]);
				// Assigning the setting to headers array
				$this->data['headers-unset'][$header]=true;
			}
			return true;
		}
	
	// TERMINAL
	
		/**
		 * This method is wrapper function for making terminal calls. It attempts to detect 
		 * what terminal is available on the system, if any, and then execute the call and 
		 * return the results of the call.
		 *
		 * @param string [$command] command to be executed
		 * return mixed
		 */
		final public function terminal($command){
		
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
			return array('output'=>$output,'status'=>$status);
			
		}
	
}
	
?>