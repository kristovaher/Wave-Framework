<?php

/**
 * Wave Framework <http://www.example.com>
 * Session Handler
 *
 * This is a project specific sessions handler that is used by Wave Framework. Custom sessions 
 * handler is needed by Wave Framework because certain projects need distributed sessions or 
 * other forms of session handling. Note that while you can edit this class to your liking, 
 * you should not change the default method names and variables as these are called by State 
 * class of Wave Framework. This class does not do garbage collection, it is recommended to use 
 * Cleaner maintenance script to clean session storage every now and then.
 *
 * @package    State
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/sessions.htm
 * @since      3.2.0
 * @version    3.2.2
 */
 
final class WWW_Sessions {
	 
	/**
	 * This is the default session name. Wave Framework does not use 'PHPSESSID' by default and 
	 * instead sets the session name (and cookie value) that starts with WWW prefix and is followed
	 * by a number generated from system folder specific folders.
	 */
	public $sessionName='PHPSESSID';

	/**
	 * This carries the currently known session ID value. This is the value of the session cookie.
	 */
	public $sessionId=false;

	/**
	 * This array stores session data. This data will be stored as serialized string in session storage
	 * in filesystem or (if you rewrite methods of this class) in database.
	 */
	public $sessionData=array();
	
	/**
	 * This holds information about session cookie and its various configuration settings. These will 
	 * be used whenever sessions are created.
	 */
	public $sessionCookie=array(
		'lifetime'=>0,
		'path'=>'/',
		'domain'=>false,
		'secure'=>false,
		'http-only'=>true
	);
	
	/*
	 * This is a true-false flag about whether session ID should be regenerated. This can be sent by 
	 * the system that uses the Session Handler at any time before sessions are commited. Wave 
	 * Framework uses this to regenerate cookies when it detects the cookie lifetime is about to end.
	 */
	public $regenerateId=false;
	
	/**
	 * This tells Wave Framework to remove the old cookie if ID is being regenerated.
	 */
	public $regenerateRemoveOld=true;
	
	/**
	 * This holds connection to database link. This is WWW_Database class by default, if database 
	 * connection is used. Thus you can use these values to log session creation or entirely replace
	 * filesystem specific session creation with your own.
	 */
	private $databaseConnection=false;
	
	/**
	 * This holds the cookie folder for the session. This is where the cookie will be stored in the 
	 * filesystem.
	 */
	private $cookieFolder=false;
	
	/**
	 * This holds whether sessions are actually commited or not. This is set to true when Test Suite is run.
	 */
	private $noCommit=false;
	
	/**
	 * Construction method of Session Handler takes two parameters. One for session name and another
	 * for database connection link to (preferably) WWW_Database class. Construction also starts 
	 * sessions automatically if it detects that the user agent has provided a session cookie with
	 * a value.
	 *
	 * @param string $sessionName session and cookie name
	 * @param integer $sessionLifetime how long sessions last
	 * @param object $databaseConnection database object
	 * @param boolean $noCommit whether databases will actually be commited
	 * @return object
	 */
	public function __construct($sessionName='PHPSESSID',$sessionLifetime=0,$databaseConnection=false,$noCommit=false){
	
		// This loads the same database connection that is loaded by Data and API Handlers if
		// the settings are used in Configuration file. Database is not used by Session Handler
		// by default, but it can be used for distributed database sessions.
		$this->databaseConnection=$databaseConnection;
		
		// Session name is what will be set as cookie name
		$this->sessionName=$sessionName;
		
		// Session name is what will be set as cookie name
		$this->noCommit=$noCommit;
	
		// Handler will attempt to load session data if session cookie is sent by the user agent
		if(isset($_COOKIE[$this->sessionName])){
			$this->sessionOpen($this->sessionName,$sessionLifetime);
		}
		
	}
	
	/**
	 * This method opens sessions. It checks if a previous session is active and closes it. This
	 * method functions differently based on if session cookie is set or not. If session cookie
	 * is set, then it attempts to populate session data from session storage if it finds a matching
	 * session. If not, then it generates a new session ID and clears the session data array.
	 *
	 * @param string $sessionName if session name is sent, then sessions will be started with this name.
	 * @param integer $sessionLifetime the age of token file to be still considered a valid token
	 * @return boolean
	 */
	public function sessionOpen($sessionName=false,$sessionLifetime=0){
	
		// Assigning session name, if sent
		if($sessionName){
			// New session name based on the value sent
			$this->sessionName=$sessionName;
		}
		
		// If another session is already running, it is commited
		if($this->sessionId){
			// This stores session data in filesystem or in database
			$this->sessionCommit();
			// Defaults for regenerate values for the newly opened session
			$this->regenerateId=false;
			$this->regenerateRemoveOld=true;
			$this->sessionData=array();
		}
		
		// If session cookie exists, then session data will be loaded
		if(isset($_COOKIE[$this->sessionName])){
		
			// Making sure that the session does not include any dangerous characters before it is used to load session data
			$this->sessionId=preg_replace('/[^0-9a-z]/i','',$_COOKIE[$this->sessionName]);
			
			// Session storage address is based on session ID name in the filesystem
			$this->cookieFolder=__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.substr($this->sessionId,0,2).DIRECTORY_SEPARATOR;
			
			// If file exists and is not older than the maximum allowed age for a session storage file, then session data is loaded from that file
			if(file_exists($this->cookieFolder.$this->sessionId.'.tmp') && ($sessionLifetime==0 || filemtime($this->cookieFolder.$this->sessionId.'.tmp')>($_SERVER['REQUEST_TIME']-$sessionLifetime))){
					
				// Loading actual session data and casting the data to array
				$this->sessionData=json_decode(file_get_contents($this->cookieFolder.$this->sessionId.'.tmp'),true);
				// Updating the last-modified time of the session storage file
				if(!$this->noCommit){
					touch($this->cookieFolder.$this->sessionId.'.tmp');
				}
				
			} else {
			
				// Since session cookie was found, but there was no session storage anymore, 
				// the session cookie is assigned to be regenerated
				$this->regenerateId=true;
				$this->regenerateRemoveOld=true;
				// Data array is also emptied
				$this->sessionData=array();
				
			}
			
		}
		
		// Sessions are started
		return true;
		
	}
	
