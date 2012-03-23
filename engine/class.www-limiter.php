<?php

/*
WWW - PHP micro-framework
Request limiter class

This is an optional class that is used to limit requests based on client by Index gateway. 
WWW_Limiter can be used to block IP's if they make too many requests per minute, block requests 
if server load is detected as too high, block the request if it comes from blacklist provided 
by the system, ask for HTTP authentication or force the client to use HTTPS. Note that some 
of this functionality can be achieved by Apache configuration and modules, but it is provided 
here for cases where the project developer might not have control over server configuration.

* Requires /filesystem/limiter/ folder to be writeable by server
* Limiter does not work on Windows servers

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Limiter {

	// Directory of log files
	private $logDir;
	
	// Logger object
	public $logger=false;
	
	// Default values are assigned when Limiter is constructed
	// It is good to initiate Limiter as early as possible
	// * logDir - location of directory to store log files at
	public function __construct($logDir){
		
		// Checking if log directory is valid
		if(is_dir($logDir)){
			// Log directory is assigned
			$this->logDir=$logDir;
		} else {
			// Assigned folder is not detected as being a folder
			trigger_error('Assigned limiter folder does not exist',E_USER_ERROR);
		}
		
	}
	
	// Blocks the client if too many requests have been called per minute
	// * limit - Amount of requests that cannot be exceeded per minute
	// * duration - Duration of how long the IP will be blocked if limit is exceeded
	// Returns true if not limited, throws 403 page if limit exceeded, throws error if log file cannot be created
	public function limitRequestCount($limit=400,$duration=3600){
	
		// Limiter is only used if limit is set higher than 0 and request does not originate from the same server
		if($limit!=0 && $_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR']){
		
			// Log filename is hashed clients IP
			$logFilename=md5($_SERVER['REMOTE_ADDR']);
			
			// Subfolder name is derived from log filename
			$cacheSubfolder=substr($logFilename,0,2);
			
			// If log directory does not exist, then it is created
			$this->logDir.=$cacheSubfolder.DIRECTORY_SEPARATOR;
			if(!is_dir($this->logDir)){
			
				// Error is returned if creating the limiter folder with proper permissions does not work
				if(!mkdir($this->logDir,0777)){
					trigger_error('Cannot create limiter folder',E_USER_ERROR);
				}
				
			}
			
			// If file exists, then the amount of requests are checked, if file does not exist then it is created
			if(file_exists($this->logDir.$logFilename.'.tmp')){
			
				// Loading current contents of the file
				$data=file_get_contents($this->logDir.$logFilename.'.tmp');
				
				// If current file does not say that the IP is blocked, the request frequency is checked
				if($data!='BLOCKED'){
				
					// Limit is checked by counting the most recent requests stored in the file					
					$data=explode("\n",$data);
					if(count($data)>=$limit){
					
						// Limited amount of rows is taken from the file before data is flipped, minimizing the timestamps for check
						$data=array_slice($data,-$limit); 
						$checkData=array_flip($data);
						
						// Limit has been reached by all of the requests happening in the same minute
						if(count($checkData)==1){
						
							// Request is logged and can be used for performance review later
							if($this->logger){
								$this->logger->writeLog('403');
							}
							
							// Block file is created and 403 page thrown to the client
							file_put_contents($this->logDir.$logFilename.'.tmp','BLOCKED');
							header('HTTP/1.1 403 Forbidden');
							echo '<h1>HTTP/1.1 403 Forbidden</h1>';
							echo '<h2>Client is temporarily blacklisted</h2>';
							die();
							
						}
						
					}
					// When limit was not exceeded, file is stored again with new data
					$limiterData=implode("\n",$data);
					file_put_contents($this->logDir.$logFilename.'.tmp',$limiterData."\n".date('Y-m-d H:i',$_SERVER['REQUEST_TIME']));
					
				} else {
				
					// If the file that has blocked the requests is older than the limit duration, then block is deleted, otherwise 403 page is shown
					if(time()-filemtime($this->logDir.$logFilename.'.tmp')>=$duration){
					
						// Block file is removed
						unlink($this->logDir.$logFilename.'.tmp');
						
					} else {
					
						// Request is logged and can be used for performance review later
						if($this->logger){
							$this->logger->writeLog('403');
						}
						
						// Returning 403 data
						header('HTTP/1.1 403 Forbidden');
						echo '<h1>HTTP/1.1 403 Forbidden</h1>';
						echo '<h2>Client is temporarily blacklisted</h2>';
						die();
						
					}
					
				}
				
			} else {
			
				// Current date, hour and minute are stored in the file
				file_put_contents($this->logDir.$logFilename.'.tmp',date('Y-m-d H:i',$_SERVER['REQUEST_TIME']));
				
			}
		}
		
		// Request limiter processed
		return true;
		
	}
	
	// Blocks clients request if server load is too high
	// * limit - Server load that, if exceeded, causes the clients request to be blocked
	// Returns true if server load below limit, throws 503 page if load above limit
	public function limitServerLoad($limit=80){
	
		// System load is checked only if limit is not set
		if($limit!=0){
		
			// Returns system load in the last 1, 5 and 15 minutes.
			$load=sys_getloadavg();
			
			// 503 page is returned if load is above limit
			if($load[0]>$limit){
			
				// Request is logged and can be used for performance review later
				if($this->logger){
					$this->logger->writeLog('503');
				}
				
				// Returning 503 data
				header('HTTP/1.1 503 Service Unavailable');
				echo '<h1>HTTP/1.1 503 Service Unavailable</h1>';
				echo '<h2>Server load is too high, please try again later</h2>';
				die();
				
			}
		}
		
		// Server load limiter processed
		return true;
	
	}
	
	// Checks if current IP is listed in an array of blacklisted IP's
	// * blackList - comma-separated list of blacklisted IP addresses
	// Returns true, if not blacklisted, throws 403 error if blacklisted
	public function limitBlacklisted($blackList=''){
	
		// This value should be a comma-separated string of blacklisted IP's
		if($blackList!=''){
		
			// Exploding string of IP's into an array
			$blackList=explode(',',$blackList);

			// Checking if the client IP is set in blacklist array
			if(!empty($blackList) && in_array($_SERVER['REMOTE_ADDR'],$blackList)){
			
				// Request is logged and can be used for performance review later
				if($this->logger){
					$this->logger->writeLog('403');
				}
				
				// Returning 403 data
				header('HTTP/1.1 403 Forbidden');
				echo '<h1>HTTP/1.1 403 Forbidden</h1>';
				echo '<h2>Client is blacklisted</h2>';
				die();
				
			}
			
		}
		
		// Blacklist processed
		return true;
		
	}
	
	// Checks if client is authenticated and has provided HTTP credentials
	// * username - correct username for the request
	// * password - correct password for the request
	// Returns true if authorized, throws 401 error if incorrect credentials
	public function limitUnauthorized($username,$password){
	
		// If provided username and password are not correct, then 401 page is displayed to the client
		if(!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$username || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$password){
		
			// Request is logged and can be used for performance review later
			if($this->logger){
				$this->logger->writeLog('401');
			}
			
			// Returning 401 data
			header('WWW-Authenticate: Basic realm="Login"');
			header('HTTP/1.1 401 Unauthorized');
			echo '<h1>HTTP/1.1 401 Unauthorized</h1>';
			echo '<h2>Username and password need to be provided by the client</h2>';
			die();
			
		}
		
		// HTTP authorization processed
		return true;
		
	}
	
	// Redirects the client to HTTPS or throws an error if HTTPS is not used
	// * autoRedirect - If this is set to true, then system redirects client to HTTPS
	// Returns true if on HTTPS, redirects the client or throws 401 page if not
	public function limitNonSecureRequests($autoRedirect=true){
	
		// HTTPS is detected from $_SERVER variables
		if(!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on')){
		
			// If auto redirect is on, client is forwarded by replacing the http:// protocol with https://
			if($autoRedirect){
			
				// Redirecting to HTTPS address
				header('Location: '.str_replace('http://','https://',$_SERVER['SCRIPT_URI']));
				
			} else {
			
				// Request is logged and can be used for performance review later
				if($this->logger){
					$this->logger->writeLog('401');
				}
				
				// Returning 401 data
				header('HTTP/1.1 401 Unauthorized');
				echo '<h1>HTTP/1.1 401 Unauthorized</h1>';
				echo '<h2>Client needs to connect through HTTPS</h2>';
				
			}
			
			// Script is halted
			die();
			
		}
		
		// HTTPS check processed
		return true;
		
	}

}
	
?>