	/**
	 * This method commits sessions to database. It uses current session configuration and 
	 * also creates or removes session cookies, if this is necessary. Wave Framework calls
	 * this method ONLY if sessions have actually changed or if they are assigned to be
	 * regenerated. This method is not the place to track if sessions are still active (use
	 * the sessionOpen() method for that).
	 * 
	 * @return boolean
	 */
	public function sessionCommit(){
		
		// If session data is empty, then session storage is deleted and session cookie
		// is removed if the user agent has one set.
		if(isset($_COOKIE[$this->sessionName]) && empty($this->sessionData)){
		
			// This is only done if sessions are actually commited and not just simulated
			if(!$this->noCommit){
			
				// Removing the session storage file
				if(file_exists($this->cookieFolder.$this->sessionId.'.tmp')){
					unlink($this->cookieFolder.$this->sessionId.'.tmp');
				}
				
				// Removing session cookie
				if(isset($_COOKIE[$this->sessionName])){
					setcookie($this->sessionName,false,1,$this->sessionCookie['path'],$this->sessionCookie['domain'],$this->sessionCookie['secure'],$this->sessionCookie['http-only']);
				}
				
			}
			
		} else {
		
			// This is only done if sessions are actually commited and not just simulated
			if(!$this->noCommit){
		
				// If there is no session cookie from the user agent or it is set to be regenerated
				if(!isset($_COOKIE[$this->sessionName]) || $this->regenerateId){
				
					// If the old storage is assigned to be deleted
					if($this->regenerateRemoveOld){
						if(file_exists($this->cookieFolder.$this->sessionId.'.tmp')){
							unlink($this->cookieFolder.$this->sessionId.'.tmp');
						}
					}
					
					// Generating a new session ID
					$this->sessionId=$this->generateSessionId();
					// Session storage address is based on session ID name in the filesystem
					$this->cookieFolder=__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.substr($this->sessionId,0,2).DIRECTORY_SEPARATOR;
					
					// If lifetime is set to 0, then cookie will last as long as session lasts in browser
					if($this->sessionCookie['lifetime']==0){
						setcookie($this->sessionName,$this->sessionId,0,$this->sessionCookie['path'],$this->sessionCookie['domain'],$this->sessionCookie['secure'],$this->sessionCookie['http-only']);
					} else {
						setcookie($this->sessionName,$this->sessionId,(time()+$this->sessionCookie['lifetime']),$this->sessionCookie['path'],$this->sessionCookie['domain'],$this->sessionCookie['secure'],$this->sessionCookie['http-only']);
					}
					
				}
				
				// If session cookie storage subfolder does not exist, then the handler creates it
				if(!is_dir($this->cookieFolder)){
					if(!mkdir($this->cookieFolder,0755)){
						trigger_error('Cannot create session folder in '.$this->cookieFolder,E_USER_ERROR);
					}
				}
				
				// Session data is stored in session storage
				if(!file_put_contents($this->cookieFolder.$this->sessionId.'.tmp',json_encode($this->sessionData))){
					trigger_error('Cannot write session data to /filesystem/sessions/',E_USER_ERROR);
				}	
			
			}
			
		}
		
		// Commit complete
		return true;
		
	}
	
	/**
	 * This function simply sets session cookie configuration to Session Handler. This
	 * is used by Session Handler to set session cookies.
	 * 
	 * @param array $settings array of session cookie configuration
	 * @return boolean
	 */
	public function sessionCookie($settings){
	
		// Configuration must be an array
		if(is_array($settings)){
			// Overloading the default cookie session data with the new settings
			$this->sessionCookie=$settings+$this->sessionCookie;
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * This method generates a new session ID value. By default the SHA-1 hash string is generated 
	 * from user IP, server host address, request unique ID, microtime() and a custom salt.
	 *
	 * @param string $salt used for salting session hash
	 * @return string
	 */
	private function generateSessionId($salt=''){
	
		// Salt is not sent by default in Wave Framework
		return sha1(__IP__.$_SERVER['HTTP_HOST'].$_SERVER['UNIQUE_ID'].microtime().$salt);
		
	}

}
	
?>